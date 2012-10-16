<?php

require_once "phing/Task.php";

class DumpPluginVersionTask extends Task
{
	private $xmlFile;

	public function setFile( $str )
	{
		$this->xmlFile = $str;
	}

	public function main()
	{
		$xml = simplexml_load_file( $this->xmlFile );

		$version = (string) $xml->version;

		file_put_contents('version.txt', $version);

		return;
	}
}
