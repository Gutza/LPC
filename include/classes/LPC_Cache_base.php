<?php

abstract class LPC_Cache_base implements iLPC_Cache
{

	/*
	* Unique name -- provides a proper unique name for this installation
	*/
	final function un($name)
	{
		return LPC_INSTALLATION_KEY.'.'.$name;
	}

}
