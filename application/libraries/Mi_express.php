<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mi_express {
	private $express;
	
	function __construct()
	{
	
	}

	function set_express($express = NULL)
	{
		$this->express = $express;
	}

	function parser($vars = array())
	{
		if (!empty($vars)) {
			foreach ($vars as $key => $value) {

				eval('$'.$key.' = '.$value.';');
			}
		}
		$express_array = explode(';', $this->express);
		if (count($express_array) == 1) {
			eval('$result = '.$this->express.';');
		} else {
			foreach ($express_array as $key => $value) {
				if ($key != count($express_array) - 1) {
					eval($value.';');
				} else {
					eval('$result = '.$value.';');
				}
			}
		}

		return $result;
	}

}

/* End of file: Layout.php */