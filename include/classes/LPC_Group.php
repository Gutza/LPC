<?php

class LPC_Group extends LPC_Base
{
	private $cacheData=array();

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

	protected function ensureCacheData($grp=false)
	{
		if (!$this->id)
			throw new RuntimeException("Need an id!");
		if (isset($this->cacheData['id']) && $this->cacheData['id']==$this->id)
			return;

		if (!$grp)
			$grp=new LPC_Group($this->id);

		$this->cacheData=array(
			'id'=>$this->id,
			'name'=>$grp->getAttr('name'),
			'isPermission'=>$grp->getAttr('type')=='permission',
			'project'=>$grp->getAttr('project'),
		);
	}

	protected function onLoad()
	{
		$this->ensureCacheData($this);
	}

	protected function beforeDelete($id)
	{
		$this->ensureCacheData();
		return true;
	}

	protected function onDelete($id)
	{
		if (
			!$this->cacheData ||
			!isset($this->cacheData['id']) ||
			$this->cacheData['id']!=$id
		)
			throw new RuntimeException("You failed to ensure the cache data before deleting this object! This should NEVER happen.");

		LPC_User::expireCache($this->cacheData['project'],0);

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

	protected function beforeSave($new, $id, $force)
	{
		if ($new)
			return true;
		$this->ensureCacheData();
		return true;
	}

	protected function onSave($new)
	{
		if ($new)
			return;
		$isPermission='permission'==$this->getAttr('type');
		if (
			$isPermission!=$this->cacheData['isPermission'] ||
			(
				$isPermission &&
				$this->getAttr('name')!=$this->cacheData['name']
			)
		)
			// Now permission, old permission no more, or perm name change
			LPC_User::expireCache(0,0);
		elseif ($this->cacheData['project']!=$this->getAttr('project')) {
			// Project change
			if (
				$this->cacheData['project']==0 ||
				$this->getAttr('project')==0
			)
				// Added or removed from global scope
				LPC_User::expireCache(0,0);
			else {
				// Moved from one project to another
				LPC_User::expireCache($this->cacheData['project'],0);
				LPC_User::expireCache($this->getAttr('project'),0);
			}
		}
	}

	/**
	* Returns the IDs of the direct user members of group with id $id in
	* project $project. If $id==0, the current group is used.
	* If $project==0, the current project is used.
	* Similar to {@link getMemberUsers()}, only this returns IDs.
	* @param integer $project the project to use (or 0 for the current project)
	* @param integer $id the group to use (or 0 for the current group)
	* @return array an indexed array of IDs
	*/
	function getMemberUserIDs($project=false, $id=0)
	{
		$groupID=$this->defaultID($id);
		if ($project===false) {
			$rs=$this->query("
				SELECT user_member
				FROM LPC_user_membership
				WHERE
					member_to=".$groupID
			);
		} else {
			$project=$this->defaultProject($project);
			if ($this->getAttr('project')!=0 && $this->getAttr('project')!=$projectID)
				return array();

			$rs=$this->query("
				SELECT user_member
				FROM LPC_user_membership
				WHERE
					project IN (0,".$project->id.") AND
					member_to=".$groupID
			);
		}
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}

	/**
	* Same as {@link getMemberUserIDs}(), but returns instantiated user objects.
	*/
	function getMemberUsers($project=false, $id=0)
	{
		return $this->instantiate($this->getMemberUserIDs($project,$id),LPC_User::getUserClass());
	}

	/**
	* Returns the IDs of the direct member groups in this group.
	* Similar to {@link getMemberGroups()}, only this returns IDs.
	*/
	function getMemberGroupIDs($id=0, $project=false)
	{
		$group=$this->defaultObject($id);
		if (!$group->id)
			throw new RuntimeException("LPC_Group::getMemberGroupIDs() needs either an explicit or an implicit group ID. Neither was provided.");
		if ($project===false) {
			$rs=$this->query("
				SELECT gg.group_member
				FROM LPC_group_membership AS gg
				WHERE
					gg.member_to=".$group->id."
			");
		} else {
			$projectID=$this->defaultProject($project)->id;
			$rs=$this->query("
				SELECT gg.group_member
				FROM LPC_group_membership AS gg
				LEFT JOIN LPC_group AS g ON g.id=gg.group_member
				WHERE
					gg.member_to=".$group->id." AND
					g.project IN (0,".$projectID.")
			");
		}
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}

	/**
	* Returns the direct member groups in this group.
	* Similar to {@link getMemberGroupIDs()}, only this returns instantiated objects.
	*/
	function getMemberGroups($id=0,$project=false)
	{
		return $this->instantiate($this->getMemberGroupIDs($id,$project));
	}

	/**
	* Returns the IDs of ALL member groups, recursively.
	* Similar to {@link getAllMemberGroups()}, only this returns IDs.
	*/
	function getAllMemberGroupIDs($id=0,$project=0,$history=array())
	{
		$kids=$this->getMemberGroupIDs($id,$project);
		$grandkids=array();
		foreach($kids as $kid) {
			if (in_array($kid,$history))
				continue;
			$my_history=array_merge($history,array($kid));
			$grandkids=array_merge($grandkids,$this->getAllMemberGroupIDs($kid,$project,$my_history));
		}
		return array_unique(array_merge($kids,$grandkids));
	}

	/**
	* Returns ALL member groups, recursively.
	* Similar to {@link getAllMemberGroupIDs()}, only this returns instantiated objects.
	*/
	function getAllMemberGroups($project=0,$id=0)
	{
		return $this->instantiate($this->getAllMemberGroupIDs($id,$project));
	}

	/**
	* Returns the IDs of ALL member users, recursively.
	* Similar to {@link getAllMemberUsers()}, only this returns IDs.
	*/
	function getAllMemberUserIDs($project=0,$id=0)
	{
		$groupID=$this->defaultID($id);
		$groups=$this->getAllMemberGroupIDs($groupID,$project);
		$groups[]=$groupID;
		$users=array();

		foreach($groups as $group)
			$users=array_merge($users,$this->getMemberUserIDs($project,$group));

		return array_unique($users);
	}

	/**
	* Returns ALL member users, recursively.
	* Similar to {@link getAllMemberUserIDs()}, only this returns instantiated objects.
	* Also see {@link getMemberUsers()} for the types of objects it returns.
	*/
	function getAllMemberUsers($project=0,$id=0)
	{
		return $this->instantiate($this->getAllMemberUserIDs($project,$id),LPC_User::getUserClass());
	}

	/**
	* Returns the IDs of the groups in which this group is a direct member.
	* Similar to {@link getMembershipGroups()}, only this returns IDs.
	*/
	function getMembershipGroupIDs($id=0,$project=0)
	{
		$group=$this->defaultObject($id);
		if (!$group->id)
			throw new RuntimeException("This method needs either an explicit or an implicit group ID. Neither was provided.");

		$projectID=$this->defaultProject($project)->id;

		$rs=$this->query("
			SELECT gg.member_to
			FROM LPC_group_membership AS gg
			LEFT JOIN LPC_group AS g ON g.id=gg.member_to
			WHERE
				gg.group_member=".$group->id." AND
				g.project IN (0,".$projectID.")
		");
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}

	/**
	* Returns the groups in which this group is a direct member.
	* Similar to {@link getMembershipGroupIDs()}, only this returns instantiated objects.
	*/
	function getMembershipGroups($id=0,$project=0)
	{
		return $this->instantiate($this->getMembershipGroupIDs($id,$project));
	}

	/**
	* Returns the IDs of ALL groups in which this group is a member, recursively.
	* Similar to {@link getAllMembershipGroups()}, only this returns IDs.
	*/
	function getAllMembershipGroupIDs($id=0,$project=0,$history=array())
	{
		$kids=$this->getMembershipGroupIDs($id,$project);
		$grandkids=array();
		foreach($kids as $kid) {
			if (in_array($kid,$history))
				continue;
			$history=array_unique(array_merge($kids,$grandkids));
			$grandkids=array_merge($grandkids,$this->getAllMembershipGroupIDs($kid,$project,$history));
		}
		return array_unique(array_merge($kids,$grandkids));
	}

	/**
	* Returns ALL groups in which this group is a member, recursively.
	* Similar to {@link getAllMembershipGroupIDs()}, only this returns instantiated objects.
	*/
	function getAllMembershipGroups($id=0,$project=0)
	{
		return $this->instantiate($this->getAllMembershipGroupIDs($id,$project));
	}

	/**
	* Filters an indexed array of group IDs by their type property;
	* returns an indexed array of IDs (or any other property, as specified by $field).
	*/
	function filterGroupsByType($groupIDs,$type,$field='id')
	{
		if (!$groupIDs)
			return array();
		$groupIDs=array_unique($groupIDs);

		$rs=$this->query("
			SELECT ".$this->db->nameQuote.$field.$this->db->nameQuote."
			FROM LPC_group
			WHERE
				id IN (".implode(",",$groupIDs).") AND
				type=".$this->db->qstr($type)."
		");
		$filtered=array();
		while(!$rs->EOF) {
			$filtered[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $filtered;
	}

	/**
	* Searches for groups by name. Returns an indexed array
	* of instantiated LPC_Group objects; the local groups are first.
	*/
	function getGroupByName($groupName,$project=0)
	{
		$projectID=$this->defaultProject($project)->id;
		$rs=$this->query("
			SELECT id
			FROM LPC_group
			WHERE
				project IN (0,".$projectID.") AND
				name=".$this->db->qstr($groupName)."
			ORDER BY project DESC
		");
		$groups=array();
		while(!$rs->EOF) {
			$groups[]=new LPC_Group($rs->fields[0]);
			$rs->MoveNext();
		}
		return $groups;
	}

	/**
	* Adds this group to the group identified by name as $groupName.
	* Be advised that this is project-dependant -- the group by that name is
	* searched for in the global scope (project=0) and in the local scope
	* (project=current). If both are found, the local project is used.
	*/
	function addToGroupByName($groupName,$project=0)
	{
		$groups=$this->getGroupByName($groupName,$project);
		if (!$groups)
			throw new RuntimeException("Group \"".$groupName."\" was not found!");
		if (count($groups)>1)
			trigger_error(
				"LPC_Group::addToGroupByName(\"".$groupName."\",".$project."): ".
				"multiple groups matched! Adding to group #".$groups[0]->id,
				E_USER_WARNING
			);
		return $this->addToGroup($groups[0]);
	}

	/**
	* Adds this group to the group specified.
	* @param $group mixed integer for the group ID or instantiated object
	* @return mixed true on success, false on failure or NULL if this group was already a member.
	*/
	function addToGroup($group)
	{
		if (is_integer($group)) {
			$groupID=$group;
			$group=new LPC_Group($groupID);
		} elseif ($group instanceof LPC_Group)
			$groupID=$group->id;
		else
			throw new RuntimeException("Unknown parameter type! Expecting an integer or a LPC_Group instance. Received ".print_r($group,1));

		$rs=$this->query("
			SELECT COUNT(*)
			FROM LPC_group_membership
			WHERE
				group_member=".$this->id." AND
				member_to=".$groupID
		);
		if ($rs->fields[0])
			return NULL;

		if (
			$this->getAttr('project') &&
			$group->getAttr('project') &&
			$this->getAttr('project')!=$group->getAttr('project')
		)
			throw new RuntimeException("Local groups can't be members into each other unless they are local to the same project!");

		$success=(bool) $this->query("
			INSERT INTO LPC_group_membership
				(group_member, member_to)
				VALUES (".$this->id.", ".$groupID.")
		");
		if (!$success)
			return false;

		LPC_User::expireCache(min($this->getAttr('project'),$group->getAttr('project')),0);
		return true;
	}

	function removeFromGroupByName($groupName,$project)
	{
		$groups=$this->getGroupByName($groupName,$project);
		if (!$groups)
			return NULL;

		$linkCount=0;
		foreach($grous as $grp)
			$linkCount+=$this->removeFromGroup($grp);

		return $linkCount;
	}

	function removeFromGroup($group)
	{
		if (is_integer($group)) {
			$groupID=$group;
			$group=new LPC_Group($groupID);
		} elseif ($group instanceof LPC_Group)
			$groupID=$group->id;
		else
			throw new RuntimeException("Unknown parameter type! Expecting an integer or a LPC_Group instance.");

		LPC_User::expireCache($this->getAttr('project'),0);

		$rs=$this->query("
			DELETE
			FROM LPC_group_membership
			WHERE
				group_member=".$this->id." AND
				member_to=".$groupID
		);
		return $rs->Affected_Rows;
	}
}
