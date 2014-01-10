<?php
if ( ! defined( 'DUPLICATOR_VERSION' ) ) exit; // Exit if accessed directly
require_once (DUPLICATOR_PLUGIN_PATH . 'classes/package.archive.php');

/**
 *  DUP_ZIP
 *  Creates a zip file using the built in PHP ZipArchive class
 */
class DUP_Zip  extends DUP_Archive {
	
	//PRIVATE 
	private static $compressDir;	
	private static $countDirs  = 0;
	private static $countFiles = 0;
	private static $countLinks = 0;
	private static $filterExtsOn;
	private static $filterDirsOn;
	private static $filterDirsArray;	
	private static $filterDirsList;
	private static $filterExtsArray;
	private static $filterExtsList;
	private static $filterOn = false;
	private static $sqlPath;
	private static $zipPath;
	private static $zipFileSize;
	private static $zipArchive;
	
	private static $limit = DUPLICATOR_ZIP_FLUSH_TRIGGER;
	private static $limitItems = 0;
	private static $networkFlush = false;
	
	/**
     *  CREATE
     *  Creates the zip file and adds the SQL file to the archive
     */
	static public function Create(DUP_Archive $archive) {
		  try {
		    
			$timerAllStart = DUP_Util::GetMicrotime();
			$package_zip_flush = DUP_Settings::Get('package_zip_flush');
			
			self::$compressDir		= rtrim(DUP_Util::SafePath($archive->PackDir), '/');
			self::$filterDirsArray	= array_map('DUP_Util::SafePath', explode(";", $archive->FilterDirs, -1));
			self::$filterDirsList	= $archive->FilterDirs;
			self::$filterExtsArray	= explode(";", $archive->FilterExts, -1);
			self::$filterExtsList	= $archive->FilterExts;
			self::$filterOn			= $archive->FilterOn;
			self::$sqlPath			= DUP_Util::SafePath("{$archive->Package->StorePath}/{$archive->Package->Database->File}");
			self::$zipPath			= DUP_Util::SafePath("{$archive->Package->StorePath}/{$archive->File}");
			self::$zipArchive		= new ZipArchive();
			self::$filterDirsOn		= count(self::$filterDirsArray);
			self::$filterExtsOn		= count(self::$filterExtsArray);
			self::$networkFlush		= empty($package_zip_flush) ? false : $package_zip_flush;
			
			DUP_Log::Info("\n********************************************************************************");
			DUP_Log::Info("ARCHIVE (ZIP):");
			DUP_Log::Info("********************************************************************************");
            DUP_Log::Info("ARCHIVE DIR:  " . self::$compressDir);
            DUP_Log::Info("ARCHIVE FILE: " . self::$zipPath );
			DUP_Log::Info("FILTER DIRS:  " . self::$filterDirsList);
			DUP_Log::Info("FILTER EXTS:  " . self::$filterExtsList);
            DUP_Log::Info('----------------------------------------');
			
			//--------------------------------
			//OPEN ZIP
			$isZipOpen = (self::$zipArchive->open(self::$zipPath, ZIPARCHIVE::CREATE) === TRUE);
			($isZipOpen)
                ? DUP_Log::Info("STARTING ARCHIVE BUILD")
				: DUP_Log::Error("Cannot open zip file with PHP ZipArchive.", "Path location [" . self::$zipPath . "]");
            
			//--------------------------------
			//ADD SQL 
			$isSQLInZip = self::$zipArchive->addFile(self::$sqlPath, "database.sql");
			($isSQLInZip) 
				? DUP_Log::Info("ADDED SQL: " . self::$sqlPath)
				: DUP_Log::Error("Unable to add database.sql file to archive.", "SQL File Path [" . self::$sqlath . "]");
			self::$zipArchive->close();
			self::$zipArchive->open(self::$zipPath, ZipArchive::CREATE);
			
			//--------------------------------
            //ADD FILES
			$timerFilesStart = DUP_Util::GetMicrotime();
			if (self::$filterOn && (self::$filterDirsOn || self::$filterExtsOn)) {
				DUP_Log::Info("FILE SCAN FILTERS *ON*");
				(! in_array(self::$compressDir, self::$filterDirsArray)) 
					? self::recurseDirsWithFilters(self::$compressDir)
					: DUP_Log::Info("path filter found: [" . self::$compressDir . "]");
			} else {
				DUP_Log::Info("FILE SCAN FILTERS *OFF*");
				self::recurseDirs(self::$compressDir);
			}
            
            $timerFilesEnd = DUP_Util::GetMicrotime();
            $timerFilesSum = DUP_Util::ElapsedTime($timerFilesEnd, $timerFilesStart);
			DUP_Log::Info("FILE SCAN STATS: Dirs " . self::$countDirs . " | Files " . self::$countFiles . " | Links " . self::$countLinks );
			DUP_Log::Info("FILE SCAN TIME: {$timerFilesSum}");
            DUP_Log::Info("\nZIP INFO: " . print_r(self::$zipArchive, true));
			
			//--------------------------------
			//LOG FINAL RESULTS
			DUP_Log::Info("CREATING ARCHIVE");
			DUP_Util::FcgiFlush();
            $zipCloseResult = self::$zipArchive->close();
			($zipCloseResult) 
				? DUP_Log::Info("CLOSING ARCHIVE RESULT: '{$zipCloseResult}'")
				: DUP_Log::Error("ZipArchive close failure.", "This hosted server may have a disk quota limit.\nCheck to make sure this archive file can be stored.");
		
            $timerAllEnd = DUP_Util::GetMicrotime();
            $timerAllSum = DUP_Util::ElapsedTime($timerAllEnd, $timerAllStart);
			
			self::$zipFileSize = @filesize(self::$zipPath);
			DUP_Log::Info("ARCHIVE FILE SIZE: " . DUP_Util::ByteSize(self::$zipFileSize));
            DUP_Log::Info("ARCHIVE RUNTIME: {$timerAllSum}");
        } 
        catch (Exception $e) {
			DUP_Log::Error("Runtime error in package.archive.zip.php constructor.", "Exception: {$e}");
        }
	}
	
