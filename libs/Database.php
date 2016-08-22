<?php

class Database extends PDO {

	public $rowCount = 0;

	public function __construct($dbType, $dbHost, $dbDatabase, $dbUser, $dbPass){
		$dsn = $dbType . ':host=' . $dbHost . ';dbname='. $dbDatabase;
		try {
			parent::__construct($dsn, $dbUser, $dbPass);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	/**
	 * select
	 * @param string $sql An SQL query string
	 * @param array $array Parameters to bind
	 * @param constant $fetchMode A PDO Fetch mode
	 * @return mixed, false if no rows
	 */
	public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		$sth = $this->prepare($sql);
		foreach($array as $key => $value){
			$sth->bindValue($key, $value);
		}
		$sth->execute();
		if ($sth->rowCount() > 0){
			$this->rowCount = $sth->rowCount();
			return $sth->fetchAll($fetchMode);
		} else {
			return false;
		}
	}

	public function selectSingle($table, $field, $where)
	{
		$query = "SELECT $field FROM $table WHERE $where";
		if(!$result = $this->select($query)) {
			return false;
		}
		return $result[0][$field];
	}

	/**
	 * delete
	 * @param string $table The table to delete from
	 * @param string $where The WHERE string
	 *	@param integer $limit
	 * @return integer
	 */
	public function delete($table, $where, $limit = 1)
	{
		$return = $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
	}

	/**
	 * insert
	 * @param string $table A table name to insert into
	 * @param array $data An Associative array of data to be inserted ($field => $value)
	 */
	public function insert($table, $data)
	{
		ksort($data);
		$fieldNames = implode('`, `', array_keys($data));
		$fieldValues = ':'. implode(', :', array_keys($data));

		$query = "INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)";
		$sth = $this->prepare($query);

		foreach($data as $key => $value){
			$sth->bindValue(":$key", $value);
		}
		$sth->execute();
	}

	/**
	 * update
	 * @param string $table A table name to update
	 * @param array $data An Associative array
	 * @param string $where The WHERE query bit
	 */
	public function update($table, $data, $where)
	{
		ksort($data);
		$fieldDetails = NULL;
		foreach($data as $key => $value){
			$fieldDetails .= "`$key` = :$key,";
		}

		$fieldDetails = rtrim($fieldDetails, ',');

		$query = "UPDATE $table SET $fieldDetails WHERE $where";
		$sth = $this->prepare($query);

		foreach($data as $key => $value){
			$sth->bindValue(":$key", $value);
		}

		$sth->execute();
		if ($sth->rowCount() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * countRows
	 * @param string $table A table name to check
	 * @param array $array Parameters to bind
	 * @param string $whereValue The value in a simple WHERE clause
	 */
	public function countRows($table, $where, $data)
	{
		$query = "SELECT * FROM $table WHERE $where";
		if($this->select($query, $data)) {
			return $this->rowCount;
		} else {
			return 0;
		}
	}
}