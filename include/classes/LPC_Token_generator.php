<?php

/**
* This class generates a unique token for the specified field in the specified class, populates it and saves the object.
* Race conditions are avoided as long as this runs on a single server.
* Tokens are guaranteed to only contain alphanumeric characters, plus "-" and "_" for base64.
*
* Note: This SAVES the object, so make sure you account for that in your code's logic.
* Bogdan, 2011-02
*/
class LPC_Token_generator
{
/*
// Code sample:

<?php

$foo = new LPC_My_object();
$foo->setAttrs(array(
	"bar" => "bar",
	"baz" => "baz",
));

$tg = new LPC_Token_generator($foo, "hash");
$tg->length = 4;
$tg_options = LPC_Token_generator::OPT_HUMAN_UPPERCASE;

if ($tg->generate())
	echo "Object foo was saved!";
else
	echo "Failed saving object foo!";

?>

*/
	var $method=self::METHOD_SHA1; // choose any of the METHOD constants below
	var $encoding=self::ENCODING_BASE64; // plain or base64; applied before trimming
	var $length=40; // Specifies the maximum length of the final string
	var $options=0;

	/**
	* An array that dictates the format of the token.
	*
	* This MUST be an associative array with two keys: "pattern" and "replacement".
	* If set, the token is passed through preg_replace with these two parameters,
	* and the resulting value is then checked for uniqueness and populated in the
	* database.
	*/
	var $format = array(
		"pattern" => "",
		"replacement" => "",
	);

	const METHOD_SHA1 = "sha1";
	const METHOD_SHA256 = "sha256";
	const METHOD_SHA512 = "sha512";

	const ENCODING_BASE64 = "base64";
	const ENCODING_PLAIN = "plain";

	const OPT_NO_ZERO = 1;
	const OPT_NO_O = 2;
	const OPT_NO_ONE = 4;
	const OPT_NO_L = 8;
	const OPT_ALL_UPPERCASE = 16;
	const OPT_ALL_LOWERCASE = 32;
	const OPT_START_END_ALPHANUM = 64;
	const OPT_ALL_ALPHANUM = 128;

	const OPT_HUMAN_UPPERCASE = 147; // OPT_ALL_ALPHANUM + OPT_ALL_UPPERCASE + OPT_NO_ZERO + OPT_NO_O
	const OPT_HUMAN_LOWERCASE = 175; // OPT_ALL_ALPHANUM + OPT_ALL_LOWERCASE + OPT_NO_ZERO + OPT_NO_O + OPT_NO_L + OPT_NO_ONE
	const OPT_HUMAN_MIXEDCASE = 143; // OPT_ALL_ALPHANUM + OPT_NO_ZERO + OPT_NO_O + OPT_NO_L + OPT_NO_ONE

	public $object = NULL;
	public $field = NULL;

	function __construct($object = NULL, $field = NULL)
	{
		$this->object = $object;
		$this->field = $field;
	}

	function generate()
	{
		$fp = fopen(__FILE__,'r');
		flock($fp, LOCK_EX);

		$success = false;
		while(!$success) {
			$token = $this->instantToken();
			$success = !$this->object->searchCount($this->field, $token);
		}
		$this->object->setAttr($this->field, $token);
		$success = $this->object->save();

		flock($fp, LOCK_UN);
		fclose($fp);

		return $success;
	}

	function instantToken()
	{
		switch($this->method) {
			case self::METHOD_SHA1:
				$rawSize = 20;
				break;
			case self::METHOD_SHA256:
				$rawSize = 32;
				break;
			case self::METHOD_SHA512:
				$rawSize = 64;
				break;
			default:
				throw new RuntimeException("Unknown token generation method (".$this->method.")");
		}
		return
			$this->processOptions(
				$this->encode(
					hash(
						$this->method,
						openssl_random_pseudo_bytes($rawSize),
						true
					)
				)
			);
	}

	function processOptions($token)
	{
		if ($this->length < strlen($token))
			$token = substr($token, 0, $this->length);

		if ($this->options & self::OPT_ALL_ALPHANUM) {
			for ($i=0; $i<strlen($token); $i++)
				$token[$i] = $this->to_alphanum($token[$i]);
		} elseif ($this->options & self::OPT_START_END_ALPHANUM) {
			$token[0] = $this->to_alphanum($token[0]);
			$last = strlen($token)-1;
			$token[$last] = $this->to_alphanum($token[$last]);
		}

		if ($this->options & self::OPT_NO_ZERO)
			$token = str_replace('0', 'Z', $token);
		if ($this->options & self::OPT_NO_O)
			$token = str_replace('O', 'E', str_replace('o', 'm', $token));
		if ($this->options & self::OPT_NO_ONE)
			$token = str_replace('1', 'X', $token);
		if ($this->options & self::OPT_NO_L)
			$token = str_replace('L', 'U', str_replace('o', 't', $token));

		if ($this->options & self::OPT_ALL_UPPERCASE)
			$token = strtoupper($token);
		elseif ($this->options & self::OPT_ALL_LOWERCASE)
			$token = strtolower($token);

		if (strlen($this->format["pattern"]) && strlen($this->format["replacement"]))
			$token = preg_replace($this->format["pattern"], $this->format["replacement"], $token);
		return $token;
	}

	private function to_alphanum($char)
	{
		if (preg_match("/[0-9a-zA-Z]/", $char))
			// Most of the time, this is the case
			return $char;

		$ascii = rand(0, 61);
		if ($ascii < 10) // 0-9
			return chr($ascii + ord('0'));
		$ascii -= 10;
		if ($ascii < 26) // uppercase
			return chr($ascii + ord('A'));
		$ascii -= 26;
		return chr($ascii + ord('a'));
	}

	function encode($token)
	{
		switch($this->encoding) {
			case self::ENCODING_PLAIN:
				return bin2hex($token);
			case self::ENCODING_BASE64:
				return
					str_replace("+", "-",
						str_replace("/", "_",
							substr(
								base64_encode($token),
							0, -1)
						)
					);
			default:
				throw new RuntimeException("Unknown encoding (".$this->encoding.")");
		}
	}

}
