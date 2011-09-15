<?php

/**
 * LPC Foreign Dependency Manager class.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) November 2009, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id: LPC_foreign_dependency_manager.php,v 1.3 2011/01/12 21:35:24 bogdan Exp $
 */
class LPC_foreign_dependency_manager
{
	static private $dependencies=array();

	private function __construct() {}

	public static function setDependencies($class,$depData)
	{
		foreach($depData as $k=>$v)
			if (!isset($v['dbKey']))
				throw new RuntimeException("Foreign dependencies MUST define the database key (key 'dbKey' in dependency '$k')");
		if (!isset(self::$dependencies[$class]))
			self::$dependencies[$class]=$depData;
		else
			self::$dependencies[$class]=array_merge(self::$dependencies[$class],$depData);
	}

	public static function getDependencies($class)
	{
		if (isset(self::$dependencies[$class]))
			return self::$dependencies[$class];
		return array();
	}
}
