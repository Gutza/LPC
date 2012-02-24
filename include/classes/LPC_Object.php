<?php
// vim: fdm=marker:
/**
 * LPC Object, a.k.a. SC (for super class).
 * @author Bogdan Stancescu <bogdan@moongate.ro>
 * @copyright Copyright (c) October 2002, Bogdan Stancescu
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
 */
abstract class LPC_Object implements Serializable
{
// {{{ CLASS PROPERTIES
	// ----------------------------------
	//          CLASS VARIABLES
	// ----------------------------------
	/**
	 * The id of this object in the database
	 */
	var $id;

	/**
	 * The name of the class which extends this object's
	 * attributes for internationalization
	 */
	static $i18n_class="";

	/**
	 * The actual associated i18n object instance
	 */
	var $i18n_object=NULL;

	/**
	 * This object's language ID
	 */
	var $i18n_langID=0;

	/**
	 * An indexed array of fields that shouldn't be shown in the
	 * scaffolding field list. This is not a security measure,
	 * it's just meant to simplify the interface.
	 */
	var $scaffoldingHiddenAttributes=array();

	/**
	 * Field mappings for user-defined fields
	 */
	var $user_fields=array();

	/**
	 * The attributes of this object - an associative array
	 */
	var $attr;

	/**
	 * Whether any of this object's fields have been modified
	 * (which would require it to be saved).
	 */
	var $modified=false;

	/**
	 * Attribute flags - you shouldn't typically need to use this
	 * directly - it's an array like above containing associative
	 * arrays marking status flags for each attribute (such as
	 * 'modified')
	 */
	var $attr_flags;

	/**
	 * The data structure of this object
	 * Please see the documentation - won't duplicate the whole
	 * explanation here.
	 */
	var $dataStruct;

	/**
	 * Somewhat similar to $this->dataStruct, this one lists the dependencies.
	 * Used when removing (deleting) this object from the database.
	 */
	var $dependencies;

	/**
	 * Array of associative arrays of messages associated to this object.
	 *
	 * The following format is used for each element:
	 *      'id' => error number (see IDW PHP Coding Conventions in OPT)
	 *    'type' => error type (idem)
	 * 'message' => human-readable message
	 *  'obj_id' => the current object id ($this->id)
	 *  'sub_id' => subsystem error # (e.g. MySQL error #)
	 * Please remember $messages contains an ARRAY of such arrays!
	 */
	var $messages;

	/**
	 * The database key for the current object.
	 */
	var $dbKey;

	/**
	 * An ADOdb object which contains all the stuff needed for this object.
	 *
	 * Should be initialized by the constructor based on $this->dbKey,
	 * typically using {@link dbInit}.
	 */
	var $db;

	/**
	 * Tree-structured objects root cache.
	 *
	 * For the so many objects which have a tree structure, this should
	 * be set to the root of the tree in which the current object resides,
	 * if known. If not set, methods which need it MUST be able to find
	 * it out using $this->getRoot() which in turn relies on $this->dataStructure
	 * to find out which attribute to use for parenting relationship.
	 */
	var $rootId=0;

	/**
	 * This structure details the way attributes will be shown in the object list.
	 */
	var $showAttr;

	/**
	 * If set to true, logging on this type of object will not occur.
	 */
	var $noLogging=false;

	/**
	 * The module in which this object resides. Typically should only be set
	 * in top-level objects.
	 */
	var $module='LFX';

	/**
	 * This variable controls whether the consistency checker should skip any
	 * consistency tests for this class. The possible values are:
	 * 'link' => the links in this class are not tested for consistency
	 * 'depend' => the dependencies of this class are not tested
	 * 'all' => no consistency checking is performed for this class.
	 * Please note that even if you set this to 'all', other classes which
	 * link to or depend upon this class will still be fully checked for
	 * consistency, including tests which include this class, unless explicitly
	 * disabled in those classes as well.
	 * You should not disable the consistency checking for this class, that
	 * should only be done for descendants. The definition of the variable is
	 * here only for reference.
	 */
	var $disableConsistency='';

	 /**
	 * Internal; stores whether the change being performed should be logged
	 */
	var $loggableChange=false;

	/**
	* Whether this object has ever been loaded (even partially), using {@link load}()
	*/
	var $loaded=false;

	/**
	* An associative array which stores $attrName=>$attrAlias for linked fields
	*/
	private $scaffoldingIDs=array();

	static protected $scaffoldingSortableAttributesCache;
// }}}
// {{{ CONSTRUCTOR
	// ----------------------------------
	//            Constructor
	// ----------------------------------
	/**
	 * The constructor. Please *always* run parent
	 * constructors *explicitly* when extending a class
	 * (unless that has extremely adverse effects to your code)
	 * In other words, it is highly recommended that any
	 * class LFX_random extending LFX_object runs $this->LFX_object()
	 * in the constructor, any class extending LFX_random runs
	 * $this->LFX_random() in the constructor and so on.
	 * The request for explicit running the constructor means that
	 * you should not use
	 * < ?
	 *   $parent=get_parent_class($this);
	 *   $this->$parent();
	 * ? >
	 * because that fails when your class is extended and will end
	 * up calling itself over and over, looping indefinitely.
	 * The LPC_Object constructor only sets $this->id, if $id is passed.
	 * @param integer $id [optional] The id of the newly instantiated object
	 */
	function __construct($id=0)
	{
		if ($id)
			$this->id=$id;

		if (!$this->dbKey)
			throw new LogicException("Property dbKey not available in the constructor!");

		$this->dataStructure=$this->registerDataStructure();
		$this->fillDataStructure();
	}
// }}}
// {{{ OVERRIDABLE METHODS
	// ----------------------------------------------
	// Methods that require overriding by descendants
	// ----------------------------------------------
	// {{{ registerDataStructure()
	// You MUST override this in descendants!
	abstract function registerDataStructure();
	// }}}
// }}}
// {{{ PUBLIC METHODS
	// ----------------------------------
	//          PUBLIC METHODS
	// ----------------------------------
	// {{{ dbInit()
	/**
	 * Initializes $this->db.
	 *
	 * If $this->dbKey is set, it uses the values there to
	 * initialize the database class. This needs to be separate from
	 * {@link _doDbInit}() because this MUST populate $this->db,
	 * while _doDbInit() MUST NOT populate it (it returns it instead).
	 */
	function dbInit()
	{
		if ($this->db)
			return NULL;

		$this->db=$this->_doDbInit($this->dbKey);
	}
	// }}}
	// {{{ beginTransaction()
	function beginTransaction()
	{
		$this->dbInit();
		return $this->db->BeginTrans();
	}
	// }}}
	// {{{ commitTransaction()
	function commitTransaction()
	{
		$this->dbInit();
		return $this->db->commitTrans();
	}
	// }}}
	// {{{ rollbackTransaction()
	function rollbackTransaction()
	{
		$this->dbInit();
		return $this->db->rollbackTrans();
	}
	// }}}
	// {{{ addError()
	/**
	* Deprecated method, it really shouldn't be used anywhere any more.
	* Currently just throws an exception unconditionally.
	*/
	function addError($message)
	{
		throw new RuntimeException($message);
	}
	// }}}
	// {{{ onLoad()
	/**
	 * Gets executed just after loading or quickloading.
	 * Should be overridden by descendants.
	 * @param boolean $full true if it's a full load or false if it's a quickload.
	 */
	protected function onLoad($full)
	{
	}
	// }}}
	// {{{ load()
	/**
	 * Loads the object with the specified id from the database.
	 * Sets the correct $this->id upon success.
	 *
	 * @param integer $id the id of the object to load;
	 *   if not specified, it will use the current id ($this->id)
	 * @param boolean $force if false, it will avoid loading the attributes
	 *   which have already been set. Default false because 
	 * @param boolean $lazy if true, the object will only be loaded from
	 *   the database if it hasn't been loaded already;
	 * @return boolean true on success or false on error.
	 */
	function load($id=0,$force=true,$lazy=true)
	{
		if ($lazy && $this->isLoaded())
			return NULL;

		if ($result=$this->_doLoad($id, $force))
			$this->onLoad(true);
		$this->loaded=true;
		return $result;
	}
	// }}}
	// {{{ beforeSave()
	/**
	 * Gets executed just before {@link save} performs the actual saving.
	 *
	 * Should be overridden by descendants - the SC incarnation returns
	 * true unconditionally.
	 * @param boolean $new true if this will be an insert
	 * @param integer $id the id of the object which will be saved (might
	 *   not be set for inserts - see {@link onSave} if you need it)
	 * @param boolean $force same as the one passed to {@link save}
	 * @return boolean true to proceed saving or false to abort.
	 */
	protected function beforeSave($new, $id, $force)
	{
		return true;
	}
	// }}}
	// {{{ internal_beforeSave()
	protected function internal_beforeSave($new, $id, $force)
	{
		return $this->beforeSave($new, $id, $force);
	}
	// }}}
	// {{{ onSave()
	/**
	 * Gets executed immediately after a {@link save}
	 * Should be overridden by descendants.
	 * @param boolean $new set if this was an insert
	 */
	protected function onSave($new)
	{
	}
	// }}}
	// {{{ internal_onSave()
	protected function internal_onSave($new)
	{
		$this->save_i18n($new);
		$this->onSave($new);
	}
	// }}}
	// {{{ internal_onDelete()
	protected function internal_onDelete($id)
	{
		$this->onDelete($id);
	}
	// }}}
	// {{{ internal_beforeDelete()
	protected function internal_beforeDelete($id) 
	{
		return $this->beforeDelete($id);
	}
	// }}}

	// TO DO (code duplication): save(), insert() and insertWithId() share
	// quite a lot of duplicate code, should merge them or build extra
	// functions to take over the common functionality.

