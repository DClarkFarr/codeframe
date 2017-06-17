<?php

use Illuminate\Session\Store;
use Illuminate\Session\DatabaseSessionHandler;

class Session{
	/**
	 * Property: name
	 * =========================================================================
	 * Used to identify the session, the name of the actual session cookie.
	 */
	protected $injectName;
	/**
	 * Property: lifetime
	 * =========================================================================
	 * The time in seconds before garbage collection is run on the server.
	 */
	protected $injectLifetime;
	/**
	 * Property: path
	 * =========================================================================
	 * This is passed directly to setcookie.
	 * See: http://php.net/manual/en/function.setcookie.php
	 */
	protected $injectPath;
	/**
	 * Property: domain
	 * =========================================================================
	 * This is passed directly to setcookie.
	 * See: http://php.net/manual/en/function.setcookie.php
	 */
	protected $injectDomain;
	/**
	 * Property: secure
	 * =========================================================================
	 * This is passed directly to setcookie.
	 * See: http://php.net/manual/en/function.setcookie.php
	 */
	protected $injectSecure;
	/**
	 * Property: dbConfig
	 * =========================================================================
	 * This is an array of configuration data that can be used to create the
	 * dbConnection. This **must** be injected, if you do not inject your very
	 * own dbConnection.
	 */
	protected $injectDbConfig;
	/**
	 * Property: table
	 * =========================================================================
	 * The name of the database table to use for session storage.
	 */
	protected $injectTable;
	/**
	 * Property: dbConnection
	 * =========================================================================
	 * An instance of ```\Illuminate\Database\Connection```.
	 */
	protected $injectDbConnection;
	/**
	 * Property: databaseSessionHandler
	 * =========================================================================
	 * An instance of ```Illuminate\Session\DatabaseSessionHandler```.
	 */
	protected $injectDatabaseSessionHandler;
	/**
	 * Property: sessionStore
	 * =========================================================================
	 * An instance of ```Illuminate\Session\Store```.
	 */
	protected $injectSessionStore;
	/**
	 * Property: expired
	 * =========================================================================
	 * We have added in some extra functionality. We can now easily check to
	 * see if the session has expired. If it has we reset the cookie with a
	 * new id, etc.
	 */
	private $expired = false;
	/**
	 * Property: instance
	 * =========================================================================
	 * This is used as part of the globalise functionality.
	 */
	private static $instance;
	/**
	 * Method: setDefaults
	 * =========================================================================
	 * This is where we set all our defaults. If you need to customise this
	 * container this is a good place to look to see what can be configured
	 * and how to configure it.
	 * 
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 * 
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function setDefaults(){
		$config = Config::get('session');

		$this->name = $config->name;
		$this->table = $config->table;
		$this->lifetime = $config->lifetime;
		$this->path = $config->path;
		$this->domain = $config->domain;
		$this->secure = $config->secure;

		$connection_name = $config->connection;

		$this->expires = time() + $this->lifetime;
		$this->dbConnection = DB::$capsule;

		$this->databaseSessionHandler = new DatabaseSessionHandler(
			$this->dbConnection->connection($connection_name),
			$this->table,
			$this->lifetime / 60
		);
		$this->sessionStore = new Store(
			$this->name,
			$this->databaseSessionHandler
		);
	}

	static function bootstrap(){
		$session = new Session();

		$session->setDefaults();

		// Install the session api
		$session->install();
	}
	/**
	 * Method: install
	 * =========================================================================
	 * Once the container has been configured. Please call this method to
	 * install the session api into your application.
	 *
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * - $global: If set to true we will also run globalise after setup.
	 *
	 * Returns:
	 * -------------------------------------------------------------------------
	 * void
	 */
	public function install(){
		// Make sure we have a sessions table
		$schema = $this->dbConnection->connection()->getSchemaBuilder();
		if (!$schema->hasTable($this->table)){

			$schema->create($this->table, function ($table) {
			    $table->string('id')->unique();
			    $table->unsignedInteger('user_id')->nullable();
			    $table->string('ip_address', 45)->nullable();
			    $table->text('user_agent')->nullable();
			    $table->text('payload');
			    $table->integer('last_activity');
			});
		}
		// Run the garbage collection
		$this->sessionStore->getHandler()->gc($this->expires);
		
		// Check for our session cookie
		if (isset($_COOKIE[$this->name])){
			// Grab the session id from the cookie
			$cookie_id = $_COOKIE[$this->name];
			// Does the session exist in the db?
			$session = (object) $this->dbConnection
				->table($this->table)
				->find($cookie_id)
			;

			if(!empty($session)){
				$this->sessionStore->setId($cookie_id);
			}else{
				// Set the expired flag
				$this->expired = true;
				// NOTE: We do not need to set the id here.
				// As it has already been set by the constructor of the Store.
			}				
		}
		// Set / reset the session cookie
		if (!isset($_COOKIE[$this->name]) || $this->expired){
			setcookie(
				$this->name,
				$this->sessionStore->getId(),
				$this->expires,
				$this->path,
				$this->domain,
				$this->secure,
				true
			);
		}
		// Start the session
		$this->sessionStore->start();
	
		self::$instance = $this;
	}
	/**
	 * Method: hasExpired
	 * =========================================================================
	 * Pretty simple, if the session has previously been set and now has been
	 * expired by means of garbage collection on the server, this will return
	 * true, otherwise false.
	 *
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * n/a
	 *
	 * Returns:
	 * -------------------------------------------------------------------------
	 * boolean
	 */
	public function hasExpired()
	{
		return $this->expired;
	}
	/**
	 * Method: regenerate
	 * =========================================================================
	 * When the session id is regenerated we need to reset the cookie.
	 *
	 * Parameters:
	 * -------------------------------------------------------------------------
	 * - $destroy: If set to true the previous session will be deleted.
	 *
	 * Returns:
	 * -------------------------------------------------------------------------
	 * boolean
	 */
	public function regenerate($destroy = false){
		if ($this->sessionStore->regenerate($destroy)){
			setcookie(
				$this->sessionStore->getName(),
				$this->sessionStore->getId(),
				0,
				$this->path,
				$this->domain,
				$this->secure,
				true
			);
			return  true;
		}else{
			return false;
		}
	}
	
	static $write_funcs = ['put', 'push', 'flash', 'forget', 'flush'];
	public function __call($name, $args){
		$res = call_user_func_array([$this->sessionStore, $name], $args);
		if(in_array($name, self::$write_funcs)){
			$this->sessionStore->save();
		}
		return $res;
	}
	
	public static function __callStatic($name, $args){
		if (empty(self::$instance)){
			throw new RuntimeException('You need to run install first!');
		}

		$res = call_user_func_array([self::$instance, $name], $args);
		if(in_array($name, self::$write_funcs)){
			self::$instance->sessionStore->save();
		}
		return $res;
	}
}