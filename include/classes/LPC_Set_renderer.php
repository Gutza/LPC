<?php
// vim: fdm=marker:
/**
 * LPC Set Renderer base class.
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) June 2010, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 * @version $Id: LPC_Set_renderer.php,v 1.10 2010/10/08 10:05:15 bogdan Exp $
 */
class LPC_Set_renderer
{
// {{{ CLASS VARIABLES
	// The structure to process
	var $structure=array();
	var $new_structure;
	var $dependencies;
	private $all_includes;
	private $unused_includes;
	private $name_regexp="[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*";
	const default_atom_name='DEFAULT';
// }}}
// {{{ CONSTRUCTOR
	public function __construct($structure=NULL)
	{
		if (!empty($structure)) {
			$this->structure=$structure;
		}
	}
// }}}
// {{{ OVERRIDABLE METHODS
	// {{{ preProcessGenericAtom()
	// This can be overriden by descendants
	function &preProcessGenericAtom(&$atom,&$parent,$atom_key)
	{
		return $atom;
	}
	// }}}
	// {{{ preShouldInclude()
	// This can be overriden by descendants
	function preShouldInclude($atomName)
	{
		return false;
	}
	// }}}
// }}}
// {{{ PUBLIC METHODS
	function render($structure=array(),$criteria=NULL)
	{
		if ($criteria===NULL)
			$criteria=$structure;
		else
			$this->structure=$structure;
		if (empty($criteria))
			$criteria=self::default_atom_name;
		if (!is_array($criteria))
			$criteria=array($criteria);
		$this->processDependencies($criteria);
		$this->processStructure($criteria);
		return $this->new_structure;
	}
// }}}
// {{{ PRIVATE METHODS
	// {{{ processDependencies()
	function processDependencies($criteria)
	{
		$this->dependencies=array();
		$this->all_includes=$criteria;
		if (empty($this->structure['dependencies']))
			return;
		if (!is_array($this->structure['dependencies']))
			throw new RuntimeException("Key 'dependencies' in sets must be an array!");
		do {
			$last_deps=$this->dependencies;
			$this->processDependenciesOnce($this->all_includes);
		} while ($last_deps!=$this->dependencies);
	}
	// }}}
	// {{{ processDependenciesOnce()
	function processDependenciesOnce($criteria)
	{
		$all_deps=$this->structure['dependencies'];
		$my_deps=array();
		foreach($criteria as $criterion) {
			if (empty($all_deps[$criterion]))
				continue;
			if (is_array($all_deps[$criterion]))
				$my_deps=array_merge($my_deps,$all_deps[$criterion]);
			else
				$my_deps[]=$all_deps[$criterion];
		}
		$my_deps=array_unique($my_deps);
		$forbidden=array();
		foreach($my_deps as $key=>$value)
			if (substr($value,0,1)=='!') {
				unset($my_deps[$key]);
				$forbidden[]=substr($value,1);
			}
		$this->dependencies=array_values($my_deps);
		$this->all_includes=array_unique(array_merge($criteria,$this->dependencies));
		$conflicts=array_intersect($forbidden,$this->all_includes);
		if ($conflicts)
			throw new RuntimeException("Dependency conflict in set: the following atoms(s) conflict with other atoms, per the dependency rules specified in the set descriptor: '".implode("', '",$conflicts)."'");
	}
	// }}}
	// {{{ processStructure()
	function processStructure($criteria)
	{
		$this->new_structure=$this->structure;
		$this->unused_includes=$this->all_includes;
		if (isset($this->new_structure['dependencies']))
			unset($this->new_structure['dependencies']);
		$this->processAtoms($this->new_structure);
		$this->unused_includes=array_diff($this->unused_includes,array(self::default_atom_name));
		if ($this->unused_includes)
			$this->php_warning("Unused includes: ".implode(", ",$this->unused_includes));
	}
	// }}}
	// {{{ processAtoms()
	function processAtoms(&$parent)
	{
		while($this->processAtomsOnce($parent));
	}
	// }}}
	// {{{ processAtomsOnce()
	function processAtomsOnce(&$parent)
	{
		$process=false;
		foreach($parent as $key=>$struct) {
			$process=$process || $this->processAtom($parent[$key],$parent,$key);
			if (
				isset($parent[$key]['type']) &&
				$parent[$key]['type']=='alternative' &&
				isset($parent[$key]['content'])
			) {
				if (count($parent[$key]['content'])>1)
					$this->php_warning("More than one child left under key $key's content: ".implode(", ",array_keys($parent[$key]['content']))."; only the first one will be retained");
				$parent[$key]=array_shift($parent[$key]['content']);
			}
		}
		return $process;
	}
	// }}}
	// {{{ processAtom()
	function processAtom(&$atom,&$parent,$atom_key)
	{
		if (is_array($atom))
			return $this->processArrayAtom($atom,$parent,$atom_key);
		else
			return $this->processGenericAtom($atom,$parent,$atom_key);
	}
	// }}}
	// {{{ processArrayAtom()
	function processArrayAtom(&$atom,&$parent,$atom_key)
	{
		$parent_changes=false;
		if (isset($atom['name'])) {
			$this->checkAtomName($atom['name']);
			if ($this->shouldInclude($atom['name'])) {
				$this->unused_includes=array_diff($this->unused_includes,array($atom['name']));
				if (isset($atom['content'])) {
					// The parent atom should only contain keys "name" and "content".
					// Let's give them fair warning if that's not the case
					if ($diff=array_diff(array_keys($atom),array("name","content")))
						$this->php_warning("The following unknown array key(s) will be overwritten: ".implode(", ",$diff));
					$parent[$atom_key]=$atom['content'];
					$parent_changes=true;
				} else
					unset($atom['name']);
			} else {
				unset($parent[$atom_key]);
				// This entire branch is extinct now
				return false;
			}
		}
		if ($parent_changes)
			// We need to re-process this entire branch
			return true;
		$this->processAtoms($atom);
	}
	// }}}
	// {{{ shouldInclude()
	function shouldInclude($atomName)
	{
		if ($this->preShouldInclude($atomName))
			return true;
		return in_array($atomName,$this->all_includes);
	}
	// }}}
	// {{{ processGenericAtom()
	// Checks for callbacks and other stuff
	function processGenericAtom(&$atom,&$parent,$atom_key)
	{
		$atom=&$this->preProcessGenericAtom($atomm,$parent,$atom_key);
		if (!preg_match_all("/{:(".$this->name_regexp.")}/",$atom,$matches))
			return false;
		$callbacks=$matches[1];
		if (count($callbacks)==1 && $atom=='{:'.$callbacks[0].'}') {
			$atom=call_user_func($callbacks[0]);
			return true; // Might contain wicked stuff
		}
		$callbacks=array_unique($callbacks); // Why call the same function several times?
		foreach($callbacks as $callback) {
			$result=call_user_func($callback);
			$atom=str_replace("{:".$callback."}",$result,$atom);
		}
		return true; // Might also contain callbacks of its own
	}
	// }}}
	// {{{ checkAtomName()
	function checkAtomName($name)
	{
		if (preg_match("/^".$this->name_regexp."$/",$name))
			return true;
		throw new RuntimeException("Atom name '$name' invalid; atom names follow the same rules as PHP variables and functions.");
	}
	// }}}
	// {{{ php_warning()
	function php_warning($message)
	{
		$db=debug_backtrace();
		$encountered=false;
		for($i=0;$i<count($db);$i++) {
			if ($encountered && $db[$i]['file']!=__FILE__)
				break;
			if (!$encountered && $db[$i]['file']==__FILE__)
				$encountered=true;
		}
		trigger_error($message." (in ".$db[$i]['file']." on line ".$db[$i]['line'].")",E_USER_WARNING);
	}
	// }}}
// }}}
}