	// {{{ save()
	/**
	 * Saves the current objfile:///var/internal_public/helpdesk_merged/LFXlib/include/classes/LFX_object.phpect to the database.
	 *
	 * If $id is not specified, $this->id is used.
	 * If $this->id is not set (or evaluates to false) then it
	 * adds a new record with the current object's attributes
	 * to the database (actually executes $this->insert()).
	 * Sets the correct $this->id upon success.
	 * @return mixed id on success, false on error
	 *   or NULL if {@link internal_beforeSave} aborts the save
	 */
	function save($id=0, $force=0)
	{
		$id=$id?$id:$this->id;
		if (!$id)
			return $this->insert();

		$this->id=$id;

		if (!$this->modified && !$force)
			return $this->id;

		if ($this->internal_beforeSave(false, $id, $force)!==true)
			return NULL;

		$query=$this->_makeSaveQuery($force);
		if (!$query) {
			$this->save_i18n(false);
			return $this->id;
		}

		$id=$this->db->qstr($id);
		$id_fld=$this->dataStructure['id_field'];
		$table=$this->dataStructure['table_name'];
		$sql="UPDATE ".$table." SET ".$query." WHERE ".$id_fld."=".$id;
		if (!$this->query($sql))
			return false;

		foreach($this->dataStructure['fields'] as $attName=>$dataEntry)
			$this->attr_flags[$attName]['modified']=false;

		$this->modified=false;

		$this->log('update');
		$this->internal_onSave(false);
		return $this->id;
	}
	// }}}
	// {{{ insert()
	/**
	 * You shouldn't call this directly
	 *
	 * This is called by {@link save} if no id is specified
	 * AND if $this->id is not set.
	 * You REALLY don't need to call this; should you ever
	 * need to however, please note it accepts no parameters.
	 * @return boolean true on success or false on error
	 */
	function insert($withID=false)
	{
		if ($this->internal_beforeSave(true, NULL, false)!==true)
			return NULL;

		$table=$this->dataStructure['table_name'];

		$query=$this->_makeSaveQuery(false,true); // We DON'T want to force specifying all fields on inserts

		if (!$query)
			// **TODO** Handle this in fillDataStructure -- yeah, but how?
			throw new BadMethodCallException("Malformed object data structure!");

		if (!$this->query("INSERT INTO ".$table." ".$query))
			return false;

		if (!$withID) {
			$this->id = $this->db->insert_id();
			if (!$this->id)
				throw new RangeException("Failed retrieving LAST INSERT ID!");
		}

		foreach($this->dataStructure['fields'] as $attName=>$dataEntry)
			$this->attr_flags[$attName]['modified']=false;

		$this->modified=false;

		// **TODO** find out what loggableChange does
		$this->loggableChange=true;
		$this->log('insert');

		$this->internal_onSave(true);
		return $this->id;
	}
	// }}}
	// {{{ insertWithId()
	/**
	* TODO: this is prone to race conditions!
	*/
	function insertWithId($id=NULL,$force=false)
	{
		if (!isset($id))
			$id=$this->id;
		if (!isset($id))
			throw new RuntimeException("LPC::insertWithId requires either an explicit or an implicit ID -- neither was provided!");

		if (
			$this->probe($id) && (
				!$force ||
				!$this->delete($id)
			)
		)
			return false;

		$id_fld=$this->dataStructure['id_field'];

		// Set fake attribute for the field
		$this->attr[$id_fld]=array();
		$this->attr_flags[$id_fld]=array();
		$this->dataStructure['fields'][$id_fld]=array('fld_name'=>$id_fld,'flags'=>array());
		$this->setAttr($id_fld,$id);

		// Do the insert
		$result=$this->insert(true);

		// Unset all fake attributes
		unset(
			$this->attr[$id_fld],
			$this->attr_flags[$id_fld],
			$this->dataStructure['fields'][$id_fld]
		);
		return $result;
	}
	// }}}
	// {{{ beforeDelete()
	/**
	 * This method gets to run BEFORE even trying to delete object with id $id.
	 *
	 * Please note that's no guarantee that the delete will actually succeed,
	 * so make sure you correlate whatever you do here with the onDelete() call
	 * which gets executed after a successful removal.
	 * Should be overridden by SC descendants.
	 * @return boolean true to proceed deleting or false to abort
	 */
	protected function beforeDelete($id)
	{
		return true;
	}
	// }}}
	// {{{ onDelete()
	/**
	 * This method gets executed immediately AFTER successfully deleting
	 * object with id $id and all its dependencies.
	 * Should be overridden by SC descendants.
	 * Since you won't have much info about that object and its dependencies
	 * after they've been deleted, you may also want to overwrite
	 * beforeDelete().
	 */
	protected function onDelete($id)
	{
	}
	// }}}
	// {{{ getAllObjects()
	/**
	 * This method returns all objects from all dependencies associated
	 * with this object. This method is NOT recursive, it will only return
	 * *this* object's dependencies.
	 *
	 * @param string $depType must be one of the dependency types (e.g. 'WIFE')
	 *               or '*' for all dependencies
	 * @return indexed array of associative array of the following form:
	 *   'dep'    => the dependency name
	 *   'object' => the instantiated object
	 */
	function getAllObjects($depType='WIFE')
	{
		$myClass=get_class($this);
		$objects=array();
		$deps=$this->dataStructure['depend'];
		foreach($deps as $depName=>$dep) {
			if ($depType!='*' && $dep['on_mod']!=$depType)
				continue;

// This breaks many to many dependencies, because we don't (yet) support
// ordering for those. Don't know what "in case they need to be compared"
// might mean in a practical setp.
//			if ($tmp=$this->getObjects($depName,0)) // order by ID, in case they need to be compared
			if ($tmp=$this->getObjects($depName))
				foreach($tmp as $tmp_object)
					$objects[]=array('dep'=>$depName,'object'=>$tmp_object);
			elseif ($tmp===false)
				return false;
		}
		return $objects;
	}
	// }}}
	// {{{ delete()
	/**
	 * Delete object with specified id, or current object if no id specified
	 *
	 * Also deletes object dependencies, as specified by $this->dataStructure
	 * @param integer $id if set, the object with the respective id will be deleted
	 * @param string $purpose may be one of
	 *   'regular' => a regular delete is performed
	 *   'archive' => an archiving delete is performed, which means two things:
	 *     1. beforeDelete() and afterDelete() are not called
	 *     2. the logs are also deleted
	 * @param array $stack internal variable to keep track of dependencies and avoid cycles
	 * @return boolean true on success, false on error or
	 *   NULL if {@link internal_beforeDelete} aborts deleting.
	 */
	function delete($id=0, $purpose='regular', &$stack=array())
	{
		$victim=&$this->defaultObject($id);
		$tmp=get_class($this).'#'.$victim->id;
		$stack[]=$tmp;
		// We're inheriting the noLogging property from the current object to
		// the victim... (see (1) below)
		$victim->noLogging=$this->noLogging;
		$id=$victim->id;

		if (!$id)
			throw new BadMethodCallException("ID for deletion unspecified!");

		if (($purpose!='archive') && ($victim->internal_beforeDelete($id)!==true))
			return NULL;

		$stack=$victim->deleteDependencies($purpose,$stack);
		if ($stack===false)
			return false;

		$id_fld=$victim->dataStructure['id_field'];
		$table=$victim->dataStructure['table_name'];
		$id_formatted = $this->db->qstr($id);
		$victim->query("DELETE FROM ".$table." WHERE ".$victim->dataStructure['id_field']."=".$id_formatted);
		if ($purpose!='archive') {
			$victim->loggableChange=true;
			$victim->log('delete');
			$victim->internal_onDelete($id);
		}
		return true;
	}
	// }}}
	// {{{ deleteDependencies()
	/**
	 * Deletes this object's dependencies (sets MISTRESSes to 0 and deletes WIFEs)
	 *
	 * @return boolean true on success, false on failure
	 */
	function deleteDependencies($purpose='regular', $stack=array())
	{
		$mistresses=$this->getAllObjects('MISTRESS');
		foreach($mistresses as $mistress)
			$this->dropLink($mistress['dep'],$mistress['object']);
		
		$wives=$this->getAllObjects();
		foreach($wives as $wife) {
			$wife['object']->noLogging=$this->noLogging;
			$tmp=get_class($wife['object']).'#'.$wife['object']->id;
			if (@in_array($tmp, $stack))
				continue;
			if (($purpose=='archive') || ($wife['dep']!='lfx_log')) {
				if (!$wife['object']->delete(0, $purpose, $stack))
					return false;
				else
					$stack[]=$tmp;
			}
		}
		return $stack;
	}
	// }}}
	// {{{ getObject()
	/**
	 * Returns an attribute as an object
	 *
	 * For attributes which are defined as links in $this->dataStructure,
	 * this method returns an instantiated object representing the respective
	 * attribute.
	 * @param string $att_name the attribute associated with the object to return
	 * @return object the instantiated object representing the attribute or false on error
	 */
	function getObject($attName)
	{
		$obj_type=$this->dataStructure['fields'][$attName]['link_class'];
		if (!$obj_type) {
			if ($flex=$this->getFlexObject($attName))
				return $flex;

			throw new InvalidArgumentException("Link $attName not available in attribute list!");
		}
		$obj_id=$this->getAttr($attName);
		if (!$obj_id)
			return NULL;

		return new $obj_type($obj_id);
	}
	// }}}
	// {{{ getFlexObject()
	/**
	 * This method returns an instantiated object based on the value of the specified attribute.
	 * The attribute MUST contain an object specifier of the form "<object class>#<object id>"
	 * (e.g. "DMO_Instance#311").
	 *
	 * @param string $attName the name of the attribute to retrieve and use
	 * @return mixed the instantiated object on success, NULL if object not available or false on error.
	 */
	function getFlexObject($attName)
	{
		$att_val=$this->getAttr($attName);
		if (!$att_val || is_numeric($att_val))
			return NULL;

		if (!preg_match("/^([a-zA-Z_0-9]+)#([0-9]+)$/",$att_val,$matches))
			return NULL;

		return new $matches[1]($matches[2]);
	}
	// }}}
	// {{{ getLinks()
	/**
	 * Returns the specified dependency list as object IDs
	 *
	 * @param string $dep_name the dependency to return the list of
	 * @param string $order_att the attribute to sort the list by
	 * @param boolean $reverse if true, the list will be reversed
	 * @param boolean $countOnly if true, only the number of dependencies is returned
	 * @param mixed $id the ID of "this" object
	 * @return mixed array of instantiated objects, integer if $count_only is true, or false on error
	 */
	function getLinks($dep_name, $order_att=NULL, $reverse=false, $count_only=false, $id='')
	{
		$query=$this->_makeGetLinksQuery($dep_name, $order_att, $reverse, $count_only, $id);

		$dbKey=$this->dbKey;
		if ($this->dataStructure['depend'][$dep_name]['type']=='many')
			$dbKey=$this->dataStructure['depend'][$dep_name]['dbKey'];
		$db=$this->_doDbInit($dbKey);
		$queryObj=new LPC_Query_builder();
		$sql=$queryObj->buildSQL($query);
		$rs=$db->query($sql);
		if (!$rs)
			throw new RuntimeException("Database error #{$db->ErrorNo()}: {$db->ErrorMsg()}; Query: \"$sql\"");
		if ($count_only)
			return $rs->fields[0];

		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}
	// }}}
	// {{{ getObjectsCount()
	function getObjectsCount($depName)
	{
		return $this->getLinks($depName,NULL,false,true);
	}
	// }}}
	// {{{ getObjects()
	/**
	 * Returns the specified dependency list as objects
	 *
	 * This method uses SC::getLinks() to retrieve the object IDs and
	 * SC::instantiate() to actually instantiate them.
	 *
	 * @param string $dep_name the dependency to return the list of
	 * @param string $order_att the attribute to sort the list by
	 * @param boolean $reverse if true, the list will be reversed
	 * @return array array of instantiated objects or false on error
	 */
	function getObjects($dep_name, $order_att=NULL, $reverse=false)
	{
		$class=$this->dataStructure['depend'][$dep_name]['class'];
		if (!$class)
			throw new BadMethodCallException(
				"Dependency '$dep_name' search request on ".
				"object '".get_class($this)."' which doesn't ".
				"have dependency [class] defined in ".
				"structure! (in SC::getObjects())"
			);

		$links=$this->getLinks($dep_name, $order_att, $reverse);
		$instantiated=$this->instantiate($links,$class);
		return $instantiated;
	}
	// }}}
	// {{{ getLoadedObjects()
	// **TODO**
	/**
	 * Returns the specified dependency list as objects, and also loads
	 * them. This method should be typically used instead of {@link getObjects},
	 * unless you really only need the instantiated objects with IDs
	 * (e.g. if you only need to walk a tree).
	 * This method is considerably faster than doing a getObjects() followed
	 * by load() or getAttr() on each object, because this executes a single
	 * query, as opposed to getObjects() + load() which executes as many
	 * queries as there are objects to load, plus one (for getObjects()).
	 *
	 * This method uses SC::getLinks() to retrieve the object IDs and
	 * SC::instantiate() to actually instantiate them.
	 *
	 * @param string $dep_name the dependency to return the list of
	 * @param string $order_att the attribute to sort the list by
	 * @param boolean $reverse if true, the list will be reversed
	 * @return array array of instantiated objects or false on error
	 */
	function getLoadedObjects($dep_name, $order_att=NULL, $reverse=false)
	{
		if (empty($this->dataStructure['depend'][$dep_name]))
			throw new BadMethodCallException("Loaded objects requested on undefined dependency \"$dep_name\".");

		$class=$this->dataStructure['depend'][$dep_name]['class'];
		$links=$this->getLinks($dep_name, $order_att, $reverse);
		if (!$links)
			return array();

		$tmp=new $class;
		return $tmp->_doLoad($links);
	}
	// }}}
	// {{{ defaultID()
	/**
	* Returns the id specified, if specified, or the current object's id.
	* Also see {@link defaultObject}().
	*/
	function defaultID($id=0)
	{
		if ($id==0)
			return $this->id;
		return $id;
	}
	// }}}
	// {{{ defaultObject()
	/**
	* Returns the object specified by the respective id, or a reference
	* to the current object if $id==0. We're using this convention so
	* often within SC descendats that a method to do it was called for.
	* Also see {@link defaultID}().
	* @param integer id the id to use
	* @return reference a reference to the object with id $id or to $this if $id==0
	*/
	function defaultObject($id=0)
	{
		if (($id==0) || ($id==$this->id))
			return $this;
		$myClass=get_class($this);
		return new $myClass($id);
	}
	// }}}
	// {{{ defaultProject()
	/**
	* Returns the project specified by the respective id, or
	* a reference to the current project if $project==0.
	* Works with LPC_Project objects too.
	* @param mixed $project the id to use
	* @return reference a reference to the project object
	*/
	function defaultProject($project=0)
	{
		if ($project===0) {
			if ($proj=LPC_Project::getCurrent(true))
				return $proj;
			return new LPC_No_project();
		}
		if (is_numeric($project)) {
			$pc=LPC_project_class;
			return new $pc($project);
		}
		if (is_object($project)) {
			if (!is_a($project,"LPC_Project"))
				throw new InvalidArgumentException("Unknown class ".get_class($project));

			return $project;
		}
		throw new InvalidArgumentException("Unknown entity type: ".gettype($project));
	}
	// }}}
	// {{{ createLink()
	/**
	 * Creates a link
	 *
	 * This creates a new entry in the join table for a many to many
	 * dependency. See documentation on how to set up a many to many
	 * dependency.
	 * Also creates a link in a one to many dependency, if that is required.
	 * Note: if link already exists, it will behave as if it just created it.
	 * @param string $dep_name the many to many dependency to use
	 * @param mixed $link the numeric id or instantiated object to link to
	 * @return boolean true on success or false on error
	 */
	function createLink($dep_name, $link, $justCheck=false)
	{
		if (empty($this->dataStructure['depend'][$dep_name]))
			throw new InvalidArgumentException("Link creation requested for undefined dependency \"$dep_name\".");

		$dep=$this->dataStructure['depend'][$dep_name];
		if (!$this->id)
			throw new BadMethodCallException('Link creation requested on object with ID not set!');

		$link_class=$dep['class'];
		if (is_object($link)) {
			if (!is_subclass_of($link,'LPC_Object'))
				throw new InvalidArgumentException("Link creation requested to a \"$link_class\" object - but that's not a descendant of LPC_Object!");

			if (strtolower($link_class)!=strtolower($dep['class']))
				throw new InvalidArgumentException("Link creation requested to a $link_class object - but the dependency $dep_name refers to {$dep['class']} objects! (in SC::createLink())");

			$link_id=$link->id;
		} else
			$link_id=$link;
		if (!$link_id)
			throw new InvalidArgumentException("Link creation requested but couldn't determine link id! (in SC::createLink())");

		// Finally finished checking for errors!
		if ($dep['type']=='many') {
			$db=&$this->_doDbInit($dep['dbKey']);
			$join_table=$dep['table_name'];
			$my_fld=$dep['my_fld_name'];
			$link_fld=$dep['link_fld_name'];

			$Smy_id = $this->db->qstr($this->id);
			$Slink_id = $this->db->qstr($link_id);

			$sql = "SELECT COUNT(*) FROM ".$join_table." WHERE ".$my_fld."=".$Smy_id." AND ".$link_fld."=".$Slink_id;
			$rs=$db->query($sql);
			if ($justCheck) {
				return (bool) $rs->fields[0];
			}
			if ($rs->fields[0]) {
				// If link already exists, we will NOT issue any error - just exit as if we created it
				// If you need to know if that link exists, just check for it yourself!
				return true;
			}
			$sql = "INSERT INTO ".$join_table." (".$my_fld.", ".$link_fld.") VALUES (".$Smy_id.", ".$Slink_id.")";
			if ($db->query($sql)) {
				return true;
			} else {
				return false;
			}
		} else {
			$depObject=&new $link_class($link_id);
			if ($justCheck)
				return $dep['attr']==$this->id;

			if ($depObject->getAttr($dep['attr'])!=$this->id) {
				$depObject->setAttr($dep['attr'],$this->id);
				return $depObject->save();
			} else
				return true;
		}
	}
	// }}}
	// {{{ isLink()
	function isLink($dep_name, $link)
	{
		return $this->createLink($dep_name,$link,true);
	}
	// }}}
	// {{{ dropLink()
	/**
	 * Drops (deletes) a many to many dependency
	 *
	 * Note: if link doesn't exist, it will behave as if it just deleted it.
	 * See {@link createLink} for how to create one.
	 * @param string $dep_name the dependency to use
	 * @param mixed $link numerical id or instantiated object to drop the link to
	 * @return boolean true on success or false on error
	 */
	function dropLink($dep_name, $link)
	{
		if (empty($this->dataStructure['depend'][$dep_name])) {
			throw new InvalidArgumentException("Link drop requested for undefined dependency \"$dep_name\".");
		}
		$dep=$this->dataStructure['depend'][$dep_name];
		if (!$this->id) {
			throw new BadMethodCallException('Link drop requested on object with id not set!');
		}
		$link_class=$dep['class'];
		if (is_numeric($link))
			$link_id=$link;
		else {
			if (!is_subclass_of($link,'LPC_Object')) {
				// This MUST NOT be tested in poreDataStructure(), because that
				// would involve loading all related classes, often unnecessarily.
				throw new DomainException("Link drop requested to a \"$link_class\" object, but that's not a descendant of LPC_Object.");
			}
			if (strtolower($link_class)!=strtolower($dep['class'])) {
				throw new DomainException("Link drop requested to a \"$link_class\" object - but the dependency \"$dep_name\" refers to \"{$dep['class']}\" objects!");
			}
			$link_id=$link->id;
		}
		if (!$link_id)
			throw new InvalidArgumentException("Link drop requested but couldn't determine the link ID!");

		// Finally finished checking for errors!
		if ($dep['type']=='many') {
			$db=&$this->_doDbInit($dep['dbKey']);
			$join_table=$dep['table_name'];
			$my_fld=$dep['my_fld_name'];
			$link_fld=$dep['link_fld_name'];
			return $db->query(
				"DELETE FROM ".$join_table." WHERE ".
				$my_fld."=".$this->db->qstr($this->id)." AND ".
				$link_fld."=".$this->db->qstr($link_id)
			);
		} else {
			$depObject=&new $link_class($link_id);
			if ($depObject->getAttr($dep['attr'])==0)
				return true;

			$depObject->setAttr($dep['attr'],0);
			// We don't want to log such BS
			$depObject->noLogging=true;
			return $depObject->save();
		}
	}
	// }}}
	// {{{ dropLinks()
	/**
	 * Drops ALL many-to-many links of this object associated with the
	 * specified dependency.
	 * No information on the number of deleted links is returned;
	 * even if no links are deleted, this method will return success
	 * if it suceeded in performing the query, doesn't care about the
	 * actual deleted links. This is done for performance reasons;
	 * should you need that information use SC::getObjects() or
	 * SC::getLinks() and SC::dropLink().
	 *
	 * @param string $dep_name the dependency to delete the links for
	 * @return boolean true on success or false on failure
	 */
	function dropLinks($dep_name)
	{
		if (empty($this->dataStructure['depend'][$dep_name]))
			throw new InvalidArgumentException("Link drop requested for undefined dependency \"$dep_name\".");

		$dep=$this->dataStructure['depend'][$dep_name];
		if ($dep['type']!='many')
			throw new InvalidArgumentException("Link drop requested on one to one dependency \"$dep_name\" - use attributes for that!");

		if (!$this->id)
			throw new BadMethodCallException('Link drop requested on object with id not set!');

		// Finally finished checking for errors!
		$dep['dbKey']=$this->dbKey;

		$db=&$this->_doDbInit($dep['dbKey']);
		$join_table=$dep['table_name'];
		$my_fld=$dep['my_fld_name'];
		if ($db->query("DELETE FROM ".$join_table." WHERE ".$my_fld."=".$this->db->qstr($this->id)))
			return true;
		return false;
	}
	// }}}
	// {{{ getAttrH()
	function getAttrH($attName)
	{
		return htmlspecialchars($this->getAttr($attName),ENT_QUOTES);
	}
	// }}}
	// {{{ hasAttr()
	function hasAttr($attName)
	{
		return isset($this->dataStructure['fields'][$attName]);
	}
	// }}}
	// {{{ getAttrF()
	function getAttrF($attName)
	{
		return addslashes($this->getAttr($attName));
	}
	// }}}
	// {{{ getAttr() 
	/**
	 * Returns the 'real' value of the requested attribute
	 *
	 * This will not attempt to do anything on the attribute but load
	 * the object if the attribute is not set.
	 * Note: See $this->dataStructure flags for how dates and NULL values
	 * are managed.
	 *
	 * @param string $attName the attribute to return
	 * @return string the value of the attribute or false on error
	 */
	function getAttr($attName)
	{
		if (!$attName)
			throw new InvalidArgumentException("Please specify a non-empty attribute name!");

		if (!$this->hasAttr($attName))
			return $this->getI18nAttr($attName);

		if (
			$this->attr_flags[$attName]['loaded'] ||
			$this->attr_flags[$attName]['modified']
		)
			return $this->attr[$attName];
		elseif (!$this->id)
			return NULL;

		// We DON'T want to force overloading attributes which were already set.
		$this->load(0,false);
		return $this->attr[$attName];
	}
	// }}}
	// {{{ isModified()
	/**
	 * This method allows you to find out if an attribute has been modified
	 * since the object has been loaded from the database. This only works
	 * if you alter the value of attributes via {@link setAttr} and
	 * {@link setAttrs}; if you make direct modifications in the data structure
	 * then those changes won't be caught by this mechanism.
	 *
	 * @param string $attName the attribute name to return the modified status for;
	 * if not specified, it will return this object's modified status
	 * @return boolean true if modified, false or NULL otherwise
	 */
	function isModified($attName=NULL)
	{
		if ($attName===NULL)
			return $this->modified;

		return $this->attr_flags[$att_name]['modified'];
	}
	// }}}
	// {{{ isLoaded()
	/**
	 * This method allows you to find out if this object or one of its attributes
	 * was loaded from the database. Note that this remains set even if the attribute
	 * is overwritten using {@link setAttr} or {@link setAttrs}, as long as
	 * the attribute's value has been retrieved from the database.
	 *
	 * @param string $attName the attribute name to return the loaded status for;
	 * 	if not specified, it returns the status of the object as a whole
	 * @return boolean true if modified, false or NULL otherwise
	 */
	function isLoaded($attName=false)
	{
		if ($attName===false)
			return $this->loaded;

		return $this->attr_flags[$attName]['loaded'];
	}
	// }}}
	// {{{ touchAttr()
	/**
	 * This method touches one object attribute so it's saved at the next
	 * object save. By default, SC only saves the attributes edited with
	 * {@link setAttr}; this method fakes a change in the attribute value.
	 * @param string $attName the name of the attribute to touch
	 * @return boolean true on success.
	 */
	function touchAttr($attName)
	{
		$this->attr_flags[$attName]['modified']=true;
		$this->modified=true;
		return true;
	}
	// }}}
	// {{{ touchAllAttrs()
	/**
	 * This method walks all attributes and touches them all. Please note that
	 * you must make sure yourself that all attributes are loaded properly,
	 * otherwise good values will be deleted from the database when saving.
	 */
	function touchAllAttrs()
	{
		foreach($this->attr as $key=>$value)
			$this->touchAttr($key);
		return true;
	}
	// }}}
	// {{{ setAttr()
	/**
	 * Sets the value of the specified attribute
	 *
	 * @param string $attName the attribute to assign a value to
	 * @param mixed $attValue the value to assign it. Valid values
	 *   are strings, numeric values and the PHP NULL
	 * @return boolean true on success or false on error.
	 */
	function setAttr($attName, $attValue)
	{
		if (!$this->hasAttr($attName))
			return $this->setI18nAttr($attName,$attValue);

		if (!empty($this->dataStructure['fields'][$attName]['flags']['trim']))
			$attValue = trim($attValue);

		if (!isset($this->attr[$attName]) || ($this->attr[$attName]!==$attValue)) {
			$this->modified=true;
			$this->attr[$attName]=$attValue;
			$this->attr_flags[$attName]['modified']=true;
		}
		return true;
	}
	// }}}
	// {{{ setAttrs()
	/**
	 * Sets a set of attributes of this object based on an
	 * associative array.
	 *
	 * The functionality is simple: just pass an associative
	 * array to this method where the keys represent SC attribute
	 * names and those attributes will be set to the respective
	 * values in the array.
	 *
	 * This method does *NOT* also save your object - it just sets all
	 * the attributes in the array, just as if you'd set each at a
	 * time using {@link setAttr}.
	 *
	 * @param array $attrs associative array containing the attribute
	 *   names and values to be set
	 * @return boolean true on success or throws exception on error.
	 */
	function setAttrs($attrs)
	{
		if (!is_array($attrs))
			throw new InvalidArgumentException('Expecting an array.');

		foreach($attrs as $attName=>$attVal)
			$this->setAttr($attName,$attVal);

		return true;
	}
	// }}}
	// {{{ instantiate()
	// **TODO** -- rethink all of this madness
	// -- it's only called without the second parameter or with a string as the second parameter; the first is either an ID or an array; that's probably all the functionality we ever need, anyway
	/**
	 * This method instantiates objects based on the parameters it receives.
	 * Here are the possible parameter combinations:
	 * $ids (read it as "IDs"):
	 *   - integer: will return one object instantiated as defined by $class.
	 *     Should you need an array of one object, pass an indexed array of one id.
	 *   - array: will return an array of objects instantiated as
	 *     defined by $class. Will observe keys.
	 *   - anything else: false (error)
	 * $class:
	 *   - missing: will use the current object's class to instantiate (all) the object(s)
	 *   - string: will use the specified class to instantiate (all) the object(s)
	 *   - indexed array: will use one array entry for each element in $ids. If $ids is
	 *     integer, will only use the first entry in $class. If count($ids) is smaller
	 *     than count($class), will only use as many elements for $class as needed.
	 *     If count($ids) is larger than count($class), will use $wrap to determine
	 *     what to do. Please note this will NOT observe keys - it will expect $class
	 *     to be an indexed array with no holes.
	 *   - anything else: false (error)
	 * $wrap:
	 *   - missing or evaluating false: will use the last enty in $class for all
	 *     remaining elements in $ids (see above)
	 *   - evaluating true: will wrap $class around $ids (you know what I mean)
	 * @param mixed $ids the ID(s) to instantiate
	 * @param mixed $class the class to instantiate
	 * @param boolean true to wrap, false to copy
	 * @return array array of instantiated objects or false on error
	 */
	function instantiate($ids, $class=NULL, $wrap=false)
	{
		if ($class===NULL)
			$class=get_class($this);
		if (is_numeric($ids)) {
			if (is_array($class))
				$class=$class[0];
			return new $class($ids);
		}
		if (!is_array($ids) || empty($ids))
			return array();
		$idx=0;
		$result=array();
		foreach($ids as $key=>$id) {
			if (!is_array($class))
				$myClass=$class;
			else {
				if ($idx==count($class)) {
					if ($wrap)
						$idx=0;
					else
						$idx--;
				}
				if (!isset($class[$idx]) || !$class[$idx] || !class_exists($class[$idx]))
					throw new RuntimeException("Improperly formatted class array, or missing class (at index $idx)");
				$myClass=$class[$idx];
			}
			$result[$key]=&new $myClass($id);
			$idx++;
		}
		return $result;
	}
	// }}}
	// {{{ searchCount()
	/**
	 * Returns the number of results of a search.
	 *
	 * This method is in no way related to {@link search} except in parameters
	 * identity. Apart from that, the results are not actually retrieved - only
	 * an SQL COUNT(*) is issued in order to minimize response time. If you need
	 * both the objects count and the objects themselves, it is much more preferable
	 * to issue a {@link search} or {@link searchId} and count the resulting array
	 * because no form of caching is performed if you first issue a searchCount() and
	 * then a search().
	 *
	 * See the documentation of {@link search} for detailed parameter info
	 * @return integer number of records on success or false on error
	 */
	function searchCount($attName=NULL, $attValue=NULL, $like=false)
	{
		$query=$this->_makeSearchCountQuery($attName,$attValue,$like);
		$rs=$this->query($query);
		return $rs->fields[0];
	}
	// }}}
	// {{{ searchId()
	/**
	 * Returns the result of a search as object IDs
	 *
	 * See the documentation of {@link search} for detailed parameter info
	 * @return array array of id's on success or false on error
	 */
	function searchId($attName=NULL, $attValue=NULL, $order_att=NULL, $reverse=false, $like=false)
	{
		$query=$this->_makeSearchIdQuery($attName,$attValue,$order_att,$reverse,$like);
		$rs=$this->query($query);
		$result=array();
		while(!$rs->EOF) {
			$result[]=$rs->fields[0];
			$rs->MoveNext();
		}
		return $result;
	}
	// }}}
	// {{{ search()
	/**
	 * Returns the result of a search as instantiated objects
	 *
	 * If att_name is a string then att_value must be a string.
	 * If att_name is an array then att_value must be an array
	 * with the same number of elements. If they are arrays, then
	 * an AND search is performed for all the elements in the arrays.
	 * For instance, if $attName=array('children', 'grandchildren')
	 * and $attValue=array(33, 44) then search() will return all objects
	 * with the *attribute* 'children' equal to 33 *AND* attribute
	 * 'grandchildren' equal to 44. If both are arrays AND $like is true
	 * then all comparisons are made with 'LIKE' instead of "=".
	 *
	 * Note: if no objects are found, it returns an empty array,
	 * so make sure you use identity operators to check for errors.
	 * Note: This method uses {@link searchId} to search and
	 * {@link instantiate} to instantiate - it's a one-liner. Just
	 * so you know.
	 *
	 * @param mixed $attName the attribute(s) to search for
	 * @param mixed $attValue the value(s) to match the attribute to
	 * @param string $order_att the attribute to sort the result by;
	 *   0 in order to sort by id; NULL or unspecified to return unsorted
	 * @param boolean $reverse if true, the result will be ordered in reverse
	 * @param boolean $like if true, the attribute will be compared to
	 *   $attValue via LIKE
	 * @return array array of objects on success or false on error
	 */
	function search($attName=NULL, $attValue=NULL, $order_att=NULL, $reverse=false, $like=false)
	{
		$ids=$this->searchId($attName, $attValue, $order_att, $reverse, $like);
		if ($ids===false)
			return false;
		if (!$ids)
			return array();

		$ins=$this->instantiate($ids);
		for($i=0;$i<count($ins);$i++)
			$ins[$i]->onLoad(false);

		return $ins;
	}
	// }}}
	// {{{ fromKey()
	/**
	* Instantiates an object based on a key in an array.
	*
	* Useful for instantiating objects based on GET or POST
	* requests. It manages everything, from key availability
	* to key validity (strictly positive integer) and also
	* probes for the object.
	*
	* Please be advised this method only sets the ID of this
	* object, and can't be executed statically. If you're reusing
	* objects, you need to clean them up yourself, if that's
	* what you need.
	*
	* @return integer the new ID
	*/
	function fromKey($array,$key)
	{
		$this->id=0;
		if (!isset($array[$key]))
			return $this->id;

		$this->id=(int) abs($array[$key]);
		if ($this->probe())
			return $this->id;

		$this->id=0;
		return $this->id;
	}
	// }}}
	//{{{ idFromArrayKey()
	/**
	* Deprecated alias for {@link fromKey()}
	*/
	function idFromArrayKey($array,$key)
	{
		return $this->fromKey($array,$key);
	}
	// }}}
	// {{{ isValidFile()
	/**
	* Checks whether this object contains any valid file entries
	* named $fileName
	* @teturn boolean true if the file exists and is valid, false otherwise
	*/
	function isValidFile($fileName)
	{
		if (
			empty($this->dataStructure['files']) ||
			empty($this->dataStructure['files'][$fileName]) ||
			empty($this->dataStructure['files'][$fileName]['content'])
		)
			return false;

		return true;
	}
	// }}}
	// {{{ isPopulatedFile()
	/**
	* Checks whether this object's file entry named $fileName
	* has been filled in with data.
	* @return mixed false if invalid, the length of the file content otherwise
	*/
	function isPopulatedFile($fileName)
	{
		if (!$this->isValidFile($fileName))
			return false;
		return strlen($this->getAttr($this->dataStructure['files'][$fileName]['content']));
	}
	// }}}
// }}}
// {{{ LISTING METHODS
	// ----------------------------------
	//        LISTING METHODS
	// ----------------------------------
	// {{{ getName()
	function getName()
	{
		if (empty($this->dataStructure['title_attr']))
			return false;

		if ($name=$this->getAttr($this->dataStructure['title_attr']))
			return $name;

		return _LS('genericNoName');
	}
	// }}}
	// {{{ getNameH()
	/**
	 * Returns the HTML representation of this object - implemented by descendants
	 *
	 * PLEASE EITHER override this to return a valid HTML representation
	 * OR use the method described in the second paragraph below for all your
	 * objects.
	 *
	 * The HTML representation should be a string which you feel is reasonably
	 * representative for this object; for instance it could be
	 * $this->getAttrH('fname').' '.$this->getAttrH('lname') for a LFX_User, or
	 * '#'.$this->id.' '.$this->getAttrH('title') for an OPT_Request, or simply
	 * $this->getAttr('name') for a DMO_Instance.
	 *
	 * If you use a single attribute as representation of this object,
	 * linke in the last example above,
	 * then you can specify it as the "title_attr" entry in the data structure
	 * array you pass in the class constructor; if you do that, then you can
	 * skip overriding this method and it will simply return
	 * $this->getAttrH(<the attribute you specified>).
	 *
	 * @return string HTML representation of this object.
	 */
	function getNameH()
	{
		if (empty($this->dataStructure['title_attr']))
			return false;

		if ($name=$this->getAttrH($this->dataStructure['title_attr']))
			return $name;

		return _LS('genericNoName');
	}
	// }}}
	// {{{ getUrlH()
	/**
	 * Should return the inter-page URL to the current object's details page;
	 * implemented by descendants.
	 *
	 * "Inter-page" URL means that the resulting string must contain
	 * an URL stripped of its protocol and document root part, as in
	 * "/opt/documents" instead of "https://opt.lanifex.com/opt/documents".
	 *
	 * The aim of this method is to allow SC::{@link getLinkH}() to build
	 * its result using SC::{@link getNameH()} without having to override
	 * getLinkH().
	 *
	 * You can avoid overriding this method and SC::{@link getFullUrlH}()
	 * by providing an absolute sprintf-compatible path to the current object's page
	 * starting from the document root in this object's "object_page" entry
	 * in the dataStructure array
	 * (e.g. $this->dataStructure["object_page"]="/projects/project_details.php?id=%i").
	 *
	 * See also SC::{@link getFullLinkH}()
	 *
	 * @return string the sprintf-compatible HTML link to the current object
	 */
	function getUrlH()
	{
		if (!$this->id) {
			return '';
		}
		if ($local=$this->dataStructure['object_page']) {
			return LPC_url.sprintf($local,$this->id);
		}
		return false;
	}
	// }}}
	// {{{ getFullUrlH()
	/**
	 * Identical in every other way with SC::{@link getUrlH}(), this method
	 * should return a fully-qualified URI to the current object's details
	 * page, including the protocol and the host name.
	 *
	 * The aim of this method is to be used by SC::{@link getFullLinkH}()
	 * in order to provide the proper full-fledged URI for use in e-mail
	 * notifications.
	 *
	 * See SC::{@link getUrlH}() for more documentation
	 *
	 * @return string URI to the current object
	 */
	function getFullUrlH()
	{
		if (!$this->id) {
			return '';
		}
		if ($local=$this->dataStructure['object_page']) {
			return LPC_full_url.sprintf($local,$this->id);
		}
		return false;
	}
	// }}}
	// {{{ getLinkH()
	/**
	 * Returns an HTML link to this object - implemented by descendants
	 *
	 * PLEASE EITHER override this to return a valid HTML link to the details
	 * page for this object OR properly implement SC::{@link getNameH}() AND
	 * SC::{getUrlH}() for all your classes!
	 * (The second approach is recommended.)
	 *
	 * @return string HTML link to the details page
	 * (e.g. "<a href='/home/httpd/EH-Master/<path>?id=33><Object human representation></a>
	 * where <object human representation> would be the name of a sensor, the title of a document etc).
	 */
	function getLinkH()
	{
		if (($name=$this->getNameH()) && ($url=$this->getUrlH())) {
			return sprintf('<A HREF="%1$s">%2$s</A>',$url,$name);
		} elseif ($name) {
			// if I have a name, but I don't have an URL
			return $name;
		}
		return false;
	}
	// }}}
	// {{{ getFullLinkH()
	/**
	 * Returns the full link to the current object's representation.
	 * Please see the documentation for SC::{@link getFullUrlH}() for a
	 * wider context and more information on this method.
	 *
	 * @return string
	 */
	function getFullLinkH()
	{
		if (($name=$this->getNameH()) && ($url=$this->getFullUrlH())) {
			return sprintf('<A HREF="%1$s">%2$s</A>',$url,$name);
		}
		return false;
	}
	// }}}
	// {{{ getListLinkH()
	/**
	 * Descendants should override this to return an HTML link to the list detailed by the dependency
	 *
	 * @return string HTML link
	 */
	function getListLinkH($dep_name, $order_att=NULL, $reverse=false)
	{
		throw new BadMethodCallException('Method getListLinkH() must be defined by descendant!');
	}
	// }}}
	// {{{ prepareAttrH()
	/**
	 * Descendats should override this to manage attribute representation in the list
	 *
	 * @param string $attName the name of the attribute to represent
	 * @param string $attVal the value as it would be shown
	 * @return string HTML to represent this attribute
	 */
	function prepareAttrH($attName, $attVal)
	{
		return $attVal;
	}
	// }}}
	// {{{ getAttrName()
	function getAttrName($attName)
	{
		if (!empty($this->dataStructure['fields'][$attName]['attr_name']))
			return $this->dataStructure['fields'][$attName]['attr_name'];

		return $attName;
	}
	// }}}
	// {{{ getAttrNameH()
	function getAttrNameH($attName)
	{
		return htmlspecialchars($this->getAttrName($attName));
	}
	// }}}
	// {{{ getAttrDesc()
	function getAttrDesc($attName)
	{
		if (!empty($this->dataStructure['fields'][$attName]['attr_desc']))
			return $this->dataStructure['fields'][$attName]['attr_desc'];

		return '';
	}
	// }}}
	// {{{ getAttrDescH()
	function getAttrDescH($attName)
	{
		return htmlspecialchars($this->getAttrDesc($attName));
	}
	// }}}
// }}}
// {{{ TREE METHODS
	// ----------------------------------
	//          TREE METHODS
	// ----------------------------------
	// {{{ getRootId()
	/**
	 * Returns the root id of the tree in which this object resides
	 *
	 * Note: depends on proper definition of $this->dataStructure to determine
	 * which attribute defines the tree structure.
	 * Note: $this->rootId is used to cache the root ID. If you need re-reading
	 * the whole tree, use force! :)
	 *
	 * @param boolean $force if true, forces reading the tree instead of using the cache
	 * @return integer numerical root ID or false on error
	 */
	function getRootId($force=false)
	{
		if (!$force && $this->rootId)
			return $this->rootId;

		$tmp=$this->getParent();
		if ($tmp===false) {
			$this->debug('SC::getRootId() called SC::getParent() which failed');
			return false;
		}
		if ($tmp===NULL) {
			$this->rootId=$this->id;
			return $this->id;
		}
		$this->rootId=$tmp->getRootID();
		return $this->rootId;
	}
	// }}}
	// {{{ getRoot()
	/**
	 * Returns the object at the root of the tree this object resides in
	 *
	 * Note: Uses {@link getRootId} to retrieve the root id and {@link instantiate}
	 * to instantiate it.
	 * @return object
	 */
	function getRoot($force=false)
	{
		return $this->instantiate($this->getRootId($force));
	}
	// }}}
	// {{{ getParentAttribute()
	/**
	 * Returns the attribute used to hold the tree structure
	 * (that is, the value of $this->dataStructure['tree_link_attr']).
	 *
	 * If $informal is false (default), an exception is thrown for objects
	 * which don't define the tree attribute.
	 */
	function getParentAttribute($informal=false)
	{
		if (!$informal && empty($this->dataStructure['tree_link_attr']))
			throw new BadMethodCallException('Parent attribute requested for an object with no tree_link_attr defined in dataStructure!');

		if ($informal && empty($this->dataStructure['tree_link_attr']))
			return NULL;

		return $this->dataStructure['tree_link_attr'];
	}
	// }}}
	// {{{ getParent()
	/**
	 * Returns the parent of this object
	 *
	 * Note: needs proper definition of $this->dataStructure to determine
	 * the tree links.
	 * @return object the parent of this object or false on error.
	 */
	function getParent()
	{
		return $this->getObject($this->getParentAttribute());
	}
	// }}}
	// {{{ getParents()
	/**
	 * Instantiates the list of IDs returned by {@link getParentIDs}
	 * and returns it.
	 */
	function getParents()
	{
		return $this->instantiate($this->getParentIDs());
	}
	// }}}
	// {{{ getParentIDs()
	/**
	 * Returns the array of the IDs of all parents of this object, tree-wise.
	 *
	 * The root of the tree in which this object resides will be the element
	 * at index 0 in the array, and the parent of this object will be the
	 * last element in the array. Each index therefore represents the distance
	 * from the root in depth. You can get the distance of this object by using
	 * count() on the result of this method.
	 *
	 * @return array the array of parents or false on error
	 */
	function getParentIDs()
	{
		$ids=array();
		$tmp=$this;
		while ($tmp2=$tmp->getParent()) {
			$tmp=&$tmp2;
			if (in_array($tmp->id,$ids))
				throw new RuntimeException("Loop in tree structure at ID ".$tmp->id);

			$ids[]=$tmp->id;
		}
		return array_reverse($ids);
	}
	// }}}
	// {{{ getChildren()
	/**
	 * Returns an array of instantiated objects representing the children of this object
	 *
	 * Note: needs proper definition of $this->dataStructure to determine
	 * the tree links.
	 * @param string $order_att the attribute to order the results by
	 * @param boolean $reverse if true, it will return the objects in reverse order
	 * @return array the children of this object or false on error.
	 */
	function getChildren($order_att=NULL, $reverse=false)
	{
		$parentAtt=$this->getParentAttribute();
		// We don't use getObjects() because that works on dependencies
		// and we don't know which dependency to use. Unfortunately.
		// Because we'll have to use a weaker mechanism to make developers
		// define proper tree dependencies until it may be too late :(
		return $this->search($parentAtt, $this->id, $order_att, $reverse);
	}
	// }}}
	// {{{ getChildrenIDs()
	/**
	 * Returns an array of IDs of the children of this object
	 *
	 * Note: needs proper definition of $this->dataStructure to determine
	 * the tree links.
	 * @param string $order_att the attribute to order the results by
	 * @param boolean $reverse if true, it will return the objects in reverse order
	 * @return array the children IDs of this object or false on error.
	 */
	function getChildrenIDs($order_att=NULL, $reverse=false)
	{
		$parentAtt=$this->getParentAttribute();
		// We don't use getObjects() because that works on dependencies
		// and we don't know which dependency to use. Unfortunately.
		// Because we'll have to use a weaker mechanism to make developers
		// define proper tree dependencies until it may be too late :(
		return $this->searchID($parentAtt, $this->id, $order_att, $reverse);
	}
	// }}}
	// {{{ searchChildren()
	/**
	 * Returns an array of instantiated objects representing the children of
	 * this object which match the condition(s) $attName=$attValue.
	 * You may specify as many conditions as you wish, just like in
	 * {link @search}.
	 *
	 * Note: needs proper definition of $this->dataStructure to determine
	 * the tree links.
	 * @param mixed $attName the attribute(s) to match
	 * @param mixed $attVal the value(s) to match
	 * @param string $order_att the attribute to order the results by
	 * @param boolean $reverse if true, it will return the objects in reverse order
	 * @return array the children of this object or false on error.
	 */
	function searchChildren($attName, $attVal, $order_att=NULL, $reverse=false)
	{
		$parentAtt=$this->getParentAttribute();
		// We don't use getObjects() because that works on dependencies
		// and we don't know which dependency to use. Unfortunately.
		// Because we'll have to use a weaker mechanism to make developers
		// define proper tree dependencies until it may be too late :(
		if (is_array($attName)) {
			$attName[]=$parentAtt;
			$attVal[]=$this->id;
		} else {
			$attName=array($attName, $parentAtt);
			$attVal=array($attVal, $this->id);
		}
		$children=$this->search($attName, $attVal, $order_att, $reverse);
		return $this->_doLoad($children);
	}
	// }}}
	// {{{ getTree()
	/**
	 * Returns an array containing the tree under this object.
	 *
	 * Elements of the same level are ordered by the provided parameters,
	 * and every element is followed by its children. It's rather hard to
	 * explain - but the idea is that it does what you expect it to.
	 * The array is indexed (order as explained above), and every element
	 * is itself an associative array made of these elements:
	 * 'level' => (int) <item level in the tree, where 0 is this object>
	 * 'object' => (object) <the actual object, with only the ID set>
	 * @param string $order_att the attribute to sort siblings by
	 * @param boolean $reverse if true, will reverse the order OF SIBLINGS
	 *   (NOT of the whole tree)
	 * @param integer $level this object's level (used internally, but you
	 *   may also use it to shift levels for some reason)
	 * @return array array describing the tree or false on error.
	 */
	function getTree($order_att=NULL, $reverse=false, $level=0, $justIDs=false)
	{
		if (!$this->id) {
			throw new BadMethodCallException('Tree structure requested for object with ID not set!');
		}
		if ($justIDs) {
			$result[]=array('level'=>$level, 'object'=>$this->id);
		} else {
			$result[]=array('level'=>$level, 'object'=>$this);
		}
		$children=$this->getChildrenIDs($order_att, $reverse);
		$thisClass=get_class($this);
		while (list(,$child)=@each($children)) {
			//$child->quickLoad();
			$child=&new $thisClass($child);
			if ($justIDs) {
				$tmp=$child->getTreeIDs($order_att, $reverse, $level+1);
			} else {
				$tmp=$child->getTree($order_att, $reverse, $level+1);
			}
			@reset($tmp);
			while (list(,$tmp2)=each($tmp)) {
				if ($justIDs) {
					$result[]=$tmp2;
				} else {
					$result[]=$tmp2;
				}
			}
		}
		return $result;
	}
	// }}}
	// {{{ getTreeIDs()
	/**
	 * Identical in every way with {getTree}, this method returns the
	 * IDs of the objects, instead of the objects themselves.
	 */
	function getTreeIDs($order_att=NULL,$reverse=false,$level=0)
	{
		return $this->getTree($order_att,$reverse,$level,true);
	}
	// }}}
// }}}
// {{{ AUXILIARY METHODS
	// ----------------------------------
	//        AUXILIARY METHODS
	// ----------------------------------
	// {{{ cloneObject()
	/**
	* Creates a clone of this object, by populating all attributes from this
	* object in the new one.
	*
	* Returns the clone. The clone doesn't have an ID set and it is not
	* automatically saved.
	*/
	function cloneObject()
	{
		$class=get_class($this);
		$clone=new $class();
		foreach($this->dataStructure['fields'] as $attName=>$attMeta)
			$clone->setAttr($attName,$this->getAttr($attName));
		return $clone;
	}
	// }}}
	// {{{ fillDataStructure()
	/**
	 * Auxiliary method which pre-digests ${@link dataStructure}.
	 *
	 * It first tries to restore a previous cache, and if that's
	 * not available it calls {@link poreDataStructure}().
	 */
	function fillDataStructure()
	{
		$myClass=get_class($this);
		if (LPC_dataStructure::gotDataStructure($myClass)) {
			$this->dataStructure=LPC_dataStructure::getDataStructure($myClass);
		} else {
			$this->poreDataStructure();
		}

		$this->_initAttrFlags();

		// init db object
		$this->dbInit();
	}
	// }}}
	// {{{ poreDataStructure()
	function poreDataStructure()
	{
		$myClass=get_class($this);
		// Default ID field
		if (empty($this->dataStructure['id_field'])) {
			$this->dataStructure['id_field']='id';
		}
		if (empty($this->dataStructure['table_name'])) {
			throw new RuntimeException("Malformed data structure: no table specified! (array key 'table_name' in \$dataStructure)");
		}
		// We'll start by filling in the default field name for attributes
		// which don't define it explicitly  
		$fields=$this->dataStructure['fields'];
		$allFlags=array('NULL','sqlDate','forceSave','noLogging','trim');
		foreach($fields as $attName=>$dataDef) {
			if (empty($fields[$attName]['fld_name'])) {
				$fields[$attName]['fld_name']=$attName;
			}
			if (!isset($fields[$attName]['flags'])) {
				$fields[$attName]['flags']=array();
			}
			foreach($allFlags as $flag1) {
				if (!isset($fields[$attName]['flags'][$flag1])) {
					$fields[$attName]['flags'][$flag1]=false;
				}
			}
		}
		$this->dataStructure['fields']=$fields;

		if (!isset($this->dataStructure['depend'])) {
			$this->dataStructure['depend']=array();
		}
		// Now we need to work on the dependencies. But first let's read in
		// the foreign dependencies, if any.
		$foreignDeps=LPC_Foreign_dependency_manager::getDependencies($myClass);
		if ($foreignDeps) {
			$this->dataStructure['depend']=array_merge(
				$foreignDeps,$this->dataStructure['depend']
			);
		}

		// Ok, now we have the final dependency structure. Let's check if the
		// on_mod setting is explicitly defined, and fill in the defaults:
		// * for many to many, the default is MISTRESS (when one end is deleted,
		//   just delete the corresponding entries in the join table)
		// * for one to many, the default is WIFE (when the "one" is
		//   deleted, automatically delete the "many")
		$deps=$this->dataStructure['depend'];
		foreach($deps as $depName=>$dep) {
			if (empty($dep['dbKey']))
				$dep['dbKey']=$this->dbKey;

			if (empty($dep['type']))
				$dep['type']='one';

			if (empty($dep['on_mod'])) {
				if ($dep['type']=='many')
					$dep['on_mod']='MISTRESS';
				else
					$dep['on_mod']='WIFE';
			} elseif (!in_array($dep['on_mod'],array('WIFE','MISTRESS','STRANGER')))
				throw new RuntimeException("Unknown dependency on_mod (".$dep['on_mod'].")!");

			switch($dep['type']) {
				case 'one':
					if (empty($dep['class']))
						throw new RuntimeException("One-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the class name of the remote object! (key 'class')");

					if (empty($dep['attr']))
						throw new RuntimeException("One-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the attribute of the remote object! (key 'attr')");

					break;
				case 'many':
					if (empty($dep['class']))
						throw new RuntimeException("Many-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the class name of the remote object! (key 'class')");

					if (empty($dep['table_name']))
						throw new RuntimeException("Many-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the name of the table containing the dependencies! (key 'table_name')");

					if (empty($dep['my_fld_name']))
						throw new RuntimeException("Many-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the name of the field pointing to this class in table \"{$dep['table_name']}\"! (key 'my_fld_name')");

					if (empty($dep['link_fld_name']))
						throw new RuntimeException("Many-to-many dependency \"$depName\" in class ".get_class($this)." doesn't specify the name of the field pointing to the remote class in table \"{$dep['table_name']}\"! (key 'link_fld_name')");

					break;
				default:
					throw new RuntimeException("Unknown dependency type \"{$dep['type']}\" for dependency \"$depName\" in class ".get_class($this)."! (only 'one' and 'many' supported in key 'type')");
			}
			$deps[$depName]=$dep;
		}
		$this->dataStructure['depend']=$deps;

		// And finally, let's make sure to register the link between this object
		// and its siblings, in case a tree link attribute is defined.
		if (!empty($this->dataStructure['tree_link_attr']))
			$this->dataStructure['fields'][$this->dataStructure['tree_link_attr']]['link_class']=$myClass;

		// Now we'll fill in the validation defaults, for fields where we HAVE a type AND we DON'T have validation rules
		foreach($this->dataStructure['fields'] as $attName=>$dataDef) {
			$this->dataStructure['fields'][$attName]['flags']['required']=false;
			if (!isset($dataDef['type']) || isset($dataDef['rules']))
				continue;
			$type=explode(".",$dataDef['type']);
			$rules=array();

			if (in_array($type[0],array('text','longtext')))
				$this->dataStructure['fields'][$attName]['base_type']=$type[0];
			elseif (in_array($type[0],array('html','set','enum')))
				$this->dataStructure['fields'][$attName]['base_type']='text';
			elseif (in_array($type[0],array('integer','email','float','date','boolean'))) {
				$rules[]=$type[0];
				$this->dataStructure['fields'][$attName]['base_type']=$type[0];
			} elseif ($type[0]=='datetime') {
				$rules[]='date'; // <- we use the date rule for datetime
				$this->dataStructure['fields'][$attName]['base_type']=$type[0];
			} else
				throw new RuntimeException("Unknown field type ".$type[0]." for field ".$attName."; valid types are 'integer','boolean','email','float','date','datetime','text','longtext','html','set','enum'.");

			if (isset($type[1])) {
				if ($type[1]=='required') {
					$rules[]='required';
					$this->dataStructure['fields'][$attName]['flags']['required']=true;
				} else
					throw new RuntimeException("Unknown field modifier ".$type[1]."; the only valid modifier is 'required'.");
			}
			$this->dataStructure['fields'][$attName]['rules']=$rules;
		}

		// Ok, now for a simple cosmetic thingie, let's check if we have a
		// title_attr defined, and if not we'll look for some traditional
		// default, such as 'title' or 'name'. We'll use one of those by default
		// if any present among the existing attributes.
		if (empty($this->dataStructure['title_attr'])) {
			if (isset($this->dataStructure['fields']['title']))
				$this->dataStructure['title_attr']='title';
			elseif (isset($this->dataStructure['fields']['name']))
				$this->dataStructure['title_attr']='name';
			elseif ($this::$i18n_class) {
				$child=new $this::$i18n_class();
				$this->dataStructure['title_attr']=$child->dataStructure['title_attr'];
			}
		}

		LPC_dataStructure::registerDataStructure($myClass,$this->dataStructure);
	}
	// }}}
	// {{{ _initAttrFlags()
	function _initAttrFlags()
	{
		// Ok, now let's set up the attr_flags
		$this->attr_flags=array();
		foreach($this->dataStructure['fields'] as $attrName=>$attrData)
			$this->attr_flags[$attrName]=array(
				'loaded'=>false,
				'modified'=>false,
				'prepended'=>false
			);
	}
	// }}}
	// {{{ getFieldName()
	/**
	 * Returns the field name associated with the given attribute
	 *
	 * Note: use this if you ever need to query the database directly,
	 * instead of hardcoding field names. Field names should never appear
	 * in your code.
	 *
	 * @param string $attName the attribute to find the field associated to. Use 0 for the id field.
	 * @param boolean $simple set to true if you do NOT want the table name
	 *   (the default is returning 'tablename.fieldname')
	 */
	function getFieldName($attName, $simple=false)
	{
		if ($attName===0)
			$fld=$this->dataStructure['id_field'];
		elseif (empty($attName))
			throw new RuntimeException("The atribute name is mandatory!");
		elseif (empty($this->dataStructure['fields'][$attName]['fld_name'])) {
			if (empty($this::$i18n_class))
				throw new RuntimeException("Attribute to retrieve the field for (\"".$attName."\") wasn't defined in this class!");
			$this->initI18n();
			return $this->i18n_object->getFieldName($attName,$simple);
		} else
			$fld=$this->dataStructure['fields'][$attName]['fld_name'];

		if ($simple)
			return $fld;

		$tbl=$this->getTableName();

		return $tbl.".".$fld;
	}
	// }}}
	// {{{ getFieldNames()
	function getFieldNames($attNames, $simple=false)
	{
		$result=array();
		foreach($attNames as $key=>$attName)
			$result[$key]=$this->getFieldName($attName,$simple);
		return $result;
	}
	// }}}
	// {{{ getTableName()
	/**
	 * Returns the table name associated with the given object
	 * @param boolean $full if true, it includes the database name, resulting
	 *   in a table name of the form "database.table" -- by default, only the
	 *   table name is returned.
	 * @return string the table name
	 */
	function getTableName($full=false)
	{
		if (empty($this->dataStructure['table_name']))
			throw new RuntimeException("Table name requested for class which doesn't have a table name defined!");

		if (!$full)
			return $this->dataStructure['table_name'];

		$db=&$this->_doDbInit($this->dbKey);
		return $db->database.".".$this->dataStructure['table_name'];
	}
	// }}}
	// {{{ sqlDate()
	/**
	 * Returns a date formatted for SQL based on a PHP date
	 *
	 * You don't need this unless you're performing manual
	 * queries. For regular use, just set the sqlDate flag and
	 * use {@link getAttr} and {@link setAttr} which work
	 * with PHP dates.
	 *
	 * @param integer $timestamp if not specified it will use the current system time
	 * @return string
	 */
	function sqlDate($timestamp=false)
	{
		if ($timestamp===false)
			$timestamp = time();

		$current_default_timezone=date_default_timezone_get();
		date_default_timezone_set(ini_get('date.timezone'));
		$sqlDate=$this->db->DBTimeStamp($timestamp);
		date_default_timezone_set($current_default_timezone);
		return $sqlDate;
	}
	// }}}
	// {{{ query()
	/**
	 * A wrapper for $this->db->query - do not use $this->db->query directly.
	 *
	 * This will first set up $this->db if needed, and then perform the query.
	 * After performing the query, use $this->db methods to retrieve the result.
	 *
	 * @param mixed $sql (string) the query in plain text or
	 *  (array) the query as a LFX_Query_Manager structure
	 * @return integer the database result handle (which incidentally you don't need)
	 */
	function query($sql,$inputArr=false)
	{
/*
// Dumping all queries; sometimes useful
$hist=debug_backtrace();
$hdata="";
foreach($hist as $hkey=>$hatom) {
	if (isset($hatom['class']))
		$classInfo=$hatom['class']."::";
	else
		$classInfo="";
	$hdata.=$hatom['file'].":".$hatom['line']." -- ".$classInfo.$hatom['function'];
	if (isset($hatom['object'])) {
		if (isset($hatom['object']->id))
			$objID="#".$hatom['object']->id;
		else
			$objID="";
		$hdata.=" [".get_class($hatom['object']).$objID."]";
	}
	$hdata.="\n";
}

$fp=fopen("/tmp/LPC_queries.log","a");
fputs($fp,$sql."\n".$hdata."\n");
fclose($fp);
*/
//      echo "<hr>";

		if (!is_object($this->db))
			$this->dbInit();

		// Let's be nice and accept a query manager structure as well
		if (is_array($sql)) {
			$qb=new LPC_Query_builder($sql);
			$sql=$qb->buildSQL();
		}

		/*

		TO DO
		-----
		 * use execution history to ignore duplicate calls from the same scripts
				(might not be effective with some calls, for instance the process
				list in HelpDesk, should also allow inhibiting this filter)

	 */

		$localExplainRecord=false;
		/*
		// **TODO** re-implement this within the LPC context, it was very useful!
		if (isset($_SESSION['LFX_debug']['log_bad_db_indexes']) && $_SESSION['LFX_debug']['log_bad_db_indexes']) {
			if (strtoupper(substr($sql,0,7))=='SELECT ') {
				$result=$this->db->Execute("EXPLAIN $sql");
				if ($result) {
					if ($this->db->next_record()) {
						$localExplainRecord=$this->db->Record;
					} else {
						$this->addError('Failed to get next record when trying to log bad db indexes');
					}
				}
			}
		}
	 */

// Performance tuning
//startWatch("query");

		$result=$this->db->Execute($sql,$inputArr);

// Performance tuning (continued)
//$query_time=getWatch("query");
//if ($query_time>0.05/* || in_array(strtolower(substr(ltrim($sql),0,6)),array('insert','create')) */)
//	mail("bogdan@moongate.ro","Long query","Query time: $query_time\nQuery:\n$sql");


		if (!$result && empty($this->inhibitQueryErrors))
			throw new RuntimeException("Database error #{$this->db->ErrorNo()}: {$this->db->ErrorMsg()}; Query: \"$sql\"");

		global $_LFX;
		if (!isset($_LFX['global']['queryCount']))
			$_LFX['global']['queryCount']=1;
		else
			$_LFX['global']['queryCount']++;

		if ($localExplainRecord) {
			if ($localExplainRecord['rows']-$this->db->num_rows()>3) {
				LFX_LogMessage(
					'Too many rows checked by MySQL! ('.$this->db->num_rows().' returned out of '.$localExplainRecord['rows'].' checked)',
					'warning',
					array(
						'data_dump'=>array(
							'table'=>$localExplainRecord['table'],
							'type'=>$localExplainRecord['type'],
							'possible_keys'=>$localExplainRecord['possible_keys'],
							'key'=>$localExplainRecord['key'],
							'key_length'=>$localExplainRecord['key_len'],
							'ref'=>$localExplainRecord['ref'],
							'rows'=>$localExplainRecord['rows'],
							'extra'=>$localExplainRecord['Extra'],
							'query'=>$sql,
							'history'=>debug_backtrace()
						)
					)
				);
			}
		}
		if (!$result) {
			global $_LFX;
			$_LFX['global']['queries']['errors']++;
			if ($_LFX['global']['queries']['errors']>50)
				throw new RuntimeException("Too many errors in queries!");
		}
		return $result;
	}
	// }}}
	// {{{ query_ex()
	// **TODO**
	/**
	* Extended query function. To document.
	*
	* Sample 1:
	* SELECT {DMO_Definition.} FROM {DMO_Definition} WHERE {DMO_Definition.name}='Unknown'
	*
	* Sample 2:
	* SELECT {DMO_Definition.} FROM {DMO_Definition} WHERE {DMO_Definition.name}=:def_name:
	*
	* Sample 3:
	* SELECT {DMO_Definition|d2.}, {DMO_Definition|d2.name}
	*   FROM {DMO_Definition||d1}
	*   LEFT JOIN {DMO_Definition||d2} ON {DMO_Definition|d1/childDef|d2}
	*   WHERE {DMO_Definition|d1.name}=:name:
	*
	* Sample 3:
	* SELECT id FROM table WHERE id IN (:!myIds:)
	*
	*/
	function query_ex($query,$values=false)
	{
		// Let's be nice and accept a query manager structure as well
		if (is_array($query)) {
			$qb=new LPC_Query_builder($query);
			$query=$qb->buildSQL();
		}

		// Parsing the atoms;
		$queryData=$this->parseQuery_ex($query,$values);
		if (is_array($queryData))
			$query=$queryData['query'];
		else
			$query=$queryData;

		#echo "Final query (in query_ex):\n".$query."\n";
		if (!empty($queryData['meta']['parameters'])) {
			if (!$values)
				throw new InvalidArgumentException("Parameters used in query but not passed as parameters!");

			$param_array=$values;
			if (!$queryData['meta']['parameters_associative']) {
				$param_array=array();
				foreach($queryData['meta']['parameters'] as $param)
					$param_array[]=$values[$param];
			}
		}

		if (!$res=$this->query($query,$param_array))
			// SC::query will complain, we don't need to here
			return false;

		$queryData['resource']=$res;
		$this->db->LPC_queryData=$queryData;
		return $res;
	}
	// }}}
	// {{{ parseQuery_ex()
	/**
	* This method parses a query formatted for SC::{@link query_ex}() into
	* proper SQL.
	*/
	function parseQuery_ex($query,$values,$atom_meta=false)
	{
		// TODO: {f:date_add({u.creation_date},:lala:)}
		// Warning! the format must always be "{f:<function_name>([paramterer]*)}"
		// i.e. the function name MUST be always followed by brackets

		// TODO: SELECT {u.}, UNIX_TIMESTAMP(DATE_ADD({u.date_created!}, INTERVAL 1 DAY)) AS lala FROM {LFX_User|u}
		// Populates u.date_created, which it shouldn't!

		//echo("<pre>$query</pre>");
		// Variable initialisation
		if (!$atom_meta) {
			$atom_meta=array(
				'table_aliases_defined'=>array(),
				'table_aliases_used'=>array()
			);
		}
		$tables_started=false;
		$new_query='';
		$old_query=$query;

		// Let's first identify the atoms (curly brackets) and the parameters (columns)
		if (!preg_match_all("/\{[^\}]+\}/",$query,$all_matches) && !preg_match_all("/\:[^\:\{\}\(\)\"']+\:/",$query,$parameters))
			// No atoms? Simply return the string.
			return $query;

		$all_matches=$all_matches[0];

		// Now, let's get all regexps out of the way. We have three main types of
		// atoms:
		// (1) Table atoms which define (table) aliases;
		// (2) Field atoms which define (field) aliases;
		// (3) Table atoms which don't define aliases (these can't use aliases anyway, they can only define them).
		// (4) Field atoms which don't define aliases (we don't care much whether they use any aliases or not).
		// Let's identify all three types of atoms:

		// Type (1) -- atoms which define table aliases:
		// (an alias definition NOT preceded by any period)
		preg_match_all("/\{[^\}\.]+\|[^\}]+\}/",$query,$table_alias_def_matches);
		$table_alias_def_matches=$table_alias_def_matches[0];
		//echo("<h2>Table -- aliases</h2>".vardump($table_alias_def_matches));

		// Type (2) -- atoms which define field aliases:
		// (an alias definition which IS preceded by a period somewhere)
		preg_match_all("/\{[^\|\}]+\.[^\|\}]+\|[^\|\}]+\}/",$query,$field_alias_def_matches);
		$field_alias_def_matches=$field_alias_def_matches[0];
		//echo("<h2>Field -- aliases</h2>".vardump($field_alias_def_matches));

		// Type (3) -- table atoms which don't define any aliases
		// (no periods, no vertical bars)
		preg_match_all("/\{[^\|\}\.]+\}/",$query,$table_non_alias_def_matches);
		$table_non_alias_def_matches=$table_non_alias_def_matches[0];
		//echo("<h2>Table -- no aliases</h2>".vardump($table_non_alias_def_matches));

		// Type (4) -- field atoms which don't define any aliases
		// (at least one period, no vertical bars)
		preg_match_all("/\{[^\|\}]*\.[^\|\}]*\}/",$query,$field_non_alias_def_matches);
		$field_non_alias_def_matches=$field_non_alias_def_matches[0];
		//echo("<h2>Field -- no aliases</h2>".vardump($field_non_alias_def_matches));

		// Ok, and now parameters...
//		preg_match_all("/\:[^\:\{\}\(\)\"']+\:/",$query,$parameters);
		$parameters=$parameters[0];

		// Ok, now we have all atoms broken down into categories.
		// Let's first check if we didn't mess up with the regexps, by any chance:
		if (
			count($all_matches) !=
			count($table_alias_def_matches) +
			count($field_alias_def_matches) +
			count($table_non_alias_def_matches)+
			count($field_non_alias_def_matches)
		)
			throw new LogicException("Unexpected condition: ".
				"regular expressions count mismatch! This is a problem in the ".
				"code, please notify the developers! ".
				"The query section was '$query', please send the query along with ".
				"the bug report.");

		// Right, now that we know everything adds up, let's start by identifying
		// the table aliases. Please note we're not DOING anything yet, we're
		// simply pre-parsing these, as to populate the meta array.
		for($i=0;$i<count($table_alias_def_matches);$i++) {
			// Trimming the curly brackets...
			$match=substr($table_alias_def_matches[$i],1,-1);
			$Xmatch=explode('|',$match);
			//decho("Xmatch: ".vardump($Xmatch));
			$atom_meta['table_aliases'][$Xmatch[1]]=$Xmatch[0];
		}

		// Ok, let's parse each atom now...
		$objects_cache=array();
		for($i=0;$i<count($all_matches);$i++) {
			$is_result=true;
			unset($class_trim);
			$alias=$alias_def=$skip_atom=false;
			$meta=array();
			$new_query.=substr($old_query,0,strpos($old_query,$all_matches[$i]));
			$old_query=substr($old_query,strpos($old_query,$all_matches[$i])+strlen($all_matches[$i]));
			//decho("\$i=$i");
			//decho("\$all_matches: ".vardump($all_matches));
			//echo("<pre>$new_query<b>{$all_matches[$i]}</b>$old_query</pre>\n");
			// Trimming the curly brackets...
			$match=substr($all_matches[$i],1,-1);
			// Now let's first look for the class definition
			$class_regexp="/^[^\.\/\|\!]+/";
			if (!preg_match($class_regexp,$match,$class)) {
				$ok=false;
				if (substr($match,0,1)==='.') {
					$class_trim='';
					if (count($table_alias_def_matches) + count($table_non_alias_def_matches)==1) {
						if ($table_alias_def_matches) {
							$ok=(bool) preg_match($class_regexp,substr($table_alias_def_matches[0],1,-1),$class);
						} else {
							$ok=(bool) preg_match($class_regexp,substr($table_non_alias_def_matches[0],1,-1),$class);
						}
					}
					if (!$ok) {
						throw new InvalidArgumentException("When using multiple classes to retrieve data from, all attributes must be prepended with the proper class name -- atom '$match' in query section '$query'");
					}
				} else {
					throw new InvalidArgumentException("Atom '$match' badly formatted in query section '$query'");
				}
			}
			$class=$class[0];
			if (!isset($class_trim)) {
				$class_trim=$class;
			}
			if (substr($class,0,1)==='|') {
				// Trying to use an alias for the class; let's see if we know about it...
				$alias=$meta['alias']=substr($class,1);
				if (!$class=$atom_meta['table_aliases'][substr($class,1)]) {
					throw new InvalidArgumentException("Table alias '".substr($class,1)."' unknown, in atom '$match', in query section '$query'");
				}
			}
			// Now that we know the class, we'll try to include it
			if (!class_exists($class)) {
				if (!$aliased_class=$atom_meta['table_aliases'][$class]) {
					throw new DomainException("Neither class nor alias \"$class\" exists, in atom \"$match\", in query section \"$query\"");
				}
				if (!class_exists($aliased_class)) {
					throw new DomainException("Class doesn't exist -- \"$aliased_class\" used in atom \"$match\" in query \"$query\".");
				}
				$alias=$meta['alias']=$class;
				$class=$aliased_class;
			}
			if (!isset($objects_cache[$class])) {
				$object=new $class;
				$objects_cache[$class]=$object;
			} else {
				$object=$objects_cache[$class];
			}
			// Great! We know what the class is, and it also exists!
			// We'll now parse the rest of this atom into the $meta array
			$meta['atom']=$match;
			$meta['class']=$class;
			$rest=substr($match,strlen($class_trim));
			while($rest) {
				$rest_before=$rest;
				//decho("REST:$rest");
				switch(substr($rest,0,1)) {
					case '.': // Plain attribute, id, or special key
						$rest=substr($rest,1);
						if (preg_match("/^[^\.\/\|\(\!\~]+/",$rest,$attr)) { // plain attr
							$attr=$attr[0];
							$rest=substr($rest,strlen($attr));
							if ($attr=='*') {
								// First, clean up the remaining string
								decho("Keys: $keys[0]");
								$rest=substr($rest,strlen($keys[0]));
								// Now insert "matches" as if the user had typed all fields
								if ($alias) {
									$prefix=$alias;
								} else {
									$prefix=$class;
								}
								$insert_matches=array("\{$prefix.}");
								$insert_query="\{$prefix.}, ";
								foreach($object->dataStructure['fields'] as $field=>$field_data) {
									$insert_matches[]="\{$prefix.$field}";
									$insert_query.="\{$prefix.$field}, ";
								}
								array_splice($all_matches,$i+1,0,$insert_matches);
								$insert_query=substr($insert_query,0,-2);
								$old_query=$insert_query.$old_query;
								$skip_atom=true;
								decho("New matches: ".vardump($new_matches));
							}
						} elseif (preg_match("/^\(([^\)]+)\)$/",$rest,$keys)) { // special key
							$key=$keys[1];
							if ($key=='zazazozo') {
								// for future use
							} else {
								throw new DomainException("Special key \"$key\" unknown, in atom \"$match\", in query section \"$query\".");
							}
						} else { // ID
							$attr=false;
						}
						$meta['attribute']=$attr;
						break;

					case '!': // We need to provide raw data from the database
						$meta['raw']=true;
						$rest=substr($rest,1);
						break;

					case '~':
						$is_result=false;
						$rest=substr($rest,1);
						break;

					case '|': // Alias, or alias definition (we don't know here whether it's a table or field alias)
						$rest=substr($rest,1);
						if (true) { // Alias definition
							$alias_def=true;
							//$rest=substr($rest,1);
						} else {                       // Alias being used
							$alias_def=false;
						}
						if (preg_match("/^[^\.\/\|\!]+/",$rest,$alias)) {
							$alias=$alias[0];
							$rest=substr($rest,strlen($alias));
						} else {
							throw new DomainException("Invalid alias, in atom \"$match\", in query section \"$query\".");
						}
						if ($alias_def) {
							$meta['alias_def']=$alias;
						} else {
							$meta['alias']=$alias;
						}
						break;
				}
				if ($rest==$rest_before) {
					throw new DomainException ("Unrecognized atom leftover \"$rest\", in atom \"$match\", in query section \"$query\".");
				}
			}
			if ($skip_atom) {
				continue;
			}
			$meta['table']=$table=$object->getTableName(true);
			//echo("<font color='green'>Meta: ".vardump($meta)."</font><br />\n");
			if (!empty($meta['alias'])) {
				$table=$meta['alias'];
				$atom_meta['table_aliases_used']=array_unique(array_merge($atom_meta['table_aliases_used'],array($meta['alias'])));
			} else {
				$table=$meta['table'];
			}
			if (isset($meta['attribute'])) {
				// Please note that SC::getFieldName() returns the ID field if passed FALSE as the first parameter
				$field=$meta['field']=$object->getFieldName($meta['attribute'],true);
			}
			if (!empty($meta['alias_def'])) {
				// Ok, this is where we find out whether a defined alias if a table or a field alias.
				if (isset($meta['attribute'])) {
					// Field alias; this is the ONLY case in which a field alias is in any way recognized
					// by the SuperClass; therefore, when the two if() blocks opened above end, we simply
					// "rename" 'alias' to 'table_alias', and 'alias_def' to 'table_alias_def'.
					if (!$meta['raw'] && $object->dataStructure['fields'][$meta['attribute']]['flags']['sqlDate']) {
						$new_query.="UNIX_TIMESTAMP(".$table.".".$field.") AS ".$meta['alias_def'];
					} else {
						$new_query.=$table.".".$field." AS ".$meta['alias_def'];
					}
					$meta['field_alias_def']=$meta['alias_def'];
					$atom_meta['field_aliases_defined'][]=$meta['field_alias_def'];
					unset($meta['alias_def']);
				} else {
					// Table alias
					if (in_array($meta['alias_def'],$atom_meta['table_aliases_defined'])) {
						throw new DomainException("Alias \"{$mets['alias_def']}\" defined multiple times in query section \"$query\".");
					}
					$atom_meta['table_aliases_defined'][]=$meta['alias_def'];
					$new_query.=$meta['table'].' AS '.$meta['alias_def'];
				}
			} elseif (isset($meta['attribute'])) {
				if (!empty($meta['attribute']) && empty($meta['raw']) && $object->dataStructure['fields'][$meta['attribute']]['flags']['sqlDate']) {
					$new_query.="UNIX_TIMESTAMP(".$table.".".$field.")";
				} else {
					$new_query.=$table.".".$field;
				}
			} else {
				$new_query.=$table;
			}
			if (!empty($meta['alias_def'])) {
				$meta['table_alias_def']=$meta['alias_def'];
				unset($meta['alias_def']);
			}
			if (!empty($meta['alias'])) {
				$meta['table_alias']=$meta['alias'];
				unset($meta['alias']);
			}
			if (!empty($meta['field'])) {
				$meta['result']=$is_result && !$tables_started;
				$atom_meta['fields'][]=$meta;
			} else {
				$tables_started=true;
				$atom_meta['tables'][]=$meta;
			}
		}

		$new_query.=$old_query;
		//echo("After atom parsing: <pre>$new_query</pre>");

		// Great! Now let's parse those parameters as well.
		$old_query=$new_query;
		$new_query='';

		// oci8 uses parameter binding via associative arrays, with key format; everybody else
		// that we know of doesn't -- the rest use "select foo from bar where foobar=?".
		if (!$object) {
			$object=&$this;
		}
		$atom_meta['parameters_associative']=($object->db->databaseType=='oci8');
		$param_array=array();

		foreach($parameters as $param) {
			$new_query.=substr($old_query,0,strpos($old_query,$param));
			$old_query=substr($old_query,strpos($old_query,$param)+strlen($param));
			$param=substr($param,1,-1);
			if (substr($param,0,1)=='!') {
				if (is_array($values[$param])) {
					$myValues=array();
					foreach($values[$param] as $myValue)
						$myValues[]=$object->db->qstr($myValue);
					$myValue=implode(",",$myValues);
				} else
					$myValue=$object->db->qstr($values[$param]);
				$new_query.=$myValue;
			} else {
				if ($atom_meta['parameters_associative']) {
					$new_query.=":$param";
				} else {
					$new_query.="?";
				}
				$atom_meta['parameters'][]=$param;
			}
		}
		$new_query.=$old_query;
		//echo("After parameter parsing: <pre>$new_query</pre>");

		for($i=0;$i<count($atom_meta['table_aliases_used']);$i++) {
			if (!in_array($atom_meta['table_aliases_used'][$i],$atom_meta['table_aliases_defined'])) {
				throw new DomainException("Alias \"{$atom_meta['table_aliases_used'][$i]}\" used but not defined, in query section \"$query\".");
			}
		}
		for($i=0;$i<count($atom_meta['table_aliases_defined']);$i++) {
			if (!in_array($atom_meta['table_aliases_defined'][$i],$atom_meta['table_aliases_used'])) {
				// **TODO** -- restore this warning, one way or another
				//$this->addError("SC::parseQuery_ex warning: alias '{$atom_meta['table_aliases_defined'][$i]}' defined but not used, in query section '$query'");
			}
		}
		// TODO: also check for non-alias tables used/defined

		return array('query'=>$new_query,'original_query'=>$query,'meta'=>$atom_meta);
	}
	// }}}
	// {{{ nextObjects()
	// **TODO**
	function nextObjects($rs)
	{
		if (!$this->db)
			throw new RuntimeException("Error in SC::nextObjects: you need to execute a query first!");

		if (!$this->db->LPC_queryData)
			throw new RuntimeException("Error in SC::nextObjects: You need to execute a query using object-aware methods, such as SC::query_ex!");

		$fields=$rs->fields;
		if (!$fields)
			return NULL;

		//echo("<font color='pink'>Fields: ".vardump($fields)."</font>");
		$no=array(); // next objects
		$no_extra=array(); // extra fields resulted from the query
		$unknown=false;
		foreach($fields as $i=>$field) {
			if ($unknown) {
				$unknown=false;
				$no_extra[$i]=$rs->fields[$i]; // used to be reference to $this->db->resultSet->fields[$i]
			}

			if (!is_numeric($i))
				continue;

			// We know that the fields match our first entries in META, because you
			// can only specify the fields to select at the beginning of the query.
			$meta=$this->db->LPC_queryData['meta']['fields'][$i];
			//decho("i=$i; Field value: <b>$field</b>; meta: ".vardump($meta));
			if ((!$no_key=$this->keyName_ex($meta)) || !$meta['result']) {
				// This is an unknown entry, probably resulted from extra pieces
				// of query which didn't use curly brackets; these bits will end up
				// in local array $no_extra, and that array will be available via
				// $this->no_extra at the end of the function.
				$no_extra[$i]=$rs->fields[$i]; // used to be reference to $this->db->resultSet->fields[$i]
				$unknown=true;
				continue;
			}

			if (empty($no[$no_key]))
				$no[$no_key]=&new $meta['class'];

			$object=&$no[$no_key];
			if ($attName=$meta['attribute']) {
				$object->attr_flags[$attName]['loaded']=true;
				$object->attr[$attName]=$field;
			} else
				$object->id=$field;
		}
		$this->no=&$no;
		$this->no_extra=&$no_extra;
		$rs->MoveNext();
		return true;
	}
	// }}}
	// {{{ keyName_ex()
	function keyName_ex($meta)
	{
		if (!empty($meta['table_alias']))
			return $meta['table_alias'];

		return $meta['class'];
	}
	// }}}
	// {{{ atom2attr_ex()
	function atom2attr_ex($atom)
	{
		if (!$this->db || !$this->db->LPC_queryData['meta'])
			throw new RuntimeException("No query_ex has been executed on this object!");

		$atom=str_replace('~','',$atom);
		$meta=$this->db->LPC_queryData['meta']['fields'];
		for($i=0;$i<count($meta);$i++) {
			if (str_replace('~','',$meta[$i]['atom'])==$atom) {
				if (!$meta[$i]['attribute']) {
					return 0;
				}
				return $meta[$i]['attribute'];
			}
		}
		return NULL;
	}
	// }}}
	// {{{ log()
	/**
	* Just a placeholder for now; used for logging SQL changes (insert, delete...)
	*/
	function log($type)
	{
	}
	// }}}
	// {{{ debug()
	/**
	* Placeholder for now.
	*/
	function debug($msg)
	{
	}
	// }}}
// {{{ REALLY PRIVATE STUFF
	// Really private stuff
	// {{{ _makeSaveQuery()
	/**
	 * You definitely don't need this.
	 *
	 * Builds the save query for {@link save} and {@link insert}.
	 * It will only build the "field=value" part of the
	 * query, so save() and insert can prepend their
	 * respective statements.
	 * The unmodified attributes are not saved by default,
	 * so if you need to force including the unmodified
	 * attributes you should specify $force.
	 *
	 * If the $insert parameter is set to true, the format will be
	 * (field1, field2, field3...) VALUES (value1, value2, value3...) instead.
	 *
	 * @param boolean $force true to force saving all attributes
	 * @param boolean $insert false to format as "field=value", true to format
	 *   as (field) VALUE (value)
	 * @return string the assignment part of the query
	 */

