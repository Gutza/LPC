<?php

// TODO: Proper caching, with expiration date and so on (currently we cache all perms for the entire session)

abstract class LPC_User extends LPC_Base
{
	private static $currentInstance=NULL;

	// You should create a key on (user, password), because that's used for authentication
	var $user_fields=array(
		'user'=>'user', 	// any field type works
		'password'=>'password',	// char(40), since this contains a SHA1 checksum
		'token'=>'token',	// char(40) DEFAULT NULL, since this contains a SHA1 checksum
					// it's crucial that this can be NULL, and that it's declared as such in LPC
		'token_date'=>'token_date', // datetime DEFAULT NULL; same notes as above
		'email'=>'email',	// at least varchar(50), but typically make it varchar(255)
		'fname'=>'fname',	// at least varchar(50), but typically make it varchar(255)
		'lname'=>'lname',	// at least varchar(50), but typically make it varchar(255)
	);

	var $password_conditions=array(
		'min_length'=>6,
		'need_alpha'=>true,
		'need_numeric'=>true,
	);

	var $token_delay=7; // token validity, in days (you can use fractions if you want shorter delays)
	var $token_email_email='nobody'; // The originating e-mail address for token-related messages
	var $token_email_name=LPC_project_full_name; // The originating name for token-related messages
	var $token_invite_subject="%1\$s -- Account creation invitation";
	var $token_invite_body="Dear %1\$s,

Please create an account on %2\$s by clicking the following link:

%3\$s

This is an automated message, please do not reply.

Thank you,
The %4\$s team
";
	var $token_recover_subject="%1\$s -- Password retrieval";
	var $token_recover_body="Dear %1\$s,

Someone has initiated the password recovery procedure on %2\$s.
Please click on the link below regardless of whether you have initiated the procedure or not
(you will have an opportunity to cancel the password retrieval process, in case you haven't initiated it.)

%3\$s

Thank you,
The %4\$s team";

	const HU_KEY='perm_H'; // Cache key for whether this guy's a hyperuser
	const SU_KEY='perm_S'; // Cache key for whether this guy's a superuser in the current project
	const PD_KEY='perm_date'; // Cache key for the permissions date last read to cache
	const PE_KEY='perm_exp'; // Cache key for the permissions date last write
	const P_KEY='perms'; // Cache key which stores the permissions

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
		if (!defined("LPC_user_class"))
			throw new RuntimeException("Please define constant LPC_user_class if you want to use users.");

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
			if ($me && $me->canAuthenticate()) {
				LPC_User::setCurrent($me);
				return LPC_User::getCurrent();
			}
		}
		$this->showLoginForm();
	}

	/**
	* Override this to manage your own disabled/enabled users mechanism
	*/
	protected function canAuthenticate()
	{
		return true;
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
		$p->a("      <td>&nbsp;</td>");
		$p->a("      <td>");
		$p->a("        <input type='submit' name='login' value='Log in'>");
		$p->a("        <small><a href='".$this->recoverPasswordURL()."'>Recover password</a></small>");
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

	/**
	* Validates the permissions cache for this user in this project.
	* Here's how the permission cache works. The permissions cache
	* contains all permissions (and only permissions!) a user has
	* within a project. When the permissions cache is saved
	* (in method {@link getAllPermissions()}), it's saved with setUP().
	* At the same time, the date is recorded with setUP(self::PD_KEY).
	* Note how this is only related to READING the permissions.
	* 
	* On the other hand, there are four types of permission expiration dates:
	* - getUP(self::PE_KEY)
	* - getU(self::PE_KEY)
	* - getP(self::PE_KEY)
	* - getG(self::PE_KEY)
	* These are updated whenever the user's permissions are changed within a project,
	* a user's global permissions are changed, a group is changed within a project or
	* global groups or group relationships are changed. Method validatePermissionsCache()
	* validates the permission date against all of these expiration dates. If the permission
	* date is LATER than ALL expiration dates, the cache is valid (whatever changed, it changed
	* before the last permissions read); if the permissions cache is EARLIER than
	* ANY expiration date then the permissions are stale and the cache is cleared.
	*
	* @return mixed true if valid, false if not, NULL if no cache date was found.
	*/
	function validatePermissionsCache($projectID=0,$userID=0)
	{
		static $validated=array(); // $validates[$projectID][$userID]
		$cache=LPC_Cache::getCurrent();

		if (!isset($validated[$userID]))
			$validated[$userID]=array();
		if (!empty($validated[$userID][$projectID]))
			return true;

		$userID=$this->defaultID($userID);
		$projectID=$this->defaultProject($projectID)->id;
		$cacheDate=$cache->getUPf(self::PD_KEY,$userID,$projectID);
		if (!$cacheDate)
			return NULL;

		// !!! DO NOT REPLACE THE FOUR STATEMENTS BELOW WITH A SINGLE $cache->getUPf() !!!
		// (Any of the cache expiration dates MUST expire the cache!

		// Validate global
		if (
			!($cd=$cache->getG(self::PE_KEY)) ||
			$cacheDate<=$cd
		)
			return false;

		// Validate user, if available
		if (
			$userID &&
			(
				(!$cd=$cache->getU(self::PE_KEY,$userID)) ||
				$cacheDate<=$cd
			)
		)
			return false;

		// Validate project, if available
		if (
			$projectID &&
			(
				!($cd=$cache->getP(self::PE_KEY,$projectID)) ||
				$cacheDate<=$cd
			)
		)
			return false;

		// Validate user/project, if available
		if (
			$userID &&
			$projectID &&
			(
				!($cd=$cache->getUP(self::PE_KEY,$userID,$projectID)) ||
				$cacheDate<=$cd
			)
		)
			return false;

		$validated[$userID][$projectID]=true;

		return false;
	}

	public static function getNow()
	{
		return microtime(true);
	}

	/**
	* Fill in all cache expiration keys, if needed.
	*
	* Please note this is a static method -- both parameters
	* are mandatory, since they can't be implicit.
	*/
	private static function ensureCacheExpiration($userID,$projectID)
	{
		static $ensured=array(); // $ensured[$projectID][$userID]
		$cache=LPC_Cache::getCurrent();
		$now=self::getNow();

		// Ensure global cache expiration date
		if (empty($ensured[0])) {
			$ensured[0]=true;
			if (!$cache->getG(self::PE_KEY))
				$cache->setG(self::PE_KEY,$now);
		}

		if (!isset($ensured[$userID]))
			$ensured[$userID]=array();
		$ens=&$ensured[$userID];

		// Ensure user cache expiration date
		if (empty($ensured[$userID][0])) {
			$ensured[$userID][0]=true;
			if (!$cache->getU(self::PE_KEY,$userID))
				$cache->setU(self::PE_KEY,$now,$userID);
		}

		if (!$projectID)
			return;
		$ensured[$userID][$projectID]=true;

		// Ensure project and user/project cache expiration date
		if (!$cache->getP(self::PE_KEY,$projectID))
				$cache->setP(self::PE_KEY,$now,$projectID);
		if (!$cache->getUP(self::PE_KEY,$userID,$projectID))
			$cache->setUP(self::PE_KEY,$now,$userID,$projectID);
	}

	/**
	* Intelligent cache expiration.
	*
	* Depending on the parameters, it expires the
	* user cache only, the project cache only, the
	* user/project cache or the global cache.
	*
	* Please note this is a static method -- both parameters
	* are mandatory, since they can't be implicit.
	*/
	public static function expireCache($projectID,$userID)
	{
		$cache=LPC_Cache::getCurrent();
		if ($userID) {
			if ($projectID)
				$cache->setUP(self::PE_KEY,self::getNow(),$userID,$projectID);
			else
				$cache->setU(self::PE_KEY,self::getNow(),$userID);
		} elseif ($projectID)
			$cache->setP(self::PE_KEY,self::getNow(),$projectID);
		else
			$cache->setG(self::PE_KEY,self::getNow());
	}

	/**
	* Returns the IDs of all groups in which this user is a DIRECT member.
	* Similar to {@link getGroups()}, except this returns IDs.
	*/
	function getGroupIDs($project=0,$id=0)
	{
		$userID=$this->defaultID($id);
		$projectID=$this->defaultProject($project)->id;

		$sql="
			SELECT ug.member_to
			FROM LPC_user_membership AS ug
			LEFT JOIN LPC_group AS g ON g.id=ug.member_to
			WHERE
				ug.project IN (0,".$projectID.") AND
				user_member=".$userID." AND
				g.project IN (0,".$projectID.")
		";
		$rs=$this->query($sql);
		$groupIDs=array();
		while(!$rs->EOF) {
			$groupIDs[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $groupIDs;
	}

	/**
	* Returns the groups in which this user is a DIRECT member.
	* Similar to {@link getGroupIDs()}, except this returns instantiated objects.
	*/
	function getGroups($project=0,$id=0)
	{
		$group=new LPC_Group();
		return $group->instantiate($this->getGroupIDs($project,$id));
	}

	/**
	* Returns the IDs of ALL groups this user is a member to, recursively.
	* Similar to {@link getAllGroups()}, except this returns IDs.
	*/
	function getAllGroupIDs($project=0,$id=0)
	{
		$group=new LPC_Group();
		$groupIDs=$this->getGroupIDs($project,$id);
		$indirectIDs=array();
		foreach($groupIDs as $groupID)
			$indirectIDs=array_merge($indirectIDs,$group->getAllMembershipGroupIDs($groupID,$project));
		return array_unique(array_merge($groupIDs,$indirectIDs));
	}

	/**
	* Returns ALL groups this user is a member to, recursively.
	* Similar to {@link getAllGroupIDs()}, except this returns instantiated objects.
	*/
	function getAllGroups($project=0,$id=0)
	{
		$group=new LPC_Group();
		return $group->instantiate($this->getAllGroupIDs($project,$id));
	}

	/**
	* Retrieves all permissions this user has in the current project.
	* Notes:
	* - this method returns the names of all groups with type='permission'
	*   this user is a member of
	* - there's no way to explicitly ask for the global permissions.
	*   If the user hasn't selected a project, you get the global permissions
	*   only. If the user has selected a project, you get the global
	*   permissions AND the local permissions mangled together.
	*
	* @return array indexed array of permission names
	*/
	function getAllPermissions($projectID=0,$userID=0)
	{
		$userID=$this->defaultID($userID);
		$projectID=$this->defaultProject($projectID)->id;

		$cache=LPC_Cache::getCurrent();
		$groups=$cache->getUPf(self::P_KEY,$userID,$projectID);
		if ($groups!==false && $this->validatePermissionsCache($projectID,$userID))
			return $groups;

		self::ensureCacheExpiration($userID,$projectID);
		$cache->setUPf(self::PD_KEY,self::getNow(),$userID,$projectID);
		$groupIDs=$this->getAllGroupIDs($projectID,$userID);
		if (!$groupIDs) {
			$cache->setUPf(self::P_KEY,array(),$userID,$projectID);
			return array();
		}

		$group=new LPC_Group();
		$cache->setUPf(
			self::P_KEY,
			$groups=$group->filterGroupsByType($groupIDs,'permission','name'),
			$userID,
			$projectID
		);

		return $groups;
	}

	function hasPerm($permission,$project=0,$id=0)
	{
		if ($this->isSuperuser($project,$id))
			return true;

		return in_array($permission,$this->getAllPermissions($project,$id));
	}

	function isSuperuser($project=0,$id=0)
	{
		if ($this->isHyperuser($userID))
			return true;

		static $local_cache=array(); // A local cache, used just for runtime
		$userID=$this->defaultID($id);
		if (!isset($local_cache[$userID]))
			$local_cache[$userID]=array();

		$projectID=$this->defaultProject($project)->id;
		if (!$projectID)
			return false; // You'd be a hyperuser

		if (isset($local_cache[$userID][$projectID])) // Local superuser cache
			return $local_cache[$userID][$projectID];

		$cache=LPC_Cache::getCurrent();
		$super=$cache->getUP(self::SU_KEY,$userID,$projectID);
		if ($super!==false && $this->validatePermissionsCache($projectID,$userID)) {
			$local_cache[$userID][$projectID]=(bool) $super;
			return $local_cache[$userID][$projectID];
		}

		// We only need to set this here because isSuperuser() is always the first one to run in projects
		$cache->setUP(self::PE_KEY,time(),$userID,$projectID);

		$rs=$this->query("
			SELECT member_to
			FROM LPC_user_membership
			WHERE
				project=".$projectID." AND
				user_member=".$userID." AND
				member_to=1
		");
		$local_cache[$userID][$projectID]=$result=!$rs->EOF;

		$cache->setUP(self::SU_KEY,(int) $result,$userID,$projectID);

		return $result;
	}

	function isHyperuser($id=0)
	{
		static $local_cache=array(); // A local cache, used just for runtime
		$userID=$this->defaultID($id);
		if (isset($local_cache[$userID]))
			return $local_cache[$userID];

		$cache=LPC_Cache::getCurrent();

		$hyper=$cache->getU(self::HU_KEY,$userID);
		if ($hyper!==false && $this->validatePermissionsCache(0,$userID)) {
			$local_cache[$userID]=(bool) $hyper;
			return $local_cache[$userID];
		}

		self::ensureCacheExpiration($userID,0);

		// We only need to set this here because isHyperuser() is always the first one to run
		$cache->setU(self::PD_KEY,time(),$userID);

		$rs=$this->query("
			SELECT member_to
			FROM LPC_user_membership
			WHERE
				project=0 AND
				user_member=".$userID." AND
				member_to=1
		");
		$result=$local_cache[$userID]=!$rs->EOF;
		$cache->setU(self::HU_KEY,(int) $result,$userID);

		return $result;
	}

	/*
		Specify project 0 if you want global membership; by default, the current project is used.
	*/
	function addToGroupByName($groupName,$project=false)
	{
		$group=new LPC_Group();
		$groups=$group->getGroupByName($groupName,$project);
		if (!$groups)
			throw new RuntimeException("Group \"".$groupName."\" was not found!");
		if (count($groups)>1)
			trigger_error(
				"LPC_User::addToGroupByName(\"".$groupName."\",".$project."): ".
				"multiple groups matched! Adding to group #".$groups[0]->id,
				E_USER_WARNING
			);
		return $this->addToGroup($groups[0],$project);
	}

	/*
		Specify project 0 if you want global membership; by default, the current project is used.
	*/
	function addToGroup($group,$project=false)
	{
		if (is_numeric($group))
			$groupID=$group;
		elseif ($group instanceof LPC_Group)
			$groupID=$group->id;
		else
			throw new RuntimeException("Unknown parameter \$group type! Expecting an integer or a LPC_Group instance.");

		if ($project===false)
			$projectID=LPC_Project::getCurrent()->id;
		elseif (is_numeric($project))
			$projectID=$project;
		elseif ($project instanceof LPC_Project)
			$projectID=$project->id;
		else
			throw new RuntimeException("Unknown parameter \$project type! Expecting boolean false, an integer or a LPC_Project instance; received ".print_r($project,1));

		$rs=$this->query("
			SELECT COUNT(*)
			FROM LPC_user_membership
			WHERE
				user_member=".$this->id." AND
				member_to=".$groupID." AND
				project IN (0,".$projectID.")
		");
		if ($rs->fields[0])
			return NULL;

		$expireProjectID=$projectID;
		if (!$expireProjectID) {
			$group=new LPC_Group($groupID);
			$expireProjectID=$group->getAttr('project');
		}
		self::expireCache($expireProjectID,$this->id);

		return (bool) $this->query("
			INSERT INTO LPC_user_membership
				(user_member, member_to, project)
				VALUES (".$this->id.", ".$groupID.", $projectID)
		");
	}

	/*
		Specify project 0 if you want global membership; by default, the current project is used.
	*/
	function removeFromGroupByName($groupName,$project=false)
	{
		$group=new LPC_Group();
		$groups=$group->getGroupByName($groupName,$project);
		if (!$groups)
			return NULL;

		$linkCount=0;
		foreach($groups as $grp)
			$linkCount+=$this->removeFromGroup($grp,$project);

		return $linkCount;
	}

	/*
	* Removes ALL DIRECT memberships to that group within the context of this project.
	*
	* Specify project 0 if you only want to remove global memberships; by default,
	* global AND local relationships to this project are deleted.
	*
	* @return mixed false on error or the number of relationships that were deleted
	*   (typically 0, 1 or 2 if both a local and a global relationship were deleted)
	*/
	function removeFromGroup($group,$project=false)
	{
		if (is_integer($group))
			$groupID=$group;
		elseif ($group instanceof LPC_Group)
			$groupID=$group->id;
		else
			throw new RuntimeException("Unknown parameter \$group type! Expecting an integer or a LPC_Group instance.");

		if ($project===false)
			$projectID=LPC_Project::getCurrent()->id;
		elseif (is_integer($project))
			$projectID=$project;
		elseif ($project instanceof LPC_Project)
			$projectID=$project->id;
		else
			throw new RuntimeException("Unknown parameter \$project type! Expecting boolean false, an integer or a LPC_Project instance.");

		$rs=$this->query("
			SELECT project
			FROM LPC_user_membership
			WHERE
				user_member=".$this->id." AND
				member_to=".$groupID." AND
				project IN (0,".$projectID.")
		");
		$projectIDs=array();
		while(!$rs->EOF) {
			$projectIDs[]=$rs->fields['project'];
			$rs->MoveNext();
		}
		$linkCount=count($projectIDs);
		$projectIDs=array_unique($projectIDs);
		foreach($projectIDs as $prjID)
			self::expireCache($prjID,$this->id);

		$rs=$this->query("
			DELETE
			FROM LPC_user_membership
			WHERE
				user_member=".$this->id." AND
				member_to=".$groupID." AND
				project IN (0,".$projectID.")
		");
		if (!$rs)
			return false;

		return $linkCount;
	}

	// Token management
	function generateToken()
	{
		$this->setAttr($this->user_fields['token_date'],time()+round($this->token_delay*86400)); // days to seconds
		$tg=new LPC_Token_generator($this,$this->user_fields['token']);
		if ($tg->generate())
			return true;

		$this->resetToken();
		return false;
	}

	function resetToken()
	{
		if ($this->getAttr($this->user_fields['token'])===NULL)
			return NULL;
		$this->setAttrs(array(
			$this->user_fields['token']=>NULL,
			$this->user_fields['token_date']=>NULL,
		));
		return $this->save();
	}

	static public function recoverPasswordURL()
	{
		return LPC_url."/recover_password.php";
	}

	static public function processTokenBaseURL()
	{
		return LPC_full_url.'/welcome.php';
	}

	protected function processTokenURL()
	{
		return
			self::processTokenBaseURL().'?t='.
			$this->getAttr($this->user_fields['token']).
			"&e=".rawurlencode($this->getAttr($this->user_fields['email']));
	}

	function sendTokenMail($subject,$body)
	{
		if (mail(
			$this->getAttr($this->user_fields['email']),
			$subject,
			sprintf(
				$body,
				$this->getName(),		// %1$s
				$this->processTokenURL()	// %2$s
			),
			"From: ".$this->token_email_name." <".$this->token_email_email.">\r\n".
			"Reply-To: ".$this->token_email_name." <".$this->token_email_email.">\r\n".
			"X-Mailer: LPC Token manager",
			"-f".$this->token_email_email
		))
			return true;

		$this->resetToken();
		return false;
	}

	function sendInvitation()
	{
		if (!$this->generateToken())
			return false;

		$tis=$this->token_invite_subject;
		$tis=sprintf(__L($tis),LPC_project_name);

		$tib=$this->token_invite_body;
		$tib=sprintf(__L($tib),"%1\$s",LPC_project_full_name,"%2\$s",LPC_project_name);

		return $this->sendTokenMail($tis,$tib);
	}

	function sendRecover()
	{
		if (!$this->generateToken())
			return false;

		$trs=$this->token_recover_subject;
		$trs=sprintf(__L($trs),LPC_project_name);

		$trb=$this->token_recover_body;
		$trb=sprintf(__L($trb),"%1\$s",LPC_project_full_name,"%2\$s",LPC_project_name);

		return $this->sendTokenMail($trs,$trb);
	}

	function passwordProblems($pwd1,$pwd2)
	{
		if ($pwd1!==$pwd2)
			return __L("Please make sure you type the exact same password twice!");

		$min=$this->password_conditions['min_length'];
		if (strlen($pwd1)<$min)
			return __L("The password must be at least %d characters long.",$min);

		if ($this->password_conditions['need_alpha'] && !preg_match("/[a-zA-Z]/",$pwd1))
			return __L("At least one of the characters must be a letter.");

		if ($this->password_conditions['need_numeric'] && !preg_match("/[0-9]/",$pwd1))
			return __L("At least one of the characters in the password must be a number.");

		return false;
	}

	function getNameH()
	{
		return htmlspecialchars($this->getName());
	}

	function getName()
	{
		return $this->getAttr($this->user_fields['fname']).' '.$this->getAttr($this->user_fields['lname']);
	}
}
