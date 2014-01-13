<?php

// vim: fdm=marker:
/**
 * LPC Page class
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) November 2009, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 */
class LPC_Page extends LPC_Page_base
{
	var $footer="";
	var $startTime=NULL;
	var $menuStart=true;
	var $loadAvgFile="/proc/loadavg"; // used in the footer
	var $cpuinfoFile="/proc/cpuinfo"; // used in the footer
	var $noFooter=false; // set to true if you don't want the footer

	public function __construct()
	{
		parent::__construct();

		$this->content['head']->a(new LPC_HTML_link('stylesheet', 'text/css', LPC_css."/LPC_base.css?2013-04"), "LPC base CSS");
		$this->content['head']->a(new LPC_HTML_link('stylesheet', 'text/css', LPC_css."/LPC_default.css"), "LPC CSS");
	}

	function beforeRender($indent)
	{
		parent::beforeRender($indent);
		$this->a($this->renderFooter(), "Footer (LPC_Page)");

		return true;
	}

	protected function renderFooter()
	{
		$result=array();

		if ($this->noFooter)
			return $result;

		$copy=new LPC_HTML_node("DIV");
		$copy->setAttr('class','copyright');
		$copy->content="Powered by <b>LPC</b> v".LPC_version." by <a href='http://www.moongate.eu/'>Moongate</a>";
		$result[]=$copy;

		if (!strlen(LPC_user_class) || !LPC_User::getCurrent(true))
			return $result; // no info in footer if you're not logged in

		$loadInfo="";
		if ($this->loadAvgFile && is_readable($this->loadAvgFile)) {
			$coreCount=`cat {$this->cpuinfoFile} | grep processor | wc -l`;
			list($loadInfo)=explode(" ",file_get_contents($this->loadAvgFile));
			$loadInfo="; load 5s: ".($loadInfo*100/$coreCount)."% (avg on ".$coreCount.($coreCount>1?' cores':' core').")";
		}

		$result[]=$this->renderMessageTranslations();

		$runtime=new LPC_HTML_node("DIV");
		$runtime->setAttr('style',"color:#c0c0c0; text-align:center; font-size:80%; margin-top:10px");
		$runTime=number_format(microtime(true)-LPC_start_time,3);
		$runtime->content="Page rendered in ".$runTime." seconds".$loadInfo;
		$result[]=$runtime;

		return $result;
	}

}
