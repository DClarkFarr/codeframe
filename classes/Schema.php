<?php
namespace Codeframe;

class Schema {
	static $instance;

	static function bootstrap(){
		self::$instance = DB::schema();	
	}

	public static function __callStatic($name, $args){
		if (empty(self::$instance)){
			throw new \RuntimeException('Instance uninitialized. You need to run bootstrap first!');
		}
		if(!method_exists(self::$instance, $name)){
			throw new \RuntimeException('Method Schema::' . $name . '() does not exist.');
		}
		return call_user_func_array([self::$instance, $name], $args);		
	}
}