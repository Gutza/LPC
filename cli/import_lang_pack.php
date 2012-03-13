<?php

// feb 2012

if (isset($_SERVER["REQUEST_METHOD"])) {
	echo "This script is only intended for CLI.";
	exit;
}

$filename=dirname(dirname(__FILE__))."/docs/translations.php";
$inData=require $filename;

define("LPC_SkipAuthentication",true);
require_once dirname(dirname(__FILE__))."/include/LPC_lib.php";

while(ob_get_level())
	ob_end_clean();

// Languages
$langMapping=array();
foreach($inData['languages'] as $langData) {
	$lang=new LPC_Language();
	$langs=$lang->search('locale_POSIX',$langData['locale_POSIX']);
	if ($langs)
		$lang=$langs[0];
	else {
		$lang=new LPC_Language();
		$lang->setAttr('locale_POSIX',$langData['locale_POSIX']);
	}
	$lang->setAttr('name',$langData['name']);
	$lang->save();
	$langMapping[$langData['id']]=$lang->id;
}

// Messages
foreach($inData['messages'] as $msgKey=>$msgData) {
	$ref=new LPC_I18n_reference($msgKey);
	if (!$ref->probe())
		$ref->query("
			INSERT INTO ".$ref->getTableName()."
				(".$ref->getFieldName(0).")
				VALUES (".$ref->db->qstr($msgKey).")
		");

	$ref->setAttr('comment',$msgData['comment']);
	$ref->save();

	foreach($msgData['translations'] as $trnData) {
		$msg=new LPC_I18n_message();
		$msgs=$msg->search(
			array(
				'language',
				'message_key',
			),
			array(
				$langMapping[$trnData['language']],
				$ref->id
			)
		);
		if ($msgs)
			$msg=$msgs[0];
		else {
			$msg=new LPC_I18n_message();
			$msg->setAttrs(array(
				'language'=>$langMapping[$trnData['language']],
				'message_key'=>$ref->id,
			));
		}
		$msg->setAttr('translation',$trnData['translation']);
		$msg->save();
	}
}

echo "Clean exit, all messages from $filename imported into the local database.\n";
if (defined('LPC_CACHE_TYPE') && in_array(LPC_CACHE_TYPE,array('session','apc')))
	echo "WARNING! You're using ".LPC_CACHE_TYPE." caching, which means that messages updated by this import (if any) might still be cached in the old form!\n";
