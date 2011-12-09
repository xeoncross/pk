<?php
/**
 * Query databases using PDO
 */
class DB
{
	public$i='`',$p;
	static$q;

	/**
	 * Set the database connection on creation
	 *
	 * @param object $c PDO connection object
	 */
	function DB($c)
	{
		$this->c = $c;
	}

	/**
	 * Fetch a column offset from the result set (COUNT() queries)
	 *
	 * @param string $q query string
	 * @param array $p query parameters
	 * @param integer $k key index of column offset
	 * @return array|null
	 */
	function column($q,$p=NULL,$k=0)
	{
		if($s=$this->query($q,$p))
			return$s->fetchColumn($k);
	}

	/**
	 * Fetch a single query result row
	 *
	 * @param string $q query string
	 * @param array $p query parameters
	 * @return object|null
	 */
	function row($q,$p=NULL)
	{
		if($s=$this->query($q,$p))
			return$s->fetch();
	}

	/**
	 * Fetch all query result rows
	 *
	 * @param string $q query string
	 * @param array $p query parameters
	 * @return array|null
	 */
	function fetch($q,$p=NULL)
	{
		if($s=$this->query($q,$p))
			return$s->fetchAll();
	}

	/**
	 * Prepare and send a query returning the PDOStatement
	 *
	 * @param string $q query string
	 * @param array $p query parameters
	 * @return object|null
	 */
	function query($q,$p=NULL)
	{
		$s=$this->c->prepare(self::$q[]=strtr($q,'`',$this->i));
		$s->execute($p);
		return$s;
	}

	/**
	 * Insert a row into the database
	 *
	 * @param string $t table name
	 * @param array $d row data
	 * @return integer|null
	 */
	function insert($t,$d)
	{
		$x=$this;
		$q="INSERT INTO `$t`(`".implode('`,`',array_keys($d)).'`)VALUES('.rtrim(str_repeat('?,',count($d=array_values($d))),',').')';
		return$x->p?$x->column($q.'RETURNING `id`',$d):($x->query($q,$d)?$x->c->lastInsertId():NULL);
	}

	/**
	 * Update a database row
	 *
	 * @param string $t table name
	 * @param array $d row data
	 * @param array $w where conditions
	 * @return integer|null
	 */
	function update($t,$d,$w)
	{
		$q="UPDATE `$t` SET `".implode('`=?,`',array_keys($d)).'`=? WHERE '.(is_array($w)?$this->where($w,$d):$w);
		if($s=$this->query($q,array_values($d)))
			return$s->rowCount();
	}

	/**
	 * Issue a delete query
	 *
	 * @param string $t table name
	 * @param array $w where conditions
	 * @return integer|null
	 */
	function delete($t,$w)
	{
		$p;
		if($s=$this->query("DELETE FROM `$t` WHERE ".(is_array($w)?$this->where($w,$p):$w),$p))
			return$s->rowCount();
	}

	/**
	 * Parse an array of WHERE conditions
	 *
	 * @param array $w where conditions
	 * @param array $d query parameters
	 * @return string
	 */
	function where($w,&$p)
	{
		$s;
		foreach($w as$c=>$v){$s[]="`$c`=?";$p[]=$v;}
		return join(' AND ',$s);
	}
}
