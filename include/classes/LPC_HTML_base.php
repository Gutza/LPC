<?php

abstract class LPC_HTML_base implements iLPC_HTML
{
	public $content=array();

	public $compact=false;
	public $compactParent=false;

	public $indentCount=0;

	public $doctype;
	public $parentNode=NULL;
	public $ownerDocument=NULL;
	public $id=NULL;

	public static $uid_counter=0;

	protected $arrayKey;
	protected $shown = false;

	public function show($once = false)
	{
		if ($once && $this->shown)
			return;
		echo $this->render();
		$this->shown=true;
	}

	public function wasShown()
	{
		return $this->shown;
	}

	/**
	* You need to override this, but make sure to call parent::render()
	*/
	public function render()
	{
		// If parent is compact, so am I
		$this->compact=$this->compact || $this->compactParent;
	}

	public function updateOwner($od)
	{
		$this->ownerDocument=$od;
		if (is_array($this->content))
			foreach($this->content as $element) {
				if (is_object($element))
					$element->updateOwner($od);
			}
		elseif (is_object($this->content))
			$this->content->updateOwner($od);
	}

	private function prepareForAppend($element)
	{
		if (is_string($element) && !strlen(trim($element)))
			return false;
		if (!is_array($this->content))
			$this->content=array($this->content);
		if (is_object($element)) {
			$element->parentNode=&$this;
			if ($this->ownerDocument===NULL)
				$element->updateOwner($this);
			else
				$element->updateOwner($this->ownerDocument);
		}
		return true;
	}

	public function append($element,$key=NULL)
	{
		if (!$this->prepareForAppend($element))
			return $this;

		if ($key===NULL)
			$this->content[]=$element;
		else
			$this->content[$key]=$element;
		return $this;
	}

	public function a($element,$key=NULL)
	{
		return $this->append($element,$key);
	}

	public function prepend($element, $key=NULL)
	{
		if (!$this->prepareForAppend($element))
			return $this;

		if ($key===NULL)
			array_unshift($this->content, $element);
		elseif (array_key_exists($key, $this->content))
			$this->content[$key]=$element;
		else
			$this->content=array_merge(array($key=>$element), $this->content);

		return $this;
	}

	public function p($element,$key=NULL)
	{
		return $this->prepend($element,$key);
	}

	public function prepareContent($content)
	{
		if (is_string($content))
			return;

		if ($content instanceof iLPC_HTML_widget)
			$content->pre_prepare();
		if ($content instanceof LPC_HTML_base) {
			$this->prepareContent($content->content);
			return;
		}

		if (!is_array($content))
			return;

		foreach($content as $atom)
			$this->prepareContent($atom);
	}

	public function renderContent()
	{
		if ($this->ownerDocument==$this)
			$this->prepareContent($this->content);
		return $this->renderItem($this->content);
	}

	public function renderItem($item,$arrayKey=NULL)
	{
		if (empty($item) && !is_numeric($item))
			return "";
		if (is_string($item) || is_numeric($item))
			return $this->outputString($item);
		if (is_object($item)) {
			if (!$item instanceof iLPC_HTML)
				throw new RuntimeException("Unknown item class in HTML content (".get_class($item).")".var_export($item,true));

			$item->compactParent=$this->compact;
			$item->indentCount=$this->indentCount+1;
			$item->arrayKey=$arrayKey;
			if ($item instanceof LPC_HTML_node && (!isset($item->doctype) || !$item->doctype) && $this->doctype)
				$item->doctype=$this->doctype;
			$result=$item->render();
			if (is_string($result))
				return $result;
			return $this->renderItem($result);
		}
		if (is_array($item)) {
			$result=array();
			foreach($item as $key=>$atom)
				$result[]=$this->renderItem($atom,$key);
			if (LPC_debug)
				$glue="\n";
			else
				$glue="";
			return implode($glue,$result);
		}
		throw new RuntimeException("Unknown item type in HTML content (".gettype($item).")");
	}

	public function outputString($html)
	{
		if ($this->compact)
			return $html;

		$indent=str_repeat("\t",$this->indentCount+1);
		return $indent.str_replace("\n","\n".$indent,$html)."\n";
	}

	public function qstr($string)
	{
		return '"'.htmlspecialchars($string).'"';
	}

	public function getUID()
	{
		return self::$uid_counter++;
	}
}
