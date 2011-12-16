<?php

$p=LPC_Page::getCurrent();
$p->title=__L("Password retrieval");

if (LPC_User::getCurrent(true)) {
	$p->st();
	$p->a(new LPC_HTML_error(__L("You are already authenticated!")));
	return;
}

$u=LPC_User::newUser();
$us=$u->search(array($u->user_fields['email'],$u->user_fields['token']),array($_REQUEST['e'],$_REQUEST['t']));
if (!$us || $us[0]->getAttr($u->user_fields['token_date'])<time()) {
	$p->a(new LPC_HTML_error(__L("The data you're trying to use is invalid. Either they have already been used for access, or the token has expired, or you haven't copied the complete URL from the e-mail message.")));

	// Get them to authenticate
	LPC_User::getCurrent();

	// Once they do authenticate, send them to the project
	header("Location: ".LPC_project_url."/");
	exit;
}
$u=$us[0];

if ($u->getAttr($u->user_fields['password']))
	$p->title=__L("Password reset");
else
	$p->title=__L("Create your password");

$p->st();
if (isset($_POST['reset_password']) && $_POST['reset_password']) {
	$u->resetToken();
	if ($u->save()) {
		$p->a(new LPC_HTML_confirm(__L("You have successfully cancelled the password retrieval request.")));
		return;
	}
}
if (isset($_POST['process_password'])) {
	if ($error=$u->passwordProblems($_POST['pwd'],$_POST['pwd2'])) {
		$p->a(new LPC_HTML_error($error));
	} else {
		$u->load();
		$u->setAttr($u->user_fields['password'],$u->saltPassword($_POST['pwd']));
		$u->resetToken();
		$u->save();
		LPC_User::setCurrent($u);
		$p->a(new LPC_HTML_confirm(__L("Congratulations! You have successfully changed your password! You will be able to log in with username %1\$s and the password you just entered.",$u->getAttr($u->user_fields['user']))));
		return;
	}

}

$form=new LPC_HTML_node('form');
$form->setAttr('method','post');
$form->setAttr('action',$_SERVER['PHP_SELF']);
$p->a($form);

$info=new LPC_HTML_node('div');
if ($u->getAttr($u->user_fields['password'])) {
	$info->a(__L(
		"<p>Please use the form below to change the password for your account.</p>".
		"<p>If you have NOT requested a password reset, please click the button labeled &quot;<i>Cancel the password reset</i>&quot; at the bottom of the form.</p>"
	));
	$reset="<input type='button' name='reset' value='".__L("Cancel the password reset")."' onClick='document.forms[0].reset_password.value=1; document.forms[0].submit()'>";
	$label=__L("Reset the password");
} else {
	$info->a(__L("<p>Please create a password for this project."));
	$reset="&nbsp;";
	$label=__L("Create the account");
}
$info->a(<<<EOINFO
<input type='hidden' name='e' value="{$_REQUEST['e']}">
<input type='hidden' name='t' value="{$_REQUEST['t']}">
<input type='hidden' name='reset_password' value="0">
EOINFO
);
$form->a($info);

$table=new LPC_HTML_node('table');
$table->setAttr('class','default');
$table->setAttr('style','margin-top: 10px');
$form->a($table);

$table->a("
<tr>
	<th>".__L("Password")."<br><small>".__L("Enter the password you want to use")."</small></th>
	<td><input type='password' name='pwd' id='pwd'></td>
	<td rowspan='3' style='width: 500px; padding: 10px'>
		<h2>".__L("Password validation conditions")."</h2>".
		validation_conditions().
		"
	</td>
</tr>
<tr>
	<th>".__L("Confirm the password")."<br><small>".__L("Enter the same password again")."</small></th>
	<td><input type='password' name='pwd2'></td>
</tr>
<tr>
	<th>$reset</th>
	<td><input type='submit' name='process_password' value='$label'></td>
</tr>
");

$p->a("<script type='text/javascript'>document.getElementById('pwd').focus();</script>");

function validation_conditions()
{
	$u=LPC_User::newUser();
	$conds=array();
	$conds[]=__L("The password must be at least %d characters long.",$u->password_conditions['min_length']);
	if ($u->password_conditions['need_alpha'])
		$conds[]=__L("At least one of the characters must be a letter.");
	if ($u->password_conditions['need_numeric'])
		$conds[]=__L("At least one of the characters in the password must be a number.");
	return "<ul><li>".implode($conds,"</li><li>")."</li></ul>";
}

function error($message)
{
	$e=new LPC_HTML_node('div');
	$e->setAttr('class','error');
	$e->setAttr('style','font-weight: normal');
	$e->a("<big><b>EROARE</b></big><br>");
	$e->a($message);
	return $e;
}
