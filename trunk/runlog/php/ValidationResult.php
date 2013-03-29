<?php

/**
 *  This utility class holds validation result
 */
class ValidationResult {

	function __construct($valid, $message) {
		$this->valid = $valid;
		$this->message = $message;
	}

	function isValid() {
		return $this->valid;
	}

	function getMessage() {
		return $this->message;
	}

	private $message;
	private $valid;
}