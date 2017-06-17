<?php 

class Config {
	static $properties;

	static $autoload_files = [
		'database' => 'database.php',
		'app' => 'app.php',
		'session' => 'session.php',
	];

	static function bootstrap(){
		self::$properties = (object) [];
		self::includeFiles();
	}
	static function includeFiles(){
		foreach(self::$autoload_files as $property => $file){
			$path = __DIR__ . '/config/' . $file;
			if(is_file($path)){
				self::put($property, (object) include $path);
			}
		}

	}

	static function put($key, $val = null){	
		if(is_array($key)){
			foreach($key as $k => $v){
				self::putRecursive(self::$properties, $k, $v);
			}
		}else{
			self::putRecursive(self::$properties, $key, $val);
		}
	}

	static function putRecursive(&$base, $segments, $value){
		$str = "";
		if(!is_array($segments)){
			$segments = explode('.', $segments);
		}
		while($seg = array_shift($segments)){
			$str .= ($str ? "." : "") . $seg;
			if(!empty($base->$str)){
				if($segments){
					self::putRecursive($base->$str, $segments, $value);
					return;
				}
			}
		}
		if($str){
			$segments = explode('.', $str);
			$new = $base;
			while($seg = array_shift($segments)){
				if($segments){
					if(empty($new->$seg)){
						$new = $new->$seg = (object) [];
					}else{
						$new = $new->$seg;
					}
				}else{
					$new->$seg = $value;
				}
				
			}
		}
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
			}else if(is_array($base) && isset($base[$str])){
				$base = self::getRecursive($base[$str], $segments);
			}
		}
		return $base;
	}
	static function get($key){
		if(is_array($key)){
			$res = [];
			foreach($key as $v){
				$res[$v] = self::getRecursive(self::$properties, $v);
			}
			return $res;
		}else{
			return self::getRecursive(self::$properties, $key);
		}
	}
}