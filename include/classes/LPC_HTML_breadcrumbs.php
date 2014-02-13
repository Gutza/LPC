<?php

// Only for Bootstrap
class LPC_HTML_breadcrumbs extends LPC_HTML_widget
{
	protected $meta = array();

	function __construct(array $meta)
	{
		parent::__construct();
		$this->meta = $meta;
	}

	function prepare()
	{
		$this->addClass("container");
		$ol = new LPC_HTML_node("ol");
		$ol->setClass("breadcrumb");
		$this->a($ol);
		foreach($this->meta as $atom) {
			$li = new LPC_HTML_node("li");
			$ol->a($li);
			if (isset($atom["url"])) {
				$labelNode = new LPC_HTML_node("a");
				$labelNode->setAttr("href", $atom["url"]);
				$li->a($labelNode);
			} else {
				$labelNode = $li;
				$li->setClass("active");
			}
			$labelNode->a($atom["label"]);
		}
	}

}
