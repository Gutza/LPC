<?php

$p=LPC_Page::getCurrent();
$p->st(_LS('rightsTestCache'));

if (!LPC_User::getCurrent()->isSuperuser()) {
	$p->a(new LPC_HTML_error(_LS('genericErrorRights')));
	return;
}

$u=LPC_User::newUser();
$u->idFromArrayKey($_REQUEST, 'user_id');

$f=new LPC_HTML_form();
$p->a($f);
$f->a(htmlspecialchars(_LS('rightsTestUserID')).": <input type='text' name='user_id' value='".$u->id."'> ");
$f->a("<input type='submit' name='submit' value='"._LS('rightsTestSubmit')."'>");

if (!$u->id)
	return;

if (defined('LPC_project_class') && LPC_project_class)
	$prj=LPC_Project::getCurrent();
else
	$prj=NULL;

$cache=LPC_Cache::getCurrent();
$t=new LPC_HTML_table();
$p->a($t);
$t->a("<tr><th>"._LS('rightTestUserDate')."</td><td>".date('r', $cache->getUPf(LPC_User::PD_KEY, $u->id))." (".($u->validatePermissionsCache()?"VALID":"INVALID").")</td></tr>");
$t->a("<tr><th>"._LS('rightTestGlobalExpDate')."</th><td>".date('r', $cache->getG(LPC_User::PE_KEY))."</td></tr>");
$t->a("<tr><th>"._LS('rightTestUserExpDate')."</th><td>".date('r', $cache->getU(LPC_User::PE_KEY, $u->id))."</td></tr>");
if ($prj) {
	$t->a("<tr><th>"._LS('rightTestProjectExpDate')."</th><td>".date('r', $cache->getP(LPC_User::PE_KEY, $prj->id))."</td></tr>");
	$t->a("<tr><th>"._LS('rightTestUserProjectExpDate')."</th><td>".date('r', $cache->getUP(LPC_User::PE_KEY, $u->id, $prj->id))."</td></tr>");
}
$t->a("<tr><th>"._LS('rightTestUserPermissions')."</th><td><pre>".print_r($cache->getUPf(LPC_User::P_KEY, $u->id),1));
