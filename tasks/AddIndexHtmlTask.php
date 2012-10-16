<?php

require_once "phing/Task.php";

class AddIndexHtmlTask extends Task
{
	private $folder	= null;
	private $file	= null;

	public function setFolder( $str )
	{
		$this->folder = $str;
	}

	public function setFile( $str )
	{
		$this->file = $str;
	}

	public function main()
	{
		$folders = self::folders( $this->folder, '.', true, true );

		foreach ($folders as $folder) {
			copy($this->file, $folder . DIRECTORY_SEPARATOR . 'index.html');
		}

		return;
	}

	public static function folders($path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*'))
	{
		// Check to make sure the path valid and clean
		$path = self::clean($path);

		// Is the path a folder?
		if (!is_dir($path))
		{
			return false;
		}

		// Compute the excludefilter string
		if (count($excludefilter))
		{
			$excludefilter_string = '/(' . implode('|', $excludefilter) . ')/';
		}
		else
		{
			$excludefilter_string = '';
		}

		// Get the folders
		$arr = self::_items($path, $filter, $recurse, $full, $exclude, $excludefilter_string, false);

		// Sort the folders
		asort($arr);
		return array_values($arr);
	}

	protected static function _items($path, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles)
	{
		@set_time_limit(ini_get('max_execution_time'));

		// Initialise variables.
		$arr = array();

		// Read the source directory
		if (!($handle = @opendir($path)))
		{
			return $arr;
		}

		while (($file = readdir($handle)) !== false)
		{
			if ($file != '.' && $file != '..' && !in_array($file, $exclude)
				&& (empty($excludefilter_string) || !preg_match($excludefilter_string, $file)))
			{
				// Compute the fullpath
				$fullpath = $path . '/' . $file;

				// Compute the isDir flag
				$isDir = is_dir($fullpath);

				if (($isDir xor $findfiles) && preg_match("/$filter/", $file))
				{
					// (fullpath is dir and folders are searched or fullpath is not dir and files are searched) and file matches the filter
					if ($full)
					{
						// Full path is requested
						$arr[] = $fullpath;
					}
					else
					{
						// Filename is requested
						$arr[] = $file;
					}
				}
				if ($isDir && $recurse)
				{
					// Search recursively
					if (is_integer($recurse))
					{
						// Until depth 0 is reached
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse - 1, $full, $exclude, $excludefilter_string, $findfiles));
					}
					else
					{
						$arr = array_merge($arr, self::_items($fullpath, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles));
					}
				}
			}
		}
		closedir($handle);
		return $arr;
	}

	public static function clean($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
		$path = preg_replace('#[/\\\\]+#', $ds, $path);

		return $path;
	}
}
