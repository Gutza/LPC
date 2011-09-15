<?php

abstract class LPC_HTML_base implements iLPC_HTML
{
	public $content=array();

	public $compact=false;
	public $compactParent=false;

	public $indent_count=0;

	public $doctype;

	public function show()
	{
		echo $this->render();
	}

	/**
	* You need to override this, but make sure to call parent::render()
	*/
	public function render()
	{
		// If parent is compact, so am I
		$this->compact=$this->compact || $this->compactParent;
	}

	public function append($element,$key=NULL)
	{
		if (is_string($element) && !strlen(trim($element)))
			return null;
		if (!is_array($this->content))
			$this->content=array($this->content);
		if ($key===NULL)
			$this->content[]=$element;
		else
			$this->content[$key]=$element;
		return true;
	}

	public function a($element,$key=NULL)
	{
		return $this->append($element,$key);
	}

	public function prepend($element,$key=0)
	{
		if (!is_numeric($key) && isset($this->content[$key])) {
			// array_merge wouldn't behave the way intended here
			$this->content[$key]=$element;
			return true;
		}
		$this->content=array_merge(array($key=>$element),$this->content);
		return true;
	}

	public function p($element,$key=0)
	{
		return $this->prepend($element,$key);
	}

	public function renderContent()
	{
		return $this->renderItem($this->content);
	}

	public function renderItem($item)
	{
		if (empty($item) && !is_numeric($item))
			return "";
		if (is_string($item) || is_numeric($item))
			return $this->outputString($item);
		if (is_object($item)) {
			if ($item instanceof iLPC_HTML) {
				$item->compactParent=$this->compact;
				$item->indent_count=$this->indent_count+1;
				if ($item instanceof LPC_HTML_node && (!isset($item->doctype) || !$item->doctype) && $this->doctype)
					$item->doctype=$this->doctype;
				$result=$item->render();
				if (is_string($result))
					return $result;
				return $this->renderItem($result);
			} else
				throw new RuntimeException("Unknown item class in HTML content (".get_class($item).")".var_export($item,true));
		}
		if (is_array($item)) {
			$result="";
			foreach($item as $atom)
				$result.=$this->renderItem($atom);
			return $result;
		}
		throw new RuntimeException("Unknown item type in HTML content (".gettype($item).")");
	}

	public function outputString($html)
	{
		if ($this->compact)
			return $html;

		$indent=str_repeat("\t",$this->indent_count+1);
		return $indent.str_replace("\n","\n".$indent,$html)."\n";
	}

	public function qstr($string)
	{
		return '"'.htmlspecialchars($string).'"';
	}
}
