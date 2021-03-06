<?php

class LPC_HTML_list extends LPC_HTML_widget
{
	public $nodeName="div";
	public $tableClass="default";
	public $paginatorClass="list_paginator";
	public $sql=array();
	public $sqlParams=false;
	public $queryObject=NULL;
	public $hiddenFields=array();

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

	public $emptyListMessageKey = "lpcListEmptyMessage";

	/**
	* An associative array which lists the header labels for each key
	*/
	public $labelMapping=array();

	protected $table=NULL;

	public $defaultOrder=array('sort'=>null,'order'=>0); // 0 is ASC, 1 is DESC

	/*
	$orderPresets=array(
		<parent field>=>array(
			array(
				'sort'=><child key 1>,
				'relative_order'=>true for the same order as parent, false for opposite
					OR
				'absolute_order'=>"ASC" for ascending, "DESC" for descending
					DEFAULT order: same as parent
			),
			...
		),
		...
	)
	*/
	public $orderPresets=array();

	public $legalSortKeys=array();

	/**
	* This will be a LPC_HTML_fragment in which you're supposed to append
	* LPC_HTML_list_filter descendants under keys corresponding to the
	* respective list keys.
	*/
	public $filters=NULL;

	function __construct()
	{
		$this->setUID();
		$this->processParameters();
		$this->filters = new LPC_HTML_fragment();
	}

	function processParameters()
	{
		$page_param = $this->getParam('p');
		if (isset($_REQUEST[$page_param]))
			$this->currentPage = $_REQUEST[$page_param];
	}

	function getParam($param)
	{
		return 'l'.$this->id.'_'.$param;
	}

	function prepare()
	{
		if (LPC_HTML_Document::ENV_BOOTSTRAP == $this->ownerDocument->environment) {
			$this->addClass("container");
			$this->tableClass = "table table-hover";
		}

		if (!$this->queryObject)
			throw new RuntimeException("Query object not specified! (property queryObject)");
		if (!is_array($this->sql) || !count($this->sql))
			throw new RuntimeException("The query must be an array formatted for LPC_Query_Builder. (property sql)");

		$sql=$this->processSQL();
		$rs=$this->queryObject->query($sql, $this->sqlParams);
		if ($rs->EOF)
			return $this->prepareEmpty();

		$this->table=new LPC_HTML_node('table');
		$this->table->setAttr('class', $this->tableClass);
		$this->a($this->table,'table');

		$this->thead = new LPC_HTML_node("thead");
		$this->table->a($this->thead, "thead");

		$this->tbody = new LPC_HTML_node("tbody");
		$this->table->a($this->tbody, "tbody");

		$keys=array_filter(array_keys($rs->fields),"is_string");
		$this->populateHeader($keys);
		while(!$rs->EOF) {
			$this->populateRow($keys,$rs->fields);
			$rs->MoveNext();
		}

		$this->populatePaginating();
	}

	function prepareEmpty()
	{
		$this->a(__L($this->emptyListMessageKey));
		if (!$this->filters->content)
			return;

		$anyFilter=false;
		foreach($this->filters->content as $key=>$filter) {
			if (!strlen($filter->getCurrentValue()))
				continue;
			$anyFilter=true;
			break;
		}

		if (!$anyFilter)
			return;

		$this->a(" ".__L("lpcListSuggestRemoveFilters"));
		foreach($this->filters->content as $key=>$filter) {
			if (!strlen($filter->getCurrentValue()))
				continue;

			if (isset($this->labelMapping[$key]))
				$label=$this->labelMapping[$key];
			else
				$label=$key;
			$this->a("<div><b>".$label."</b></div>");
			$this->a($filter);
		}
	}

	function populatePaginating()
	{
		$elemCount=$this->getElementCount();
		if (!$this->entriesPerPage || $elemCount <= $this->entriesPerPage)
			return null;

		$pageCount=ceil($elemCount / $this->entriesPerPage);
		$paginator=new LPC_HTML_node("div");
		$paginator->setAttr('id', 'list_paginator_'.$this->id);
		$paginator->setAttr('class', $this->paginatorClass);
		$paginator->a(__L("lpcListPageLabel")." ");

		$pages=array();
		$firstStart = 1;
		$firstEnd = min($this->paginatorEndsPages, $pageCount);
		for($page = $firstStart; $page <= $firstEnd; $page++)
			$pages[] = $page;

		$aroundStart = max($firstStart, $this->currentPage - $this->paginatorAroundPages);
		$aroundEnd = min($pageCount, $this->currentPage + $this->paginatorAroundPages);
		for($page = $aroundStart; $page<=$aroundEnd; $page++)
			$pages[]=$page;

		$lastStart = max(1,$pageCount-$this->paginatorEndsPages);
		$lastEnd = $pageCount;

		for($page = $lastStart; $page <= $lastEnd; $page++)
			$pages[]=$page;
		$pages = array_unique($pages);

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
		$link->compact=true;
		$link->setAttr('href', LPC_URI::getCurrent()->setVar($page_param, $page));
		$link->a($page);
		return $link;
	}

