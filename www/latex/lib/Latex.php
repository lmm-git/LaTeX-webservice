<?php

namespace Latex;

class Render
{
	private $workingDir;
	private $entryFile;
	private $lbuildLog;

	public function __construct($dir, $entryFile)
	{
		if(!function_exists('exec')) {
			throw new \Exception('exec is disabled on this system. Cannot call lbuild!');
		}
		if(!is_dir($dir)) {
			throw new \InvalidArgumentException('Passed working directory ' . $dir . ' is not a directory!');
		}
		if(!is_writable($dir)) {
			throw new \InvalidArgumentException('Passed working directory ' . $dir . ' is not writable!');
		}
		$this->workingDir = $dir;

		if(!is_file($this->workingDir . '/' . $entryFile)) {
			throw new \InvalidArgumentException('Entry file ' . $entryFile . ' does not exists!');
		}
		if(!is_readable($this->workingDir . '/' . $entryFile)) {
			throw new \InvalidArgumentException('Entry file ' . $entryFile . ' is not readable!');
		}
		$this->entryFile = $entryFile;

		$this->build();
	}

	private function getEntryFilePath()
	{
		return $this->workingDir . '/' . $this->entryFile;
	}

	private function build()
	{
		$shellscript = 'cd ' . escapeshellarg($this->workingDir) . ';';
		$shellscript .= 'export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin";';
		$shellscript .= 'lbuild ' . escapeshellarg($this->entryFile) . ' 2>&1';
		$this->lbuildLog = shell_exec($shellscript);
		if($this->lbuildLog === null) {
			throw new LbuildException('Lbuild command failed!', $this->lbuildLog);
		}
	}

	public function getLog()
	{
		return $this->lbuildLog;
	}

	public function getPDF()
	{
		$pdfFile = substr($this->getEntryFilePath(), 0, -4) . '.pdf';
		if(file_exists($pdfFile)) {
			return file_get_contents($pdfFile);
		}
		return null;
	}
}