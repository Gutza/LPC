<?php

class LPC_HTML_list extends LPC_HTML_widget
{
	public $nodeName="DIV";
	public $tableClass="default";
	public $paginatorClass="list_paginator";
	public $sql=array();
	public $queryObject=NULL;
	public $hiddenFields=array();
	public $id='1';

	public $paginatorEndsPages=7;
	public $paginatorAroundPages=7;

	public $onProcessHeaderCell=NULL;
	public $onProcessHeaderRow=NULL;
	public $onProcessBodyCell=NULL;
	public $onProcessBodyRow=NULL;

	public $entriesPerPage=20;
	public $currentPage=1;
	public $totalEntries=NULL;
	public $totalPages=0;

	protected $table=NULL;

	public $defaultOrder=array('sort'=>null,'order'=>0); // 0 is ASC, 1 is DESC

	/*
	$orderPresets=array(
		<parent field>=>array(
			array(
				'sort'=><child key 1>,
				'order'=>true for the same order as parent, false for opposite
			),
			array(
				'sort'=><child key 2>,
				'order'=>true for the same order as parent, false for opposite
			),
			...
		),
		...
	)
	*/
	public $orderPresets=array();

	public $legalSortKeys=array();
	public $msgEmptyList="This list is empty.";

	function __construct()
	{
		$this->processParameters();
	}

	function processParameters()
	{
		$page_param=$this->getParam('p');
		if (isset($_REQUEST[$page_param]))
			$this->currentPage=$_REQUEST[$page_param];
	}

	function getParam($param)
	{
		return 'l'.$this->id.'_'.$param;
	}

	function prepare()
	{
		if (!$this->queryObject)
			throw new RuntimeException("Query object not specified! (property queryObject)");
		if (!is_array($this->sql) || !count($this->sql))
			throw new RuntimeException("The query must be an array formatted for LPC_Query_Builder. (property sql)");

		$rs=$this->queryObject->query($this->processSQL());
		if ($rs->EOF) {
			$this->a($this->msgEmptyList);
			return null;
		}

		$this->setAttr('id','list_'.$this->id);
		$this->table=new LPC_HTML_node('table');
		$this->table->setAttr('class',$this->tableClass);
		$this->table->setAttr('id','list_table_'.$this->id);
		$this->a($this->table,'table');

		$keys=array_filter(array_keys($rs->fields),"is_string");
		$this->populateHeader($keys);
		while(!$rs->EOF) {
			$this->populateRow($keys,$rs->fields);
			$rs->MoveNext();
		}

		$this->populatePaginating();
	}

	function populatePaginating()
	{
		$elemCount=$this->getElementCount();
		if (!$this->entriesPerPage || $elemCount<=$this->entriesPerPage)
			return null;
		$pageCount=ceil($elemCount/$this->entriesPerPage);
		$paginator=new LPC_HTML_node("DIV");
		$paginator->setAttr('id','list_paginator_'.$this->id);
		$paginator->setAttr('class',$this->paginatorClass);
		$paginator->a(__L("Page:")." ");

		$pages=array();
		for($page=1;$page<=min($this->paginatorEndsPages,$pageCount);$page++)
			$pages[]=$page;
		for($page=max(1,$this->currentPage-$this->paginatorAroundPages);$page<=min($pageCount,$this->currentPage+$this->paginatorAroundPages);$page++)
			$pages[]=$page;
		for($page=max(1,$pageCount-$this->paginatorEndsPages);$page<=$pageCount;$page++)
			$pages[]=$page;
		$pages=array_unique($pages);

		$lastPage=0;
		foreach($pages as $page) {
			if ($page!=$lastPage+1)
				$paginator->a(" … ");
			elseif ($page>1)
				$paginator->a(" &bull; ");
			$lastPage=$page;
			if ($page==$this->currentPage) {
				$paginator->a("<b>".$page."</b>");
				continue;
			}
			$paginator->a($this->getPageLink($page));
		}
		$this->a($paginator);
	}

	function getPageLink($page)
	{
		$page_param=$this->getParam('p');
		$link=new LPC_HTML_node('a');
		$url=LPC_Url::add_GET_var($_SERVER['REQUEST_URI'],$page_param,$page);
		$link->setAttr('href',$url);
		$link->a((string) $page);
		return $link;
	}

	function processSQL()
	{
		$sql=$this->sql;
		$sql=$this->processOrder($sql);
		$sql=$this->processLimit($sql);
		return $sql;
	}

	function processOrder($sql)
	{
		$sortInfo=$this->getSortInfo();
		if (!$sortInfo['sort'])
			return $sql;

		$sql['order']=$sortInfo['query'];
		return $sql;
	}

