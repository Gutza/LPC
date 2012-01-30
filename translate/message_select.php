<?php

require "common.php";
$p->title="Message selector";
$p->st();

$p->a(
	"<p>".
		"[<a href='message_check.php'>Check all message keys</a>]".
	"</p>"
);

$l=new LPC_HTML_list();
$l->queryObject=new LPC_I18n_message();
$l->sql=array(
	'select'=>array(
		'r.message_key AS message_key',
		'ISNULL(m.id) AS is_new',
		'r.comment',
	),
	'from'=>array(
		'LPC_i18n_reference AS r',
	),
	'join'=>array(
		array(
			'type'=>'left',
			'table'=>'LPC_i18n_message AS m',
			'condition'=>'m.message_key=r.message_key AND m.language='.((int) $_SESSION['LPC_target_lang'])
		)
	),
);
$l->orderPresets=array(
	'is_new'=>array(
		array(
			'sort'=>'message_key',
			'absolute_order'=>"ASC",
		)
	)
);
$l->legalSortKeys=array('message_key','is_new');
$l->labelMapping=array(
	'message_key'=>"Message key",
	'is_new'=>"New message",
	'comment'=>"Message comment",
);
$l->onProcessBodyCell='rbc';
$l->defaultOrder=array(
	'sort'=>'is_new',
	'order'=>true,
);
$mkFilter=new LPC_HTML_list_filter_string();
$mkFilter->SQL_key="r.message_key";
$l->filters->a($mkFilter,'message_key');
$p->a($l);

function rbc($key,$cell,&$rowData)
{
	switch($key) {
		case 'message_key':
			$cell->content="<a href='message_translate.php?m=".rawurlencode($rowData['message_key'])."'>".htmlspecialchars($rowData['message_key'])."</a>";
			break;
		case 'is_new':
			$cell->content=$rowData['is_new']?'YES':'no';
			break;
		case 'comment':
			$cell->content="<small>".htmlspecialchars($rowData['comment'])."</small>";
			break;
	}
	return true;
}
