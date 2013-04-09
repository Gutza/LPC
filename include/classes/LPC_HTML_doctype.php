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
		'A' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'ABBR' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'ACRONYM' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'ADDRESS' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'APPLET' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'AREA' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'B' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'BASE' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'BASEFONT' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'BDO' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'BIG' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'BLOCKQUOTE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'BODY' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'BR' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'BUTTON' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'CAPTION' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'CENTER' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'CITE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'CODE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'COL' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'COLGROUP' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DD' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DEL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DFN' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DIR' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'DIV' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'DT' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'EM' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'FIELDSET' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'FONT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'FORM' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'FRAME' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'FRAMESET' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'H1' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'H2' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'H3' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'H4' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'H5' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'H6' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'HEAD' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'HR' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'HTML' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'I' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'IFRAME' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'L',
		),
		'IMG' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'INPUT' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'INS' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'ISINDEX' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'KBD' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'LABEL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'LEGEND' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'LI' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'LINK' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'MAP' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'MENU' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'META' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'NOFRAMES' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => 'F',
		),
		'NOSCRIPT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'OBJECT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'OL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'OPTGROUP' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'OPTION' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'P' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'PARAM' => 
		array (
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'PRE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'Q' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'S' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'SAMP' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SCRIPT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SELECT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SMALL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SPAN' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'STRIKE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'STRONG' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'STYLE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SUB' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'SUP' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TABLE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TBODY' => 
		array (
			'start' => 'O',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TD' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TEXTAREA' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TFOOT' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TH' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'THEAD' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TITLE' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TR' => 
		array (
			'start' => '',
			'end' => 'O',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'TT' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'U' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => 'D',
			'dtd' => 'L',
		),
		'UL' => 
		array (
			'start' => '',
			'end' => '',
			'empty' => '',
			'deprecated' => '',
			'dtd' => '',
		),
		'VAR' => 
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
		'AREA' =>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'BASE'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'BR'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'COL'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'EMBED'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'HR'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'IMG'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'INPUT'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'KEYGEN'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'LINK'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'MENUITEM'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'META'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'PARAM'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'SOURCE'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'TRACK'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
		'WBR'=>
		array(
			'start' => '',
			'end' => 'F',
			'empty' => 'E',
			'deprecated' => '',
			'dtd' => '',
		),
	);
}
