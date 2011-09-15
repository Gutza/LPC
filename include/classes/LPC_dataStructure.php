<?php

/**
 * LPC Data Structure singleton.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) October 2009, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id: LPC_dataStructure.php,v 1.3 2011/01/12 21:35:36 bogdan Exp $
 */
class LPC_dataStructure
{

	private static $ds=array();

	private function __construct()
	{
	}

	public static function registerDataStructure($class,$ds)
	{
		if (isset(LPC_dataStructure::$ds[$class])) {
			return false;
		}
		LPC_dataStructure::$ds[$class]['ds']=$ds;
		return true;
	}

	public static function getDataStructure($class)
	{
		if (isset(LPC_dataStructure::$ds[$class]['ds'])) {
			return LPC_dataStructure::$ds[$class]['ds'];
		}
		return array();
	}

	public static function gotDataStructure($class)
	{
		return isset(LPC_dataStructure::$ds[$class]['ds']);
	}
}