	function processLimit($sql)
	{
		if (!$this->entriesPerPage)
			return $sql;

		$sql['limit']=array(
			'count'=>$this->entriesPerPage,
			'offset'=>($this->currentPage-1)*$this->entriesPerPage
		);
		return $sql;
	}

	function populateHeader($keys)
	{
		$sortInfo=$this->getSortInfo();
		$sortParam=$this->getParam('s');
		$orderParam=$this->getParam('o');

		$row=new LPC_HTML_node("TR");
		foreach($keys as $key) {
			if (in_array($key,$this->hiddenFields))
				continue;
			$cell=new LPC_HTML_node("TH");
			$cell->compact=true;

			$newOrder=0;
			$icon=NULL;

			if ($sortInfo['sort']==$key) {
				$icon=new LPC_HTML_node("IMG");
				$icon->setAttr('style','margin-bottom:-3px;');
				if ($sortInfo['order']) {
					$icon->setAttr('src',LPC_ICON_UP_ENABLED);
				} else {
					$icon->setAttr('src',LPC_ICON_DOWN_ENABLED);
					$newOrder=1;
				}
			}
			if (in_array($key,$this->legalSortKeys)) {
				$url=LPC_Url::add_GET_var($_SERVER['REQUEST_URI'],$sortParam,$key);
				$url=LPC_Url::add_GET_var($url,$orderParam,$newOrder);
				$label=new LPC_HTML_node("A");
				$label->setAttr('href',htmlspecialchars($url));
				$label->content=$key;
			} else {
				$label=new LPC_HTML_node("SPAN");
				$label->content=$key;
			}
			$cell->a($label,'label');
			if ($icon) {
				$cell->a(" ");
				$cell->a($icon,'sortIcon');
			}
			if ($this->onProcessHeaderCell($key,$cell))
				$row->a($cell);
		}
		if ($this->onProcessHeaderRow($row))
			$this->table->a($row);
	}

	function getSortInfo()
	{
		$sortParam=$this->getParam('s');
		if (
			!isset($_REQUEST[$sortParam]) ||
			!in_array($_REQUEST[$sortParam],$this->legalSortKeys)
		)
			return $this->populateExtendedSort($this->defaultOrder);

		$sort=$_REQUEST[$sortParam];

		$orderParam=$this->getParam('o');
		$order=$this->defaultOrder['order'];
		if (isset($_REQUEST[$orderParam]))
			$order=(int) (bool) $_REQUEST[$orderParam];

		return $this->populateExtendedSort(array('sort'=>$sort,'order'=>$order));
	}

	function populateExtendedSort($sortInfo)
	{
		$sortInfo['query']=array(array(
			'field'=>$sortInfo['sort'],
			'type'=>$sortInfo['order']?'DESC':'ASC'
		));
		if (!isset($this->orderPresets[$sortInfo['sort']]))
			return $sortInfo;
		foreach($this->orderPresets[$sortInfo['sort']] as $kid)
			$sortInfo['query'][]=array(
				'field'=>$kid['sort'],
				'type'=>($sortInfo['order'] xor $kid['order'])?'ASC':'DESC'
			);
		return $sortInfo;
	}

	function populateRow($keys,$rowData)
	{
		$row=new LPC_HTML_node("TR");
		foreach($keys as $key) {
			if (in_array($key,$this->hiddenFields))
				continue;
			$cell=new LPC_HTML_node("TD",false);
			$cell->compact=true;
			$cell->content=$rowData[$key];
			if ($this->onProcessBodyCell($key,$cell,$rowData))
				$row->a($cell);
		}
		if ($this->onProcessBodyRow($row,$rowData))
			$this->table->a($row);
	}

	function onProcessHeaderCell($key,$cell)
	{
		if (isset($this->onProcessHeaderCell))
			return call_user_func($this->onProcessHeaderCell,$key,$cell);
		return true;
	}

	function onProcessHeaderRow($row)
	{
		if (isset($this->onProcessHeaderRow))
			return call_user_func($this->onProcessHeaderRow,$row);
		return true;
	}

	function onProcessBodyCell($key,$cell,&$rowData)
	{
		if (isset($this->onProcessBodyCell))
			return call_user_func($this->onProcessBodyCell,$key,$cell,$rowData);
		return true;
	}

	function onProcessBodyRow($row,&$rowData)
	{
		if (isset($this->onProcessBodyRow))
			return call_user_func($this->onProcessBodyRow,$row,$rowData);
		return true;
	}

	function getElementCount()
	{
		if (isset($this->totalEntries))
			return $this->totalEntries;
		$sql=$this->sql;
		$sql['select']=array('1');
		unset($sql['order']);
		$rs=$this->queryObject->query($sql);
		$this->totalEntries=$rs->recordCount();
		return $this->totalEntries;
	}

}
