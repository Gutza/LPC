<?php
/**
* LPC Barcode Class
* Bogdan Stancescu <bogdan@moongate.ro>, November 2006
* @package LPC
*/
	
class LPC_Barcode
{

	/**
	* The barcode specification; only barcode 128 supported at this time.
	*/
	var $type='128';
	
	/**
	* Which coding should be used? Defaults to "Code B" (alphanumeric),
	* but "Code A" (alphanumeric+control characters) and "Code C"
	* (numeric) are also supported for barcode 128.
	*/
	var $code='Code B';
	
	var $h_multiplier=1;
	var $v_size=91;

	/**
	* Whether to include the text label with the code
	*/
	var $include_label=true;

	/**
	* Image format. Only PNG supported for now.
	*/
	var $format='PNG';

	/**
	* Whether to include the quiet zones in the image. Defaults to true.
	*/
	var $include_quiet=true;
	
	/**
	* Constructor; does nothing at this time except checking whether GD is
	* installed, and setting the barcode type (default 128; none others
	* supported at this time).
	*/
	function __construct($type='128')
	{
		if (!function_exists('imagecreatetruecolor'))
			throw new RuntimeException("You don't seem to have the GD extension for PHP ".
				"installed; the barcode class relies on it, therefore you won't ".
				"get any barcodes without it. Please see this URL for details: ".
				"http://www.php.net/gd");

		$this->type=$type;
	}
	
	/**
	* This is the barcode generation wrapper; it calls the various specific
	* barcode generation methods.
	*/
	function generate($data,$format='PNG')
	{
		$this->data=$data;
		$this->format=$format;
		switch($this->type) {
			case '128':
				$success=$this->generate128();
				break;
			default:
				throw new RuntimeException("Unknown barcode type: {$this->type}");
		}
		if (!$success)
			return false;

		return $this->barcode_image;
	}
	
	/**
	* This method generates Barcode 128 codes. At this time, only Code Set A
	* is supported.
	*/
	function generate128()
	{
		// Reading the specs
		if (!$this->barcode128_specs())
			// We already complained
			return false;

		if (!$this->generateCode128())
			return false;

		if (!$this->generateImage())
			return false;

		return true;
	}
	
	/**
	* This method generates an ASCII representation of a barcode 128:
	* bars are represented as "|" and spaces as "."
	*/
	function generateCode128()
	{
		switch($this->code) {
			case 'Code A':
				$idx='codeA';
				$string=chr(103); // Start A
				break;
			case 'Code B':
				$idx='codeB';
				$string=chr(104); // Start B
				break;
			case 'Code C':
				$idx='codeC';
				$string=chr(105); // Start C
				break;
			default:
				throw new RuntimeException(
					"Unknown code for barcode 128: \"{$this->code}\". ".
					"Only 'Code A', 'Code B' and 'Code C' supported."
				);
		}
		$checksum=ord($string);
		for($i=0;$i<strlen($this->data);$i++) {
			$val=false;
			for($j=0;$j<count($this->specs128);$j++) {
				if ($this->specs128[$j][$idx]==$this->data[$i]) {
					$val=$j;
				}
			}
			if ($val===false)
				throw new RuntimeException(
					"Failed to represent the character at index $i in ".
					"Barcode 128/{$this->code} (the character was {$this->data[$i]}, ".
					"ASCII ".ord($this->data[$i]).", from the string \"{$this->data}\")."
				);

			$string.=chr($val);
			$checksum+=($i+1)*$val;
		}
		$string.=chr($checksum%103).chr(106);
		$barcode='';
		for($i=0;$i<strlen($string);$i++) {
			$fill='|';
			for(
				$code_progress=0;
				$code_progress<strlen($this->specs128[ord($string[$i])]['barcode']);
				$code_progress++
			) {
				$barcode.=str_repeat($fill,$this->specs128[ord($string[$i])]['barcode'][$code_progress]);
				if ($fill=='|') {
					$fill='.';
				} else {
					$fill='|';
				}
			}
		}
		$this->barcode_data=$barcode;
		return $barcode;
	}
	
