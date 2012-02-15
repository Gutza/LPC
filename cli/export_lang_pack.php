<?php

// feb 2012

if (isset($_SERVER["REQUEST_METHOD"])) {
	echo "This script is only intended for CLI.";
	exit;
}

$filename=dirname(dirname(__FILE__))."/docs/translations.php";
$fp=fopen($filename,'w');
if (!$fp) {
	fputs(STDERR,"ERROR: Failed opening $filename for writing!\n");
	exit;
}

define("LPC_SkipAuthentication",true);
require_once dirname(dirname(__FILE__))."/include/LPC_lib.php";

while(ob_get_level())
	ob_end_clean();

$result=array(
	'languages'=>array(),
	'messages'=>array(),
);

// Languages
$langs=new LPC_Language();
$langs=$langs->search(NULL,NULL,0);
foreach($langs as $lang)
	$result['languages'][]=array(
		'id'=>$lang->id,
		'name'=>$lang->getAttr('name'),
		'locale_POSIX'=>$lang->getAttr('locale_POSIX'),
	);

// Messages
$refs=new LPC_I18n_reference();
$refs=$refs->search('system',1,0);
foreach($refs as $ref) {
	$transData=array();
	$transs=$ref->getObjects('translations','language');
	foreach($transs as $trans)
		$transData[]=array(
			'language'=>$trans->getAttr('language'),
			'translation'=>$trans->getAttr('translation'),
		);
	if (!$transData)
		continue;
	$result['messages'][$ref->id]=array(
		'comment'=>$ref->getAttr('comment'),
		'system'=>$ref->getAttr('system'),
		'translations'=>$transData,
	);
}

$outData="<?php\n\n// Automated export on ".gmdate('r')."\n\nreturn ".var_export($result,true).";";

fputs($fp,$outData);
fclose($fp);

echo "Clean exit: all system messages have been exported to ".$filename."\n";

