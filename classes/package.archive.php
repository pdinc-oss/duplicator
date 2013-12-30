<?php
if ( ! defined( 'DUPLICATOR_VERSION' ) ) exit; // Exit if accessed directly

require_once (DUPLICATOR_PLUGIN_PATH . 'classes/package.archive.zip.php');

class DUP_Archive {
	
	//PUBLIC
	public $FilterDirs;
	public $FilterExts;
	public $FilterOn;
	public $File;
	public $Format;
	public $PackDir;
	public $DirCount	 = 0;
	public $FileCount	 = 0;
	public $LinkCount	 = 0;		
	public $Size		 = 0;
	public $BigFileList  = array();
	public $LongFileList = array();
	
	//PROTECTED
	protected $Package;
	
	//PRIVATE
	private $filerDirsArray = array();
	private $filerExtsArray = array();

	
	public function __construct($package) {
		$this->Package   = $package;
		$this->FilterOn  = false;
	}
	
	public function Build() {
		try {
			
			if (!isset($this->PackDir) && ! is_dir($this->PackDir)) throw new Exception("The 'PackDir' property must be a valid diretory.");
			if (!isset($this->File)) throw new Exception("A 'File' property must be set.");
		
			$this->Package->SetStatus(DUP_PackageStatus::ARCSTART);
			switch ($this->Format) {
				case 'TAR':			break;
				case 'TAR-GZIP': 	break;
				default:

					if (class_exists(ZipArchive)) {
						$this->Format = 'ZIP';
						DUP_Zip::Create($this);
					} else {
						//TODO:PECL and SHELL FORMATS
					}
					break;
			}
			
			$storePath = "{$this->Package->StorePath}/{$this->File}";
			$this->Size   = @filesize($storePath);
			$this->Package->SetStatus(DUP_PackageStatus::ARCDONE);
		
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	
	public function GetFilterDirAsArray() {
		return array_map('DUP_Util::SafePath', explode(";", $this->FilterDirs, -1));
	}
	
	public function GetFilterExtsAsArray() {
		return explode(";", $this->FilterExts, -1);
	}
	
	/**
	 *  DIRSTATS
	 *  Get the directory size recursively, but don't calc the snapshot directory, exclusion diretories
	 *  @param string $directory		The directory to calculate
	 *  @returns array					An array of values for the directory stats
	 */	
	public function GetStats() {
		$this->filerDirsArray = $this->GetFilterDirAsArray();
		$this->filerExtsArray = $this->GetFilterExtsAsArray();
		$rootPath = rtrim(DUPLICATOR_WPROOTPATH, '//' );
	
		if ($this->FilterOn) {
			if (! in_array($rootPath, $this->filerDirsArray) ) {
				$this->runDirStats($this->PackDir);
				return $this;
			}
		} else {
			$this->runDirStats($this->PackDir);
			return $this;
		}
		return null;
	}
	

	private function runDirStats($directory) {
		
		$currentPath = DUP_Util::SafePath($directory);
		//EXCLUDE: Snapshot directory
		if (strstr($currentPath, DUPLICATOR_SSDIR_PATH) || empty($currentPath)) {
			return;
		}
		
		$dh = new DirectoryIterator($currentPath);
		foreach ($dh as $file) {
			if (!$file->isDot()) {
				$nextpath	= "{$currentPath}/{$file}";
				if ($file->isDir()) {
					if (! in_array($nextpath, $this->filerDirsArray)) {						
						$result = $this->runDirStats($nextpath);
						$this->DirCount++;
					}

				} else if ($file->isFile() && $file->isReadable()) {
					if (!in_array(@pathinfo($nextpath, PATHINFO_EXTENSION), $this->filerExtsArray)) {
						$this->Size += $file->getSize();
						$this->FileCount++;
						if (strlen($nextpath) > 200) 
							array_push($this->LongFileList, $nextpath);
						if ($file->getSize() > DUPLICATOR_SCAN_BIGFILE) 
							array_push($this->BigFileList, $nextpath);
					}
				} else if ($file->isLink()) {
					$this->LinkCount++;
				} 
			}	 
		}
	}	
	
}
?>
