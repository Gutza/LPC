<?php

$p = LPC_Page::getCurrent();
$u = LPC_User::getCurrent(true);
if (!$u)
	$p->a(new LPC_HTML_error(__L("lpcLogoutAlready")));
else {
	$u->logout();
	$p->a(new LPC_HTML_confirm(__L("lpcLogoutConfirm")));
}
$p->show();
