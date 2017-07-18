<?php 
namespace Codeframe\Traits;

trait Storage {
	static function _get($obj, $key){
		if(is_array($key)){
			$res = [];
			foreach($key as $v){
				$res[$v] = self::getRecursive($obj, $v);
			}
			return $res;
		}else{
			return self::getRecursive($obj, $key);
		}
	}
	static function _put(&$obj, $key, $val = null){
		if(is_array($key)){
			foreach($key as $k => $v){
				self::putRecursive($obj, $k, $v, true);
			}
		}else{
			self::putRecursive($obj, $key, $val, true);
		}
	}
	static function _merge(&$obj, $key, $val = null){
		if(is_array($key)){
			foreach($key as $k => $v){
				self::putRecursive($obj, $k, $v, false);
			}
		}else{
			self::putRecursive($obj, $key, $val, false);
		}
	}

	static function putRecursive(&$base, $key, $value, $createOnEmpty = null){
		//if full key matches, set
		if(self::isProp($base, $key)){
			self::setProp($base, $key, $value);
			return;
		}

		$segments = $key;
		if(!is_array($segments)){
			$segments = explode('.', $segments);
		}

		//matched fullest path
		for($i = count($segments) - 1; $i > -1; $i--){

			$str = implode('.', array_slice($segments, 0, $i > 0 ? $i : null));
			
			if(self::isProp($base, $str)){
				if(is_array($base)){
					self::putRecursive($base[$str], implode('.', array_slice($segments, $i)), $value, $createOnEmpty);
				}else if(is_object($base)){
					self::putRecursive($base->$str, implode('.', array_slice($segments, $i)), $value, $createOnEmpty);
				}
				return;
			}
		}	

		//didn't match, so build if createOnEmpty
		if($segments && $createOnEmpty){
			$seg = array_shift($segments);

			if($segments){
				if(is_array($base)){
					$base[$seg] = [];
					self::putRecursive($base[$seg], implode('.', $segments), $value, $createOnEmpty);
				}else if(is_object($base)){
					$base->$seg = (object) [];
					self::putRecursive($base->$seg, implode('.', $segments), $value, $createOnEmpty);
				}
			}else{
				if(is_array($base)){
					$base[$seg] = $value;
				}else if(is_object($base)){
					$base->$seg = $value;
				}
			}
		}

		
	}
	static function isProp($obj, $prop){
		if(is_object($obj) && property_exists($obj, $prop)){
			return true;
		}else if(is_array($obj) && isset($obj[$prop])){
			return true;
		}
	}
	static function prop($obj, $prop){
		if(empty($obj)){
			return null;
		}
		if(is_object($obj) && property_exists($obj, $prop)){
			return $obj->$prop;
		}else if(is_array($obj) && isset($obj[$prop])){
			return $obj[$prop];
		}
		return null;
	}
	static function setProp(&$obj, $prop, $val){
		if(is_object($obj)){
			$obj->$prop = $val;
		}else if(is_array($obj)){
			$obj[$prop] = $val;
		}
		return $val;
	}
	static function getRecursive($base, $segments){
		if(!is_array($segments)){
			$segments = explode('.', $segments);
		}
		$str = "";

		while($seg = array_shift($segments)){
			$str .= ($str ? '.' : '') . $seg;

			if(is_object($base) && !empty($base->$str)){
				$base = self::getRecursive($base->$str, $segments);
				break;
			}else if(is_array($base) && isset($base[$str])){
				$base = self::getRecursive($base[$str], $segments);
				break;
			}else{
				$base = null;
			}
		}
		return $base;
	}
}