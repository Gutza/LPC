<?php

$u=LPC_User::getCurrent();
$p=LPC_Page::getCurrent();
if (!$u->isHyperuser()) {
	$p->a(new LPC_HTML_error(__L("genericErrorRights")));
	return;
}

$u->expireCache(0,0);
$p->a(new LPC_HTML_confirm(__L("lpcFlushPrivsConfirm")));

