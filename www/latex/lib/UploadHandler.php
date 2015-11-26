<?php

namespace UploadHandler;

class UploadHandler
{
	private $fileArray;
	private $fileLocation;
	private $unpackLocation = null;
	private $tmpDir;

	public function __construct($fileArray)
	{
		$this->tmpDir = sys_get_temp_dir();

		if(!is_array($fileArray)) {
			throw new \InvalidArgumentException('Passed fileArray is not an array! Did you uploaded a file?');
		}
		if($fileArray['error'] != UPLOAD_ERR_OK) {
			throw new \Exception('There was an error during file upload!');
		}
		$this->fileArray = $fileArray;

		$this->fileLocation = tempnam($this->fileLocation, 'LATEXSOURCE');
		if(!move_uploaded_file($fileArray['tmp_name'], $this->fileLocation)) {
			throw new \Exception('Cannot copy uploaded file! Was the file upload successfull?');
		}
	}

	public function __destruct()
	{
		unlink($this->fileLocation);
		if($this->unpackLocation != null) {
			#self::deleteDir($this->unpackLocation);
		}
	}

	public function unpack($destination = null)
	{
		if($destination == null) {
			$destination = self::getTmpDir();
		}
		$this->unpackLocation = $destination;
		if(!is_dir($destination)) {
			throw new \InvalidArgumentException('Passed destination ' . $destination . ' is not a directory!');
		}
		if(!is_writable($destination)) {
			throw new \InvalidArgumentException('Passed destination ' . $destination . ' is not writable!');
		}

		// unpack all files to destination directory
		$zip = new \ZipArchive();
		if(!$zip->open($this->fileLocation)) {
			throw new \Exception('Invalid zip file uploaded!');
		}
		$zip->extractTo($destination);
		$zip->close();

		return $this->unpackLocation;
	}

	private static function getTmpDir()
	{
		$tmpdir = tempnam(sys_get_temp_dir(),'');
		unlink($tmpdir);
		mkdir($tmpdir);
		return $tmpdir;
	}

	// function taken from http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
	public static function deleteDir($dir) {
		$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new \RecursiveIteratorIterator($it,
					 \RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
	}
}