<?php

class LPC_HTML_html_editor extends LPC_HTML_fidget
{
	var $textarea = NULL;
	var $init = false;

	function __construct($textarea, $init = false)
	{
		$this->textarea = $textarea;
		$this->init = $init;
	}

	function prepare()
	{
		// Prepare JS
		$this->ownerDocument->head
			->a(new LPC_HTML_script("//tinymce.cachefly.net/4.0/tinymce.min.js"), "TinyMCE")
			->a(new LPC_HTML_script(LPC_js."/LPC_HTML_editor.js"), "LPC HTML editor");

		// Prepare textarea
		$this->textarea->setUID();
		$this->a($this->textarea);

		// Prepare controls
		$controlsDiv = new LPC_HTML_node();
		$this->a($controlsDiv);
		$checkbox = new LPC_HTML_node('input');
		$controlsDiv->a($checkbox);
		$checkbox->setUID();
		$checkbox->setAttrs(array(
			'type' => 'checkbox',
			'onChange' => "LPC_HTML_editor.showHideHandle(this, '".$this->textarea->id."')",
		));
		$label = new LPC_HTML_node("label");
		$controlsDiv->a($label);
		$label->setAttr("for", $checkbox->id);
		$label->a(" ".__L('scaffoldingUseHtmlEditor'));
		if ($this->init) {
			$js = new LPC_HTML_script();
			$js->a("$(document).ready(function() { LPC_HTML_editor.initAndShow($('#".$checkbox->id."').get(0), '".$this->textarea->id."'); });");
			$this->a($js);
		}
	}
}