	function processSQL()
	{
		$sql=$this->sql;
		$sql=$this->processOrder($sql);
		$sql=$this->processLimit($sql);
		$sql=$this->processFilters($sql);
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

	function processFilters($sql)
	{
		$filterBase=$this->getParam('f');
		$filterWhere=array();
		foreach($this->filters->content as $key=>$filter) {
			if (!$filter instanceof LPC_HTML_list_filter)
				throw new RuntimeException("List filters MUST be descendants of LPC_HTML_list_filter!");
			if (!isset($filter->GET_key))
				$filter->GET_key=$filterBase.'_'.$key;
			$filter->list_key=$key;
			$filter->listObject=$this;
			$thisFilter=$filter->getSQL();
			if (!$thisFilter)
				continue;
			$filterWhere[]=$thisFilter;
		}
		if (!$filterWhere)
			return $sql;
		if (!isset($sql['where'])) {
			$sql['where']=array(
				'type'=>'and',
				'conditions'=>$filterWhere
			);
			return $sql;
		}
		if (strtoupper($sql['where']['type'])=='AND') {
			$sql['where']['conditions']=array_merge(
				$sql['where']['conditions'],
				$filterWhere
			);
			return $sql;
		}
		$origWhere=$sql['where'];
		$sql['where']=array(
			'type'=>'AND',
			'conditions'=>array_merge(
				array($origWhere),
				$filterWhere
			)
		);
		return $sql;
	}

	function populateHeader($keys)
	{
		$sortInfo=$this->getSortInfo();
		$sortParam=$this->getParam('s');
		$orderParam=$this->getParam('o');
		$filterBase=$this->getParam('f');

		$row=new LPC_HTML_node("tr");
		foreach($keys as $key) {
			if (in_array($key, $this->hiddenFields))
				continue;
			$cell=new LPC_HTML_node("th");
			$cell->setAttr('style', 'vertical-align: top');
			$cell->compact=true;

			$newOrder=0;
			$icon=NULL;

			if ($sortInfo['sort']==$key) {
				if ($sortInfo['order'])
					$order = "up";
				else {
					$order = "down";
					$newOrder=1;
				}
				$icon = $this->getIcon($order);
			}

			if (isset($this->labelMapping[$key]))
				$labelText=$this->labelMapping[$key];
			else
				$labelText=$key;

			if (in_array($key, $this->legalSortKeys)) {
				$label=new LPC_HTML_node("a");
				$label->setAttr('href',
					LPC_URI::getCurrent()->setVars(array(
						$sortParam => $key,
						$orderParam => $newOrder,
					))
				);
			} else
				$label=new LPC_HTML_node("span");

			$label->content=$labelText;
			$cell->a($label, 'label');

			if ($icon) {
				$cell->a(" ");
				$cell->a($icon, 'sortIcon');
			}

			if (isset($this->filters->content[$key]))
				$cell->a($this->filters->content[$key]);

			if ($this->onProcessHeaderCell($key, $cell))
				$row->a($cell);
		}

		if ($this->onProcessHeaderRow($row))
			$this->thead->a($row);
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
		foreach($this->orderPresets[$sortInfo['sort']] as $kid) {
			if (isset($kid['relative_order']))
				$order=($sortInfo['order'] xor $kid['order'])?"ASC":"DESC";
			elseif (isset($kid['absolute_order']))
				$order=$kid['absolute_order'];
			else
				$order=$kid['order']?"ASC":"DESC";
			$sortInfo['query'][]=array(
				'field'=>$kid['sort'],
				'type'=>$order,
			);
		}
		return $sortInfo;
	}

	function populateRow($keys,$rowData)
	{
		$row=new LPC_HTML_node("tr");
		foreach($keys as $key) {
			if (in_array($key,$this->hiddenFields))
				continue;
			$cell=new LPC_HTML_node("td", false);
			$cell->compact=true;
			$cell->content=htmlspecialchars($rowData[$key]);
			if ($this->onProcessBodyCell($key,$cell,$rowData))
				$row->a($cell);
		}

		if ($this->onProcessBodyRow($row,$rowData))
			$this->tbody->a($row);
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
		$sql=$this->processFilters($sql);
		$sql['select']=array('1');
		unset($sql['order']);
		$rs=$this->queryObject->query($sql, $this->sqlParams);
		$this->totalEntries=$rs->recordCount();
		return $this->totalEntries;
	}

	function getIcon($order)
	{
		$env = $this->ownerDocument->environment;

		switch($env) {
		case LPC_HTML_Document::ENV_HTML:
			return $this->getIconHTML($order);
		case LPC_HTML_Document::ENV_BOOTSTRAP:
			return $this->getIconconBS($order);
		default:
			throw new RuntimeException("Unknown environment!");
		}
	}

	function getIconHTML($order)
	{
		$icon = new LPC_HTML_node("img");
		$icon->setAttr('style', 'margin-bottom:-3px;');

		if ($order == "up")
			$icon->setAttr("src", LPC_ICON_UP_ENABLED);
		else
			$icon->setAttr('src', LPC_ICON_DOWN_ENABLED);

		return $icon;
	}

	function getIconconBS($order)
	{
		$icon = new LPC_HTML_node("span");
		$icon->setClass("glyphicon")->setAttr("style", "margin-left: 3px");
		if ($order == "up")
			$icon->addClass("glyphicon-sort-by-attributes-alt");
		else
			$icon->addClass("glyphicon-sort-by-attributes");

		return $icon;
	}
}
