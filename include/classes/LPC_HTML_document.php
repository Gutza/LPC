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

	protected $page_title_key = 'LPC_page_title';

	// Render mode constants
	const RM_NONE = "none"; // don't show anything
	const RM_HTML = "HTML"; // full rendering (default)
	const RM_RAW  = "raw";  // raw rendering (just the body)

	// One of the above
	public $renderMode=self::RM_HTML;

	public $gz_enable=true; // disable if you're processing it yourself
	public $gz_thresh=10240; // 10 KiB

	const ENV_HTML = 0;
	const ENV_BOOTSTRAP = 1;

	public $environment = self::ENV_HTML;

	public function __construct()
	{
		$this->doctype=LPC_HTML_doctype::HTML4S;

		$this->head=new LPC_HTML_node('head');
		$this->head->ownerDocument=$this;
		$this->head->parentNode=$this;

		$this->body=new LPC_HTML_node('body');
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

	public function show($once = false)
	{
		ob_start();
		parent::show($once);
		$output = ob_get_clean();

		if ($this->gz_enable && strlen($output) > $this->gz_thresh)
			ob_start("ob_gzhandler");

		echo $output;
	}

	public function render($indent=0)
	{
		$this->prepareContent($this->content);
		if ($this->doctype & LPC_HTML_doctype::type_XHTML1)
			$this->setAttr("xmlns","http://www.w3.org/1999/xhtml");

		if (!$this->body->content || self::RM_NONE == $this->renderMode)
			return '';

		if (!$this->beforeRender($indent))
			return '';

		if (self::RM_RAW == $this->renderMode)
			return $this->body->renderContent($indent);

		if (self::RM_HTML != $this->renderMode)
			throw new RuntimeException("Unknown render mode: [".$this->renderMode."]");

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

	public function st($title=NULL)
	{
		if (!is_null($title))
			$this->title=$title;
		$this->a("<h1>".$this->title."</h1>", $this->page_title_key);
		return $this;
	}

}
