<?php

class LPC_HTML_jQuery_JSON
{
	function show($data)
	{
		header("Content-Type: application/javascript");
		ob_start("ob_gzhandler");
		if (empty($_GET['callback'])) {
			echo LPC_JSON::encode($data);
			exit;
		}

		echo
			$_GET['callback']."(".
			LPC_JSON::encode($data).
			");";
		exit;
	}
}
