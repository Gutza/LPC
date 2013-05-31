<?php

if (LPC_skip())
	return;

if (LPC_GUI) {
	$p=LPC_Page::getCurrent();
	if (LPC_GUI_OB) {
		$content="";
		while(ob_get_level()>LPC_OB_BASELINE)
			$content=ob_get_clean().$content;
		$p->a($content);
	}
	$p->show(true);
}
