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
	public function set($array)
	{
		foreach($array as $k => $v) $this->$k = $v;
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