	function _makeSaveQuery($force=false,$insert=false)
	{
		$q_fields=$q_values=array();
		foreach($this->dataStructure['fields'] as $attName=>$dataEntry) {
			if (
				!$force &&
				empty($dataEntry['flags']['forceSave']) &&
				(
					//!isset($this->attr[$attName]) || // commented out because we want to save NULL values for null fields
					!$this->attr_flags[$attName]['modified']
				)
			) {
				continue;
			}

			if (empty($dataEntry['flags']['noLogging']))
				$this->loggableChange=true;

			$fld_name=$dataEntry['fld_name'];
			$fld_data=$this->attr[$attName];
			$q_fields[]=$fld_name;

			if ($fld_data===NULL && $dataEntry['flags']['NULL'])
				$q_values[]='NULL';
			elseif (!empty($dataEntry['flags']['sqlDate']))
				$q_values[]=$this->sqlDate($fld_data);
			else
				$q_values[]=$this->db->qstr($fld_data);
		}
		$query='';
		if (!$insert) {
			for($i=0;$i<count($q_fields);$i++)
				$query.=$q_fields[$i]."=".$q_values[$i].", ";

			$query=substr($query, 0, -2);
		} else {
			$q1=$q2='';
			for($i=0;$i<count($q_fields);$i++) {
				$q1.=$q_fields[$i].", ";
				$q2.=$q_values[$i].", ";
			}
			$q1=substr($q1,0,-2);
			$q2=substr($q2,0,-2);
			$query="(".$q1.") VALUES (".$q2.")";
		}
		return $query;
	}
	// }}}
	// {{{ probe()
	/**
	 * A cute little method which checks if the respective object exists
	 * in the database. Should be used as a test before a {@link load} for instance.
	 * If you try to load an object which doesn't exist, you'll get an error.
	 * You can therefore use probe() to check if the entry actually exists, and
	 * only proceed loading it if it does.
	 * An error can be issued either on database error, or if the id could
	 * not be determined -- for instance if you try to use probe with no params
	 * on an object which has been neither loaded nor saved.
	 * @param integer id the id of the object to probe; by default this object
	 * @return mixed true if exists, NULL if it doesn't and false on error
	 */
	function probe($id=0)
	{
		if (!$id) {
			$id=$this->id;
		}
		if (!$id) {
			return false; // Duh!
		}
		$id_fld=$this->dataStructure['id_field'];

		$table=$this->dataStructure['table_name'];

		$sql="SELECT ".$id_fld." FROM ".$table." WHERE ".$id_fld."=".$this->db->qstr($id);
		$rs=$this->query($sql);
		if (!$rs)
			return false;

		if ($rs->fields[0])
			return true;

		return NULL;
	}
	// }}}
	// {{{ probeLink()
	function probeLink($attr)
	{
		$obj=$this->getObject($attr);
		if (!$obj)
			return false;
		return $obj->probe();
	}
	// }}}
	// {{{ _doLoad()
	/**
	 * You definitely don't need this.
	 *
	 * This is called by both {@link load} and {@link quickLoad}
	 * to perform the loading. Actually you could call {@link load}
	 * and {@link quickLoad} aliases of this method.
	 *
	 * @param mixed $id the id of the object(s) to load. $this->id will be used
	 *   if not specified. If evaluates to false, the current object is loaded;
	 *   if string or numeric, it loads the object with that id; if array,
	 *   it loads the objects with the id's in the respective indexed array.
	 * @param boolean $force true to force loading all attributes from the
	 *   database even if some have already been set
	 * @param mixed $order_att specifies an order for the result set. Can be
	 *   a string, in which case that attribute is used, can be an array
	 *   in which case it should be an indexed array with attributes, or it
	 *   can be false, in which case the input order of the id's is preserved.
	 * @param mixed $reverse specifies whether the order should be reversed.
	 *   Can be a boolean, in which case all attributes to order by are (not)
	 *   reversed, or it can be an associative array of boolean values, in
	 *   which case the keys of the array specify the attributes for which
	 *   those boolean values are applied
	 * @return mixed false on error; true on success for single objects or
	 *   the indexed array of instantiated objects if multiple objects are
	 *   desired. In that case the current object is not affected.
	 */
		// TODO: $id should accept free-form arrays, and retain the keys
		// TODO: $reverse is a mess, figure out a way to clean up that act
		// TODO: all of that $newResult thing at the end is fishy, look into it

