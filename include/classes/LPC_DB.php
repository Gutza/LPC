<?php

/**
 * LPC Database connection singleton.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) October 2009, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id: LPC_DB.php,v 1.7 2011/06/10 18:56:10 bogdan Exp $
 */
class LPC_DB
{
	private static $dbStruct=array();

	private function __construct()
	{
	}

	public static function registerKey($key,$data)
	{
		if (isset(self::$dbStruct[$key]))
			return false;
		self::$dbStruct[$key]['data']=$data;
		return true;
	}

	public static function getConnection($key)
	{
		if (!isset(self::$dbStruct[$key]))
			throw new RuntimeException("Unknown database key \"$key\" in LPC_DB::getConnection()! Use LPC_DB::registerKey() to register this key.");

		if (isset(self::$dbStruct[$key]['connection']))
			return self::$dbStruct[$key]['connection'];

		$cd=&self::$dbStruct[$key]['data']; // connection data
		if (!function_exists("adonewconnection"))
			throw new RuntimeException("You must include the ADOdb library before calling LPC_DB::getConnection()!");

		self::$dbStruct[$key]['connection']=ADONewConnection($cd['type']);
		self::$dbStruct[$key]['connection']->Connect($cd['host'],$cd['user'],$cd['password'],$cd['database'],true);

		if (isset(self::$dbStruct[$key]['data']['collation']))
			self::$dbStruct[$key]['connection']->execute("SET NAMES '".self::$dbStruct[$key]['data']['collation']."'");

		self::$dbStruct[$key]['connection']->SetFetchMode(ADODB_FETCH_NUM + ADODB_FETCH_ASSOC);

		return self::$dbStruct[$key]['connection'];
	}
}
