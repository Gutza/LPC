<?php

class LPCI_Page extends LPC_Page
{
	var $title_default='LPC Management';
	var $menu;

	function __construct()
	{
		parent::__construct();

		$headbox=new LPC_HTML_node("DIV");
		$headbox->setAttr("class","content header");

		$a=new LPC_HTML_node("A");
		$a->setAttr('href','?');
		$img=new LPC_HTML_node("IMG");
		$img->setAttr('src',LPC_css."/images/LPC_medium_logo.png");
		$img->setAttr('style','float:left; margin-top: -45px');
		$img->setAttr('alt','LPC logo');
		$a->a($img);

		$headbox->a($a);

		$this->menu=new LPCI_Menu($this);
		$headbox->a($this->menu);

		$this->a($headbox);

		$inner=new LPC_HTML_node("DIV");
		$inner->setAttr('class','content');
		$this->a($inner,"inner");

		$this->body=&$this->content['body']->content['inner'];
	}

	function beforeRender($indent)
	{
		//$this->content['body']->p($this->menu->render());
		return parent::beforeRender($indent);
	}

}
