<?php

class LPC_HTML_node extends LPC_HTML_base
{
	public $attributes=array();
	public $nodeName="DIV"; // Just an example, you're supposed to override it; plus, it's a reasonable default
	public $shortTag=true; // If tag is empty, it will be closed with <foo />

	public $doctype=0;
	public $endTagAllowed=true;

	const tagStart=1;
	const tagEnd=2;
	const tagBody=4;

	public function __construct($nodeName=NULL,$shortTag=NULL)
	{
		if ($nodeName!==NULL)
			$this->nodeName=strtoupper($nodeName);
		if ($shortTag!==NULL)
			$this->shortTag=(bool) $shortTag;
	}

	public function render()
	{
		parent::render();

		if (!$this->doctype) {
			$p=LPC_Page::getCurrent();
			$this->doctype=$p->doctype;
		}

		$short=$this->determineTagEnd() && $this->shortTag && !$this->content;
		$result=$this->renderTagStart($short);
		if ($short)
			return $result;
		if (!$this->content && !is_numeric($this->content)) {
			if ($this->endTagAllowed)
				return rtrim($result).ltrim($this->renderTagEnd());
			else
				return $result;
		}

		$result.=$this->renderItem($this->content);
		if ($this->endTagAllowed)
			$result.=$this->renderTagEnd();
		return $result;
	}

	/**
	* Sets $this->endTagAllowed to false if this element doesn't allow a tag end
	* in this document type. Returns true if short tag is allowed for this tag
	* in this document type.
	*/
	public function determineTagEnd()
	{
		if (!$this->doctype || !($this->doctype & LPC_HTML_doctype::type_HTML4))
			// I have no doctype, or it's not HTML4
			return $this->endTagAllowed;

		$nn=strtoupper($this->nodeName);
		if (!isset(LPC_HTML_doctype::$HTML4_spec[$nn]))
			// All is well, except this node name doesn't exist
			return $this->endTagAllowed;

		$this->endTagAllowed=LPC_HTML_doctype::$HTML4_spec[$nn]['end']!='F';
		return $this->endTagAllowed && LPC_HTML_doctype::$HTML4_spec[$nn]['end']=='O';
	}

	public function renderTagStart($closed=false)
	{
		$result="<".strtolower($this->nodeName);
		if ($this->attributes) {
			foreach($this->attributes as $key=>$attribute) {
				if ($attribute===null)
					continue;
				if (is_string($attribute) || is_numeric($attribute)) {
					$result.=' '.$key.'="'.$attribute.'"';
					continue;
				}
				if ($attribute instanceof LPC_HTML_attribute) {
					$result.=' '.$attribute->render();
					continue;
				}
				if ($attribute instanceof iLPC_HTML) {
					$result.=' '.$key.'="'.trim($attribute->render()).'"';
					continue;
				}
				$xtra="";
				if (is_object($attribute))
					$xtra=get_class($attribute)." ";
				throw new RuntimeException("Unknown attribute type (".gettype($attribute)." $xtra-- ".$key."=".$attribute.")");
			}
		}
		$tagType=self::tagStart;
		if ($closed) {
			$result.=" /";
			$tagType+=self::tagEnd;
		}
		$result.=">";
		return $this->output($result,$tagType);
	}

	public function renderTagEnd()
	{
		return $this->output("</".strtolower($this->nodeName).">",self::tagEnd);
	}

	public function output($html,$tagType=self::tagBody)
	{
		if ($tagType==self::tagBody)
			return $this->renderString($html);

		$indent=str_repeat("\t",$this->indent_count);
		if ($tagType & self::tagBody)
			$indent.="\t";

		if ($this->compact)
			// I am compact, regardless of my parent
			$html=str_replace("\n","",$html);
		else
			// I am not compact
			$html=str_replace("\n","\n".$indent,$html);

		if (!$this->compact)
			// Nobody's compact
			return $indent.$html."\n";

		if ($this->compactParent)
			// Parent is compact (therefore so am I)
			return $html;

		// I am compact, parent is not
		$result=$html;
		if ($tagType & self::tagStart)
			$result=$indent.$result;
		if ($tagType & self::tagEnd)
			$result.="\n";
		return $result;
	}

	public function setAttr($key,$value=NULL)
	{
		$this->attributes[$key]=$value;
	}

	public function getAttr($key)
	{
		if (!isset($this->attributes[$key]))
			return NULL;
		return $this->attributes[$key];
	}

}
