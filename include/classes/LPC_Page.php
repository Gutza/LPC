<?php

// vim: fdm=marker:
/**
 * LPC Page base class
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) November 2009, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id: LPC_Page.php,v 1.23 2011/09/07 10:46:24 bogdan Exp $
 */
class LPC_Page extends LPC_HTML_document
{
	var $footer="";
	var $startTime=NULL;
	var $menuStart=true;
	var $appendBuffer=false; // whether to automatically append PHP's output buffer
	var $loadAvgFile="/proc/loadavg"; // used in the footer
	var $cpuinfoFile="/proc/cpuinfo"; // used in the footer
	var $noFooter=false; // set to true if you don't want the footer

	private static $currentInstance=NULL;

	public function __construct()
	{
		parent::__construct();

		$this->content['head']->content['icon']=new LPC_HTML_link('icon','image/vnd.microsoft.icon',LPC_url."/favicon.ico");
		$this->content['head']->content['LPC base CSS']=new LPC_HTML_link('stylesheet','text/css',LPC_css."/LPC_base.css");
		$this->content['head']->content['LPC CSS']=new LPC_HTML_link('stylesheet','text/css',LPC_css."/LPC_default.css");
		$this->content['head']->content['content-type']=new LPC_HTML_meta(array('http-equiv'=>'Content-type'),'text/html;charset=UTF-8');
	}

	public static function setCurrent($object=NULL)
	{
		if (isset(self::$currentInstance))
			return false;

		if (isset($object)) {
			self::$currentInstance=$object;
			return true;
		}

		self::$currentInstance=self::newInstance();
		return true;
	}

	public static function newInstance()
	{
		$class=self::getPageClass();
		return new $class();
	}

	public static function getCurrent($info=false)
	{
		if (!$info)
			self::setCurrent();

		return self::$currentInstance;
	}

	public function getPageClass()
	{
		if (defined("LPC_page_class"))
			return LPC_page_class;

		return "LPC_Page";
	}

	public function render()
	{
		if ($this->appendBuffer)
			$this->body->a(ob_get_clean());

		return parent::render();
	}

	function st($title=NULL)
	{
		if (!is_null($title))
			$this->title=$title;
		$this->a("<h1>".$this->title."</h1>");
	}

	// {{{ browserIsMobile()
	function browserIsMobile()
	{
		$useragent=$_SERVER['HTTP_USER_AGENT'];
		return
			preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent) ||
			preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))
		;
	}
	// }}}

	function beforeRender()
	{
		$this->a($this->renderFooter());
		return true;
	}

	function onRender()
	{
	}

	function renderFooter()
	{
		$result=array();

		if ($this->noFooter)
			return $result;

		$copy=new LPC_HTML_node("DIV");
		$copy->setAttr('class','copyright');
		$copy->content="Powered by <a href='http://www.moongate.ro/'>LPC</a> &mdash; Version 1.0 alpha";
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

	function renderMessageTranslations()
	{
		if (empty($_SESSION['LPC_display_message_translations']))
			return NULL;

		return new LPC_I18n_messageList();
	}
}
