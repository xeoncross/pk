<?php
/**
 * Validation class based on anonymous functions
 *
 * @see http://php.net/manual/en/functions.anonymous.php
 */
class Validation
{
	protected $s=array();

	function __set($k,$c)
	{
		$this->s[$k]=$c;
	}

	function __get($k)
	{
		return$this->s[$k]($this);
	}

	/**
	 * Validate the given array of data using the functions set
	 *
	 * @param array $d data to validate
	 * @return array
	 */
	function validate($d)
	{
		$a;
		foreach($this->s as$k=>$f)
		{
			if($e=$f(isset($d[$k])?$d[$k]:NULL,$k))
			{
				$a[$k]=$e;
			}
		}
		return$a;
	}
}
