<?php
/**
 * Fetch input values safely with support for default values.
 */
class I
{
	public static function __callStatic($method, $args)
	{
		// Function calls are slow
		//$method = '_' . strtoupper($method);

		$types = array(
			'session' => '_SESSION',
			'post' => '_POST',
			'get' => '_GET',
			'server' => '_SERVER',
			'files' => '_FILES',
			'cookie' => '_COOKIE',
			'env' => '_ENV',
			'request' => '_REQUEST'
		);

		$method = $types[$method];

		if(isset($GLOBALS[$method][$args[0]]))
		{
			return $GLOBALS[$method][$args[0]];
		}

		return isset($args[1]) ? $args[1] : NULL;
	}
}
