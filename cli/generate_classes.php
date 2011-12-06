<?php

define("LPC_SkipAuthentication",true);

require_once dirname(dirname(__FILE__))."/include/LPC_lib.php";

$generator=new LPC_Class_Generator();

class LPC_Class_Generator
{
	private $baseClass=NULL;
	private $dbKey=NULL;
	private $path=NULL;
	private $db=NULL;
	private $prefix=NULL;

	public function __construct()
	{
		$this->testCLI();
		$this->run();
	}

	private function testCLI()
	{
		if (isset($_SERVER['REQUEST_METHOD'])) {
			echo "This script must run from the command line only.";
			exit;
		}
	}

	private function run()
	{
		$this->processParameters();
		$this->testPath();
		$this->initConnection();
		$this->processBaseClass();
		$this->createBaseClass();
		$this->processTables();
	}

	private function processBaseClass()
	{
		$x=explode("_",$this->baseClass);
		if (count($x)==1) {
			echo "      INFO: Unusual base class name; the prefix will be ".$this->baseClass."!\n";
			$this->prefix=$this->baseClass."_";
		} else {
			$this->prefix=$x[0]."_";
		}
	}

	private function createBaseClass()
	{
		$filename=$this->path."/".$this->baseClass.".php";
		if (file_exists($filename)) {
			echo "      INFO: Base class already exists -- skipping.\n";
			return;
		}
		echo "ACTION: Creating base class in $filename\n";
		$fp=fopen($filename,'w');
		if (!$fp) {
			echo "WARNING: Failed opening base class file ".$filename." for writing. Skipping.\n";
			return;
		}
		fputs($fp,"<?php\n");
		fputs($fp,"\n");
		fputs($fp,"abstract class ".$this->baseClass." extends LPC_Object\n");
		fputs($fp,"{\n");
		fputs($fp,"\tvar \$dbKey='".$this->dbKey."';\n");
		fputs($fp,"}\n");
		fclose($fp);
	}

	private function processTables()
	{
		$rs=$this->db->Execute("SHOW TABLES");
		if (!$rs) {
			echo "ERROR: Failed connecting to database!\n";
			exit;
		}
		while(!$rs->EOF) {
			if (substr($rs->fields[0],0,4)=='LPC_')
				echo "      INFO: Table ".$rs->fields[0]." seems to be LPC -- skipping.\n";
			else
				$this->processTable($rs->fields[0]);
			$rs->MoveNext();
		}
	}

	private function processTable($table)
	{
		$class=$this->prefix.ucfirst($table);
		$filename=$this->path."/".$class.".php";
		if (file_exists($filename)) {
			echo "      INFO: File $filename already exists -- skipping.\n";
			return;
		}
		$fp=fopen($filename,'w');
		if (!$fp) {
			echo "WARNING: Failed opening file $filename for writing -- skipping.\n";
			return;
		}
		$rs=$this->db->Execute("DESCRIBE $table");
		$valid=false;
		while(!$rs->EOF) {
			if (!$valid) {
				if ($rs->fields['Field']=='id') {
					echo "ACTION: Creating file $filename\n";
					$valid=true;
					$this->writeHeader($class,$fp);
					$rs->MoveNext();
					continue;
				} else {
					echo "      INFO: Table $table doesn't start with an ID field -- skipping.\n";
					fclose($fp); 
					unlink($filename);
					return;
				}
			}

			// Get canonical type
			preg_match("/^[a-z]+/",$rs->fields['Type'],$matches);
			$db_type=$matches[0];

			$flags=array();
			$type='';

			// Flags and date-related types
			if (in_array($db_type,array('date','datetime','timestamp'))) {
				// MySQL TIME and YEAR types are NOT supported natively!
				$flags['sqlDate']=true;
				if ($db_type=='timestamp') {
					$flags[]='forceSave';
				}
				if ($db_type=='date') {
					$type='date';
				} else {
					$type='datetime';
				}
			}
			if ($rs->fields['Null']=='YES') {
				$flags['NULL']=true;
			}

			// Other types
			if (in_array($db_type,array('tinyint','smallint','mediumint','bigint'))) {
				$type='integer';
			} elseif (in_array($db_type,array('float','double','real','decimal','numeric'))) {
				$type='float';
			}

			$str=array();
			if ($flags) {
				$str[]="\n\t\t\t\t'flags'=>".$this->exportArray($flags,4,true);
			}
			if ($type) {
				$str[]="\n\t\t\t\t'type'=>".$this->exportArray($type,4,true);
			}
			if ($str) {
				$str=implode(",",$str).",\n\t\t\t";
			} else {
				$str="";
			}
			fputs($fp,"\t\t\t\"".addslashes($rs->fields['Field'])."\"=>array(".$str."),\n");
			$rs->MoveNext();
		}
		fputs($fp,"\t\t);\n");
		$this->writeFooter($table,$fp);
		fclose($fp);
	}

