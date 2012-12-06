<?php

require_once "PHPExcel/PHPExcel.php";

/*

If you want to export PDF files:
1. Download the fonts:
	svn co https://PHPExcel.svn.codeplex.com/svn/trunk/Classes/PHPExcel/Shared/PDF/fonts
2. Tell PHPExcel where you downloaded the fonts:
	define('K_PATH_FONTS',<directory>);

*/

/**
* The generic LPC class for Excel spreadsheets
* @author Bogdan Stancescu <bogdan@moongate.ro>
* @copyright Copyright (c) 2011, Bogdan Stancescu
* @license http://www.gnu.org/licenses/gpl.html GNU Public License v3 or later
*/
class LPC_Excel_base
{
	public $excel;

	public $borderStyle = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

	public function __construct($object=NULL)
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
	public function coord_L2E($coord)
	{
		return PHPExcel_Cell::stringFromColumnIndex($coord[0]).($coord[1]+1);
	}

	// Excel -> LPC (string -> array)
	public function coord_E2L($coord)
	{
		$Xcoord=PHPExcel_Cell::coordinateFromString($coord);
		return array(
			PHPExcel_Cell::columnIndexFromString($Xcoord[0])-1,
			$Xcoord[1]-1
		);
	}

	public function setCell($coord,$content)
	{
		$this->excel->getActiveSheet()->SetCellValue($this->coord_L2E($coord),$content);
	}

	public function getCell($coord)
	{
		return $this->excel->getActiveSheet()->getCell($this->coord_L2E($coord))->getCalculatedValue();
	}

	public function getCellDate($coord)
	{
		return PHPExcel_Shared_Date::ExcelToPHP($this->getCell($coord));
	}

	public function setBorder($coord1,$coord2=false)
	{
		$c=$this->coord_L2E($coord1);
		if ($coord2)
			$c.=":".$this->coord_L2E($coord2);
		$this->excel->getActiveSheet()->getStyle($c)->applyFromArray($this->borderStyle);
	}

	public function mergeCells($coord1,$coord2)
	{
		$c1=$this->coord_L2E($coord1);
		$c2=$this->coord_L2E($coord2);
		$this->excel->getActiveSheet()->mergeCells($c1.":".$c2);
	}

	public function export($final_filename, $temp_filename=NULL)
	{
		$finalFilename=$tempFilename=$final_filename;
		if (!empty($temp_filename))
			$tempFilename=$temp_filename;

		$Xfile=explode(".",$finalFilename);
		$fileX=end($Xfile);
		switch($fileX) {
			case 'xls':
				return $this->exportWithType($tempFilename, 'Excel5');
			case 'pdf':
				return $this->exportWithType($tempFilename, 'PDF');
			default:
				return $this->exportWithType($tempFilename, 'Excel2007');
		}
	}

	public function exportWithType($filename, $filetype)
	{
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, $filetype);
		$objWriter->save($filename);
	}

	// Deprecated
	public function import5($filename)
	{
		return $this->import($filename);
	}

	// Deprecated
	public function import2007($filename)
	{
		return $this->import($filename);
	}

	public function import($filename)
	{
		$this->excel = PHPExcel_IOFactory::load($filename);
		return $this->excel->getSheetCount();
	}

	public function setNumberFormat($coord,$format)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->getNumberFormat()->setFormatCode($format);
	}

	public function getNumberFormat($coord)
	{
		return $this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->getNumberFormat()->getFormatCode();
	}

	public function getFormattedCell($coord)
	{
		return PHPExcel_Style_NumberFormat::toFormattedString(
			$this->getCell($coord),
			$this->getNumberFormat($coord)
		);
	}

	public function setFontSize($coord,$size)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->getFont()->setSize($size);
	}

	public function setWidth($column,$width)
	{
		$colName=substr($this->coord_L2E(array($column,1)),0,-1);
		$this->excel->getActiveSheet()->getColumnDimension($colName)->setWidth($width);
	}

	public function setHeight($row,$height)
	{
		$rowName=substr($this->coord_L2E(array(1,$row)),1);
		$this->excel->getActiveSheet()->getRowDimension($rowName)->setRowHeight($height);
	}

	public function styleFromArray($coord,$style)
	{
		$this->excel->getActiveSheet()->getStyle($this->coord_L2E($coord))->applyFromArray($style);
	}
}

