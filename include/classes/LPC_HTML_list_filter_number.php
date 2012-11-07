<?php

/**
 * The numeric list filter.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) April 2012, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id$
 */

class LPC_HTML_list_filter_number extends LPC_HTML_list_filter
{
	function prepare()
	{
		$default=addslashes($this->getCurrentValue());

		
	}
}
