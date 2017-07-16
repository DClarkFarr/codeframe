<?php
use Illuminate\Database\Capsule\Manager as Capsule;

class DB {
	static $capsule;
	static $PDO;
	static $MYSQLI;

	static function bootstrap(){
		self::$capsule = new Capsule;

		$connection_name = Config::get('database.default_connection');

		$connection = array_merge([
		    'driver'    => 'mysql',	    
		    'charset'   => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		    'prefix'    => '',
		], Config::get('database.connections')[$connection_name]);

		self::$capsule->addConnection($connection);

		self::$capsule->setAsGlobal();
		self::$capsule->bootEloquent();

		self::$PDO = DB::$capsule->connection()->getPdo();

		self::$MYSQLI = Mysqli\connect_db($connection);
	}
	static function connection(){
		return self::$capsule->connection();
	}
	static function pdo(){
		return self::$PDO;
	}
	static function mysqli(){
		return self::$MYSQLI;
	}
}