<?php

require_once "PHPExcel/PHPExcel.php";

class LPC_Excel_base
{
	var $excel;

	var $borderStyle = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	function __construct($object=NULL)
	{
		if ($object===NULL) {
			$this->excel=new PHPExcel();
			$this->excel->setActiveSheetIndex(0);
			return;
		}

		if (!is_object($object))
			throw new RuntimeException("The base object must be an object!");

		if ($object instanceof LPC_Excel_base) {
			$this->excel=$object->excel;
			return;
		}

		if ($object instanceof PHPExcel) {
			$this->excel=$object;
			return;
		}

		throw new RuntimeException("Unknown base class: ".get_class($object));
	}

	// LPC -> Excel (array -> string)
	function coord_L2E($coord)
	{
		return PHPExcel_Cell::stringFromColumnIndex($coord[0]).($coord[1]+1);
	}

	// Excel -> LPC (string -> array)
	function coord_E2L($coord)
	{
		$Xcoord=PHPExcel_Cell::coordinateFromString($coord);
		return array(
			PHPExcel_Cell::columnIndexFromString($Xcoord[0])-1,
			$Xcoord[1]-1
		);
	}

	function setCell($coord,$content)
	{
		$this->excel->getActiveSheet()->SetCellValue($this->coord_L2E($coord),$content);
	}

	function getCell($coord)
	{
		return $this->excel->getActiveSheet()->getCell($this->coord_L2E($coord))->getCalculatedValue();
	}

	function getCellDate($coord)
	{
		return PHPExcel_Shared_Date::ExcelToPHP($this->getCell($coord));
	}

	function setBorder($coord1,$coord2=false)
	{
		$c=$this->coord_L2E($coord1);
		if ($coord2)
			$c.=":".$this->coord_L2E($coord2);
		$this->excel->getActiveSheet()->getStyle($c)->applyFromArray($this->borderStyle);
	}

	function mergeCells($coord1,$coord2)
	{
		$c1=$this->coord_L2E($coord1);
		$c2=$this->coord_L2E($coord2);
		$this->excel->getActiveSheet()->mergeCells($c1.":".$c2);
	}

	function export($filename)
	{
		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$objWriter->save($filename);
	}

	function import5($filename)
	{
		$objReader = new PHPExcel_Reader_Excel5();
		return $this->importFromReader($objReader,$filename);
	}

	function import2007($filename)
	{
		$objReader = new PHPExcel_Reader_Excel2007();
		return $this->importFromReader($objReader,$filename);
	}

	private function importFromReader($objReader,$filename)
	{
		$this->excel=$objReader->load($filename);
		return $this->excel->getSheetCount();
	}

	function import($filename)
	{
		if ($sheetCount=$this->import2007($filename))
			return $sheetCount;
		return $this->import5($filename);
	}

	function setNumberFormat($coord,$format)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->getNumberFormat()->setFormatCode($format);
	}

	function setFontSize($coord,$size)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->getFont()->setSize($size);
	}

	function setWidth($column,$width)
	{
		$colName=substr($this->coord_L2E(array($column,1)),0,-1);
		$this->excel->getActiveSheet()->getColumnDimension($colName)->setWidth($width);
	}

	function styleFromArray($coord,$style)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->applyFromArray($style);
	}
}