	function _doLoad($id=0, $force=true, $order_att=NULL, $reverse=NULL)
	{
		#unset($this->attr);
		#unset($this->attr_flags);
		if (is_array($id) && !count($id))
			return array();
		$id=$id?$id:$this->id;
		if (!$id)
			throw new BadMethodCallException('ID to load not specified!');

		$id_fld=$id_fld_clean=$this->dataStructure['id_field'];
		if (empty($this->db))
			// We should NEVER end up executing this, but hey! Who knows...
			$this->dbInit();
		if (is_array($id)) {
			$SQLid=$id_fld." IN (";
			foreach($id as $myID)
				$SQLid.=$this->db->qstr($myID).", ";
			$SQLid=substr($SQLid,0,-2).")";
		} else
			$SQLid=$id_fld."=".$this->db->qstr($id);
		$fields=$this->dataStructure['fields'];
		// To check if $fields is an array and issue error otherwise
		$query='';
		foreach($this->dataStructure['fields'] as $attName=>$dataEntry) {
			if (
				!is_array($id) &&
				!$force &&
				$this->attr_flags[$attName]['modified']
			) {
				// Skipping fields which have already been set if loading
				// just this object, and not forced to load them
				$newResult->attr[$attName]=$this->attr[$attName];
				continue;
			} elseif (
				// If loading a single object and we're forced to load
				// all fields then mark modified fields as unmodified,
				// because we're about to overwrite them (actually, we should TODO
				// this later on, when the object has been properly loaded).
				!is_array($id) &&
				$force &&
				$this->attr_flags[$attName]['modified']
			)
				$this->attr_flags[$attName]['modified']=false;
			$defld_name = $dataEntry['fld_name'];
			if ($dataEntry['flags']['sqlDate'])
				$query.=", UNIX_TIMESTAMP(".$defld_name.") AS ".$defld_name;
			else
				$query.=", ".$defld_name;
			if ($dataEntry['flags']['NULL'])
				// If it may be null, then we have to check for that as well!
				$query.=", ISNULL(".$defld_name.") AS ".$defld_name."ISNULL";
		}
		$query=substr($query, 2);
		$table=$this->dataStructure['table_name'];
		if (!$query)
			// **TODO** Move this test to fillDataStructure -- yes, but how?
			throw new BadMethodCallException('Malformed object data structure!');
		$sql="SELECT ".$id_fld.", ".$query." FROM ".$table." WHERE ".$SQLid;

		if ($order_att) {
			if (is_array($order_att)) {
				$sortSQL=' ORDER BY ';
				for($i=0;$i<count($order_att);$i++) {
					$order_fld=$this->dataStructure['fields'][$order_att[$i]]['fld_name'];
					if (!$order_fld) {
						throw new InvalidArgumentException("Unknown attribute to order by (\"$order_att[$i]\").");
					} else {
						$sortSQL.="$order_fld";
						if (
							(is_array($reverse) && ($reverse[$order_att])) ||
							$reverse===true
						)
							$sortSQL.=" DESC";
						$sortSQL.=", ";
					}
				}
				$sql.=substr($sortSQL,0,-2);
			} else {
				$order_fld=$this->dataStructure['fields'][$order_att]['fld_name'];
				if (!$order_fld)
					throw new InvalidArgumentException("Unknown attribute to order by (\"$order_att\")");
				else {
					$sql.=" ORDER BY ".$order_fld;
					if ($reverse)
						$sql.=" DESC";
				}
			}
		}

		$rs=$this->query($sql);
		$result=array();
		$myClass=get_class($this);
		$newResult=NULL;

		while(!$rs->EOF) {
			reset($fields);
			$newResult=new $myClass();
			$newResult->id=$rs->fields[$id_fld_clean];
			if (!is_array($id))
				$newResult->attr=$this->attr;
			while (list($attName,$dataEntry)=@each($fields)) {
				if (!is_array($id) && !$force && $this->attr_flags[$attName]['modified']) {
					// Skipping fields which have already been set if loading
					// just this object, if not forced to load them
					continue;
				}
				if (
					$dataEntry['flags']['NULL'] &&
					$rs->fields[$dataEntry['fld_name'].'ISNULL']
				)
					$newResult->attr[$attName]=NULL;
				else
					$newResult->attr[$attName]=$rs->fields[$dataEntry['fld_name']];

				if (is_array($id))
					$newResult->attr_flags[$attName]['loaded']=true;
				else
					$this->attr_flags[$attName]['loaded']=true;
			}
			$result[$newResult->id]=$newResult;
			//echo("Finished block for record #$recordNo<br />\n");
			$rs->MoveNext();
		}
		//echo("Done all $recordNo records.<br />\n");
		if (!$newResult) {
			if (!$id)
				$err_id=$this->id;
			elseif (is_numeric($id))
				$err_id=$id;
			elseif (is_array($id))
				$err_id=implode(',',$id);
			elseif (is_string($id))
				$err_id=$id;
			else
				$err_id='???';
			throw new RuntimeException("Object(s) ".get_class($this)."#$err_id not found in database!");
		}
		if (!is_array($id)) {
			$this->id=$id;
			$this->attr=$newResult->attr;
		} elseif ($order_att)
			return array_values($result);
		else {
			// Got to make sure we return the results in the same order as the IDs
			$finalResult=array();
			for($i=0;$i<count($id);$i++)
				$finalResult[]=$result[$id[$i]];
			return $finalResult;
		}
		return true;
	}
	// }}}
	// {{{ _doDbInit()
	/**
	 * This will use a generic database descriptor like $this->dbKey
	 * to instantiate a PHPlib database.
	 *
	 * You shouldn't need to call this directly - it's typically called by
	 * {@link dbInit} to initialize $this->db.
	 *
	 * @param array $dbKey the database descriptor
	 * @return object ADOdb database instance or false on error
	 */
	function _doDbInit($dbKey=NULL)
	{
		if (!$dbKey)
			$dbKey = $this->dbKey;
		return LPC_DB::getConnection($dbKey);
	}
	// }}}
	// {{{ cloneInClass()
	/**
	 * Clones all attributes of this object in a new object with a new class.
	 *
	 * This can be useful for conditional polymorphism. For instance,
	 * class Shape instantiates area() which returns 0,
	 * class Triangle extends Shape (and overrides area()),
	 * class Square extends Shape (and overrides area()),
	 * and then you can use $shape=new Shape; $shape->setAttr(...whatever...),
	 * followed by conditionally instantiating descendants with
	 * $shape->cloneInClass('Triangle') or $shape->cloneInClass('Square') --
	 * the cool thing is that the new object retains all attributes set with
	 * setAttr() on $shape -- afterwards, you can call method area() on any
	 * of the resulting objects, and get valid results.
	 *
	 * If no class name is specified, the parent class of this one is used.
	 *
	 * @param string $className the new class to instantiate
	 * @return mixed the instantiated object on success, or false on failure
	 */
	function cloneInClass($className)
	{
		if ($className===NULL)
			$className=get_parent_class(get_class($this));

		if (!LFX_include_class($className))
			return false;
		$clone=new $className();
		$vars=get_object_vars($this);
		while(list($var,$val)=each($vars))
			if (!in_array($var,array('dataStructure','module','dbKey')))
				$clone->$var=$val;

		return $clone;
	}
	// }}}
	// {{{ _makeGetObjectsQuery()
	function _makeGetLinksQuery($dep_name, $order_att=NULL, $reverse=false, $count_only=false, $id=0)
	{
		$id=$this->defaultId($id);
		if (!$id)
			throw new BadMethodCallException("Dependency search request on object with ID not set either explicitly or implicitly!");

		if (empty($this->dataStructure['depend'][$dep_name]))
			throw new InvalidArgumentException("Dependency search request on \"$dep_name\", but that dependency is not defined in this object!");
		$dep=$this->dataStructure['depend'][$dep_name];

		switch($dep['type']) {
			case 'one':
				$query=$this->_makeGetLinksOneQuery($dep,$order_att,$reverse,$id);
				break;
			case 'many':
				$query=$this->_makeGetLinksManyQuery($dep,$order_att,$reverse,$id);
				break;
			default:
				throw new RuntimeException("Unknown dependency type (\"".$dep['type']."\")");
		}
		if ($count_only)
			$query['select']=array("COUNT(*)");
		return $query;
	}
	// }}}
	// {{{ _makeGetLinksOneQuery()
	function _makeGetLinksOneQuery($dep,$order_att,$reverse,$id)
	{
		$tmp=new $dep['class'];
		return $tmp->_makeSearchIdQuery($dep['attr'],$id,$order_att,$reverse);
	}
	// }}}
	// {{{ _makeGetLinksManyQuery()
	function _makeGetLinksManyQuery($dep,$order_att,$reverse,$id)
	{
		$link_fld=$dep['link_fld_name'];
		$my_fld=$dep['my_fld_name'];
		$this->dbInit();
		$tbl = $dep['table_name'];
		$myId = $this->db->qstr($id);
		$otherObj=new $dep['class']();
		$query=array(
			'select'=>array($link_fld),
			'from'=>array($tbl),
			'join'=>array(
				array(
					'type'=>'left',
					'table'=>$otherObj->getTableName(),
					'condition'=>$otherObj->getFieldName(0)."=".$link_fld,
				),
			),
			'where'=>array(
				'type'=>'AND',
				'conditions'=>array(
					$tbl.'.'.$my_fld."=".$myId
				),
			),
		);
		if ($order_att!==NULL) {
			$query['order']=array(
				array(
					'field'=>$otherObj->getFieldName($order_att),
					'type'=>($reverse?'DESC':'ASC'),
				),
			);
			if (!isset($otherObj->dataStructure['fields'][$order_att]))
				$query['join'][]=$otherObj->_getI18nJoin();
		}
		return $query;
	}
	// }}}
	// {{{ _makeSearchCountQuery()
	function _makeSearchCountQuery($attName=NULL, $attValue=NULL, $like=false)
	{
		$query=$this->_makeRawSearchQuery($attName,$attValue,$like);
		$query['select'][]="COUNT(*)";
		return $query;
	}
	// }}}
	// {{{ _makeSearchIdQuery()
	function _makeSearchIdQuery($attName=NULL, $attValue=NULL, $order_att=NULL, $reverse=false, $like=false)
	{
		$query=$this->_makeRawSearchQuery($attName,$attValue,$like);
		$query['select'][]=$this->getFieldName(0); // id

		if ($order_att===NULL)
			return $query;

		$query['order']=array(
			array(
				'field'=>$this->getFieldName($order_att),
				'type'=>($reverse?'DESC':'ASC'),
			),
		);
		return $query;
	}
	// }}}
	// {{{ _makeRawSearchQuery()
	function _makeRawSearchQuery($attName=NULL, $attValue=NULL, $like=false)
	{
		$query=array(
			'select'=>array(),
			'from'=>array(
				$this->getTableName(),
			),
			'join'=>array(),
			'where'=>array(
				'type'=>'AND',
				'conditions'=>array(),
			),
		);
		if ($this::$i18n_class)
			$query['join'][]=$this->_getI18nJoin();
		
		if ($attName===NULL)
			return $query;

		$where=array();
		if (is_array($attName)) {
			if (!is_array($attValue))
				throw new RuntimeException("Parameter att_name is an array but att_value isn't.");

			if (count($attName)!=count($attValue))
				throw new RuntimeException("Array att_name has different number of elements from att_value.");

			foreach($attName as $idx=>$att)
				$where=array_merge($where,$this->_makeSearchQueryAtom($att,$attValue[$idx],$like));
		} else
			$where=$this->_makeSearchQueryAtom($attName,$attValue,$like);

		$query['where']['conditions']=$where;
		return $query;
	}
	// }}}
	// {{{ _makeSearchQueryAtom()
	function _makeSearchQueryAtom($attName=NULL, $attValue=NULL, $like=false)
	{
		$where=array();
		$field=$this->getFieldName($attName);

		if ($this->dataStructure['fields'][$attName]['flags']['trim'])
			$attValue = trim($attValue);

		if (($attValue===NULL) && ($this->dataStructure['fields'][$attName]['flags']['NULL']))
			$where[]=$field." IS NULL";
		else {
			if ($this->dataStructure['fields'][$attName]['flags']['sqlDate'])
				$Sval = $this->sqlDate($attValue);
			else
				$Sval = $this->db->qstr($attValue);

			if ($like)
				$where[]=$field." LIKE ".$Sval;
			else
				$where[]=$field." = ".$Sval;
		}
		return $where;
	}
	// }}}
	// {{{ _getI18nJoin()
	function _getI18nJoin($myAlias=NULL,$i18nAlias=NULL)
	{
		static $i18n_obj;
		if (!isset($i18n_obj))
			$i18n_obj=new $this::$i18n_class();

		$join=array(
			'type'=>'left',
			'table'=>$i18n_obj->getTableName(),
		);
		$iParentAttr=$i18n_obj->user_fields['i18n_parent'];
		$iLanguageAttr=$i18n_obj->user_fields['i18n_language'];
		if ($myAlias && $i18nAlias) {
			$join['table'].=" AS ".$i18nAlias;
			$iParentField=$i18nAlias.".".$i18n_obj->getFieldName($iParentAttr,true);
			$iLanguageField=$i18nAlias.".".$i18n_obj->getFieldName($iLanguageAttr,true);
			$mIdField=$myAlias.'.'.$this->getFieldName(0,true);
		} else {
			$iParentField=$i18n_obj->getFieldName($iParentAttr);
			$iLanguageField=$i18n_obj->getFieldName($iLanguageAttr);
			$mIdField=$this->getFieldName(0);
		}
		$join['condition']=
			$iParentField.'='.$mIdField." AND ".
			$iLanguageField.'='.LPC_Language::getCurrent()->id
		;

		return $join;
	}
	// }}}
// }}}
// }}}
// {{{ STATE TRANSITION METHODS
	// ------------------ State transition methods ---------------------------
	// State transition documentation available at
	// http://wiki.lanifex.com/LFXlib/TransitionManagement
	// {{{ getStateSet()
	function getStateSet($attr,$stateSet)
	{
		// should manage errors if undefined
		return $this->dataStructure['fields'][$attr]['state_data']['sets'][$stateSet];
	}
	// }}}
	// {{{ inStateSet()
	function inStateSet($attr,$stateSet)
	{
		return in_array($this->getAttr($attr),$this->getStateSet($attr,$stateSet));
	}
	// }}}
	// {{{ getStateTransition()
	function getStateTransition($attr,$transition)
	{
		// should manage errors if undefined
		return $this->dataStructure['fields'][$attr]['state_data']['transitions'][$transition];
	}
	// }}}
	// {{{ stateTransition()
	function stateTransition($attr,$transition,$noSave=false)
	{
		if ($this->validateTransitions()===false) {
			// SC::validateTransitions() must have complained already.
			return false;
		}
		$tData=$this->getStateTransition($attr,$transition);
		if ($sState=$tData['start_state']) {
			if ($sState!=$this->getAttr($attr)) {
				$this->stateTransitionError($attr,$transition,"object not in state \"$sState\"");
				return false;
			}
		} elseif ($sSet=$tData['start_state_set']) {
			if (!$this->inStateSet($sSet)) {
				$this->stateTransitionError($attr,$transition,"object not in state set \"$sSet\"");
				return false;
			}
		} else {
			$this->stateTransitionError($attr,$transition,"malformed transition definition: ".
				"neither start_state nor start_state_set defined");
			return false;
		}
		// The start condition is ok
		if (!$this->beforeStateTransition($attr,$transition)) {
			return NULL;
		}
		$this->setAttr($attr,$tData['end_state']);
		if (!$noSave) {
			$this->save();
		}
		$this->onStateTransition($attr,$transition);
		return true;
	}
	// }}}
	// {{{ beforeStateTransition()
	function beforeStateTransition($attr,$transition)
	{
		return true;
	}
	// }}}
	// {{{ onStateTransition()
	function onStateTransition($attr,$transition)
	{
		return true;
	}
	// }}}
	// {{{ stateTransitionError()
	// **TODO** -- should this throw an exception or is it just informal?
	function stateTransitionError($attr,$transition,$msg)
	{
		throw new RuntimeException("Failed performing transition $transition on attribute ".
                        "$attr associated with this object: $msg");
	}
	// }}}
	// {{{ validateTransitions()
	function validateTransitions()
	{
		// Currently only checking if we have multiple transitions with the same end state.
		// Returns true if valid, NULL on warnings or false on invalid.
		global $_LFX;
		if ($_LFX['global']['validated_transitions'][get_class($this)]) {
			return true;
		}
		$valid=true;
		foreach($this->dataStructure['fields'] as $attr=>$fData) {
			if (!$fData['state_data']) {
				continue;
			}
			$trs=$fData['state_data']['transitions'];
			$tTargets=$tRepo=array();
			foreach($trs as $tName=>$tData) {
				if (false!==($key=array_search($tData['end_state'],$tTargets,true))) {
					// Uh-oh!
					// **TODO** error management
					$otherTName=$tRepo[$key];
					$this->addError("Warning! Transitions \"$tName\" and \"$otherTName\" ".
						"associated with attribute \"$attr\" in this class have the same ".
						"end state. This is bad design -- consider breaking the current state ".
						"\"{$tData['end_state']}\" in two distinct states if you need this ".
						"kind of functionality. This message is only issued once per page.");
					if ($valid) $valid=NULL;
				} else {
					$tTargets[]=$tData['end_state'];
					$tRepo[]=$tName;
				}
			}
		}
		$_LFX['global']['validated_transitions'][get_class($this)]=true;
		return $valid;
	}
	// }}}
	// ---------------- End state transition methods --------------------------
// }}}
// {{{ VALIDATION METHODS
	// ---------------- Start validation --------------------------------------
	// {{{ validate()
	function validate($attr)
	{
		if (!isset($this->dataStructure['fields'][$attr])) {
			throw new RuntimeException("Attribute ".$attr." not defined (no such entry in dataStructure['fields']).");
		}
		if (empty($this->dataStructure['fields'][$attr]['rules']))
			return array();

		$validator=new LPC_Validator();
		return $validator->validate(
			$this->getAttr($attr),
			$this->dataStructure['fields'][$attr]['rules'],
			$this->getAttrName($attr)
		);
	}
	// }}}
	// ---------------- End validation ----------------------------------------
// }}}
// {{{ SERIALIZATION METHODS
	public function serialize()
	{
		$this->load();
		return serialize(array(
			'id'=>$this->id,
			'attr'=>$this->attr,
			'rootId'=>$this->rootId,
		));
	}
	public function unserialize($data)
	{
		self::__construct();
		$data=unserialize($data);
		$this->id=$data['id'];
		$this->attr=$data['attr'];
		$this->rootId=$data['rootId'];
	}
// }}}
// {{{ SCAFFOLDING-RELATED METHODS
	// {{{ hasScaffoldingRight()
	/**
	* Checks whether the current user has a specific CRUD scaffolding right.
	*
	* Superusers (and hyperusers) always have all rights by default.
	* Other users (including anonymous users) never have any right by default.
	* Override this method in descendants if you want to customize it.
	*
	* Typically you'll want to start with the following code:
	* if (parent::hasScaffoldingRight($right)) return true;
	*
	* @param char $right the right to check for; one of "C", "R", "U", "D"
	* @return boolean true if the current user does have the specified right, false otherwise.
	*/
	public function hasScaffoldingRight($right)
	{
		if (!defined('LPC_user_class'))
			// This project doesn't have registered users
			return false;

		$u=LPC_User::getCurrent();
		return $u->isSuperuser();
	}
	// }}}
	// {{{ getScaffoldingList()
	/**
	* Returns a LPC_HTML_list object which is the list of
	* objects in the database.
	*
	* @return object LPC_HTML_list object
	*/
	public function getScaffoldingList($query=NULL)
	{
		$l=$this->getBaseList($query);
		$l->onProcessHeaderCell=array($this,'onScaffoldingHeaderCell');
		$l->onProcessHeaderRow=array($this,'onScaffoldingHeaderRow');
		$l->onProcessBodyCell=array($this,'onScaffoldingBodyCell');
		$l->onProcessBodyRow=array($this,'onScaffoldingBodyRow');
		$l->msgEmptyList=_LH("scaffoldingMessageNoObjectsInClass");
		$l->hiddenFields=array_values($this->scaffoldingIDs);
		return $l;
	}
	// }}}
	// {{{ getBaseList()
	public function getBaseList($filterQuery=NULL)
	{
		$l=new LPC_HTML_list();
		$l->queryObject=$this;
		$query=array(
			'select'=>array(),
			'from'=>$this->getTableName(),
			'join'=>array(),
			'where'=>array(
				'type'=>'AND',
				'conditions'=>array(),
			),
		);
		if ($filterQuery) {
			$qb=new LPC_Query_builder();
			$query['where']['conditions'][]=$this->getFieldName(0)." IN (".$qb->buildSQL($filterQuery).")";
		}
		if (empty($this->dataStructure['files']))
			$attrs=$this->getScaffoldingAttributes();
		else {
			$attributes=$this->getScaffoldingAttributes();
			$file_attrs=array();
			foreach($this->dataStructure['files'] as $meta)
				$file_attrs=array_merge($file_attrs,array_values($meta));

			$attrs=array();
			foreach($attributes as $attName)
				if (!in_array($attName,$file_attrs))
					$attrs[]=$attName;
		}
		$attrs=array_diff($attrs,$this->scaffoldingHiddenAttributes);

		// Process attributes; make sure we join the linked table, and the link table's i18n table where needed
		$linkData=array();
		foreach($attrs as $attrName) {
			if (empty($this->dataStructure['fields'][$attrName]['link_class'])) {
				$query['select'][]=$this->getFieldName($attrName);
				continue;
			}
			$parent=new $this->dataStructure['fields'][$attrName]['link_class']();
			if (empty($parent->dataStructure['title_attr'])) {
				$query['select'][]=$this->getFieldName($attrName);
				continue;
			}

			$query['select'][]=$this->getFieldName($attrName)." AS ".$attrName."_hidden_ID";
			$this->scaffoldingIDs[$attrName]=$attrName."_hidden_ID";
			$parentAlias="link_".$attrName;
			$parentAttr=$parent->dataStructure['title_attr'];
			$query['join'][]=array(
				'type'=>'left',
				'table'=>$parent->getTableName()." AS ".$parentAlias,
				'condition'=>$this->getFieldName($attrName)."=".$parentAlias.'.'.$parent->getFieldName(0,true),
			);

			if (isset($parent->dataStructure['fields'][$parentAttr])) {
				$sql_field=$parentAlias.'.'.$parent->getFieldName($parentAttr,true);
				$query['select'][]=$sql_field." AS ".$attrName;
				$linkData[$attrName]=array(
					'meta'=>$parent->dataStructure['fields'][$parentAttr],
					'SQL_key'=>$sql_field,
				);
			} else {
				$parentI18nAlias=$parentAlias.'_i18n';
				$sql_field=$parentI18nAlias.'.'.$parent->getFieldName($parentAttr,true);
				$query['join'][]=$parent->_getI18nJoin($parentAlias,$parentI18nAlias);
				$query['select'][]=$sql_field." AS ".$attrName;

				$parentI18n=new $parent::$i18n_class();
				$linkData[$attrName]=array(
					'meta'=>$parentI18n->dataStructure['fields'][$parentAttr],
					'SQL_key'=>$sql_field,
				);
			}
		}
		$l->legalSortKeys=$this->getScaffoldingSortableAttributes();
		$l->legalSortKeys[]=$this->getFieldName(0,true);
		if ($this::$i18n_class) {
			$query['join'][]=$this->_getI18nJoin();

			$i18n_obj=new $this::$i18n_class();
			$attrs=$i18n_obj->getScaffoldingAttributes();
			$attrs=array_diff($attrs,$this->scaffoldingHiddenAttributes);
			$query['select']=array_merge($query['select'],$i18n_obj->getFieldNames($attrs));
			$l->legalSortKeys=array_merge($l->legalSortKeys,$i18n_obj->getScaffoldingSortableAttributes());
		}
		$this::$scaffoldingSortableAttributesCache=$l->legalSortKeys;
		array_unshift($query['select'],$this->getFieldName(0));
		$l->sql=$query;
		$l->defaultOrder=array(
			'sort'=>$this->dataStructure['id_field'],
			'order'=>0,
		);
		$l->filters=$this->getScaffoldingFilters($linkData);
		return $l;
	}
	// }}}
	// {{{ getScaffoldingFilters()
	function getScaffoldingFilters($linkData)
	{
		$filters=new LPC_HTML_fragment();
		$attrs=$this->getScaffoldingAttributes();
		foreach($attrs as $attName) {
			if (!empty($this->dataStructure['fields'][$attName]['link_class'])) {
				if (!isset($linkData[$attName]))
					// no filters for anonymous links
					continue;
				if (empty($linkData[$attName]['meta']['type'])) {
					$filter=new LPC_HTML_list_filter_string();
					$filter->SQL_key=$linkData[$attName]['SQL_key'];
					$filter->input_size=10;
					$filters->a($filter,$attName);
				}
				continue;
			}
			if (empty($this->dataStructure['fields'][$attName]['type'])) {
				$filter=new LPC_HTML_list_filter_string();
				$filter->input_size=10;
				$filter->SQL_key=$this->getFieldName($attName);
				$filters->a($filter,$attName);
			}
		}
		if ($this::$i18n_class) {
			$i18n_obj=new $this::$i18n_class();
			$i18n_filters=$i18n_obj->getScaffoldingFilters();
			$filters->content=array_merge($filters->content,$i18n_filters->content);
		}
		return $filters;
	}
	// }}}
	// {{{ onScaffoldingHeaderCell()
	public function onScaffoldingHeaderCell($key,$cell)
	{
		return true;
	}
	// }}}
	// {{{ onScaffoldingHeaderRow()
	public function onScaffoldingHeaderRow($row)
	{
		foreach($this->dataStructure['files'] as $fname=>$fmeta) {
			if (in_array($fname,$this->scaffoldingHiddenAttributes))
				continue;
			$th=new LPC_HTML_node('th');
			$row->a($th);
			$th->a($fname);
		}
		$th=new LPC_HTML_node('th');
		$row->a($th);
		$th->a(_LH('scaffoldingActionHeader'));
		return true;
	}
	// }}}
	// {{{ onScaffoldingBodyCell()
	public function onScaffoldingBodyCell($key,$cell,&$rowData)
	{
		$cell->content=htmlspecialchars($rowData[$key]);
		if (!empty($this->dataStructure['fields'][$key]['link_class'])) {
			if ($rowData[$key]) {
				if (empty($this->scaffoldingIDs[$key]))
					$idKey=$key;
				else
					$idKey=$this->scaffoldingIDs[$key];
				$cell->content="<a href='objectEdit.php?c=".$this->dataStructure['fields'][$key]['link_class']."&amp;id=".rawurlencode($rowData[$idKey])."'>".$cell->content."</a>";
			}
		} elseif (!in_array($key,$this::$scaffoldingSortableAttributesCache)) {
			if (strlen($rowData[$key])>10)
				$cell->content=htmlspecialchars(mb_substr($rowData[$key],0,10)."…");
		}
		return true;
	}
	// }}}
	// {{{ onScaffoldingBodyRow()
	public function onScaffoldingBodyRow($row,&$rowData)
	{
		$id=$rowData[$this->dataStructure['id_field']];
		foreach($this->dataStructure['files'] as $fname=>$fmeta) {
			if (in_array($fname,$this->scaffoldingHiddenAttributes))
				continue;
			$td=new LPC_HTML_node('td');
			$row->a($td);
			$td->a("<a href='fileDownload.php?c=".get_class($this)."&amp;id=".$id."&amp;file=".rawurlencode($fname)."'>"._LS('scaffoldingDownloadFile')."</a>");
		}
		$td=new LPC_HTML_node('td');
		$row->a($td);
		$td->a("[<a href='objectEdit.php?c=".get_class($this)."&amp;id=".$id."&amp;rt=".rawurlencode($_SERVER['REQUEST_URI'])."'>"._LS('scaffoldingEditAction')."</a>]");
		$td->a("&bull;");
		$td->a("[<a href='objectDelete.php?c=".get_class($this)."&amp;id=".$id."&amp;k=".LPC_Session_key::get()."'>"._LS('scaffoldingDeleteAction')."</a>]");
		foreach($this->dataStructure['depend'] as $depName=>$depData) {
			$td->a("&bull;");
			$suffix="";
			if ($depData['type']=='many')
				$suffix.=" <a href='objectMany.php?c=".rawurlencode($depData['class'])."&amp;rd=".rawurlencode($depName)."&amp;rc=".rawurlencode(get_class($this))."&amp;rid=".rawurlencode($id)."&amp;rt=".rawurlencode($_SERVER['REQUEST_URI'])."'>☞</a>";
			if ($depCount=$this->getLinks($depName,NULL,false,true,$id))
				$suffix.=" (<a href='objectList.php?c=".rawurlencode($depData['class'])."&amp;rd=".rawurlencode($depName)."&amp;rc=".rawurlencode(get_class($this))."&amp;rid=".rawurlencode($id)."'>".$depCount."</a>)";
			$td->a("[<a href='objectEdit.php?c=".rawurlencode($depData['class'])."&amp;rd=".rawurlencode($depName)."&amp;rc=".rawurlencode(get_class($this))."&amp;rid=".rawurlencode($id)."'>"._LS('scaffoldingCreateDependency',htmlspecialchars($depName))."</a>".$suffix."]");
		}

		return true;
	}
	// }}}
	// {{{ getScaffoldingFileRow()
	protected function getScaffoldingFileRow($attName,$options=array())
	{
		if (!isset($this->dataStructure['files']))
			return false;
		$found=false;
		foreach($this->dataStructure['files'] as $fname=>$fdata) {
			foreach($fdata as $type=>$att) {
				if ($att!=$attName)
					continue;
				if ($type!='content')
					return "";
				$download="";
				if ($this->isPopulatedFile($fname))
					$download=" <a href='".LPC_url."/scaffolding/fileDownload.php?c=".
						get_class($this)."&amp;id=".$this->id."&amp;file=".rawurlencode($fname).
						"'>"._LS('scaffoldingDownloadFile')."</a>";
				$desc=$fname;
				if (empty($options['NO_SQL_DESC']))
					$desc.="<div style='font-weight:normal; font-size:80%; opacity: 0.5'><tt><i>LPC file</i></tt></div>";
				return new LPC_HTML_form_row(array(
					'label'=>$desc,
					'input'=>"<input type='file' name='file[".$fname."]'>".$download,
				));
			}
		}
		return false;
	}
	// }}}
	// {{{ getScaffoldingEditRow()
	public function getScaffoldingEditRow($attName,$options=array())
	{
		$row=$this->getScaffoldingFileRow($attName,$options);
		if ($row!==false)
			return $row;

		$link="";
		if (isset($this->dataStructure['fields'][$attName]['link_class'])) {
			$class=$this->dataStructure['fields'][$attName]['link_class'];
			if (
				$this->id &&
				$this->getAttr($attName)
			) {
				$obj=$this->getObject($attName);
				$name=$obj->getNameH();
				if ($name)
					$name=" (".$name.")";
				$link=$name." <a href='objectEdit.php?c=".rawurlencode($class)."&amp;id=".rawurlencode($this->getAttr($attName))."'>"._LS('scaffoldingEditLink',htmlspecialchars($class),$this->getAttrH($attName))."</a>";
			}
			$link.=" <a href='#' onClick='return LPC_scaffolding_pickObject(\"".addslashes($class)."\",$(this).prevAll(\"input\").get(0))'>☞</a>";
		}
		if (!$this->id)
			$this->setAttr($attName,$this->getScaffoldingDefault($attName));
		$type="";
		if (isset($this->dataStructure['fields'][$attName]['type']))
			$type=$this->dataStructure['fields'][$attName]['type'];
		switch($type) {
			case 'integer':
				$input="<input type='text' name='attr[$attName]' size='6' value=\"".$this->getAttrH($attName)."\">".$link;
				break;
			case 'date':
				$input="<input type='text' name='attr[$attName]' value=\"".date('Y-m-d',$this->getAttr($attName))."\" class='input-date'>";
				break;
			case 'longtext':
			case 'html':
				$input="<textarea name='attr[$attName]' rows='5' style='width:100%'>".$this->getAttrH($attName)."</textarea>";
				break;
			case 'boolean':
				if ($this->getAttr($attName)) {
					$checked_yes=" checked";
					$checked_no="";
				} else {
					$checked_yes="";
					$checked_no=" checked";
				}
				$input=
					"<input type='radio' name='attr[$attName]' value='1'$checked_yes id='{$attName}_yes'> <label for='{$attName}_yes'>"._LH('scaffoldingBooleanYes')."</label><br>".
					"<input type='radio' name='attr[$attName]' value='0'$checked_no id='{$attName}_no'> <label for='{$attName}_no'>"._LH('scaffoldingBooleanNo')."</label>";
				break;
			case 'enum':
			case 'set':
				if (!isset($this->dataStructure['fields'][$attName]['options']))
					throw new RuntimeException("You need to define the options explicitly for enum and set fields (key 'options' in the data structure).");
				$input=new LPC_HTML_node('div');

				$inputS=new LPC_HTML_select("attr[$attName]");
				$input->a($inputS);
				if ($type=='set') {
					// Allow for an empty set by providing an empty option which will be processed on POST
					$input->a("<input type='hidden' name=\"attr[$attName][]\" value='NULL'>");

					$inputS->setAttr('name',"attr[$attName][]");
					$inputS->setAttr('multiple','multiple');
					$inputS->setAttr('size',min(5,count($this->dataStructure['fields'][$attName]['options'])));
				}
				$values=explode(",",$this->getAttr($attName));
				foreach($this->dataStructure['fields'][$attName]['options'] as $option) {
					$optionH=new LPC_HTML_node('option');
					$optionH->compact=true;
					if (in_array($option,$values))
						$optionH->setAttr('selected',1);
					$optionH->setAttr('value',addslashes($option));
					$optionH->a(htmlspecialchars($option));
					$inputS->a($optionH);
				}
				break;
			default:
				$input="<input type='text' name='attr[$attName]' value=\"".$this->getAttrH($attName)."\" style='width:100%'>".$link;
		}
		$attDesc=$attName;
		if (empty($options['NO_SQL_DESC'])) {
			$rs=$this->query("DESCRIBE ".$this->getTableName()." ".$this->getFieldName($attName,true));
			$attDesc.="<div style='font-weight:normal; font-size:80%; opacity: 0.5'><tt>".htmlspecialchars($rs->fields['Type'])."</tt></div>";
		}

		$row=new LPC_HTML_form_row(array(
			'label'=>$attDesc,
			'input'=>$input,
		));
		$row->compact=true;
		return $row;
	}
	// }}}
	// {{{ processScaffoldingAttributes()
	protected function processScaffoldingAttributes()
	{
		foreach($_POST['attr'] as $attName=>$attValue) {
			if (is_array($attValue))
				$attValue=implode(",",$attValue);
			$this->setAttr($attName,$attValue);
		}
	}
	// }}}
	// {{{ processScaffoldingFile()
	protected function processScaffoldingFile($key)
	{
		if (
			empty($this->dataStructure['files']) ||
			empty($this->dataStructure['files'][$key])
		)
			return;

		$meta=$this->dataStructure['files'][$key];
		foreach($meta as $type=>$attr) {
			switch($type) {
				case 'content':
					$this->setAttr($attr,file_get_contents($_FILES['file']['tmp_name'][$key]));
					break;
				case 'mime':
					$fname=$_FILES['file']['name'][$key];
					$tname=tempnam(sys_get_temp_dir(),'LPC_scaff_');
					$tfname=$tname.$fname;
					copy($_FILES['file']['tmp_name'][$key],$tfname);

					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime=finfo_file($finfo, $tfname);
					finfo_close($finfo);

					$this->setAttr($attr,$mime);
					finfo_close($finfo);

					unlink($tname);
					unlink($tfname);

					break;
				case 'name':
					$this->setAttr($attr,$_FILES['file']['name'][$key]);
					break;
				case 'date':
					$this->setAttr($attr,time());
					break;
				default:
					throw new RuntimeException("Unknown file entry type (\"".$type."\") for file key \"".$key."\"");
			}
		}
	}
	// }}}
	// {{{ processScaffoldingFiles()
	protected function processScaffoldingFiles()
	{
		if (empty($_FILES) || empty($_FILES['file']))
			return;
		foreach($_FILES['file']['tmp_name'] as $key=>$tmp_name) {
			if (!is_uploaded_file($tmp_name))
				continue;
			$this->processScaffoldingFile($key);
		}
	}
	// }}}
	// {{{ processScaffoldingEdit()
	public function processScaffoldingEdit()
	{
		$this->processScaffoldingAttributes();
		$this->processScaffoldingFiles();
		if ($this->save())
			return $this->onScaffoldingEdit();
	}
	// }}}
	// {{{ onScaffoldingEdit()
	public function onScaffoldingEdit()
	{
	}
	// }}}
	// {{{ processScaffoldingDelete()
	public function processScaffoldingDelete()
	{
		if ($this->delete())
			return $this->onScaffoldingDelete();
	}
	// }}}
	// {{{ onScaffoldingDelete()
	public function onScaffoldingDelete()
	{
		header("Location: objectList.php?c=".get_class($this));
		exit;
	}
	// }}}
	// {{{ getScaffoldingAttributes()
	function getScaffoldingAttributes()
	{
		$fields=array_keys($this->dataStructure['fields']);
		if (!empty($this->user_fields['i18n_parent']))
			$fields=array_diff($fields,array(
				$this->user_fields['i18n_parent'],
				$this->user_fields['i18n_language'],
			));
		return $fields;
	}
	// }}}
	// {{{ getScaffoldingSortableAttributes()
	function getScaffoldingSortableAttributes()
	{
		$attrs=$this->getScaffoldingAttributes();
		$sortable=array();
		foreach($attrs as $attName) {
			if (
				!isset($this->dataStructure['fields'][$attName]['type']) ||
				$this->dataStructure['fields'][$attName]['type']!='longtext')
			$sortable[]=$attName;
		}
		return $sortable;
	}
	// }}}
	// {{{ getScaffoldingDefault()
	function getScaffoldingDefault($attName)
	{
		if ($this->hasAttr($attName))
			return LPC_Scaffolding_default::getDefault(get_class($this),$attName,$this->i18n_langID);
		if (empty($this::$i18n_class))
			throw new RuntimeException("Attribute $attName is not defined in this object!");
		return LPC_Scaffolding_default::getDefault($this::$i18n_class,$attName,$this->i18n_langID);
	}
	// }}}
// }}}
// {{{ I18N-RELATED METHODS
	// {{{ initializeI18nObject()
	function initializeI18nObject()
	{
		$this->i18n_object=new $this::$i18n_class();
		$this->i18n_object->initI18nChild($this->id,$this->i18n_langID);
	}
	// }}}
	// {{{ getI18nAttr()
	function getI18nAttr($attName)
	{
		if (!$this::$i18n_class)
			throw new DomainException("Attribute \"$attName\" has never been defined in this class, and no i18n class is defined!");
		$this->initI18n();
		return $this->i18n_object->getAttr($attName);
	}
	// }}}
	// {{{ setI18nAttr()
	function setI18nAttr($attName,$attValue)
	{
		if (!$this::$i18n_class)
			throw new InvalidArgumentException("Attribute \"$attName\" has never been defined in class, and no i18n class is defined!");
		$this->initI18n();
		$this->i18n_object->setAttr($attName,$attValue);
		if (!$this->modified)
			$this->modified=$this->i18n_object->modified;
		return true;
	}
	// }}}
	// {{{ switchLanguage()
	function switchLanguage($lang)
	{
		if (is_object($lang))
			$langID=$lang->id;
		else
			$langID=$lang;
		$this->i18n_langID=$langID;
		$this->initI18n(true);
	}
	// }}}
	// {{{ initI18n()
	function initI18n($force=false)
	{
		if ($this->i18n_object && !$force)
			return;
		if (!$this->id)
			return $this->initializeI18nObject();
		$obj=new $this::$i18n_class();
		$obj=$obj->findI18nParent($this->id,$this->i18n_langID);
		if (!$obj)
			return $this->initializeI18nObject();

		$this->i18n_object=$obj;
	}
	// }}}
	// {{{ checkI18nChild()
	function checkI18nChild()
	{
		if (empty($this->user_fields['i18n_parent']))
			throw new RuntimeException("Internationalization children must define \$user_fields['i18n_parent'], as the name of the attribute pointing to the parent object.");
		if (empty($this->user_fields['i18n_language']))
			throw new RuntimeException("Internationalization children must define \$user_fields['i18n_language'], as the name of the attribute pointing to the translation language ID.");
	}
	// }}}
	// {{{ findI18nParent()
	function findI18nParent($id,$langID=0)
	{
		$this->checkI18nChild();
		if (!$langID)
			$langID=LPC_Language::getCurrent()->id;
		$objects=$this->search(
			array(
				$this->user_fields['i18n_parent'],
				$this->user_fields['i18n_language'],
			),
			array(
				$id,
				$langID,
			)
		);
		if (!$objects)
			return false;
		return $objects[0];
	}
	// }}}
	// {{{ initI18nChild()
	function initI18nChild($id,$langID=0)
	{
		$this->checkI18nChild();
		if (!$langID)
			$langID=LPC_Language::getCurrent()->id;
		$this->setAttr($this->user_fields['i18n_parent'],$id);
		$this->setAttr($this->user_fields['i18n_language'],$langID);
	}
	// }}}
	// {{{ save_i18n()
	function save_i18n($new)
	{
		if (!$this->i18n_object)
			return;
		if ($new)
			$this->i18n_object->initI18nChild($this->id,$this->i18n_langID);
		$this->i18n_object->save();
	}
	// }}}
// }}}
}
