<?php

class LPC_HTML_node extends LPC_HTML_base
{
	public $attributes=array();
	public $nodeName="div"; // Just an example, you're supposed to override it; plus, it's a reasonable default
	public $shortTag=true; // If tag is empty, it will be closed with <foo />

	public $doctype=0;
	public $endTagAllowed=true;

	const tagStart=1;
	const tagEnd=2;
	const tagBody=4;

	public $classes=array();

	public function __construct($nodeName=NULL, $shortTag=NULL)
	{
		if (!is_null($nodeName))
			$this->nodeName = $nodeName;

		if (!is_null($shortTag))
			$this->shortTag = (bool) $shortTag;
	}

	public function render()
	{
		$this->nodeName = strtolower($this->nodeName);

		parent::render();

		if (isset($this->id))
			$this->setAttr('id', $this->id);

		if (!$this->doctype && !empty($this->ownerDocument))
			$this->doctype = $this->ownerDocument->doctype;

		$emptyContent = self::isEmptyContent($this->content);

		$short = $this->shortTag && $emptyContent && $this->determineTagEnd();
		$result = $this->renderTagStart($short);
		if ($short)
			return $this->debugify($result);
		if ($emptyContent) {
			if ($this->endTagAllowed)
				return $this->debugify(rtrim($result).ltrim($this->renderTagEnd()));
			else
				return $this->debugify($result);
		}

		$result.=$this->renderContent();
		if ($this->endTagAllowed)
			$result.=$this->renderTagEnd();
		return $this->debugify($result);
	}

	protected function debugify($payload)
	{
		if (!LPC_debug)
			return $payload;

		$commentTag=get_class($this);
		if (isset($this->arrayKey)) {
			if (is_numeric($this->arrayKey))
				$key=$this->arrayKey;
			else
				$key='"'.addslashes($this->arrayKey).'"';
			$commentTag.="[".$key."]";
		}

		return
			$this->output("<!-- ".$commentTag." -->",self::tagStart+self::tagEnd).
			$payload.
			$this->output("<!-- /".$commentTag." -->",self::tagStart+self::tagEnd);
	}

	/**
	* Sets $this->endTagAllowed to false if this element doesn't allow a tag end
	* in this document type. Returns true if short tag is allowed for this tag
	* in this document type.
	*/
	public function determineTagEnd()
	{
		if (!$this->doctype || !(
			$this->doctype & (LPC_HTML_doctype::type_HTML4 | LPC_HTML_doctype::type_HTML5))
		)
			// I have no doctype, or it's not HTML4
			return $this->endTagAllowed;

		if ($this->doctype & LPC_HTML_doctype::type_HTML4)
			$spec=&LPC_HTML_doctype::$HTML4_spec;
		elseif ($this->doctype & LPC_HTML_doctype::type_HTML5)
			$spec=&LPC_HTML_doctype::$HTML5_spec;
		else
			throw new RuntimeException("This should never happen");

		$nn=&$this->nodeName;
		if (!isset($spec[$nn]))
			// All is well, except this node name is unknown.
			// To be on the safe side, DON'T allow short tags.
			return false;

		$this->endTagAllowed=$spec[$nn]['end']!='F';
		return $this->endTagAllowed && $spec[$nn]['end']=='O';
	}

	private function _classesFromAttr()
	{
		if (!strlen($this->getAttr('classes')))
			return;

		$this->classes=array_unique(array_merge($this->classes,explode(" ",$this->getAttr('classes'))));
		$this->setAttr('class');
	}

	public function setClass($classes)
	{
		$this->classes=explode(" ",$classes);
		return $this;
	}

	public function addClass($classes)
	{
		$this->_classesFromAttr();
		$this->classes=array_unique(array_merge($this->classes,explode(" ",$classes)));
		return $this;
	}

	public function removeClass($classes)
	{
		$this->_classesFromAttr();
		$this->classes=array_diff(array_unique($this->classes),explode(" ",$classes));
		return $this;
	}

	private function _processClasses()
	{
		if (empty($this->classes))
			return;

		$this->_classesFromAttr();
		$this->setAttr('class',implode(" ",$this->classes));
	} 

	public function renderTagStart($closed=false)
	{
		$this->_processClasses();

		$result="<".$this->nodeName;
		if ($this->attributes) {
			foreach($this->attributes as $key=>$attribute) {
				if ($attribute===null)
					continue;
				if ($attribute===true) {
					$result.=' '.$key;
					continue;
				}
				if (is_string($attribute) || is_numeric($attribute)) {
					$result.=' '.$key.'="'.htmlspecialchars($attribute).'"';
					continue;
				}
/*
				// Currently there is no such thing
				if ($attribute instanceof LPC_HTML_attribute) {
					$result.=' '.$attribute->render();
					continue;
				}
*/
				if ($attribute instanceof iLPC_HTML) {
					$result.=' '.$key.'="'.htmlspecialchars(trim($attribute->render())).'"';
					continue;
				}
				$xtra="";
				if (is_object($attribute))
					$xtra=get_class($attribute)." ";
				throw new RuntimeException("Unknown attribute type (".gettype($attribute)." $xtra-- ".$key."=".$attribute." for tag ".$this->nodeName.")");
			}
		}
		$tagType=self::tagStart;
		if ($closed) {
			$result.=" /";
			$tagType+=self::tagEnd;
		}
		$result.=">";
		return $this->output($result, $tagType);
	}

	public function renderTagEnd()
	{
		return $this->output("</".$this->nodeName.">", self::tagEnd);
	}

	public function output($html,$tagType=self::tagBody)
	{
		if ($tagType==self::tagBody)
			return $this->renderString($html);

		$indent=str_repeat("\t",$this->indentCount);
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

	public function setAttr($key, $value=NULL, $inhibitID=false)
	{
		if ($key=='id' && !$inhibitID)
			return $this->setID($value);

		$this->attributes[$key]=$value;
		return $this;
	}

	public function setAttrs($attrs)
	{
		foreach($attrs as $attName=>$attValue)
			$this->setAttr($attName, $attValue);
		return $this;
	}

	public function getAttr($key)
	{
		if (!isset($this->attributes[$key]))
			return NULL;
		return $this->attributes[$key];
	}

	public function setUID()
	{
		if (isset($this->id))
			return $this->id;
		return $this->setID('LPC_'.$this->getUID());
	}

	public function setID($id)
	{
		$this->id=$id;
		$this->setAttr('id', $id, true);
		return $this;
	}
}
