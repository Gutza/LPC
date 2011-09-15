<?php

// TODO: Proper caching, with expiration date and so on (currently we cache all perms for the entire session)

abstract class LPC_User extends LPC_Base
{
	private static $currentInstance=NULL;

	// You should create a key on (user, password), because that's used for authentication
	var $user_fields=array(
		'user'=>'user', 	// any field type works
		'password'=>'password'	// char(40), since this contains a SHA1 checksum
	);

	function onDelete($id)
	{
		$this->query("
			DELETE
			FROM LPC_user_membership
			WHERE
				user_member=$id
		");
	}

	public static function setCurrent($object)
	{
		if (isset(self::$currentInstance)) {
			return false;
		}
		if (!isset($object) || !$object->id) {
			unset($_SESSION['LPC']['current_user_id']);
			return true;
		}
		self::$currentInstance=$object;
		$_SESSION['LPC']['current_user_id']=$object->id;
		return true;
	}

	public static function logout()
	{
		if (!self::getCurrent(true))
			return;

		self::$currentInstance=NULL;
		session_destroy();
	}

	/**
	* Returns the current user, or show the authentication form and exit.
	*
	* @param bool $info if true, don't exit if there's not user, just return NULL
	*/
	public static function getCurrent($info=false)
	{
		if (isset(self::$currentInstance)) {
			return self::$currentInstance;
		}
		if (isset($_SESSION['LPC']['current_user_id'])) {
			$u=self::newUser($_SESSION['LPC']['current_user_id']);
			if ($u->probe())
				self::setCurrent($u);
		}
		if (isset(self::$currentInstance)) {
			return self::$currentInstance;
		}
		if ($info) {
			return NULL;
		}
		$u=self::newUser();
		$u->authenticate();
		if (isset(self::$currentInstance)) {
			return self::$currentInstance;
		}

		// We should NEVER end up executing this code!
		throw new RuntimeException(
			"Unexpected condition: ".
			self::getUserClass()."::authenticate() ".
			"returned without setting the current user!"
		);
	}

	public static function newUser($id=0)
	{
		$class=self::getUserClass();
		return new $class($id);
	}

	public function getUserClass()
	{
		if (!defined("LPC_user_class")) {
			throw new RuntimeException("Please define constant LPC_user_class if you want to use users.");
		}
		return LPC_user_class;
	}

	public function matchCredentials($uname,$pwd)
	{
		$salted=$this->saltPassword($pwd);
		$u=$this->search(
			array(
				$this->user_fields['user'],
				$this->user_fields['password']
			),
			array($uname,$salted)
		);
		if (!$u)
			return false;
		if (count($u)>1)
			throw new RuntimeException("Multiple users found with the same credentials!");
		return $u[0];
	}

	public function saltPassword($pass)
	{
		return sha1("Al".sha1($pass)."Pacino");
	}

	public function newSecret($field='',$length=40)
	{
		do {
			if (isset($secret)) {
				usleep(3);
			}
			$secret=substr(sha1("Robert".microtime()."De".rand()."Niro"),0,$length);
		} while (!$this->validateSecret($secret,$field));
	}

	private function validateSecret($secret,$field)
	{
		if (!$field) {
			return true;
		}
		return !$this->searchCount($field,$secret);
	}

	public function authenticate()
	{
		if (isset($_POST['login'])) {
			$me=$this->matchCredentials($_POST['username'],$_POST['password']);
			if ($me) {
				LPC_User::setCurrent($me);
				return LPC_User::getCurrent();
			}
		}
		$this->showLoginForm();
	}

	protected function populateLogin($p)
	{
		$p->title="Authentication";
		$p->a("<h1 style='text-align:center'>Authentication</h1>");

		$p->a("<form method='POST' action=''>");
		$p->a("  <table class='login_form'>");
		$p->a("    <tr>");
		$p->a("      <td style='text-align:right; width:50%'>Username/email</td>");
		$p->a("    <td><input type='text' name='username' id='username'></td>");
		$p->a("    </tr>");
		$p->a("    <tr>");
		$p->a("      <td style='text-align:right'>Password</td>");
		$p->a("      <td><input type='password' name='password'></td>");
		$p->a("    </tr>");
		$p->a("    <tr>");
		$p->a("      <td colspan=2 style='text-align:center'>");
		$p->a("        <input type='submit' name='login' value='Log in'>");
		$p->a("      </td>");
		$p->a("    </tr>");
		$p->a("  </table>");
		$p->a("</form>");
	}

	protected function showLoginForm()
	{
		$p=LPC_Page::getCurrent();
		$this->populateLogin($p);
		$this->postShowLogin($p);
		$p->show();
		exit;
	}

	function postShowLogin($p)
	{
		$js=new LPC_HTML_script();
		$p->a($js);

		$js->content=<<<EOJS
var inputs=document.getElementsByTagName('input');
for(var i in inputs) {
	if (inputs[i].name!='username')
		continue;
	inputs[i].focus();
	break;
}
EOJS;
	}

	function flushPermissionsCache($projectID=0,$userID=0)
	{
		$userID=$this->defaultID($userID);
		$projectID=&$this->defaultProject($projectID)->id;
		$cache=LPC_Cache::getCurrent();

		$cache->deleteUP("permissions_date",$userID,$projectID);
		$cache->deleteUP("superuser",$userID,$projectID);
		$cache->deleteUP("permissions",$userID,$projectID);
	}

	function validatePermissionsCache($projectID=0,$userID=0)
	{
		$userID=$this->defaultID($userID);
		$cache=LPC_Cache::getCurrent();

		$cacheDate=$cache->getU("permissions_date",$userID);
		if (!$projectID)
			$projectID=LPC_Project::getCurrent(true)->id;

		if ($projectID)
			$cacheDate=min($cacheDate,$cache->getUP("permissions_date",$userID,$projectID));

		if (!$cacheDate)
			return NULL;

		if ($projectID)
			$projectDate=$cache->getP("premissions_expiration",$projectID);
		else
			$projectDate=false;

		$globalDate=$cache->getG("permissions_expiration");
		if (
			$projectDate &&
			$globalDate &&
			$cacheDate>$projectDate &&
			$cacheDate>$globalDate
		)
			return true; // all is well

		// All is not well -- flushing all relevant caches
		if ($projectID)
			$this->flushPermissionsCache($userID,$projectID);

		$cache->deleteU("permissions_date",$userID);
		$cache->deleteU("hyperuser",$userID);
		return false;
	}

	function getAllPermissions($project=0,$id=0)
	{
		$userID=$this->defaultID($id);
		$projectID=&$this->defaultProject($project)->id;
		$cache=LPC_Cache::getCurrent();
		$groups=$cache->getUP("permissions",$userID,$projectID);
		if ($groups!==false && $this->validatePermissionsCache($projectID,$userID))
			return $groups;

		$rs=$this->query("
			SELECT member_to
			FROM LPC_user_membership
			WHERE
				project IN (0,".$projectID.") AND
				user_member=".$userID
		);

		if ($rs->EOF) {
			$cache->setUP("permissions",array(),$userID,$projectID);
			return array();
		}
		$group=new LPC_Group();
		$groupIDs=array();
		while(!$rs->EOF) {
			$groupIDs[]=$rs->fields[0];
			$groupIDs=array_merge($groupIDs,$group->getAllMemberGroupIDs($rs->fields[0]));
			$rs->MoveNext();
		}
		$groupIDs=array_unique($groupIDs);

		$rs=$this->query("
			SELECT name
			FROM LPC_group
			WHERE
				id IN (".implode(",",$groupIDs).") AND
				type='permission'
		");
		$groups=array();
		while(!$rs->EOF) {
			$groups[]=$rs->fields[0];
			$rs->MoveNext();
		}

		$cache->setUP("permissions",$groups,$userID,$projectID);

		return $groups;
	}

	function hasPerm($permission,$project=0,$id=0)
	{
		if ($this->isSuperuser($project))
			return true;

		return in_array($permission,$this->getAllPermissions($project,$id));
	}

	function isSuperuser($project=0,$id=0)
	{
		if ($this->isHyperuser($id))
			return true;
		$userID=$this->defaultID($id);
		$projectID=$this->defaultProject($project)->id;

		$cache=LPC_Cache::getCurrent();
		$super=$cache->getUP('superuser',$userID,$projectID);
		if ($super!==false && $this->validatePermissionsCache($projectID,$userID))
			return (bool) $super;

		$rs=$this->query("
			SELECT member_to
			FROM LPC_user_membership
			WHERE
				project=".$projectID." AND
				user_member=".$userID." AND
				member_to=1
		");
		$result=!$rs->EOF;

		$cache->setUP('superuser',(int) $result,$userID,$projectID);
		// We only need to set this here because isSuperuser() is always the first one to run in projects
		$cache->setUP("permissions_date",time(),$userID,$projectID);

		return $result;
	}

	function isHyperuser($id=0)
	{
		$userID=$this->defaultID($id);
		$cache=LPC_Cache::getCurrent();
		$hyper=$cache->getU('hyperuser',$userID);
		if ($hyper!==false && $this->validatePermissionsCache(0,$userID))
			return (bool) $hyper;

		$rs=$this->query("
			SELECT member_to
			FROM LPC_user_membership
			WHERE
				project=0 AND
				user_member=".$userID." AND
				member_to=1
		");
		$result=!$rs->EOF;
		$cache->setU('hyperuser',(int) $result,$userID);
		// We only need to set this here because isHyperuser() is always the first one to run
		$cache->setU("permissions_date",time(),$userID);
		return $result;
	}

	/*
		Specify project 0 if you want global membership; by default, the current project is used.
	*/
	function addToGroup($groupName,$project=false)
	{
		$gs=new LPC_Group();
		$gs=$gs->search("name",$groupName);
		if (!$gs)
			throw new RuntimeException("Group \"".$groupName."\" was not found!");

		if ($project===false)
			$projectID=LPC_Project::getCurrent()->id;
		elseif (is_object($project))
			$projectID=$project->id;
		else
			$projectID=$project;

		$rs=$this->query("
			SELECT COUNT(*)
			FROM LPC_user_membership
			WHERE
				user_member=".$this->id." AND
				member_to=".$gs[0]->id." AND
				project=$projectID
		");
		if ($rs->fields[0])
			return NULL;

		return (bool) $this->query("
			INSERT INTO LPC_user_membership
				(user_member, member_to, project)
				VALUES (".$this->id.", ".$gs[0]->id.", $projectID)
		");
	}
}
