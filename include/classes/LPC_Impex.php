<?
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
	var $objects=array();
	var $data=array();
	
	function __construct($data=NULL)
	{
		if (is_object($data))
			$this->parseObject($data);
		elseif (is_array($data))
			$this->parseData($data);
		elseif ($data!==NULL)
			throw new RuntimeException("Unknown data type!");
	}
	
	function parseObject($object)
	{
		if (!is_object($object))
			throw new RuntimeException("Parameter is not an object!");

		if (!($object instanceof LPC_Object))
			throw new RuntimeException("Parameter is not a LPC_Object instance!");

		if (empty($object->id))
			throw new RuntimeException("Object doesn't have any ID!");

		if (!$object->load())
			throw new RuntimeException("Failed loading object!");

		$this->objects[]=$object;
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
		lead to recursivity, and we don't do that here.
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
		$this->data[]=$data;
		return(true);
	}
	
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
	
	private function doExport($objects)
	{
		if (!is_array($objects))
			$objects=array($objects);

		foreach($objects as $object) {
			if (!($current=$this->parseObject($object)))
				return false;

			if (!$current)
				continue;

			$wives=$object->getAllObjects();
			foreach($wives as $wife) {
				if (in_array(get_class($wife['object'])."_".$wife['object']->id,$this->objectList))
					continue;
				if (!$this->doExport($wife['object']))
					return false;

				$this->objectList[]=get_class($wife['object'])."_".$wife['object']->id;
			}
		}

		return $this->data;
	}
	
	function init()
	{
		unset($this->objects,$this->data);
		$this->objectList=array();
	}
	
	function import($data)
	{
		$this->init();
		return($this->doImport($data));
	}
	
	function export($object)
	{
		$this->init();
		return($this->doExport($object));
	}
}
