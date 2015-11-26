<?php

namespace Response;

class GeneratorResponse
{
	private $success = false;
	private $message = null;
	private $file = null;
	private $log = null;
	private $creationDate;

	public function __construct()
	{
		$this->creationDate = new \DateTime('now');
	}

	public function setSuccess($success)
	{
		$this->success = $success;
	}

	public function setMessage($msg)
	{
		$this->message = $msg;
	}

	public function setLog($log)
	{
		$this->log = $log;
	}

	public function setFile($file)
	{
		$this->file = $file;
	}

	public function getJSON()
	{
		$resAr = array('success' => $this->success,
			'message' => $this->message,
			'file' => $this->file,
			'log' => $this->log,
			'creationDate' => $this->creationDate);

		return json_encode($resAr);
	}

	public function getFile()
	{
		header("Content-type:application/pdf");
		header("Content-Disposition:attachment;filename='latex.pdf'");
		echo $this->file;
		return;
	}

	public function get()
	{
		if($this->success && $this->file != null) {
			return $this->getFile();
		}
		return $this->getJSON();
	}
}