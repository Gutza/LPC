<?php

class LPC_HTML_css_external extends LPC_HTML_link
{
	public function __construct($href)
	{
		parent::__construct('stylesheet','text/css',$href);
	}
}
