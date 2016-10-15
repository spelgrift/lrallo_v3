<?php

class Val
{

	public function __construct(){}

	public function blank($data)
	{
		if(strlen($data) == 0) {
			return "This field is required.";
		}
	}

	public function url($data)
	{
		if(filter_var($data, FILTER_VALIDATE_URL) === false) {
			return "You must enter a valid URL.";
		}
	}

	public function minlength($data, $arg)
	{
		if (strlen($data) < $arg) {
			return "Your string must be at least $arg long";
		}
	}

	public function maxlength($data, $arg)
	{
		if (strlen($data) > $arg) {
			return "Your string cannot be longer than $arg";
		}
	}

	public function digit($data)
	{
		if (!ctype_digit($data)) {
			return "Your must enter a number";
		}
	}

	public function __call($name, $arguments)
	{
		throw new Exception("$name does not exist inside of: " . __CLASS__);
	}
}