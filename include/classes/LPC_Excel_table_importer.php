<?php

class LPC_Excel_table_importer extends LPC_Excel_base
{

	var $startPosition=array(0,0);
	var $endPosition;

	protected $cursor;
	protected $busyCells=array();
	protected $maxX;

	function import($table)
	{
		unset($this->maxX);
		$this->cursor=$this->startPosition;

		$table->render(); // we want to make sure the TABLE is properly prepared
		if (strtolower($table->nodeName)!='table')
			throw new RuntimeException("The object you passed is not a TABLE node!");

		foreach($table->content as $idx=>$row) {
			if (strtolower($row->nodeName)!='tr')
				throw new RuntimeException("The content at index ".$idx." is not a TR node!");

			$this->processRow($row);
		}
		$this->setBorder($this->startPosition,$this->endPosition);
	}

	function processRow($row)
	{
		foreach($row->content as $idx=>$cell) {
			if (!in_array(strtolower($cell->nodeName),array('td','th')))
				throw new RuntimeException("The content at index ".$idx." is neither a TD nor a TH node!");

			$this->populate($cell);

			if (isset($cell->excel) && isset($cell->excel['type']))
				$this->setTDformat($cell->excel['type']);

			$merge=false;
			$mergeTo=$this->cursor;
			if (($col_count=$cell->getAttr('colspan')) && $col_count>1) {
				$merge=true;
				$mergeTo[0]+=$col_count-1;
			}
			if (($row_count=$cell->getAttr('rowspan')) && $row_count>1) {
				$merge=true;
				$mergeTo[1]+=$row_count-1;
			}
			if ($merge)
				$this->mergeTo($mergeTo);
			else
				$this->setBusy($this->cursor);


			$this->advanceColumn();
		}
		if (!isset($this->maxX))
			$this->maxX=$this->cursor[0]-1;

		$this->endPosition=array($this->maxX,$this->cursor[1]);

		$this->advanceRow();
	}

	function setTDformat($TDformat)
	{
		// See constants in /usr/share/pear/PHPExcel/PHPExcel/Style/NumberFormat.php
		$formats=array(
			//'percent'=>PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
			'percent'=>"0.0%",
		);
		if (isset($formats[$TDformat]))
			$format=$formats[$TDformat];
		else
			$format=$TDformat;
		$this->setNumberFormat($this->cursor,$format);
	}

	function mergeTo($mergeTo)
	{
		$this->mergeCells($this->cursor,$mergeTo);
		for($x=$this->cursor[0];$x<=$mergeTo[0];$x++)
			for($y=$this->cursor[1];$y<=$mergeTo[1];$y++)
				$this->setBusy(array($x,$y));
	}

	function setBusy($coord)
	{
		$cx=$coord[0];
		$cy=$coord[1];
		if (!isset($this->busyCells[$cx]))
			$this->busyCells[$cx]=array();
		if (isset($this->busyCells[$cx][$cy]))
			return;
		$this->busyCells[$cx][$cy]=true;
	}

	function isBusy($coord)
	{
		$cx=$coord[0];
		$cy=$coord[1];
		return isset($this->busyCells[$cx]) && isset($this->busyCells[$cx][$cy]);
	}

	private function _advanceRow()
	{
		$this->cursor=array($this->startPosition[0],$this->cursor[1]+1);
	}

	function advanceRow()
	{
		$this->_advanceRow();
		$this->ensureFreeCell(true); // the entire row might be busy
	}

	private function _advanceColumn()
	{
		$this->cursor[0]++;
	}

	function advanceColumn()
	{
		$this->_advanceColumn();
		$this->ensureFreeCell(false); // don't advance the row, even if we spill over
	}

	function ensureFreeCell($allowRowAdvance)
	{
		while(true) {
			if (!$this->isBusy($this->cursor))
				return;
			if (!$allowRowAdvance || !isset($this->maxX) || $this->maxX>$this->cursor[0]) {
				$this->_advanceColumn();
				continue;
			}
			$this->_advanceRow();
		}
	}

	function populate($cell)
	{
		$compact=$cell->compact;
		$cell->compact=true;
		$cell->renderMode='Excel';
		$cell->render(); // yes, we now need to prepare the CELL again, after setting the renderMode
		$this->setCell($this->cursor,$cell->renderContent());
		$cell->compact=$compact;
		$cell->renderMode='HTML'; // this should be safe (at least for now, since it's only used here)
	}
}
