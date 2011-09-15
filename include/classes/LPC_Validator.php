<?php

class LPC_Validator
{

	public $rules=array(
		'required', // strlen()>0
		'integer',
		'float',
		'email',
		'less', // strict <
		'more', // strict >
		'min', // <=
		'max', // >=
		'min_length', // strlen()>=
		'max_length', // strlen()<=
		'date', // UNIX timestamp
	);

	private $field_name="";

	public function __construct()
	{
	}

	public function validate($value,$rules,$field_name="")
	{
		$this->field_name=$field_name;
		$results=array();
		foreach($rules as $rule) {
			$result=$this->validateRule($value,$rule);
			if (empty($result))
				continue;
			$results[]=$result;
		}
		return $results;
	}

	public function validateRule($value,$rule)
	{
		if (is_array($rule)) {
			$ruleName=array_shift($rule);
		} else {
			$ruleName=$rule;
			$rule=array();
		}
		if (!in_array($ruleName,$this->rules)) {
			throw new RuntimeException("Unknown rule: ".$ruleName);
		}
		// Empty attributes are only tested against the 'required' rule
		if (!strlen($value) && $ruleName!='required')
			return NULL;
		$method='validate_'.$ruleName;
		return $this->$method($value,$rule);
	}

	public function validate_integer($value,$ruleParams)
	{
		if (preg_match("/^[0-9]+$/",$value))
			return NULL;
		return sprintf(__L("Field %s must be an integer!"),__L($this->field_name));
	}

	public function validate_float($value,$ruleParams)
	{
		if (is_numeric($value))
			return NULL;
		return sprintf(__L("Field %s must be a number!"),__L($this->field_name));
	}

	public function validate_email($value,$ruleParams)
	{
		if (preg_match("/@.*\.[a-z]{2,4}$/",$value))
			return NULL;
		return sprintf(__L("Field %s must be an e-mail address!"),__L($this->field_name));
	}

	public function validate_less($value,$ruleParams)
	{
		if ($value<$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be smaller than %2\$d!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_more($value,$ruleParams)
	{
		if ($value>$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be larger than %2\$d!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_min($value,$ruleParams)
	{
		if ($value>=$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be at least %2\$d!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_max($value,$ruleParams)
	{
		if ($value<=$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be at most %2\$d!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_min_length($value,$ruleParams)
	{
		if (strlen($value)>=$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be at least %2\$d characters long!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_max_length($value,$ruleParams)
	{
		if (strlen($value)<=$ruleParams[0])
			return NULL;
		return sprintf(__L("The value for field %1\$s must be at most %2\$d characters long!"),__L($this->field_name),$ruleParams[0]);
	}

	public function validate_required($value)
	{
		if (strlen($value)) {
			return NULL;
		}
		return sprintf(__L("Field %s is required!"),__L($this->field_name));
	}

	public function validate_date($value)
	{
		if (preg_match("/^[0-9]+$/",$value))
			return NULL;
		return sprintf(__L("Field %s must be a date!"),__L($this->field_name));
	}
}
