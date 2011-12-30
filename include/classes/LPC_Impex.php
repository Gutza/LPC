<?php
// vim: fdm=marker:
/**
* The LPC import/export class.
* Please note that this class only retrieves an object's data into an
* array and respectively builds an object based on a similar array;
* it does not actually manage the resulting/incoming files!
* Retrofitted for LPC and PHP 5.x, December 2011
* Bogdan Stancescu <bogdan@moongate.ro>, July 2004
* @package LPC
* $Id$
*/

/**
* This class should be used via its {@link import()} and {@link export()}
* methods.
*/
class LPC_Impex
{
	static $objects=array();
	static $data=array();
	static $objectList=array();
//{{{ __construct($data=NULL)
	function __construct($data=NULL)
	{
		if (is_object($data))
			$this->parseObject($data);
		elseif (is_array($data))
			$this->parseData($data);
		elseif ($data!==NULL)
			throw new RuntimeException("Unknown data type!");
	}
//}}}
//{{{ parseObject($object)
	function parseObject($object)
	{
		if (!is_object($object))
			throw new RuntimeException("Parameter is not an object!");

		if (!($object instanceof LPC_Object))
			throw new RuntimeException("Parameter is not an LPC_Object instance!");

		if (empty($object->id))
			throw new RuntimeException("Object doesn't have an ID!");

		if (!$object->load())
			throw new RuntimeException("Failed loading object!");

		self::$objects[]=$object;
		$data=array(
			'class'=>get_class($object),
			'id'=>$object->id,
			'attr'=>$object->attr,
			'links'=>array()
		);

		/*
		Now that we have the basic object saved, we look for MISTRESS
		dependencies. We don't care for STRANGER dependencies (ever),
		and we don't care about WIFE dependencies right now (that would
		lead to recursivity, and we don't do that here).
		*/
		foreach($object->dataStructure['depend'] as $depName=>$dep) {
			if ($dep['on_mod']!='MISTRESS')
				continue;
			$objects=$object->getObjects($depName);
			foreach($objects as $linked)
				$data['links'][]=array(
					'dep'=>$depName,
					'id'=>$linked->id
				);
		}
		self::$data[]=$data;
	}
//}}}
//{{{ parseData($data)
	function parseData($data)
	{
		if (!is_array($data))
			throw new RuntimeException("Parameter is expected to be an array!");

		if (!$data['class'] || !$data['id'] || !$data['attr'])
			throw new RuntimeException("Badly formatted array!");

		$this->data[]=$data;
		$object=new $data['class'];
		$object->id=$data['id'];
		$object->attr=$data['attr'];
		$this->objects[]=$object;
		
		return($object);
	}
//}}}
//{{{ doImport($data)
	private function doImport($data)
	{
		foreach($data as $key=>$entry) {
			if (is_numeric($key))
				continue;
			$data=array($data);
			break;
		}

		$success=true;
		foreach($data as $entry) {
			if (!($current=$this->parseData($entry)))
				return false;
			$current->insertWithId();
			foreach($entry['links'] as $link)
				if (!$current->createLink($link['dep'],$link['id']))
					return false;
		}
		return true;
	}
//}}}
//{{{ doExport($objects)
	private function doExport($objects)
	{
		if (!is_array($objects))
			$objects=array($objects);

		foreach($objects as $object) {
			if (!self::parseObject($object))
				return false;

			$wives=$object->getAllObjects();
			foreach($wives as $wife) {
				$wife_key=get_class($wife['object'])."_".$wife['object']->id;
				if (in_array($wife_key,self::$objectList))
					continue;

				if (!self::doExport($wife['object']))
					return false;

				self::$objectList[]=$wife_key;
			}
		}

		return true;
	}
//}}}
//{{{ init()
	function init()
	{
		self::$objects=array();
		self::$data=NULL;
		self::$objectList=array();
	}
//}}}
//{{{ import($data)
	function import($data)
	{
		self::init();
		return self::doImport($data);
	}
//}}}
//{{{ export($object)
	function export($object)
	{
		self::init();
		if (!self::doExport($object))
			return false;
		return self::$data;
	}
//}}}
}