	private function exportArray($var,$indent,$omitFirst)
	{
		$exported=explode("\n",var_export($var,true));
		if (!is_array($exported)) {
			$exported=array($exported);
		}
		$pre=str_repeat("\t",$indent);
		$first=true;
		foreach($exported as $idx=>$line) {
			if ($omitFirst && $first) {
				$first=false;
				continue;
			}
			if (preg_match("/^(\s+)/",$line,$matches)) {
				$line_pre=str_replace("  ","\t",$matches[0]);
			} else {
				$line_pre="";
			}
			$exported[$idx]=$pre.$line_pre.trim($line);
		}
		return implode("\n",$exported);
	}

	private function writeHeader($className,$fp)
	{
		fputs($fp,"<?php\n");
		fputs($fp,"\n");
		fputs($fp,"class $className extends ".$this->baseClass."\n");
		fputs($fp,"{\n");
		fputs($fp,"\tfunction registerDataStructure()\n");
		fputs($fp,"\t{\n");
		fputs($fp,"\t\t\$fields=array(\n");
	}

	private function writeFooter($tableName,$fp)
	{
		fputs($fp,"\n");
		fputs($fp,"\t\t\$depend=array();\n");
		fputs($fp,"\n");
		fputs($fp,"\t\treturn array(\n");
		fputs($fp,"\t\t\t'table_name'=>'$tableName',\n");
		fputs($fp,"\t\t\t'id_field'=>'id',\n");
		fputs($fp,"\t\t\t'fields'=>\$fields,\n");
		fputs($fp,"\t\t\t'depend'=>\$depend\n");
		fputs($fp,"\t\t);\n");
		fputs($fp,"\t}\n");
		fputs($fp,"}\n");
	}

	private function initConnection()
	{
		$this->db=LPC_DB::getConnection($this->dbKey);
	}

	private function testPath()
	{
		if (!$this->path) {
			$this->path=LPC_classes;
		}
		if (!is_dir($this->path)) {
			echo "ERROR: Path ".$this->path." not found!\n";
			exit;
		}
		return true;
	}

	private function processParameters()
	{
		global $argv, $argc;
		if ($argc<3) {
			$this->showHelp();
			exit;
		}
		$this->dbKey=$argv[1];
		$this->baseClass=$argv[2];
		if (isset($argv[3])) {
			$this->path=$argv[3];
			while(substr($this->path,-1)=='/') {
				$this->path=substr($this->path,0,-1);
			}
		} else {
			echo "WARNING: Using default path ".LPC_classes."\n";
		}
	}

	private function showHelp()
	{
		echo "Usage:\n";
		echo "php generate_classes.php <dbKey> <base class> [<path>]\n";
		echo "Where\n";
		echo "* dbKey is the name of a database key registered with LPC_DB.\n";
		echo "* base class is the name of the base class for this application.\n";
		echo "  For example POLL_Base, PROF_Base, BLOG_Base, etc.\n";
		echo "  If the base class doesn't exist it will also be created.\n";
		echo "* path is an optional parameter specifying where the classes should be written.\n";
		echo "  If left blank, the default path is used:\n";
		echo "  ".LPC_classes."\n";
	}
}
