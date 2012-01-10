<?php
  /**
  * The LPC Query Management Library
  * Bogdan Stancescu <bogdan@moongate.ro>, November 2004
  */

  class LPC_Query_builder
  {

    var $array=array();

    function __construct($array=array())
    {
      if ($array && is_array($array)) {
        $this->array=$array;
      }
    }

      /**
      * This method builds an SQL string from an array which describes it.
      *
      * This is extremely useful when you have a page which issues the same
      * base SQL but with variations depending on a number of factors.
      *
      * The SQL is described by an associative array of the following form:
      * array(
      *   'select' => indexed array of elements to select (M)
      *   'from'   => indexed array which makes up the FROM list (M)
      *   'join'   => indexed array of JOIN sub-arrays, each formatted like this:
      *     array(
      *       'type'      => the join type, typically 'LEFT' or 'RIGHT',
      *       'table'     => what table to perform this join with,
      *       'condition' => what conditions should be met by the elements,
      *     ),
      *   'where'  => associative array which describes the WHERE conditions:
      *     array(
      *       'type'       => how to "glue" the conditions, "AND" or "OR",
      *       'conditions' => indexed array of conditions to be glued; each
      *                       element in this array can be either a string
      *                       which describes a condition or another associative
      *                       array like this one ('type','conditions'). Each
      *                       of the 'conditions' array can in turn be a string
      *                       or an associative array like this one, and so on.
      *     ),
      *   'group'  => an indexed array which contains GROUP BY elements as arrays:
      *     array(
      *       'field' => the field to group by,
      *       'type'  => the GROUP BY type ('ASC' or 'DESC') (optional)
      *     ),
      *   'order'  => an indexed array which contains ORDER BY elements with
      *               the same format as the GROUP BY elements above,
      *   'limit'  => associative array which describes the SQL LIMIT:
      *     array(
      *       'count'  => the maximum number of elements to return,
      *       'offset' => the offset (optional)
      *     )
      * )
      *
      * <b>Notes:</b>
      * - Elements marked with (M) are mandatory, all others are optional
      * - Elements explicitly marked as (optional) can miss from the respective
      *   structure, even if the structure is present (e.g. while the 'group'
      *   array is optional itself, even if you need it you can skip the 'type'
      *   entry from any of its elements).
      * - the "condition" element in the JOIN array can be either a plain string
      *   or a condition array with the same structure as the one used in WHERE.
      *
      * @param array $query associative array which describes the query
      * @return string the actual query
      */
    function buildSQL($query=array())
    {
      if (!$query) {
        $query=$this->array;
      }
      if (!$query || !is_array($query)) {
        throw new InvalidArgumentException("Expecting an array");
      }
      $this->array=&$query;
      $sql="SELECT\n";
      if (!is_array($query['select']))
        $query['select']=array($query['select']);
      $sql.="\t".implode(",\n\t",$query['select'])."\n";
      if (!is_array($query['from']))
        $query['from']=array($query['from']);
      $sql.="FROM\n\t".implode(",\n\t",$query['from'])."\n";
      if (!isset($query['join'])) {
        $query['join']=array();
      }
      foreach($query['join'] as $joinAtom) {
        if (is_array($joinAtom['condition'])) {
          $myCondition=$this->buildSQLConditions($joinAtom['condition'],1);
        } else {
          $myCondition=$joinAtom['condition'];
        }
        $sql.=strtoupper($joinAtom['type']).' JOIN '.
        $joinAtom['table'].' ON '.$myCondition."\n";
      }
      if (isset($query['where'])) {
        $sql.=$this->buildSQLConditions($query['where']);
      }
      if (isset($query['group'])) {
        $sql.="GROUP BY\n";
        foreach($query['group'] as $groupAtom) {
          $sql.="\t".$groupAtom['field'].' ';
          if ($groupAtom['type']) {
            $sql.=strtoupper($groupAtom['type']).' ';
          }
          $sql.=",\n";
        }
        $sql=substr($sql,0,-2)."\n";
      }
      if (!empty($query['order'])) {
        $sql.="ORDER BY\n";
        foreach($query['order'] as $orderAtom) {
          $sql.="\t".$orderAtom['field'].' ';
          if (isset($orderAtom['type'])) {
            $sql.=strtoupper($orderAtom['type']).' ';
          }
          $sql.=",\n";
        }
        $sql=substr($sql,0,-2)."\n";
      }
      if (isset($query['limit'])) {
        $sql.='LIMIT ';
        if ($query['limit']['offset']) {
          $sql.=$query['limit']['offset'].', '.$query['limit']['count'];
        } else {
          $sql.=$query['limit']['count'];
        }
      }
      return $sql;
    }

      /**
      * You shouldn't need to use this directly, it's an auxiliary function
      * for LFX_buildSQL which parses recursive conditions.
      *
      * @param array $where the conditions structure
      * @param integer $indent the indent (recursiveness) level, used to
      *   render the SQL nicely, with proper indentation
      * @return string the conditions parsed as SQL
      */
    function buildSQLConditions($where,$indent=0)
    {
      // Must first clear out empty arrays, as to avoid ending up with queries
      // such as "WHERE (id=5) AND () AND () AND (status!='resolved')".
      $where=$this->cleanupSQLConditions($where);
      $sql='';
      $ind1=str_repeat("\t",$indent);
      $ind2=str_repeat("\t",$indent+1);
      foreach($where['conditions'] as $condAtom) {
        if (!$sql) {
          if (!$indent) {
            $sql="WHERE\n";
          }
        } else {
          $sql.=strtoupper($where['type'])." ";
        }
        $sql.="(\n".$ind2;
        if (is_string($condAtom)) {
          $sql.=$condAtom;
        } else {
          $sql.=$this->buildSQLConditions($condAtom,$indent+1);
        }
        $sql.="\n".$ind1.") ";
      }
      if ($sql && !$indent) {
        $sql.="\n";
      }
      return $sql;
    }

      /**
      * This function cleans up SQL conditions recursively.
      * SQL conditions of the form ((cond1) AND (() OR ()) AND (cond2)) are
      * returned as ((cond1) AND (cond2)).
      * @param array $where the SQL structure to clean up
      * @return array the cleaned up SQL conditions structure
      */
    function cleanupSQLConditions($where)
    {
      if (!is_array($where)) {
        return $where;
      }
      if (!isset($where['type'])) {
        $where['type']='AND';
      }
      $newWhere=array('type'=>$where['type']);
      $success=false;
      if (!isset($where['conditions'])) {
        $where['conditions']=array();
      }
      if (!is_array($where['conditions'])) {
        $where['conditions']=array($where['conditions']);
      }
      foreach($where['conditions'] as $condAtom) {
        if($localWhere=$this->cleanupSQLConditions($condAtom)) {
          $newWhere['conditions'][]=$localWhere;
          $success=true;
        }
      }
      if ($success) {
        return $newWhere;
      }
      return array();
    }
  }

