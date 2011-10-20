<?php

$u=LPC_User::getCurrent();
$p=LPC_Page::getCurrent();
if (!$u->isHyperuser()) {
	$p->a(new LPC_HTML_error(__L("You do not have the necessary rights to flush privileges.")));
	return;
}

$u->expireCache(0,0);
$p->a(new LPC_HTML_confirm(__L("Done, all privileges have been flushed.")));

