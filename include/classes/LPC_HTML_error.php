<?php

class LPC_HTML_error extends LPC_HTML_message
{
	protected $messageClass = array(
		LPC_HTML_Document::ENV_HTML => "error_message",
		LPC_HTML_Document::ENV_BOOTSTRAP => "alert alert-danger",
	);
}
