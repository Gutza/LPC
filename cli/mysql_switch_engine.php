<?php

if (isset($_SERVER['REQUEST_METHOD'])) {
	echo "This script must run from the command line only.";
	exit;
}

require_once dirname(dirname(__FILE__))."/include/LPC_lib.php";

// Parameters
if ($argc<2) {
	echo <<<EOHELP
Usage:
php mysql_switch_engine.php <dbKey>
Where
* dbKey is the name of a database key registered with LPC_DB.

EOHELP;
	exit;
}

$dbKey=$argv[1];
$newEngine="InnoDB";

// Init DB connection
$db=LPC_DB::getConnection($dbKey);

// Get tables
$rs=$db->query("SHOW TABLES");
while(!$rs->EOF) {
	$table=$rs->fields[0];
	$rs->MoveNext();
	echo $table."... ";
	$rs2=$db->query("
		SELECT ENGINE
		FROM information_schema.TABLES
		WHERE
			TABLE_SCHEMA='".LPC_DB::getDatabaseName($dbKey)."' AND
			TABLE_NAME='".$table."'
	");
	if ($rs2->fields[0]==$newEngine) {
		echo "already ".$newEngine."\n";
		continue;
	}
	$rs2=$db->query("ALTER TABLE `".$table."` ENGINE=".$newEngine);
	if (!$rs2) {
		echo $db->ErrorMsg()."\n";
		continue;
	}
	echo "DONE\n";
}

echo "Clean exit.\n";

