<?php

$p=LPC_Page::getCurrent();
$p->title=_LS('lpcAuthRecoverTitle');

if (LPC_User::getCurrent(true)) {
	$p->st();
	$p->a(new LPC_HTML_error(_LH('lpcAuthErrAlreadyLoggedOn')));
	return;
}

$u=LPC_User::newUser();
$us=$u->search(array($u->user_fields['email'],$u->user_fields['token']),array($_REQUEST['e'],$_REQUEST['t']));
if (!$us || $us[0]->getAttr($u->user_fields['token_date'])<time()) {
	$p->a(new LPC_HTML_error(_LH('lpcAuthInvalidToken')));
	return;
}
$u=$us[0];

if ($u->getAttr($u->user_fields['password']))
	$p->title=_LS('lpcAuthResetPasswordTitle');
else
	$p->title=_LS('lpcAuthCreatePasswordTitle');

$p->st();
if (isset($_POST['reset_password']) && $_POST['reset_password']) {
	$u->resetToken();
	if ($u->save()) {
		$p->a(new LPC_HTML_confirm(_LH('lpcAuthDoneCancel')));
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
		$p->a(new LPC_HTML_confirm(_LH('lpcAuthDoneResetConfirm',$u->getAttrH($u->user_fields['user']))));
		if ($u->includeAfterReset)
			include $u->includeAfterReset;
		return;
	}

}

$form=new LPC_HTML_node('form');
$form->setAttr('method','post');
$form->setAttr('action',$_SERVER['PHP_SELF']);
$p->a($form);

$info=new LPC_HTML_node('div');
if ($u->getAttr($u->user_fields['password'])) {
	$info->a(_LH('lpcAuthResetFormInfo',_LS('lpcAuthCancelResetButton')));
	$reset="<input type='button' name='reset' value='"._LS('lpcAuthCancelResetButton')."' onClick='document.forms[0].reset_password.value=1; document.forms[0].submit()'>";
	$label=_LS('lpcAuthResetPasswordButton');
} else {
	$info->a(_LH('lpcAuthCreatePasswordFormInfo'));
	$reset="&nbsp;";
	$label=_LS('lpcAuthCreatePasswordLabel');
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
	<th>"._LH('lpcAuthPassword')."<br><small>"._LH('lpcAuthPasswordFieldExplain')."</small></th>
	<td><input type='password' name='pwd' id='pwd'></td>
	<td rowspan='3' style='width: 500px; padding: 10px'>
		<h2>"._LH('lpcAuthValidConditionsTitle')."</h2>".
		validation_conditions().
		"
	</td>
</tr>
<tr>
	<th>"._LH('lpcAuthPasswordConfirm')."<br><small>"._LH('lpcAuthPasswordConfirmExplain')."</small></th>
	<td><input type='password' name='pwd2'></td>
</tr>
<tr>
	<th style='text-align: center'>$reset</th>
	<td style='text-align: center'><input type='submit' name='process_password' value='$label'></td>
</tr>
");

$p->a("<script type='text/javascript'>document.getElementById('pwd').focus();</script>");

function validation_conditions()
{
	$u=LPC_User::newUser();
	$conds=array();
	$conds[]=_LH('lpcAuthValidCondMinLength',$u->password_conditions['min_length']);
	if ($u->password_conditions['need_alpha'])
		$conds[]=_LH('lpcAuthValidCondAlpha');
	if ($u->password_conditions['need_numeric'])
		$conds[]=_LH('lpcAuthValidCondNumeric');
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
