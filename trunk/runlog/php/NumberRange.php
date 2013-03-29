<?php
/*
 * Created on Apr 28, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /**
  * A class tat can hold number range
  */
class NumberRange {
	function __construct($min, $max) {
		$this->min = $min;
		$this->max = $max;
	}
	
	function getMin() {
		return $this->min;
	}
	
	function getMax() {
		return $this->max;
	}
	
	private $min;
	private $max;
} 	
?>
