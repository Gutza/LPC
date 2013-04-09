<?php

class LPC_HTML_document extends LPC_HTML_node
{

	public $doctype;

	public $nodeName="html";

	public $body;
	public $head;

	public $title='';
	public $title_default='LPC Project';
	public $title_format='LPC &ndash; %s';

	// 'HTML' for regular rendering
	// 'none' for no rendering
	// 'raw' to render only $body
	public $renderMode='HTML';

	public $gz_enable=true; // disable if you're processing it yourself
	public $gz_thresh=10240; // 10 KiB

	public function __construct()
	{
		$this->doctype=LPC_HTML_doctype::HTML4S;
		$this->head=new LPC_HTML_node('HEAD');
		$this->head->ownerDocument=$this;
		$this->head->parentNode=$this;
		$this->body=new LPC_HTML_node('BODY');
		$this->body->ownerDocument=$this;
		$this->body->parentNode=$this;
		$this->content=array(
			'head'=>&$this->head,
			'body'=>&$this->body
		);

		$title=new LPC_HTML_node('title');
		$title->compact=true;
		$this->content['head']->a($title,'title');
	}

	public function show()
	{
		if (!$this->gz_enable || $this->renderMode=='none')
			return parent::show();

		$output=$this->render();
		if (strlen($output)<$this->gz_thresh) {
			echo $output;
			return;
		}

		ob_start("ob_gzhandler");
		echo $output;
	}

	public function render($indent=0)
	{
		$this->prepareContent($this->content);
		if ($this->doctype & LPC_HTML_doctype::type_XHTML1)
			$this->setAttr("xmlns","http://www.w3.org/1999/xhtml");
		if (!$this->body->content || $this->renderMode=='none')
			return '';

		if (!$this->beforeRender($indent))
			return '';

		if ($this->renderMode=='raw')
			return $this->body->renderContent($indent);

		$title="";
		if ($this->title) {
			$titleObj=new LPC_HTML_fragment();
			$titleObj->content=$this->title;
			$titleObj->compact=true;
			$title=$titleObj->render();
		}

		if (strlen(trim($title)))
			$this->content['head']->content['title']->content=sprintf($this->title_format,$title);
		else
			$this->content['head']->content['title']->content=$this->title_default;

		$content=$this->output(LPC_HTML_doctype::$doctypes[$this->doctype],$indent); // <!DOCTYPE...>
		$content.=parent::render($indent);
//mail("bogdan@moongate.ro","render",print_r($this->content['head'],1));
		$this->onRender($indent);
		return $content;
	}

	public function a($element,$key=NULL)
	{
		return $this->body->append($element,$key);
	}

	public function append($element,$key=NULL)
	{
		return $this->body->append($element,$key);
	}

	public function p($element,$key=NULL)
	{
		return $this->body->prepend($element,$key);
	}

	public function prepend($element,$key=NULL)
	{
		return $this->body->prepend($element,$key);
	}

	public function clear()
	{
		$this->body->content=array();
	}

	protected function beforeRender($indent)
	{
		return true;
	}

	protected function onRender($indent)
	{
	}

	private function debug($text)
	{
		echo "<hr>";
		echo "<pre>";
		echo htmlspecialchars($text);
		echo "</pre>";
	}

}
