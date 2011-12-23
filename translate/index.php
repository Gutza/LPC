<?php

require "common.php";

if (empty($_SESSION['LPC_target_lang']) || empty ($_SESSION['LPC_reference_lang']))
	header("Location: lang_select.php?m=".rawurlencode($msgKey));
elseif ($msgKey)
	header("Location: message_translate.php?m=".rawurlencode($msgKey));
else
	header("Location: message_select.php");

exit;
