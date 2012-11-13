<?php

class LPC_Password_mangler
{
	var $salt_prefix='$6$rounds=200000$';
	var $salt_suffix='$';

	function salt($password)
	{
		return crypt(
			$password,
			$this->generateSalt()
		);
	}

	function generateSalt()
	{
		return
			$this->salt_prefix.
			base64_encode(openssl_random_pseudo_bytes(15)).
			$this->salt_suffix
		;
	}

	function matches($password, $salted)
	{
		return $salted == crypt($password, $salted);
	}

}
