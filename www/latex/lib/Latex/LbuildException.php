<?php

namespace Latex;

class LbuildException extends \Exception {
	private $log = null;

	public function __construct($message, $log, $code = 0, Exception $previous = null)
	{
		$this->log = $log;
		parent::__construct($message, $code, $previous);
	}

	public function getLog()
	{
		return $this->log;
	}
}