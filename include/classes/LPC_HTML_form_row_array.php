<?php

// For Bootstrap
class LPC_HTML_form_row_array extends LPC_HTML_widget
{
	protected $colClass;
	protected $meta = array();

	function __construct(array $meta, $colClass = "col-sm-6")
	{
		parent::__construct();
		$this->meta = $meta;
		$this->colClass = $colClass;
	}

	function prepare()
	{
		$this->setClass("row");
		foreach($this->meta as $atom) {
			$col = new LPC_HTML_node();
			$col->setClass($this->colClass);
			$this->a($col);

			if (isset($atom["content"])) {
				$col->a($atom["content"]);
				continue;
			}

			$ig = new LPC_HTML_node();
			$ig->setClass("input-group");
			$col->a($ig);

			$labelSpan = new LPC_HTML_node("span");
			$labelSpan->setClass("input-group-addon");
			$labelSpan->a($atom["label"]);
			$ig->a($labelSpan);

			if (is_object($atom["input"])) {
				$atom["input"]->setClass("form-control");
				$ig->a($atom["input"]);
			} else {
				$fc = new LPC_HTML_node("span");
				$fc->setClass("form-control");
				$fc->a($atom["input"]);
				$ig->a($fc);
			}
		}
	}
}