	function generateImage()
	{
		$pw=strlen($this->barcode_data)*$this->h_multiplier;
		$shift=0;
		if ($this->include_quiet) {
			$shift=10*$this->h_multiplier;
			$pw+=2*$shift;
		}
		$ph=$this->v_size;
		$pic=imagecreatetruecolor($pw,$ph);
		$black=imagecolorallocate($pic,0,0,0);
		$white=imagecolorallocate($pic,255,255,255);
		imagefilledrectangle($pic,0,0,$pw,$ph,$white);
		for($i=0;$i<strlen($this->barcode_data);$i++) {
			if ($this->barcode_data[$i]=='.')
				continue;

			imagefilledrectangle($pic,$shift+$i*$this->h_multiplier,0,$shift+($i+1)*$this->h_multiplier-1,$this->v_size,$black);
		}

		if ($this->include_label) {
			$font=4;
			$w=strlen($this->data)*imagefontwidth($font);
			$h=imagefontheight($font);
			$tx1=round($pw/2-$w/2);
			$tx2=round($pw/2+$w/2);
			$ty1=$ph-$h-2;
			$ty2=$ph-2;
			imagefilledrectangle($pic,$tx1-$this->h_multiplier*2,$ty1-$this->h_multiplier*2,$tx2+$this->h_multiplier*2,$ty2+$this->h_multiplier*2,$white);
			imagestring($pic,$font,$tx1,$ty1,$this->data,$black);
		}
		ob_start();

		// Change to a switch() when more formats are added -- too lazy now.
		if (strtoupper($this->format)=='PNG')
			imagepng($pic);
		else
			throw new RuntimeException("Unknown image format: {$this->format}");

		$this->barcode_image=ob_get_clean();
		imagedestroy($pic);
		return true;
	}

