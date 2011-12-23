<?php

if (isset($_SERVER["REQUEST_METHOD"])) {
	echo "This script is only intended for CLI.";
	exit;
}

define("LPC_SkipAuthentication",true);
require_once dirname(dirname(__FILE__))."/include/LPC_lib.php";

while(ob_get_level())
	ob_end_clean();

if (!isset($argv[1])) {
	echo "Please specify the username as the first parameter.\n";
	exit;
}

$username=$argv[1];

$u=LPC_User::newUser();
if (
	empty($u->user_fields["user"]) ||
	empty($u->user_fields['password']) ||
	empty($u->dataStructure['fields'][$u->user_fields["user"]]) ||
	empty($u->dataStructure['fields'][$u->user_fields['password']])
) {
	echo "Your user class must properly define both user_fields['user'] and user_fields['password'].\n";
	exit;
}
	

$password="";
while(!strlen($password)) {
	echo "Password for user ".$username.": ";
	$password=trim(fgets(STDIN));
	if (!strlen($password))
		echo "Please enter a password or interrupt to exit.\n";
}

$us=$u->search($u->user_fields["user"],$username);
if ($us)
	$u=$us[0];
else {
	$u=LPC_User::newUser();
	$u->setAttr($u->user_fields['user'],$username);
}
$u->setAttr($u->user_fields['password'],$u->saltPassword($password));

if ($u->save())
	echo "User ".$username." successfully added/edited.\n";
else
	echo "Failed creating/editing user ".$username."\n";