	//BASIC RECURSION
	//Only called when no filters are provided tighter loop structer for speed
    private static function recurseDirs($directory) {
		
		$currentPath = DUP_Util::SafePath($directory);
		//EXCLUDE: Snapshot directory
		if (strstr($currentPath, DUPLICATOR_SSDIR_PATH) || empty($currentPath)) {
			return;
		}
		
		//DIRECTORIES
		$dh = new DirectoryIterator($currentPath);
		foreach ($dh as $file) {
			if (!$file->isDot()) {
				$fullPath	= "{$currentPath}/{$file}";
				$zipPath	= str_replace(self::$compressDir, '', $currentPath);
				$zipPath	= empty($zipPath) ? $file : ltrim("{$zipPath}/{$file}", '/');
				if ($file->isDir()) {
					if ($file->isReadable() && self::$zipArchive->addEmptyDir($zipPath)) {
						self::$countDirs++;
						self::recurseDirs($fullPath);
					} else {
						DUP_Log::Info("WARNING: Unable to add directory: {$fullPath}");
					}
				} else if ($file->isFile() && $file->isReadable()) {
					(self::$zipArchive->addFile($fullPath, $zipPath))
						? self::$countFiles++
						: DUP_Log::Info("WARNING: Unable to add file: {$fullPath}");
				} else if ($file->isLink()) {
					self::$countLinks++;
				} 
				self::$limitItems++;
			}
		}
		
		@closedir($dh);
		if (self::$networkFlush)
			self::flushResponse();
	} 
	
	//FILTER RECURSION
	//Called when filters are enabled
	//Notes: $file->getExtension() is not reliable as it silently fails at least in php 5.2.17 
	//when a file has a permission such as 705 falling back to pathinfo is more stable
    private static function recurseDirsWithFilters($directory) {
		$currentPath = DUP_Util::SafePath($directory);

		//EXCLUDE: Snapshot directory
		if (strstr($currentPath, DUPLICATOR_SSDIR_PATH) || empty($currentPath)) {
			return;
		}

		//DIRECTORIES
		$dh = new DirectoryIterator($currentPath);
		foreach ($dh as $file) {
			if (!$file->isDot()) {
				$fullPath	= "{$currentPath}/{$file}";
				$zipPath	= str_replace(self::$compressDir, '', $currentPath);
				$zipPath	= empty($zipPath) ? $file : ltrim("{$zipPath}/{$file}", '/');
				if ($file->isDir()) {
					if (! in_array($fullPath, self::$filterDirsArray)) {
						if ($file->isReadable() && self::$zipArchive->addEmptyDir($zipPath)) {
							self::$countDirs++;
							self::recurseDirsWithFilters($fullPath);
						} else {
							DUP_Log::Info("WARNING: Unable to add directory: {$fullPath}");
						}
					}  else {
						DUP_Log::Info("path filter found: {$fullPath}");
					}
				} else if ($file->isFile() && $file->isReadable()) {
					if (self::$filterExtsOn) {
						$ext = @pathinfo($fullPath, PATHINFO_EXTENSION);
						if (! in_array($ext, self::$filterExtsArray) || empty($ext)) {
							self::$zipArchive->addFile($fullPath, $zipPath);
							self::$countFiles++;
						}
					} else {
						(self::$zipArchive->addFile($fullPath, $zipPath))
							? self::$countFiles++
							: DUP_Log::Info("WARNING: Unable to add file: $fullPath");
					}
				} else if ($file->isLink()) {
					self::$countLinks++;
				} 
				self::$limitItems++;
			}
		}
		@closedir($dh);
		if (self::$networkFlush)
			self::flushResponse();
	} 	
	
	
	/* This allows the process to not timeout on fcgi 
	 * setups that need a response every X seconds */
	private static function flushResponse() {
		//Check if were over our count*/
		if(self::$limitItems > self::$limit) {
			$sumItems = (self::$countDirs + self::$countFiles + self::$countLinks);
			self::$zipArchive->close();
			self::$zipArchive->open(self::$zipPath);
			self::$limitItems = 0;
			DUP_Util::FcgiFlush();
			DUP_Log::Info("Items archived [{$sumItems}] flushing response.");
		}
	}
	
}
?>