	function barcode128_specs()
	{
		if ($this->specs128)
			return NULL;

		$this->specs128=array (
			0 =>
			array (
				'codeA' => ' ',
				'codeB' => ' ',
				'codeC' => 'chr(0)',
				'barcode' => '212222',
			),
			1 =>
			array (
				'codeA' => '!',
				'codeB' => '!',
				'codeC' => 'chr(1)',
				'barcode' => '222122',
			),
			2 =>
			array (
				'codeA' => '"',
				'codeB' => '"',
				'codeC' => 'chr(2)',
				'barcode' => '222221',
			),
			3 =>
			array (
				'codeA' => '#',
				'codeB' => '#',
				'codeC' => 'chr(3)',
				'barcode' => '121223',
			),
			4 =>
			array (
				'codeA' => '$',
				'codeB' => '$',
				'codeC' => 'chr(4)',
				'barcode' => '121322',
			),
			5 =>
			array (
				'codeA' => '%',
				'codeB' => '%',
				'codeC' => 'chr(5)',
				'barcode' => '131222',
			),
			6 =>
			array (
				'codeA' => '&',
				'codeB' => '&',
				'codeC' => 'chr(6)',
				'barcode' => '122213',
			),
			7 =>
			array (
				'codeA' => '\'',
				'codeB' => '\'',
				'codeC' => 'chr(7)',
				'barcode' => '122312',
			),
			8 =>
			array (
				'codeA' => '(',
				'codeB' => '(',
				'codeC' => 'chr(8)',
				'barcode' => '132212',
			),
			9 =>
			array (
				'codeA' => ')',
				'codeB' => ')',
				'codeC' => 'chr(9)',
				'barcode' => '221213',
			),
			10 =>
			array (
				'codeA' => '*',
				'codeB' => '*',
				'codeC' => 'chr(10)',
				'barcode' => '221312',
			),
			11 =>
			array (
				'codeA' => '+',
				'codeB' => '+',
				'codeC' => 'chr(11)',
				'barcode' => '231212',
			),
			12 =>
			array (
				'codeA' => ',',
				'codeB' => ',',
				'codeC' => 'chr(12)',
				'barcode' => '112232',
			),
			13 =>
			array (
				'codeA' => '-',
				'codeB' => '-',
				'codeC' => 'chr(13)',
				'barcode' => '122132',
			),
			14 =>
			array (
				'codeA' => '.',
				'codeB' => '.',
				'codeC' => 'chr(14)',
				'barcode' => '122231',
			),
			15 =>
			array (
				'codeA' => '/',
				'codeB' => '/',
				'codeC' => 'chr(15)',
				'barcode' => '113222',
			),
			16 =>
			array (
				'codeA' => '0',
				'codeB' => '0',
				'codeC' => 'chr(16)',
				'barcode' => '123122',
			),
			17 =>
			array (
				'codeA' => '1',
				'codeB' => '1',
				'codeC' => 'chr(17)',
				'barcode' => '123221',
			),
			18 =>
			array (
				'codeA' => '2',
				'codeB' => '2',
				'codeC' => 'chr(18)',
				'barcode' => '223211',
			),
			19 =>
			array (
				'codeA' => '3',
				'codeB' => '3',
				'codeC' => 'chr(19)',
				'barcode' => '221132',
			),
			20 =>
			array (
				'codeA' => '4',
				'codeB' => '4',
				'codeC' => 'chr(20)',
				'barcode' => '221231',
			),
			21 =>
			array (
				'codeA' => '5',
				'codeB' => '5',
				'codeC' => 'chr(21)',
				'barcode' => '213212',
			),
			22 =>
			array (
				'codeA' => '6',
				'codeB' => '6',
				'codeC' => 'chr(22)',
				'barcode' => '223112',
			),
			23 =>
			array (
				'codeA' => '7',
				'codeB' => '7',
				'codeC' => 'chr(23)',
				'barcode' => '312131',
			),
			24 =>
			array (
				'codeA' => '8',
				'codeB' => '8',
				'codeC' => 'chr(24)',
				'barcode' => '311222',
			),
			25 =>
			array (
				'codeA' => '9',
				'codeB' => '9',
				'codeC' => 'chr(25)',
				'barcode' => '321122',
			),
			26 =>
			array (
				'codeA' => ':',
				'codeB' => ':',
				'codeC' => 'chr(26)',
				'barcode' => '321221',
			),
			27 =>
			array (
				'codeA' => ';',
				'codeB' => ';',
				'codeC' => 'chr(27)',
				'barcode' => '312212',
			),
			28 =>
			array (
				'codeA' => '<',
				'codeB' => '<',
				'codeC' => 'chr(28)',
				'barcode' => '322112',
			),
			29 =>
			array (
				'codeA' => '=',
				'codeB' => '=',
				'codeC' => 'chr(29)',
				'barcode' => '322211',
			),
			30 =>
			array (
				'codeA' => '>',
				'codeB' => '>',
				'codeC' => 'chr(30)',
				'barcode' => '212123',
			),
			31 =>
			array (
				'codeA' => '?',
				'codeB' => '?',
				'codeC' => 'chr(31)',
				'barcode' => '212321',
			),
			32 =>
			array (
				'codeA' => '@',
				'codeB' => '@',
				'codeC' => ' ',
				'barcode' => '232121',
			),
			33 =>
			array (
				'codeA' => 'A',
				'codeB' => 'A',
				'codeC' => '!',
				'barcode' => '111323',
			),
			34 =>
			array (
				'codeA' => 'B',
				'codeB' => 'B',
				'codeC' => '"',
				'barcode' => '131123',
			),
			35 =>
			array (
				'codeA' => 'C',
				'codeB' => 'C',
				'codeC' => '#',
				'barcode' => '131321',
			),
			36 =>
			array (
				'codeA' => 'D',
				'codeB' => 'D',
				'codeC' => '$',
				'barcode' => '112313',
			),
			37 =>
			array (
				'codeA' => 'E',
				'codeB' => 'E',
				'codeC' => '%',
				'barcode' => '132113',
			),
			38 =>
			array (
				'codeA' => 'F',
				'codeB' => 'F',
				'codeC' => '&',
				'barcode' => '132311',
			),
			39 =>
			array (
				'codeA' => 'G',
				'codeB' => 'G',
				'codeC' => '\'',
				'barcode' => '211313',
			),
			40 =>
			array (
				'codeA' => 'H',
				'codeB' => 'H',
				'codeC' => '(',
				'barcode' => '231113',
			),
			41 =>
			array (
				'codeA' => 'I',
				'codeB' => 'I',
				'codeC' => ')',
				'barcode' => '231311',
			),
			42 =>
			array (
				'codeA' => 'J',
				'codeB' => 'J',
				'codeC' => '*',
				'barcode' => '112133',
			),
			43 =>
			array (
				'codeA' => 'K',
				'codeB' => 'K',
				'codeC' => '+',
				'barcode' => '112331',
			),
			44 =>
			array (
				'codeA' => 'L',
				'codeB' => 'L',
				'codeC' => ',',
				'barcode' => '132131',
			),
			45 =>
			array (
				'codeA' => 'M',
				'codeB' => 'M',
				'codeC' => '-',
				'barcode' => '113123',
			),
			46 =>
			array (
				'codeA' => 'N',
				'codeB' => 'N',
				'codeC' => '.',
				'barcode' => '113321',
			),
			47 =>
			array (
				'codeA' => 'O',
				'codeB' => 'O',
				'codeC' => '/',
				'barcode' => '133121',
			),
			48 =>
			array (
				'codeA' => 'P',
				'codeB' => 'P',
				'codeC' => '0',
				'barcode' => '313121',
			),
			49 =>
			array (
				'codeA' => 'Q',
				'codeB' => 'Q',
				'codeC' => '1',
				'barcode' => '211331',
			),
			50 =>
			array (
				'codeA' => 'R',
				'codeB' => 'R',
				'codeC' => '2',
				'barcode' => '231131',
			),
			51 =>
			array (
				'codeA' => 'S',
				'codeB' => 'S',
				'codeC' => '3',
				'barcode' => '213113',
			),
			52 =>
			array (
				'codeA' => 'T',
				'codeB' => 'T',
				'codeC' => '4',
				'barcode' => '213311',
			),
			53 =>
			array (
				'codeA' => 'U',
				'codeB' => 'U',
				'codeC' => '5',
				'barcode' => '213131',
			),
			54 =>
			array (
				'codeA' => 'V',
				'codeB' => 'V',
				'codeC' => '6',
				'barcode' => '311123',
			),
			55 =>
			array (
				'codeA' => 'W',
				'codeB' => 'W',
				'codeC' => '7',
				'barcode' => '311321',
			),
			56 =>
			array (
				'codeA' => 'X',
				'codeB' => 'X',
				'codeC' => '8',
				'barcode' => '331121',
			),
			57 =>
			array (
				'codeA' => 'Y',
				'codeB' => 'Y',
				'codeC' => '9',
				'barcode' => '312113',
			),
			58 =>
			array (
				'codeA' => 'Z',
				'codeB' => 'Z',
				'codeC' => ':',
				'barcode' => '312311',
			),
			59 =>
			array (
				'codeA' => '[',
				'codeB' => '[',
				'codeC' => ';',
				'barcode' => '332111',
			),
			60 =>
			array (
				'codeA' => '\\',
				'codeB' => '\\',
				'codeC' => '<',
				'barcode' => '314111',
			),
			61 =>
			array (
				'codeA' => ']',
				'codeB' => ']',
				'codeC' => '=',
				'barcode' => '221411',
			),
			62 =>
			array (
				'codeA' => '^',
				'codeB' => '^',
				'codeC' => '>',
				'barcode' => '431111',
			),
			63 =>
			array (
				'codeA' => '_',
				'codeB' => '_',
				'codeC' => '?',
				'barcode' => '111224',
			),
			64 =>
			array (
				'codeA' => 'chr(0)',
				'codeB' => '`',
				'codeC' => '@',
				'barcode' => '111422',
			),
			65 =>
			array (
				'codeA' => 'chr(1)',
				'codeB' => 'a',
				'codeC' => 'A',
				'barcode' => '121124',
			),
			66 =>
			array (
				'codeA' => 'chr(2)',
				'codeB' => 'b',
				'codeC' => 'B',
				'barcode' => '121421',
			),
			67 =>
			array (
				'codeA' => 'chr(3)',
				'codeB' => 'c',
				'codeC' => 'C',
				'barcode' => '141122',
			),
			68 =>
			array (
				'codeA' => 'chr(4)',
				'codeB' => 'd',
				'codeC' => 'D',
				'barcode' => '141221',
			),
			69 =>
			array (
				'codeA' => 'chr(5)',
				'codeB' => 'e',
				'codeC' => 'E',
				'barcode' => '112214',
			),
			70 =>
			array (
				'codeA' => 'chr(6)',
				'codeB' => 'f',
				'codeC' => 'F',
				'barcode' => '112412',
			),
			71 =>
			array (
				'codeA' => 'chr(7)',
				'codeB' => 'g',
				'codeC' => 'G',
				'barcode' => '122114',
			),
			72 =>
			array (
				'codeA' => 'chr(8)',
				'codeB' => 'h',
				'codeC' => 'H',
				'barcode' => '122411',
			),
			73 =>
			array (
				'codeA' => 'chr(9)',
				'codeB' => 'i',
				'codeC' => 'I',
				'barcode' => '142112',
			),
			74 =>
			array (
				'codeA' => 'chr(10)',
				'codeB' => 'j',
				'codeC' => 'J',
				'barcode' => '142211',
			),
			75 =>
			array (
				'codeA' => 'chr(11)',
				'codeB' => 'k',
				'codeC' => 'K',
				'barcode' => '241211',
			),
			76 =>
			array (
				'codeA' => 'chr(12)',
				'codeB' => 'l',
				'codeC' => 'L',
				'barcode' => '221114',
			),
			77 =>
			array (
				'codeA' => 'chr(13)',
				'codeB' => 'm',
				'codeC' => 'M',
				'barcode' => '413111',
			),
			78 =>
			array (
				'codeA' => 'chr(14)',
				'codeB' => 'n',
				'codeC' => 'N',
				'barcode' => '241112',
			),
			79 =>
			array (
				'codeA' => 'chr(15)',
				'codeB' => 'o',
				'codeC' => 'O',
				'barcode' => '134111',
			),
			80 =>
			array (
				'codeA' => 'chr(16)',
				'codeB' => 'p',
				'codeC' => 'P',
				'barcode' => '111242',
			),
			81 =>
			array (
				'codeA' => 'chr(17)',
				'codeB' => 'q',
				'codeC' => 'Q',
				'barcode' => '121142',
			),
			82 =>
			array (
				'codeA' => 'chr(18)',
				'codeB' => 'r',
				'codeC' => 'R',
				'barcode' => '121241',
			),
			83 =>
			array (
				'codeA' => 'chr(19)',
				'codeB' => 's',
				'codeC' => 'S',
				'barcode' => '114212',
			),
			84 =>
			array (
				'codeA' => 'chr(20)',
				'codeB' => 't',
				'codeC' => 'T',
				'barcode' => '124112',
			),
			85 =>
			array (
				'codeA' => 'chr(21)',
				'codeB' => 'u',
				'codeC' => 'U',
				'barcode' => '124211',
			),
			86 =>
			array (
				'codeA' => 'chr(22)',
				'codeB' => 'v',
				'codeC' => 'V',
				'barcode' => '411212',
			),
			87 =>
			array (
				'codeA' => 'chr(23)',
				'codeB' => 'w',
				'codeC' => 'W',
				'barcode' => '421112',
			),
			88 =>
			array (
				'codeA' => 'chr(24)',
				'codeB' => 'x',
				'codeC' => 'X',
				'barcode' => '421211',
			),
			89 =>
			array (
				'codeA' => 'chr(25)',
				'codeB' => 'y',
				'codeC' => 'Y',
				'barcode' => '212141',
			),
			90 =>
			array (
				'codeA' => 'chr(26)',
				'codeB' => 'z',
				'codeC' => 'Z',
				'barcode' => '214121',
			),
			91 =>
			array (
				'codeA' => 'chr(27)',
				'codeB' => '{',
				'codeC' => '[',
				'barcode' => '412121',
			),
			92 =>
			array (
				'codeA' => 'chr(28)',
				'codeB' => '|',
				'codeC' => '\\',
				'barcode' => '111143',
			),
			93 =>
			array (
				'codeA' => 'chr(29)',
				'codeB' => '}',
				'codeC' => ']',
				'barcode' => '111341',
			),
			94 =>
			array (
				'codeA' => 'chr(30)',
				'codeB' => '~',
				'codeC' => '^',
				'barcode' => '131141',
			),
			95 =>
			array (
				'codeA' => 'chr(31)',
				'codeB' => 'chr(127)', // DEL
				'codeC' => '_',
				'barcode' => '114113',
			),
			96 =>
			array (
				'codeA' => 'chr(196)', // FNC 3
				'codeB' => 'chr(196)', // FNC 3
				'codeC' => '`',
				'barcode' => '114311',
			),
			97 =>
			array (
				'codeA' => 'chr(197)', // FNC 2
				'codeB' => 'chr(197)', // FNC 2
				'codeC' => 'a',
				'barcode' => '411113',
			),
			98 =>
			array (
				'codeA' => 'chr(198)', // SHIFT
				'codeB' => 'chr(198)', // SHIFT
				'codeC' => 'b',
				'barcode' => '411311',
			),
			99 =>
			array (
				'codeA' => 'chr(199)', // CODE C
				'codeB' => 'chr(199)', // CODE C
				'codeC' => 'c',
				'barcode' => '113141',
			),
			100 =>
			array (
				'codeA' => 'chr(200)', // CODE B
				'codeB' => 'chr(200)', // FNC 4
				'codeC' => 'chr(200)', // CODE B
				'barcode' => '114131',
			),
			101 =>
			array (
				'codeA' => 'chr(201)', // FNC 4
				'codeB' => 'chr(201)', // CODE A
				'codeC' => 'chr(201)', // CODE A
				'barcode' => '311141',
			),
			102 => // FNC 1
			array (
				'codeA' => 'chr(202)',
				'codeB' => 'chr(202)',
				'codeC' => 'chr(202)',
				'barcode' => '411131',
			),
			103 => // Start A
			array (
				'codeA' => 'chr(203)',
				'codeB' => 'chr(203)',
				'codeC' => 'chr(203)',
				'barcode' => '211412',
			),
			104 => // Start B
			array (
				'codeA' => 'chr(204)',
				'codeB' => 'chr(204)',
				'codeC' => 'chr(204)',
				'barcode' => '211214',
			),
			105 => // Start C
			array (
				'codeA' => 'chr(205)',
				'codeB' => 'chr(205)',
				'codeC' => 'chr(205)',
				'barcode' => '211232',
			),
			106 =>
			array ( // Stop
				'codeA' => 'chr(206)',
				'codeB' => 'chr(206)',
				'codeC' => 'chr(206)',
				'barcode' => '2331112',
			),
		);
		return true;
	}
}
