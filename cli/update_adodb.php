<?php
/*

	Updates the ADOdb code base.
	Bogdan Stancescu <bogdan@moongate.ro>, June 2010
	@package LPC

*/

$tempdir=dirname(__FILE__)."/update_adodb";
$old_ado_dir=dirname(dirname(__FILE__))."/include/adodb5";

if (isset($_SERVER['REQUEST_METHOD'])) {
	echo "This script only runs in CLI.";
	exit;
}

if (!`tar --help 2>/dev/null`) {
	echo "You need tar. Aborting.\n";
	exit;
}

if (!isset($argv[1])) {
	echo "Usage: php update_adodb.php <URL>\n";
	echo "Where <URL> is the URL for an adodb tarball,\n";
	echo "e.g. http://downloads.sourceforge.net/project/adodb/adodb-php5-only/adodb-511-for-php5/adodb511.tgz?use_mirror=ignum\n";
	exit;
}
$url=$argv[1];

if (!is_dir($tempdir) && !mkdir($tempdir)) {
	echo "Failed creating directory $tempdir! Aborting.\n";
	exit;
}

$tempfile=tempnam($tempdir,'');
$out_dir=$tempfile."_uncompressed";

if (is_dir($out_dir)) {
	echo "Directory $out_dir already exists. Aborting.\n";
}

if (function_exists("curl_init")) {
	echo "***Downloading with PHP curl***\n";
	$ch=curl_init($url);
	$fp=fopen($tempfile,'w+');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	if (!curl_exec($ch)) {
		echo curl_error($ch)."\n";
	}
	curl_close($ch);
	fflush($fp); // Yes, this really is needed, for some reason (fclose() alone doesn't cut it)
	fclose($fp);
} elseif (`wget --help 2>/dev/null`) {
	echo "***Downloading with system wget***\n";
	exec("wget -O ".escapeshellcmd($tempfile)." ".escapeshellcmd($url));
} else {
	echo wordwrap("You need either the CURL extension in PHP or the wget ".
		"package in the OS in order to download the ADOdb package. ".
		"Neither was found. Aborting.")."\n";
	exit;
}

if (!file_exists($tempfile) || !filesize($tempfile)) {
	echo "Failed downloading (or writing) the file. Aborting.\n";
	exit;
}

echo "Download finished successfully in ".$tempfile."\n";

if (!mkdir($out_dir)) {
	echo "Failed creating directory $out_dir. Aborting.\n";
	exit;
}

$tarball_cmd="tar -zxvf ".escapeshellcmd($tempfile)." -C ".escapeshellcmd($out_dir);
$zip_cmd="unzip ".escapeshellcmd($tempfile)." -d ".escapeshellcmd($out_dir);

$out=$out2=array();
exec("file ".escapeshellcmd($tempfile),$out,$errstat);
if ($errstat) {
	echo "Failed determining the file type for this download! Aborting.\n";
	exit;
}
if (strstr(implode($out,"\n"),"Zip archive")) {
	echo "This seems to be a ZIP file.\n";
	if (
		!exec($zip_cmd,$out2,$errstat) ||
		$errstat
	) {
		echo "Failed uncompressing the ZIP file. Aborting.\n";
		echo "Attempted to execute the following:\n".$zip_cmd."\n";
		exit;
	}
} elseif (strstr(implode($out,"\n"),"gzip compressed")) {
	echo "This seemd to be a tarball.\n";
	if (
		!exec($tarball_cmd,$out2,$errstat) ||
		$errstat
	) {
		echo "Failed uncompressing the tarball. Aborting.\n";
		echo "Attempted to execute the following:\n".$tarball_cmd."\n";
		exit;
	}
} else {
	echo "Unknown file type: ".implode($out,"\n").". Aborting.\n";
	exit;
}

$d = dir($out_dir);
$entries=array();
while (false !== ($entry = $d->read())) {
	if (substr($entry,0,1)=='.') {
		continue;
	}
	$entries[]=$entry;
}

if (count($entries)!=1 || substr($entries[0],0,5)!='adodb') {
	echo "Malformed archive. Aborting.\n";
	exit;
}

if ($entries[0]!='adodb5') {
	echo "WARNING! This is not ADOdb 5! Nasty stuff may ensue!\n";
}

$new_ado_dir=$out_dir."/".$entries[0];

echo "***Updating ADOdb***\n";

passthru("cp -ruv ".escapeshellcmd($new_ado_dir)."/* ".escapeshellcmd($old_ado_dir),$errstat);

if ($errstat) {
	echo "There was a problem copying the files. Aborting.\n";
	exit;
}

exec("rm -rf ".escapeshellcmd($out_dir)." ".$tempfile." ".escapeshellcmd($old_ado_dir)."/tests ".escapeshellcmd($old_ado_dir)."/docs");

// PHP ensures this only works if they're empty
@rmdir($new_ado_dir);
@rmdir($tempdir);
