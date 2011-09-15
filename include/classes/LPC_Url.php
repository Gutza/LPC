<?php

/**
* URL utils
*/
class LPC_Url
{
	private function process_query($url)
	{
		$up=parse_url($url); // URL parts
		$ub=''; // URL base
		if (isset($up['host'])) {
			if (isset($up['scheme'])) {
				$ub.=$up['scheme'];
			} else {
				$ub.="http";
			}
			$ub.="://";
			if (isset($up['user'])) {
				$ub.=$up['user'];
				if (isset($up['pass'])) {
					$ub.=":".$up['pass'];
				}
				$ub.='@';
			}
			$ub.=$up['host'];
		};
		$ub.=$up['path'];

		return array(
			'url_parts'=>$up,
			'url_base'=>$ub
		);
	}

	public function add_GET_var($url,$newVar,$newValue)
	{
		$nfo=self::process_query($url);

		// Base is fine, now let's see about the variables
		@parse_str($nfo['url_parts']['query'],$query);
		$query[$newVar]=$newValue;

		return $nfo['url_base']."?".http_build_query($query);
	}

	public function remove_GET_var($url,$oldVar)
	{
		$nfo=self::process_query($url);

		@parse_str($nfo['url_parts']['query'],$query);
		if (isset($query[$oldVar]))
			unset($query[$oldVar]);
		$URLquery=http_build_query($query);
		if ($URLquery)
			$URLquery="?".$URLquery;
		return $nfo['url_base'].$URLquery;
	}
}
