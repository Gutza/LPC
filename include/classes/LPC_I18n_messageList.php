<?php

class LPC_I18n_messageList extends LPC_HTML_widget
{

	function prepare()
	{
		$this->setClass('LPC_translated_message_list');
		foreach(LPC_I18n_messageFormatter::$object_cache as $langID=>$msgs) {
			$this->a("<h1>Messages used in this page</h1>");
			$lang=LPC_Language::newLanguage($langID);
			$this->a("<h2>".$lang->getNameH()."</h2>");
			$msgList=new LPC_HTML_node();
			$this->a($msgList);
			foreach($msgs as $key=>$obj) {
				$a=new LPC_HTML_node('a');
				$a->a(htmlspecialchars($key));
				$a->setAttr('href',LPC_url."/translate/message_translate.php?l=$langID&amp;m=".rawurlencode($key));
				$a->setAttr('title',$obj->format(array()));
				if ($msgList->content)
					$msgList->a(" &bull; ");
				$msgList->a($a);
			}
		}
	}

}
