<?php

class LPC_HTML_warning extends LPC_HTML_message
{
	protected $messageClass = array(
		LPC_HTML_Document::ENV_HTML => "warning_message",
		LPC_HTML_Document::ENV_BOOTSTRAP => "alert alert-warning",
	);
}
