<?php

class LPC_Group extends LPC_Base
{
	function registerDataStructure()
	{
		$fields=array(
			"name"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"type"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"application"=>array(
				'flags'=>array (
					'NULL' => true,
				),
			),
			"category"=>array(
				'type'=>'integer',
			),
			"project"=>array(
				'type'=>'integer',
			),
		);

		$depend=array();

		return array(
			'table_name'=>'LPC_group',
			'id_field'=>'id',
			'fields'=>$fields,
			'depend'=>$depend
		);
	}

	function onDelete($id)
	{
		$this->query("
			DELETE
			FROM LPC_group_membership
			WHERE
				member_to=$id OR
				group_member=$id
		");
		$this->query("
			DELETE
			FROM LPC_user_membership
			WHERE
				member_to=$id
		");
	}

	/**
	* Returns the IDs of the direct user members of group with id $id in
	* project $project. If $id==0, the current group is used.
	* If $project==0, the current project is used.
	* @param integer $project the project to use (or 0 for the current project)
	* @param integer $id the group to use (or 0 for the current group)
	* @return array an indexed array of IDs
	*/
	function getMemberUserIDs($project=0,$id=0)
	{
		$project=&$this->defaultProject($project);
		$groupID=$this->defaultID($id);
		$rs=$this->query("
			SELECT user_member
			FROM LPC_user_membership
			WHERE
				project=".$project->id." AND
				member_to=".$group->id
		);
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}

	/**
	* Same as {@link getMemberUserIDs}(), but returns instantiated LPC_User objects.
	*/
	function getMemberUsers($project=0,$id=0)
	{
		return $this->instantiate($this->getUserIDs($project,$id),"LPC_User");
	}

	function getMemberGroupIDs($id=0)
	{
		$group=&$this->defaultObject($id);
		if (!$group->id)
			throw new RuntimeException("LPC_Group::getMemberGroupIDs() needs either an explicit or an implicit group ID. Neither was provided.");
		$rs=$this->query("
			SELECT group_member
			FROM LPC_group_membership
			WHERE
				member_to=".$group->id
		);
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}

	function getMemberGroups($id=0)
	{
		return $this->instantiate($this->getMemberGroupIDs($id));
	}

	function getAllMemberGroupIDs($id=0,$history=array())
	{
		$kids=$this->getMemberGroupIDs($id);
		$grandkids=array();
		foreach($kids as $kid) {
			if (in_array($kid,$history))
				continue;
			$history=array_unique(array_merge($kids,$grandkids));
			$grandkids=array_merge($grandkids,$this->getAllMemberGroupIDs($kid,$history));
		}
		return array_unique(array_merge($kids,$grandkids));
	}

	function getAllMemberGroups($id=0)
	{
		return $this->instantiate($this->getAllMemberGroupIDs($id));
	}

	function getAllMemberUserIDs($project=0,$id=0)
	{
		$groups=$this->getAllMemberGroupIDs($id);
		$users=array();
		foreach($groups as $group) {
			$users=array_merge($users,$this->getMemberUserIDs($project,$group));
		}
		return array_unique($users);
	}

	function getAllMemberUsers($project=0,$id=0)
	{
		return $this->instantiate($this->getAllMemberUserIDs($project,$id),'LPC_User');
	}
}
