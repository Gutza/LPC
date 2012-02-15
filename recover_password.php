<?php

$p=LPC_Page::getCurrent();
$p->title=_LS('lpcAuthRecoverTitle');
$p->st();

if (LPC_User::getCurrent(true)) {
	$p->a(new LPC_HTML_error(_LH('lpcAuthErrAlreadyLoggedOn')));
	return;
}

if (!empty($_POST['email'])) {
	// Requesting e-mail
	$us=LPC_User::newUser();
	$us=$us->search($us->user_fields["email"],$_POST['email']);
	if ($us && !$us[0]->sendRecover())
		$p->a(new LPC_HTML_error(_LH('lpcAuthErrFailEmail',$_POST['email'])));
	else
		$p->a(new LPC_HTML_confirm(_LH('lpcAuthConfirmRecoverEmail',$_POST['email'])));
	return;
}

$form=new LPC_HTML_form();
$p->a($form);
$div=new LPC_HTML_node('div');
$form->a($div);
$div->a("
<table>
<tr>
	<td>"._LS('lpcAuthRecoverEmailField')."</td>
	<td><input type='text' name='email' id='email'></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type='submit' name='submit' value='"._LS('lpcAuthRecoverButton')."'></td>
</tr>
</table>");

$p->a("<script type='text/javascript'>document.getElementById('email').focus();</script>");
