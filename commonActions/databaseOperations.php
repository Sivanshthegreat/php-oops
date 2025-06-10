<?php

namespace commonActions;

require_once "config/configuration.php";

use config\DBConnection as database;

class databaseOperation extends database
{
	protected $connection = null;

	public function __construct()
	{
		$this->connection = static::getInstance()?->connection();
	}

	/** Method to run a Query
	 * @param $query String
	 * @return Boolean | Object
	 */
	public function runQuery($query)
	{
		return $this->connection->exec($query);
	}

	/** Method to return last inserted id inside a table
	 * @return Integer
	 */
	public function lastInsertedId()
	{
		return $this->connection->lastInsertId();
	}

	/** Method to insert a single row in database table 
	 * @param $tableName String - Name of the Table
	 * @param $columns Array - Array of Column Names
	 * @param $values Array - Array of values to be inserted
	 * @return Integer
	*/
	public function insert($tableName, $columns, $values)
	{
		$columns = implode(",", $columns);
		$values = implode("','", $values);
		$query = "INSERT INTO $tableName ($columns) VALUES ('$values')";
		
		self::runQuery($query);
		return self::lastInsertedId();
	}

	/** Method to insert multiple rows in database table
	 * @param $tableName String - Name of the Table
	 * @param $columns Array - Array of Column Names
	 * @param $values Array of Arrays - Array of arrays( value's array) Example: [ ['Shubham','Dhangar'], ['Varun', 'Kumar'] ]
	 * @return Boolean
	*/
	public function insertMultiple($tableName, $columns, $arrayOfArrayvalues)
	{
		//Columns
		$columns = implode(",", $columns);

		//Structure values provided in multiple arrays
		$multipleValuesString = [];
		foreach ($arrayOfArrayvalues as $arrayValues) {
			$values = implode("','", $arrayValues);

			$multipleValuesString[] = "('$values')";
		}

		$multipleValuesString = implode(',', $multipleValuesString);
		$query = "INSERT INTO $tableName ($columns) VALUES ";

		return self::runQuery($query);
	}

	/** Delete row(s) from a table
	 * @param $tableName String- name of the table
	 * @param $conditions String- conditions comma seperated Example: "name='deepak' and company='TCS'"
	 * @return Boolean
	 */
	public function delete($tableName, $conditions = '')
	{
		$dynamicString = '';
		if (!$conditions) {
			$dynamicString = "WHERE $conditions";
		}

		$query = "DELETE FROM $tableName $dynamicString";
		return self::runQuery($query);
	}

	/** Update row(s) in a table
	 * @param $tableName String- name of the table
	 * @param $columnValueArray Array - ['name'=>'shubham','age'=>26]
	 * @param $conditions String- conditions comma seperated Example: "name='deepak' and company='TCS'"
	 * @return Integer - Number of rows updated
	 */
	public function update($tableName, $columnValueArray ,$conditions = '')
	{
		//structure updating values
		$updateString = '';
		foreach ($columnValueArray as $column=>$value) {
			$updateString = "$column = '$value',";
		}
		$updateString = rtrim(",",$updateString);

		$dynamicStringCondition = '';
		if (!$conditions) {
			$dynamicStringCondition = "WHERE $conditions";
		}

		$query = "UPDATE $tableName set $updateString $dynamicStringCondition";
		$statement = $this->connection->prepare($query);
		$statement->execute();
		return $statement->rowCount();
	}

	/** 
	 * Select row(s) from a table
	 * @param $query String - SQL Query
	 * @return Array
	 */
	public function fetchData($query,$multiple = false)
	{
		$query = $this->connection->query($query);

		//Return first result
		if (!$multiple) {
			return $query->fetch_assoc();
		}
		
		//Creating array of result
		$allData = [];
		while($data = $query->fetch_assoc()) {
			$allData[] = $data;
		}
		
		return $allData;
	}
}