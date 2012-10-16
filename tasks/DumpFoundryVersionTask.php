<?php

require_once "phing/Task.php";

class DumpFoundryVersionTask extends Task
{
	private $file;

	public function setFile( $str )
	{
		$this->file = $str;
	}

	public function main()
	{

		$version = $this->_read( $this->file );

		$version = explode('.', $version);

		$version = $version[0].'.'.$version[1];

		file_put_contents('version.txt', $version);

		return;
	}

	private function _read($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0)
	{
		// Initialise variables.
		$data = null;
		if ($amount && $chunksize > $amount)
		{
			$chunksize = $amount;
		}

		if (false === $fh = fopen($filename, 'rb', $incpath))
		{
			return false;
		}

		clearstatcache();

		if ($offset)
		{
			fseek($fh, $offset);
		}

		if ($fsize = @ filesize($filename))
		{
			if ($amount && $fsize > $amount)
			{
				$data = fread($fh, $amount);
			}
			else
			{
				$data = fread($fh, $fsize);
			}
		}
		else
		{
			$data = '';
			// While it's:
			// 1: Not the end of the file AND
			// 2a: No Max Amount set OR
			// 2b: The length of the data is less than the max amount we want
			while (!feof($fh) && (!$amount || strlen($data) < $amount))
			{
				$data .= fread($fh, $chunksize);
			}
		}
		fclose($fh);

		return $data;
	}
}
