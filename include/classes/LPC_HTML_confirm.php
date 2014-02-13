<?php

class LPC_HTML_confirm extends LPC_HTML_message
{
	protected $messageClass = array(
		LPC_HTML_Document::ENV_HTML => "confirmation_message",
		LPC_HTML_Document::ENV_BOOTSTRAP => "alert alert-success",
	);
}
