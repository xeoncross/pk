<?php
/**
 * Query databases using PDO
 */
class DB
{
	public $i='`', $c;
	static $q;

	/**
	 * Set the database connection on creation
	 *
	 * @param object $c PDO connection object
	 */
	function DB($connection)
	{
		$this->c = $connection;
	}

	/**
	 * Fetch a column offset from the result set (COUNT() queries)
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @param integer $key index of column offset
	 * @return array|null
	 */
	function column($query, $params = NULL, $key = 0)
	{
		if($statement = $this->query($query, $params))
			return $statement->fetchColumn($key);
	}

	/**
	 * Fetch a single query result row
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return object|null
	 */
	function row($query, $params = NULL)
	{
		if($statement = $this->query($query, $params))
			return $statement->fetch();
	}

	/**
	 * Fetch all query result rows
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return array|null
	 */
	function fetch($query, $params = NULL)
	{
		if($statement = $this->query($query, $params))
			return $statement->fetchAll();
	}

	/**
	 * Prepare and send a query returning the PDOStatement
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return object|null
	 */
	function query($query, $params = NULL)
	{
		$statement = $this->c->prepare(self::$q[] = strtr($query, '`', $this->i));
		$statement->execute($params);
		return $statement;
	}

	/**
	 * Insert a row into the database
	 *
	 * @param string $table name
	 * @param array $data row data
	 * @return integer|null
	 */
	function insert($table, $data)
	{
		$x = $this;
		$query = "INSERT INTO `$table`(`" . implode('`,`', array_keys($data)) . '`)VALUES('
				. rtrim(str_repeat('?,', count($data = array_values($data))), ',') . ')';

		return $x->p ? $x->column($query . 'RETURNING `id`', $data) : ($x->query($query, $data) ? $x->c->lastInsertId() : NULL);
	}

	/**
	 * Update a database row
	 *
	 * @param string $table name
	 * @param array $data row data
	 * @param array $where where conditions
	 * @return integer|null
	 */
	function update($table, $data, $where)
	{
		$query = "UPDATE `$table` SET `" . implode('`=?,`', array_keys($data)) . '`=? WHERE '
				. (is_array($where) ? $this->where($where, $data) : $where);

		if($statement = $this->query($query, array_values($data)))
			return $statement->rowCount();
	}

	/**
	 * Issue a delete query
	 *
	 * @param string $table name
	 * @param array $where where conditions
	 * @return integer|null
	 */
	function delete($table, $where)
	{
		$params;
		if($statement = $this->query("DELETE FROM `$table` WHERE ".(is_array($where) ? $this->where($where, $params) : $where), $params))
			return $statement->rowCount();
	}

	/**
	 * Parse an array of WHERE conditions
	 *
	 * @param array $where where conditions
	 * @param array $data query parameters
	 * @return string
	 */
	function where($where, &$params)
	{
		$string;
		foreach($where as $column => $value)
		{
			$string[] = "`$column`=?";
			$params[] = $value;
		}
		return join(' AND ', $string);
	}
}
