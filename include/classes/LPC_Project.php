<?php

abstract class LPC_Project extends LPC_Base
{

	var $user_fields=array(
		'name'=>'name'
	);

	var $project_POST_var='project';
	var $label_select_project="Please select a project";
	var $label_no_projects="You don't have access to any projects";
	var $default_project_name="Default project";

	static private $currentInstance;

	public static function setCurrent($object=null,$persistent=true)
	{
		if ($object===null) {
			self::$currentInstance=null;
			if ($persistent)
				unset($_SESSION['LPC']['current_project_id']);
			return null;
		}

		self::$currentInstance=$object;
		if ($persistent)
			$_SESSION['LPC']['current_project_id']=$object->id;

		return true;
	}

	/**
	* Returns the current project, or forces the user to select one.
	*
	* @param bool $info if true, the user is NOT forced to select a project;
	*	instead, NULL is returned if no project is currently set.
	*/
	public static function getCurrent($info=false)
	{
		if (isset(self::$currentInstance))
			return self::$currentInstance;

		if (!defined("LPC_project_class")) {
			self::setCurrent(new LPC_Dummy_project());
			return self::$currentInstance;
		}

		$class=self::getProjectClass($info);
		if (!$class && $info)
			return NULL;
		if (isset($_SESSION['LPC']['current_project_id'])) {
			$p=new $class($_SESSION['LPC']['current_project_id']);
			if ($p->probe()) {
				self::setCurrent($p);
				return self::$currentInstance;
			}
		}

		// Show list; if a single one, select it automatically; if none, create one and select it
		$project=new $class();
		$cproject=$project->returnCurrent($info);
		if (!$cproject) {
			if ($info)
				return NULL;
			throw new RuntimeException("Unexpected condition: $class::returnCurrent(false) returned without a project!");
		}
		$showingList=false;
		self::setCurrent($cproject);
		self::$currentInstance->onSetProject();
		return self::$currentInstance;
	}

	/**
	* This method is called when the project is set in a session
	* (i.e. either for the first time, or when it's changed).
	*/
	public function onSetProject()
	{
	}

	public static function getProjectClass($info=false)
	{
		if (!defined("LPC_project_class") || !strlen(LPC_project_class)) {
			if ($info)
				return false;
			throw new RuntimeException("Please define constant LPC_project_class if you want to use projects.");
		}
		return LPC_project_class;
	}

	public function returnCurrent($info=false)
	{
		$class=get_class($this);
		// No project in session; maybe there is a project in GET?
		static $nowSetting=false;
		if (!$nowSetting && isset($_POST[$this->project_POST_var])) {
			$nowSetting=true;
			$p=new $class();
			$p->fromKey($_POST,$this->project_POST_var);
			if ($p->id && $p->canUse()) {
				$nowSetting=false;
				return $p;
			}
		}
		if (!$this->searchCount()) {
			$p=new $class();
			$p->setAttr($this->user_fields['name'], $this->default_project_name);
			$p->save();
			return $p;
		}

		$projects = $this->allCanUse();
		if (count($projects)==1)
			return $projects[0];

		if ($info)
			return NULL;

		$this->renderList($projects);
	}

	public function allCanUse($orderField = NULL)
	{
		if (is_null($orderField))
			$orderField = $this->user_fields['name'];
		$projects = $this->search(NULL, NULL, $orderField);
		$result = array();
		foreach($projects as $project) {
			if (!$this->canUse($project))
				continue;
			$result[] = $project;
		}
		return $result;
	}

	public function canUse($projectID=0)
	{
		$projectID=$this->defaultID($projectID);
		if ($u=LPC_User::getCurrent(true))
			return (bool) $u->getAllPermissions($projectID);
		return true;
	}

	protected function getListJS()
	{
		return <<<EOJS
<script type='text/javascript'>
	function setProject(projID)
	{
		var frm=document.getElementById('set_project');
		var fld=document.getElementById('set_project_field');
		fld.value=projID;
		frm.submit();
	}
</script>
EOJS;
	}

	public function renderList($projects)
	{
		$p=LPC_Page::getCurrent();
		$p->clear();

		$p->st($this->label_select_project);
		$p->addJS($this->getListJS());
		$p->a("<ul>");
		$any_project=false;
		foreach($projects as $project) {
			$any_project=true;
			$p->a("<li><a href='#' onClick='setProject(".$project->id.")'>".$project->getAttrH($this->user_fields['name'])."</a></li>");
		}
		$p->a("</ul>");

		if (!$any_project) {
			$p->clear();
			$p->title=$this->label_no_projects;
			$p->st();
		}

		$form=new LPC_HTML_form($_SERVER['REQUEST_URI']);
		$form->setAttr('id','set_project');
		$form->a("<input type='hidden' id='set_project_field' name='".$this->project_POST_var."' value='0'>");
		$p->a($form);

		$p->show();
		exit();
	}
}
