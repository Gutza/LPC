<?php

/**
* The generic LPC image class
* @author Bogdan Stancescu <bogdan@moongate.ro>
* @copyright Copyright (c) January 2012, Bogdan Stancescu
* @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
*/
class LPC_HTTP_image extends LPC_HTTP_file
{
	/**
	* The GD image resource.
	*/
	var $imgRes=NULL;

	function ensureRes()
	{
		if ($this->imgRes)
			return;
		$this->imgRes=imagecreatefromstring($this->content);
	}

	function showJPEG($quality=75)
	{
		$this->prepareJPEG($quality);
		$this->show();
	}

	function prepareJPEG($quality=75)
	{
		$this->ensureRes();
		$this->mimeType="image/jpeg";
		ob_start();
		imagejpeg($this->imgRes,NULL,$quality);
		$this->content=ob_get_clean();
	}

	function showPNG()
	{
		$this->preparePNG();
		$this->show();
	}

	function preparePNG()
	{
		$this->ensureRes();
		$this->mimeType="image/png";
		ob_start();
		imagepng($this->imgRes);
		$this->content=ob_get_clean();
	}

	/**
	* Resizes the image proportionally as to fit it into a box of the specified size.
	*/
	function fitToBox($maxWidth,$maxHeight=0)
	{
		$this->ensureRes();
		if (!$maxHeight)
			$maxHeight=$maxWidth;

		$width=$orig_width=imagesx($this->imgRes);
		$height=$orig_height=imagesy($this->imgRes);

		list($width,$height)=$this->computeResize($width,$height,$width,$maxWidth);
		list($width,$height)=$this->computeResize($width,$height,$height,$maxHeight);

		if ($width==$orig_width && $height==$orig_height)
			return;

		$dst=imagecreatetruecolor($width,$height);
		imagealphablending($dst,false);
		imagesavealpha($dst, true);
		imagecopyresampled($dst,$this->imgRes,0,0,0,0,$width,$height,$orig_width,$orig_height);

		$this->imgRes=$dst;
	}

	function computeResize($width,$height,$metric,$max)
	{
		$factor=1;

		if ($metric>$max)
			$factor=$max/$metric;

		$width*=$factor;
		$height*=$factor;

		return array($width,$height);
	}
}
