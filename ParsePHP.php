<?php
/**
 * PHP Code Parser
 *
 * Currently used to minimize the classes of this project to get the actual
 * character count of the code written.
 */
class ParsePHP
{
	// Array of all parser tokens
	public $parser_tokens;

	// Tokens from the source
	public $tokens;

	// Convert constant names to int values. Not cross-platform (or cross-version) safe!
	//public $convert_constants = TRUE;

	/**
	 * Load the given PHP code or token array for parsing
	 *
	 * @param string|object $class to compress and obfuscate
	 */
	public function __construct($class)
	{
		// First we need to reflect the class
		$reflectedClass = new ReflectionClass($class);

		$code = '';

		if($properties = $reflectedClass->getDefaultProperties())
		{
			if($static = $reflectedClass->getStaticProperties())
			{
				$static = array_keys($static);
			}

			if($static) $code .= "\nstatic$" . join(',$', $static) . ';';

			$keys = array_diff(array_keys($properties), $static);

			if($keys)
			{
				$code .= "\npublic";
				foreach($keys as $value)
				{
					if($properties[$value] !== NULL)
					{
						$code .= '$' . $value . '=\'' . $properties[$value] . '\',';
					}
					else
					{
						$code .= '$' . $value . ',';
					}
				}
				$code = rtrim($code, ',') . ';';
			}
		}

		// Second, we need to obfuscate and compress each method of the class
		$code .= $this->obfuscate($reflectedClass);

		// Last, we take the compressed class and tokenize it
		$this->tokens = token_get_all("<?php class $class\n{\n$code\n}\n");

		// Load all parser tokens incase a child class wants them
		for ($i = 100; $i < 500; $i++)
		{
			if(($name = @token_name($i)) == 'UNKNOWN') continue;
			$this->parser_tokens[$i] = $name;
		}
	}


	/**
	 * Compress the function code by replacing variables
	 *
	 * @param object $method ReflectionMethod object
	 * @return string
	 */
	function obfuscate(ReflectionClass $reflectedClass)
	{
		// Get each methods source code
		$sources = array();
		foreach($reflectedClass->getMethods() as $method)
		{
			$sources[] = $this->getSource($method);
		}

		$output = '';
		$letters = range('a', 'z');
		//$constants = $reflectedClass->getConstants();

		// Compress each method independent of the others
		foreach($sources as $source)
		{
			/* @todo, this is not working yet... and perhaps shouldn't be...
			// Replace constants with their integer values (DANGEROUS!)
			if($this->convert_constants)
			{
				$source = str_replace(array_keys($constants), $constants, $source);
			}
			*/

			// Tokenize the method code so we can compress it correctly (remove open/close tag)
			$tokens = array_slice(token_get_all("<?php $source"), 1);
			$variables = array();

			foreach($tokens as $c)
			{
				if(is_array($c))
				{
					// Do not replace $this with a short name!
					if($c[0] === T_VARIABLE AND $c[1] !== '$this')
					{
						if( ! isset($variables[$c[1]]))
						{
							// The first item of the difference is the value we use
							$result = array_diff($letters, $variables);
							$variables[$c[1]] = array_shift($result);
						}
						$c[1] = '$' . $variables[$c[1]];

					}
					$output .= $c[1];
				}
				else
				{
					$output .= $c;
				}
			}
		}

		return $output;
	}


	/**
	 * Retrieve the contents of the class methods
	 *
	 * @param object $method the ReflectionMethod object
	 * @return string the contents of the method
	 */
	function getSource(ReflectionMethod $method)
	{
		$reflect = new ReflectionMethod($method->class, $method->name);

		$file = new \SplFileObject($reflect->getFileName());
		$file->seek(($reflect->getStartLine()-1));

		$code = '';

		while($file->key() < $reflect->getEndLine())
		{
			$code .= $file->current();
			$file->next();
		}

		//$begin = strpos($code, 'function');
		$begin = 0;
		$end = strrpos($code, '}');
		$code = substr($code, $begin, ($end - $begin + 1));

		return $code;
	}


	/**
	 * Remove unneeded code tokens such as comments and whitespace.
	 */
	public function minimize()
	{
		$remove = array_flip(array(
			T_END_HEREDOC,
			T_PRIVATE,
			//T_PUBLIC,
			T_PROTECTED,
			T_WHITESPACE,	// "\t \r\n"
			T_COMMENT,		// // or #, and /* */ in PHP 5
			T_DOC_COMMENT,	// /** Docblock
			T_BAD_CHARACTER,// anything below ASCII 32 except \t, \n and \r
			//T_OPEN_TAG	// < ?php open tag
		));

		$replace = array(
			T_PRINT => 'echo',
			T_LOGICAL_AND => '&&',
			T_LOGICAL_OR => '||',
			T_BOOL_CAST => '(bool)',
			T_INT_CAST => '(int)',
		);

		$add_space_before = array_flip(array(
			T_AS,
		));

		$add_space_after = array_flip(array(
			T_CLASS,
			T_CLONE,
			T_CONST,
			T_FINAL,
			T_FUNCTION,
			T_INSTANCEOF,
			T_NAMESPACE,
			T_NEW,
			T_STATIC,
			T_THROW,
			T_USE
		));

		$add_space = array_flip(array(
			T_EXTENDS,
			T_IMPLEMENTS,
			T_INTERFACE
		));

		$tokens = $this->tokens;

		foreach($tokens as $id => $token)
		{
			// Control characters
			if( ! is_array($token)) continue;

			list($code, $string, $line) = $token;

			// Might be able to *shrink* some stuff
			if(isset($replace[$code]))
			{
				$tokens[$id] = array($code, $replace[$code], $line);
				continue;
			}

			// Remove some stuff
			if(isset($remove[$code]))
			{
				unset($tokens[$id]);
				continue;
			}

			// "function my_function()" = T_FUNCTION then T_WHITESPACE then T_STRING
			if(isset($add_space[$code]))
			{
				$tokens[$id] = array($code, ' ' . $string . ' ', $line);
			}

			if(isset($add_space_before[$code]))
			{
				$tokens[$id] = array($code, ' ' . $string, $line);
			}

			if(isset($add_space_after[$code]))
			{
				$tokens[$id] = array($code, $string . ' ', $line);
			}

			// Look ahead for returnfunction() vs return$variables
			if($code == T_RETURN)
			{
				// Is there a function two places ahead?
				if(isset($tokens[$id + 2][0]))
				{
					$next = $tokens[$id + 2];
					if($next[0] == T_STRING)
					{
						$tokens[$id] = array($code, $string . ' ', $line);
					}
				}
			}
		}

		return $this->tokens = $tokens;
	}


	/**
	 * Convert the tokens back into a string of PHP code
	 *
	 * @return string
	 */
	public function __toString()
	{
		$output = '';

		foreach($this->tokens as $id => $token)
		{
			// Control characters
			if( ! is_array($token))
			{
				$output .= $token;
				continue;
			}

			$output .= $token[1];
		}

		return $output;
	}

}
