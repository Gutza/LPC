<?php

abstract class LPC_HTML_doctype
{

	const type_HTML2=0x10;
	const type_HTML3=0x20;
	const type_HTML4=0x40;
	const type_HTML5=0x100;
	const type_XHTML1=0x80;

	const mode_Strict=1;
	const mode_Transitional=2;
	const mode_Frameset=4;

	// HTML 2.0
	const HTML2=0x10;
	// HTML 3.2
	const HTML3=0x20;
	// HTML 4.01 Strict
	const HTML4S=0x41; // type_HTML4 + mode_Strict
	// HTML 4.01 Transitional
	const HTML4T=0x42; // type_HTML4 + mode_Transitional
	// HTML 4.01 Frameset
	const HTML4F=0x44; // type_HTML4 + mode_Frameset
	// HTML 5
	const HTML5=0x100; // type_HTML5
	// XHTML 1.0 Strict
	const XHTML1S=0x81; // type_XHTML1 + mode_Strict
	// XHTML 1.0 Transitional
	const XHTML1T=0x82; // type_XHTML1 + mode_Transitional
	// XHTML 1.0 Frameset
	const XHTML1F=0x84; // type_XHTML1 + mode_Frameset

	static $doctypes=array(
		self::HTML2=>'<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">',
		self::HTML3=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">',
		self::HTML4S=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
		self::HTML4T=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
		self::HTML4F=>'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		self::HTML5=>'<!DOCTYPE html>',
		self::XHTML1S=>'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		self::XHTML1T=>'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		self::XHTML1F=>'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'
	);

	// Element index from http://www.w3.org/TR/html401/index/elements.html
	static $HTML4_spec=array (
		'a' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'abbr' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'acronym' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'address' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'applet' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'area' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'b' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'base' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'basefont' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'bdo' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'big' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'blockquote' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'body' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'br' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'button' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'caption' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'center' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'cite' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'code' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'col' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'colgroup' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'dd' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'del' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'dfn' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'dir' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'div' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'dl' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'dt' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'em' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'fieldset' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'font' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'form' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'frame' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'frameset' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'h1' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'h2' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'h3' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'h4' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'h5' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'h6' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'head' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'hr' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'html' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'i' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'iframe' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'L',
		),
		'img' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'input' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'ins' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'isindex' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'kbd' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'label' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'legend' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'li' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'link' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'map' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'menu' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'meta' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'noframes' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'noscript' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'object' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'ol' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'optgroup' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'option' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'p' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'param' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'pre' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'q' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		's' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'samp' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'script' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'select' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'small' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'span' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'strike' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'strong' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'style' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'sub' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'sup' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'table' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'tbody' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'td' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'textarea' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'tfoot' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'th' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'thead' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'title' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'tr' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'tt' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'u' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'ul' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'var' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
	);

	// http://www.w3.org/html/wg/drafts/html/master/single-page.html#void-elements
	static $HTML5_spec=array (
		'area' =>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'base'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'br'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'col'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'embed'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'hr'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'img'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'input'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'keygen'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'link'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'menuitem'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'meta'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'param'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'source'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'track'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'wbr'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
	);
}
