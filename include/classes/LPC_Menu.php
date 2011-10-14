<?php

abstract class LPC_Menu extends LPC_HTML_widget
{
	var $nodeName="DIV";
	var $attributes=array("class"=>"sf-menu-container");

	var $structure=array();
	var $noPermMessage="
<html>
	<head>
		<title>Forbidden</title>
	</head>
	<body>
		<h1>Forbidden</h1>
		<p>You do not have the necessary permissions to access this resource.</p>
	</body>
</html>";

	function __construct($page)
	{
		// CSS
		$page->head->a(new LPC_HTML_link('stylesheet','text/css',LPC_css."/superfish.css"),"Superfish CSS");

		// JavaScript
		$page->head->a(new LPC_HTML_script(LPC_js."/jquery.js"),"jQuery JavaScript");
		$page->head->a(new LPC_HTML_script(LPC_js."/superfish.js"),"Superfish JavaScript");
		$page->head->a(new LPC_HTML_script(LPC_js."/hoverIntent.js"),"HoverIntent JavaScript");

		$js=new LPC_HTML_script();
		$js->a("jQuery(function(){ jQuery('ul.sf-menu').superfish(); });");
		$page->head->a($js,"Superfish init");
	}

	abstract function populateStructure();

	private function _doPopulateStructure()
	{
		if ($this->structure)
			return false;
		$this->populateStructure();
		$this->processPermissions($this->structure);
		return true;
	}

	function prepare()
	{
		$this->_doPopulateStructure();

		$menu=new LPC_HTML_node("UL");
		$menu->attributes=array("class"=>"sf-menu");
		$menu->a($this->processNodes($this->structure));
		$this->a($menu);
	}

	function processNodes($structure,$level=0)
	{
		$result=array();
		$first=true;
		foreach($structure as $atom) {
			if ($atom['hidden'])
				continue;
			$node=new LPC_HTML_node("LI");
			if (isset($atom['focus']))
				$node->setAttr('class','menu_focus');

			if (isset($atom['anchor']))
				$node->a($atom['anchor']);
			elseif (isset($atom['url']))
				$node->a("<a href='".$atom['url']."'>".$atom['label']."</a>");
			else
				$node->a("<a href='#' onClick='return false'>".$atom['label']."</a>");

			if (isset($atom['children'])) {
				$children=new LPC_HTML_node("UL");
				$children->a($this->processNodes($atom['children'],$level+1));
				$node->a($children);
			}
			$result[]=$node;
		}
		return $result;
	}

	function processPermissions(&$structure,$parentHidden=false)
	{
		if (strlen(LPC_user_class))
			$user=LPC_User::getCurrent(true);
		else
			$user=NULL;
		foreach($structure as $key=>$value) {
			if ($parentHidden)
				// If my parent is hidden then so am I
				$structure[$key]['hidden']=true;
			elseif (!isset($value['permission']))
				// Unset means it's free for all
				$structure[$key]['hidden']=false;
			elseif ($value['permission']===false)
				// Boolean false means the upstream code decided this shouldn't be visible
				$structure[$key]['hidden']=true;
			elseif ($value['permission']===true)
				// Boolean true means we only need an authenticated user
				$structure[$key]['hidden']=!$user;
			elseif (!$user || !$this->testPermissionStruct($value['permission']))
				// Anything else means we actually check for the permission
				$structure[$key]['hidden']=true;
			else
				// So you are authenticated and you have the permission
				$structure[$key]['hidden']=false;

			// If visible and has children, process children
			if (isset($value['children']))
				$this->processPermissions($structure[$key]['children'],$structure[$key]['hidden']);
		}
	}

	/*
		array(
			'type'=>'AND',
			'permissions'=>array(
				'Can do this',
				array(
					'type'=>'OR',
					'permissions'=>array(
						'Can do that',
						'Can do the other',
					),
				),
			),
		),
	*/
	function testPermissionStruct($struct)
	{
		$user=LPC_User::getCurrent();
		if (is_string($struct))
			return $user->hasPerm($struct);
		if (!is_array($struct))
			throw new RuntimeException("Malformed permissions structure: neither string nor array.");
		if (!isset($struct['type']) || !isset($struct['permissions']))
			throw new RuntimeException("Malformed permissions structure: key 'type' or 'permissions' missing.");
		if (!in_array($struct['type'],array('AND','OR')))
			throw new RuntimeException("Malformed permissions structure: key 'type' must be either 'AND' or 'OR'.");
		if (!is_array($struct['permissions']))
			throw new RuntimeException("Malformed permissions structure: key 'permissions' is not an array!");

		foreach($struct['permissions'] as $perm)
		{
			if ($this->testPermissionStruct($perm)) {
				if ($struct['type']=='OR')
					return true;
			} elseif ($struct['type']=='AND')
				return false;
		}
		return $struct['type']=='AND';
	}

	function resetFocus()
	{
		$this->resetStructureFocus($this->structure);
	}

	function resetStructureFocus(&$structure)
	{
		foreach($structure as $key=>$value) {
			$structure[$key]['focus']=false;
			if (isset($value['children']))
				$this->resetStructureFocus($structure[$key]['children']);
		}
	}

	function focus($focusKey)
	{
		// We want this logic here in order to test the permissions at the beginning of pages,
		// where focus is set, and not at the end of the page, when it's actually rendered,
		// because by then we might have performed some actions already.
		if (!$this->_doPopulateStructure())
			$this->resetFocus();

		if (!$this->setStructureFocus($focusKey,$this->structure))
			throw new RuntimeException("Failed finding menu key \"".$focusKey."\"");
	}

	function noPermission($atom)
	{
		header('HTTP/1.1 403 Forbidden');
		echo $this->noPermMessage;
		trigger_error("LPC: Access denied",E_USER_WARNING);
		exit;
	}

	function testPermissions($atom)
	{
		if ($atom['hidden'])
			$this->noPermission($atom); // noPermission() actually exits
		return true;
	}

	function setStructureFocus($focusKey,&$structure)
	{
		foreach($structure as $key=>$value) {
			if ($key==$focusKey) {
				$structure[$key]['focus']=true;
				$this->testPermissions($value);
				return true;
			}
			if (isset($value['children'])) {
				if ($this->setStructureFocus($focusKey,$structure[$key]['children'])) {
					$structure[$key]['focus']=true;
					$this->testPermissions($value);
					return true;
				}
			}
		}
		return false;
	}

}
