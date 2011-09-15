<?php
    /**
    * This function starts a timer - use {@link getWatch} afterwards to
    * get the time elapsed.
    *
    * Calling this function again with the same parameter will reset the
    * respective watch.
    *
    * @param string $timer the name of the timer to start
    */
  function startWatch($timer)
  {
    global $_LPC;
    list($usec, $sec) = explode(" ",microtime());
    $_LPC["page"]["timers"][$timer]=(float)$usec + (float)$sec;
    return(true);
  }

    /**
    * This function returns the time elapsed in seconds since the named timer was started
    * with {@link startWatch}
    *
    * @param string $timer the timer to read
    * @return float the number of seconds elapsed since the timer was started.
    */
  function getWatch($timer,$raw=false)
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
    if ($raw) {
      return $current;
    }
    return number_format($current,3)." (delta: ".number_format($delta,3).")";
  }

