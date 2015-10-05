<?php

/**
 * elFinder driver for local filesystem.
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 **/
class elFinderVolumeLocalFileSystem extends elFinderVolumeDriver {

protected $driverId = 'l';
protected $archiveSize = 0;
protected $aroot;
public function __construct() {
	$this->options['alias']    = '';              // alias to replace root dir name
	$this->options['dirMode']  = 0755;            // new dirs mode
	$this->options['fileMode'] = 0644;            // new files mode
	$this->options['quarantine'] = '.quarantine';  // quarantine folder name - required to check archive (must be hidden)
	$this->options['maxArcFilesSize'] = 0;        // max allowed archive files size (0 - no limit)
}
protected function init() {
	if (DIRECTORY_SEPARATOR !== '/') {
		foreach(array('path', 'tmbPath', 'quarantine') as $key) {
			if ($this->options[$key]) {
				$this->options[$key] = str_replace('/', DIRECTORY_SEPARATOR, $this->options[$key]);
			}
		}
	}
	return true;
}
protected function configure() {
	$this->aroot = realpath($this->root);
	$root = $this->stat($this->root);
	if ($this->options['tmbPath']) {
		$this->options['tmbPath'] = strpos($this->options['tmbPath'], DIRECTORY_SEPARATOR) === false
			// tmb path set as dirname under root dir
			? $this->_abspath($this->options['tmbPath'])
			// tmb path as full path
			: $this->_normpath($this->options['tmbPath']);
	}

	parent::configure();
	if ($root['read'] && !$this->tmbURL && $this->URL) {
		if (strpos($this->tmbPath, $this->root) === 0) {
			$this->tmbURL = $this->URL.str_replace(DIRECTORY_SEPARATOR, '/', substr($this->tmbPath, strlen($this->root)+1));
			if (preg_match("|[^/?&=]$|", $this->tmbURL)) {
				$this->tmbURL .= '/';
			}
		}
	}
	$this->quarantine = '';
	if (!empty($this->options['quarantine'])) {
		if (is_dir($this->options['quarantine'])) {
			if (is_writable($this->options['quarantine'])) {
				$this->quarantine = $this->options['quarantine'];
			}
			$this->options['quarantine'] = '';
		} else {
			$this->quarantine = $this->_abspath($this->options['quarantine']);
			if ((!is_dir($this->quarantine) && !$this->_mkdir($this->root, $this->options['quarantine'])) || !is_writable($this->quarantine)) {
				$this->options['quarantine'] = $this->quarantine = '';
			}
		}
	}
	
	if (!$this->quarantine) {
		$this->archivers['extract'] = array();
		$this->disabled[] = 'extract';
	}
	
	if ($this->options['quarantine']) {
		$this->attributes[] = array(
				'pattern' => '~^'.preg_quote(DIRECTORY_SEPARATOR.$this->options['quarantine']).'$~',
				'read'    => false,
				'write'   => false,
				'locked'  => true,
				'hidden'  => true
		);
	}
}
protected function _dirname($path) {
	return dirname($path);
}
protected function _basename($path) {
	return basename($path);
}
protected function _joinPath($dir, $name) {
	return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
}
protected function _normpath($path) {
	if (empty($path)) {
		return '.';
	}
	
	$changeSep = (DIRECTORY_SEPARATOR !== '/');
	if ($changeSep) {
		$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
	}

	if (strpos($path, '/') === 0) {
		$initial_slashes = true;
	} else {
		$initial_slashes = false;
	}
		
	if (($initial_slashes) 
	&& (strpos($path, '//') === 0) 
	&& (strpos($path, '///') === false)) {
		$initial_slashes = 2;
	}
		
	$initial_slashes = (int) $initial_slashes;

	$comps = explode('/', $path);
	$new_comps = array();
	foreach ($comps as $comp) {
		if (in_array($comp, array('', '.'))) {
			continue;
		}
			
		if (($comp != '..') 
		|| (!$initial_slashes && !$new_comps) 
		|| ($new_comps && (end($new_comps) == '..'))) {
			array_push($new_comps, $comp);
		} elseif ($new_comps) {
			array_pop($new_comps);
		}
	}
	$comps = $new_comps;
	$path = implode('/', $comps);
	if ($initial_slashes) {
		$path = str_repeat('/', $initial_slashes) . $path;
	}
	
	if ($changeSep) {
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
	
	return $path ? $path : '.';
}
protected function _relpath($path) {
	return $path == $this->root ? '' : substr($path, strlen(rtrim($this->root, DIRECTORY_SEPARATOR)) + 1);
}
protected function _abspath($path) {
	return $path == DIRECTORY_SEPARATOR ? $this->root : $this->_joinPath($this->root, $path);
}
protected function _path($path) {
	return $this->rootName.($path == $this->root ? '' : $this->separator.$this->_relpath($path));
}
protected function _inpath($path, $parent) {
	$real_path = realpath($path);
	$real_parent = realpath($parent);
	if ($real_path && $real_parent) {
		return $real_path === $real_parent || strpos($real_path, rtrim($real_parent, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR) === 0;
	}
	return false;
}
protected function _stat($path) {
	$stat = array();

	if (!file_exists($path)) {
		return $stat;
	}
	if (!$this->aroot) {
		// for Inheritance class ( not calling parent::configure() )
		$this->aroot = realpath($this->root);
	}
	if (!$this->_inpath($path, $this->aroot)) {
		return $stat;
	}

	if ($path != $this->root && is_link($path)) {
		if (($target = $this->readlink($path)) == false 
		|| $target == $path) {
			$stat['mime']  = 'symlink-broken';
			$stat['read']  = false;
			$stat['write'] = false;
			$stat['size']  = 0;
			return $stat;
		}
		$stat['alias']  = $this->_path($target);
		$stat['target'] = $target;
		$path  = $target;
		$lstat = lstat($path);
		$size  = $lstat['size'];
	} else {
		$size = @filesize($path);
	}
	
	$dir = is_dir($path);
	
	$stat['mime']  = $dir ? 'directory' : $this->mimetype($path);
	$stat['ts']    = filemtime($path);
	//logical rights first
	$stat['read'] = is_readable($path)? null : false;
	$stat['write'] = is_writable($path)? null : false;

	if (is_null($stat['read'])) {
		$stat['size'] = $dir ? 0 : $size;
	}
	
	return $stat;
}
protected function _subdirs($path) {

	if (($dir = dir($path))) {
		$dir = dir($path);
		while (($entry = $dir->read()) !== false) {
			$p = $dir->path.DIRECTORY_SEPARATOR.$entry;
			if ($entry != '.' && $entry != '..' && is_dir($p) && !$this->attr($p, 'hidden')) {
				$dir->close();
				return true;
			}
		}
		$dir->close();
	}
	return false;
}
protected function _dimensions($path, $mime) {
	clearstatcache();
	return strpos($mime, 'image') === 0 && ($s = @getimagesize($path)) !== false 
		? $s[0].'x'.$s[1] 
		: false;
}
protected function readlink($path) {
	if (!($target = @readlink($path))) {
		return false;
	}
	
	if (substr($target, 0, 1) != DIRECTORY_SEPARATOR) {
		$target = $this->_joinPath(dirname($path), $target);
	}
	
	if ($this->_inpath($target, $this->aroot)) {
		$atarget = realpath($target);
		return $this->_normpath($this->_joinPath($this->root, substr($atarget, strlen(rtrim($this->aroot, DIRECTORY_SEPARATOR)) + 1)));
	}

	return false;
}
protected function _scandir($path) {
	$files = array();
	
	foreach (scandir($path) as $name) {
		if ($name != '.' && $name != '..') {
			$files[] = $this->_joinPath($path, $name);
		}
	}
	return $files;
}
protected function _fopen($path, $mode='rb') {
	return @fopen($path, $mode);
}
protected function _fclose($fp, $path='') {
	return @fclose($fp);
}
protected function _mkdir($path, $name) {
	$path = $this->_joinPath($path, $name);

	if (@mkdir($path)) {
		@chmod($path, $this->options['dirMode']);
		return $path;
	}

	return false;
}
protected function _mkfile($path, $name) {
	$path = $this->_joinPath($path, $name);
	
	if (($fp = @fopen($path, 'w'))) {
		@fclose($fp);
		@chmod($path, $this->options['fileMode']);
		return $path;
	}
	return false;
}
protected function _symlink($source, $targetDir, $name) {
	return @symlink($source, $this->_joinPath($targetDir, $name));
}
protected function _copy($source, $targetDir, $name) {
	return copy($source, $this->_joinPath($targetDir, $name));
}
protected function _move($source, $targetDir, $name) {
	$target = $this->_joinPath($targetDir, $name);
	return @rename($source, $target) ? $target : false;
}
protected function _unlink($path) {
	return @unlink($path);
}
protected function _rmdir($path) {
	return @rmdir($path);
}
protected function _save($fp, $dir, $name, $stat) {
	$path = $this->_joinPath($dir, $name);

	if (@file_put_contents($path, $fp, LOCK_EX) === false) {
		return false;
	}

	@chmod($path, $this->options['fileMode']);
	clearstatcache();
	return $path;
}
protected function _getContents($path) {
	return file_get_contents($path);
}
protected function _filePutContents($path, $content) {
	if (@file_put_contents($path, $content, LOCK_EX) !== false) {
		clearstatcache();
		return true;
	}
	return false;
}
protected function _checkArchivers() {
	$this->archivers = $this->getArchivers();
	return;
}
protected function _unpack($path, $arc) {
	$cwd = getcwd();
	$dir = $this->_dirname($path);
	chdir($dir);
	$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg($this->_basename($path));
	$this->procExec($cmd, $o, $c);
	chdir($cwd);
}
protected function _findSymlinks($path) {
	if (is_link($path)) {
		return true;
	}
	
	if (is_dir($path)) {
		foreach (scandir($path) as $name) {
			if ($name != '.' && $name != '..') {
				$p = $path.DIRECTORY_SEPARATOR.$name;
				if (is_link($p) || !$this->nameAccepted($name)) {
					return true;
				}
				if (is_dir($p) && $this->_findSymlinks($p)) {
					return true;
				} elseif (is_file($p)) {
					$this->archiveSize += filesize($p);
				}
			}
		}
	} else {
		
		$this->archiveSize += filesize($path);
	}
	
	return false;
}
protected function _extract($path, $arc) {
	
	if ($this->quarantine) {
		$dir     = $this->quarantine.DIRECTORY_SEPARATOR.str_replace(' ', '_', microtime()).basename($path);
		$archive = $dir.DIRECTORY_SEPARATOR.basename($path);
		
		if (!@mkdir($dir)) {
			return false;
		}
		
		chmod($dir, 0777);
		if (!copy($path, $archive)) {
			return false;
		}
		$this->_unpack($archive, $arc);
		unlink($archive);
		$ls = array();
		foreach (scandir($dir) as $i => $name) {
			if ($name != '.' && $name != '..') {
				$ls[] = $name;
			}
		}
		if (empty($ls)) {
			return false;
		}
		
		$this->archiveSize = 0;
		$symlinks = $this->_findSymlinks($dir);
		$this->remove($dir);
		
		if ($symlinks) {
			return $this->setError(elFinder::ERROR_ARC_SYMLINKS);
		}
		if ($this->options['maxArcFilesSize'] > 0 && $this->options['maxArcFilesSize'] < $this->archiveSize) {
			return $this->setError(elFinder::ERROR_ARC_MAXSIZE);
		}
		if (count($ls) == 1) {
			$this->_unpack($path, $arc);
			$result = dirname($path).DIRECTORY_SEPARATOR.$ls[0];
			

		} else {
			$name = basename($path);
			if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
				$name = substr($name, 0,  strlen($name)-strlen($m[0]));
			}
			$test = dirname($path).DIRECTORY_SEPARATOR.$name;
			if (file_exists($test) || is_link($test)) {
				$name = $this->uniqueName(dirname($path), $name, '-', false);
			}
			
			$result  = dirname($path).DIRECTORY_SEPARATOR.$name;
			$archive = $result.DIRECTORY_SEPARATOR.basename($path);

			if (!$this->_mkdir(dirname($path), $name) || !copy($path, $archive)) {
				return false;
			}
			
			$this->_unpack($archive, $arc);
			@unlink($archive);
		}
		
		return file_exists($result) ? $result : false;
	}
}
protected function _archive($dir, $files, $name, $arc) {
	$cwd = getcwd();
	chdir($dir);
	
	$files = array_map('escapeshellarg', $files);
	
	$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg($name).' '.implode(' ', $files);
	$this->procExec($cmd, $o, $c);
	chdir($cwd);

	$path = $dir.DIRECTORY_SEPARATOR.$name;
	return file_exists($path) ? $path : false;
}
	
} // END class 
