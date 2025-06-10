<?php

namespace config;

/**
 * A Singleton Class for database connection
 */
class DBConnection
{
	private static $instance = null;
	protected $conn;
	protected function __construct()
	{
		$databaseName = "basic-setup";
		$databaseUserName = "root";
		$host = "localhost";
		$dbPassword = "root@123";

		try {
			$this->conn = new \PDO("mysql:host=$host;dbname=$databaseName",$databaseUserName,$dbPassword );
			
			// set the PDO error mode to exception
  			$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
		} catch(\PDOException $e) {
			die("Connection failed: " . $e->getMessage());
		}
	}

	/**Prevent unserialization */
	protected function __wakeup()
	{

	}

	/**Prevent cloning of this object */
	protected function _clone()
	{
		
	} 

	/**Method to create an instance of the class
	 * @return Object of this class
	 */
	public static function getInstance()
	{
		if ( self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Method to return connection variable of database to perform operations
	 * @return connection object of mysql
	*/
	public function connection()
	{
		return $this->conn;
	}

	/**
	 * Closing the connection
	 */
	public function __destruct()
	{
		$this->conn = null;
	}
}