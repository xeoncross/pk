<?php
/**
 * HTML template views
 */
class View
{
	private $__v;

	/**
	 * Returns a new view object for the given view.
	 *
	 * @param string $file the view file to load
	 * @param string $path to load from
	 */
	public function __construct($file, $path = __DIR__)
	{
		$this->__v = "$path/view/$file.php";
	}


	/**
	 * Convert special characters to HTML safe entities.
	 *
	 * @param string $s string to encode
	 * @return string
	 */
	public function e($s)
	{
		return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	}


	/**
	 * Convert dangerous HTML entities into special characters
	 *
	 * @param string $s string to decode
	 * @return string
	 */
	public function d($s)
	{
		return htmlspecialchars_decode($s, ENT_QUOTES, 'UTF-8');
	}


	/**
	 * Allows setting view values while still returning the object instance.
	 * $view->title($title)->text($text);
	 *
	 * @return this
	 */
	public function __call($key, $args)
	{
		$this->$key = $args[0];
		return $this;
	}

	/**
	 * Set an array of values
	 *
	 * @param array $array of values
	 */
	public function set($a)
	{
		foreach($a as $k => $v) $this->$k = $v;
		return $this;
	}


	/**
	 * Return the view's HTML
	 *
	 * @return string
	 */
	public function __toString()
	{
		try {
			ob_start();
			extract((array) $this);
			require $__v;
			return ob_get_clean();
		}
		catch(\Exception $e)
		{
			return '' . $e;
		}
	}


	/**
	 * Compiles an array of HTML attributes into an attribute string and
	 * HTML escape it to prevent malformed (but not malicious) data.
	 *
	 * @param array $a the tag's attribute list
	 * @return string
	 *
	public static function attributes(array $a = NULL)
	{
		if( ! $a) return;

		asort($a);

		$h = '';
		//foreach($a as $k => $v) $h .= " $k=\"". $this->e($this->d($v)) .'"';
		foreach($a as $k => $v) $h .= " $k=\"$v\"";
		return $h;
	}


	/**
	 * Create an HTML tag
	 *
	 * @param string $tag the tag name
	 * @param string $text the text to insert between the tags
	 * @param array $a of additional tag attributes
	 * @return string
	 *
	public static function __callStatic($tag, $a)
	{
		//return"\n<$tag" . self::attributes(isset($a[1])?$a[1]:NULL) . ($a[0] === 0 ? ' />' : ">{$a[0]}</$tag>");
		$a = self::attributes(isset($a[1])?$a[1]:NULL);return"\n<$tag$a>{$a[0]}</$tag>");
	}
	/*
	public function tag($tag, $text = '', array $a = NULL)
	{
		return"\n<$tag" . self::attributes($a) . ($text === 0 ? ' />' : ">$text</$tag>");
	}*/


	/**
	 * Create an HTML Link
	 *
	 * @param string $url for the link
	 * @param string $text the link text
	 * @param array $a of additional tag settings
	 * @return string
	 *
	public function link($url, $text = '', array $a = NULL)
	{
		return self::tag('a', $text, (array) $a + array('href' => $url));
	}


	/**
	 * Auto creates a form select dropdown from the options given .
	 *
	 * @param string $name the select element name
	 * @param array $options the select options
	 * @param mixed $selected the selected options(s)
	 * @param array $a of additional tag settings
	 * @return string
	 *
	public function select($name, array $options, $selected = NULL, array $a = NULL)
	{
		$h = '';
		foreach($options as $k => $v)
		{
			$x = array('value' => $this->e($this->d($k)));
			//$x = array('value' => $k);

			// Is this element one of the selected options?
			if(in_array($k, (array) $selected)) $x['selected'] = 'selected';

			//$h .= self::tag('option', $v, $a);
			//$h .= '<option'.self::attributes($a).">$v</option>";
			$h .= self::option($this->e($v), $a);
		}

		//return self::tag('select', $h, (array)$a+array('name' => $name));

		$a=(array)$a+array('name' => $name);return"<select$a>$h</select";
	}


	/**
	 * Generates an HTML tag
	 *
	 * @param    string           The tag name
	 * @param    array|string     Tag attributes
	 * @param    string|boolean   The content to place in the tag, or false for no closing tag
	 * @return   string           Returns the generated HTML tag
	 */
	/**
	 * The magic call static method is triggered when invoking inaccessible
	 * methods in a static context.
	 *
	 * This method exists to allow dynamic usage of the ::node() methods,
	 * allowing html nodes to map directly to method names:
	 *
	 *     Html::div(array('id' => 'myDiv'), 'This is div content.');
	 *
	 * @param    string           The method name being called
	 * @param    array            Parameters passed to the called method
	 * @return   mixed            Returns the value of the intercepted method call
	 *
	public static function __callStatic($tag, $a)
	{
		$has_content = (bool) ($content !== false and $content !== null);

		$html  = '<'.$tag.$this->attributes($attributes);
		$html .= $has_content ? '>' : ' />';
		$html .= $has_content ? $content.'</'.$tag.'>' : '';

		return $html;
	}
	*/
}

function view($file)
{
	return new View($file);
}

/* Examples

//-- Version 1
$view = new View('blog/posts');
$view->set(array(
	'posts' => $posts,
	'user' => $this->user,
	'date' => time(),
));
$view->foo = 'bar';
print $view;


//-- Version 2
$view = view('blog/posts')
			->posts($posts)
			->user($this->user)
			->date(time());
$view->foo = 'bar';
print $view;
*/

/*
input value name id class

$this->tag('input', 0, array('value' => $_POST['text']));
<input value="<?php print $_POST['text']; ?>" />

$this->link($url, $text);
<a href="<?php print $url; ?>">$text</a>

View::b($text);
View::textarea($text);
*/
