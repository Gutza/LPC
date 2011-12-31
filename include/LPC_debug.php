<?php
function LPC_exceptionHandler($exception)
{
	echo "<h1>LPC: Uncaught exception</h1>";
	echo "<h2 style='color:red'>", htmlspecialchars($exception->getMessage()), "</h2>";
	echo "<p>Exception trace:<ul>";
	$trace=$exception->getTrace();
	foreach($trace as $atom) {
		echo "<li>";
		echo $atom['file'].':'.$atom['line']." &mdash; ".$atom['function']."()";
		echo "</li>";
	}
	echo "</ul>";
	exit;
}
set_exception_handler('LPC_exceptionHandler');
/**
* This function starts a timer - use {@link LPC_getTimer()} afterwards to
* get the time elapsed.
*
* Calling this function again with the same parameter will reset the
* respective watch.
*
* @param string $timer the name of the timer to start
*/
function LPC_startTimer($timer)
{
	global $_LPC;
	list($usec, $sec) = explode(" ",microtime());
	$_LPC["page"]["timers"][$timer]=(float)$usec + (float)$sec;
	return(true);
}

/**
* This function returns the time elapsed in seconds since the named timer was started
* with {@link LPC_startTimer()}.
*
* @param string $timer the timer to read
* @return float the number of seconds elapsed since the timer was started.
*/
function LPC_getTimer($timer,$raw=false)
{
	global $_LPC;
	if (!isset($_LPC["page"]["timers"][$timer])) {
		echo "TIMER: Warning: timer $timer not initialized. Starting it now and ignoring request. (in getWatch())";
		startWatch($timer);
		return(0);
	}
	static $prev=NULL;
	list($usec, $sec) = explode(" ",microtime());
	$current=(float)$usec + (float)$sec-$_LPC["page"]["timers"][$timer];
	if ($prev===NULL) $prev=$current;
	$delta=$current-$prev;
	$prev=$current;
	if ($raw)
		return $current;

	return number_format($current,3)." (delta: ".number_format($delta,3).")";
}

function LPC_backtrace()
{
	$raw=array_reverse(debug_backtrace());
	array_pop($raw);
	$simple=array();
	foreach($raw as $Ratom) {
		$Satom=$Ratom['file'].":".$Ratom['line'];
		if (isset($Ratom['class']))
			$Satom.=" => ".$Ratom['class']."::".$Ratom['function']."(";
		elseif (isset($Ratom['function']))
			$Satom.=" => ".$Ratom['function']."(";
		if (isset($Ratom['args'])) {
			$args=array();
			foreach($Ratom['args'] as $arg) {
				if (is_object($arg))
					$args[]=get_class($arg)." object";
				elseif (is_string($arg))
					$args[]='"'.addslashes($arg).'"';
				else
					$args[]=(string) $arg;
			}
			$Satom.=implode(", ",$args).")";
		}
		$simple[]=htmlspecialchars($Satom);
	}
	return "<div style='padding: 5px; border: 1px solid red'><h2>LPC Backtrace</h2><ol><li>".implode("</li><li>",$simple)."</li></ol></div>";
}
