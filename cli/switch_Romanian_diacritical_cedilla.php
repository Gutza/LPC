<?php

foreach ($argv as $idx=>$fileName) {
	if (0 == $idx || __FILE__ == realpath($fileName))
		continue;
	echo "Processing $fileName... ";
	if (!is_writable($fileName)) {
		echo "not writable!\n";
		continue;
	}
	$fdata = file_get_contents($fileName);
	echo "[".strlen($fdata)." bytes]... ";

	$c = array(0, 0, 0, 0);

	$fdata = str_replace("ş", "ș", $fdata, $c[0]);
	$fdata = str_replace("Ş", "Ș", $fdata, $c[1]);
	$fdata = str_replace("ţ", "ț", $fdata, $c[2]);
	$fdata = str_replace("Ţ", "Ț", $fdata, $c[3]);

	$cSum = array_sum($c);
	if (0 == $cSum) {
		echo "no changes!\n";
		continue;
	}

	echo "writing... ";
	file_put_contents($fileName, $fdata);
	echo "OK (".$cSum." changes)\n";
}

