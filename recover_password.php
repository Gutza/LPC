<?php

$p=LPC_Page::getCurrent();
$p->title=__L("Password recovery");
$p->st();

if (LPC_User::getCurrent(true)) {
	$p->a(new LPC_HTML_error(__L("You are already authenticated!")));
	return;
}

//$p->menu->focus('recover');

if (isset($_POST['submit'])) {
	// Requesting e-mail
	$us=LPC_User::newUser();
	$us=$us->search($us->user_fields["email"],$_POST['email']);
	if ($us && !$us[0]->sendRecover())
		$p->a(new LPC_HTML_error(__L("The password recovery e-mail message has not been sent. Please make sure you entered the correct e-mail address.")));
	else
		$p->a(new LPC_HTML_confirm(__L("If there is any registered user with the e-mail address you have entered, the password recovery e-mail has been successfully sent.")));
	return;
}

$form=new LPC_HTML_form();
$p->a($form);
$div=new LPC_HTML_node('div');
$form->a($div);
$div->a("
<table>
<tr>
	<td>".__L("Your e-mail address")."</td>
	<td><input type='text' name='email' id='email'></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type='submit' name='submit' value='".__L("Recover password")."'></td>
</tr>
</table>");

$p->a("<script type='text/javascript'>document.getElementById('email').focus();</script>");
