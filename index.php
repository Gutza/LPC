<?php

$p = LPC_Page::getCurrent();
$u = LPC_User::getCurrent();

if (!$u->isSuperuser()) {
	$p->a(new LPC_HTML_error(__L('genericErrorRights')));
	return;
}

$p->st("LPC main menu");

$p->a("<ul>
<li><a href='translate/'>Translations</a></li>
<li><a href='flush_privileges.php'>Flush privileges</a></li>
<li><a href='scaffolding/'>Scaffolding</a></li>
<li><a href='logout.php'>Log out</a></li>
</ul>");
