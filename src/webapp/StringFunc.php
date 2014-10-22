<?php #GENERAL PHP functions v1.0
	  # GROUP 13 
	  # 2014

namespace tdt4237\webapp;

class StringFunc
{
	/**
	 * returns a shorter string specified by a length
	 * @param  [string]  $text   [the text to shorten]
	 * @param  integer $length [max number of characters]
	 * @return [string]          [the truncated string]
	 */
	static function shrtn($text,$length=50)
	{
		return substr($text,0,$length);
	}
}
	

?>