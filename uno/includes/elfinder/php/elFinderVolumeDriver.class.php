<?php
/**
 * Base class for elFinder volume.
 * Provide 2 layers:
 *  1. Public API (commands)
 *  2. abstract fs API
 *
 * All abstract methods begin with "_"
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 * @author Alexey Sukhotin
 **/
abstract class elFinderVolumeDriver
{
	protected $driverId = 'a';
	protected $id = '';
	protected $mounted = false;
	protected $root = '';
	protected $rootName = '';
	protected $startPath = '';
	protected $URL = '';
	protected $tmbPath = '';
	protected $tmbPathWritable = false;
	protected $tmbURL = '';
	protected $tmbSize = 48;
	protected $imgLib = 'auto';
	protected $cryptLib = '';
	protected $archivers = array(
		'create'  => array(),
		'extract' => array()
	);
	protected $treeDeep = 1;
	protected $error = array();
	protected $today = 0;
	protected $yesterday = 0;
	protected $options = array(
		'id'              => '',
		'path'            => '',
		'startPath'       => '',
		'treeDeep'        => 1,
		'URL'             => '',
		'separator'       => DIRECTORY_SEPARATOR,
		'cryptLib'        => '',
		'mimeDetect'      => 'auto',
		'mimefile'        => '',
		'tmbPath'         => '.tmb',
		'tmbPathMode'     => 0777,
		'tmbURL'          => '',
		'tmbSize'         => 48,
		'tmbCrop'         => true,
		'tmbBgColor'      => '#ffffff',
		'imgLib'          => 'auto',
		'copyOverwrite'   => true,
		'copyJoin'        => true,
		'uploadOverwrite' => true,
		'uploadAllow'     => array(),
		'uploadDeny'      => array(),
		'uploadOrder'     => array('deny', 'allow'),
		'uploadMaxSize'   => 0,
		'dateFormat'      => 'j M Y H:i',
		'timeFormat'      => 'H:i',
		'checkSubfolders' => true,
		'copyFrom'        => true,
		'copyTo'          => true,
		'disabled'        => array(),
		'acceptedName'    => '/^\w[\w\s\.\%\-\(\)\[\]]*$/u',
		'accessControl'   => null,
		'accessControlData' => null,
		'defaults'     => array(
			'read'   => true,
			'write'  => true
		),
		'attributes'   => array(),
		'archiveMimes' => array(),
		'archivers'    => array(),
		'utf8fix'      => false,
		'utf8patterns' => array("\u0438\u0306", "\u0435\u0308", "\u0418\u0306", "\u0415\u0308", "\u00d8A", "\u030a"),
		'utf8replace'  => array("\u0439", "\u0451", "\u0419", "\u0401", "\u00d8", "\u00c5")
	);
	protected $defaults = array(
		'read'   => true,
		'write'  => true,
		'locked' => false,
		'hidden' => false
	);
	protected $attributes = array();
	protected $access = null;
	protected $uploadAllow = array();
	protected $uploadDeny = array();
	protected $uploadOrder = array();
	protected $uploadMaxSize = 0;
	protected $mimeDetect = 'auto';
	private static $mimetypesLoaded = false;
	protected $finfo = null;
	protected $diabled = array();
	protected static $mimetypes = array(
		// applications
		'ai'    => 'application/postscript',
		'eps'   => 'application/postscript',
		'exe'   => 'application/x-executable',
		'doc'   => 'application/vnd.ms-word',
		'xls'   => 'application/vnd.ms-excel',
		'ppt'   => 'application/vnd.ms-powerpoint',
		'pps'   => 'application/vnd.ms-powerpoint',
		'pdf'   => 'application/pdf',
		'xml'   => 'application/xml',
		'odt'   => 'application/vnd.oasis.opendocument.text',
		'swf'   => 'application/x-shockwave-flash',
		'torrent' => 'application/x-bittorrent',
		'jar'   => 'application/x-jar',
		// archives
		'gz'    => 'application/x-gzip',
		'tgz'   => 'application/x-gzip',
		'bz'    => 'application/x-bzip2',
		'bz2'   => 'application/x-bzip2',
		'tbz'   => 'application/x-bzip2',
		'zip'   => 'application/zip',
		'rar'   => 'application/x-rar',
		'tar'   => 'application/x-tar',
		'7z'    => 'application/x-7z-compressed',
		// texts
		'txt'   => 'text/plain',
		'php'   => 'text/x-php',
		'html'  => 'text/html',
		'htm'   => 'text/html',
		'js'    => 'text/javascript',
		'css'   => 'text/css',
		'rtf'   => 'text/rtf',
		'rtfd'  => 'text/rtfd',
		'py'    => 'text/x-python',
		'java'  => 'text/x-java-source',
		'rb'    => 'text/x-ruby',
		'sh'    => 'text/x-shellscript',
		'pl'    => 'text/x-perl',
		'xml'   => 'text/xml',
		'sql'   => 'text/x-sql',
		'c'     => 'text/x-csrc',
		'h'     => 'text/x-chdr',
		'cpp'   => 'text/x-c++src',
		'hh'    => 'text/x-c++hdr',
		'log'   => 'text/plain',
		'csv'   => 'text/x-comma-separated-values',
		// images
		'bmp'   => 'image/x-ms-bmp',
		'jpg'   => 'image/jpeg',
		'jpeg'  => 'image/jpeg',
		'gif'   => 'image/gif',
		'png'   => 'image/png',
		'tif'   => 'image/tiff',
		'tiff'  => 'image/tiff',
		'tga'   => 'image/x-targa',
		'psd'   => 'image/vnd.adobe.photoshop',
		'ai'    => 'image/vnd.adobe.photoshop',
		'xbm'   => 'image/xbm',
		'pxm'   => 'image/pxm',
		//audio
		'mp3'   => 'audio/mpeg',
		'mid'   => 'audio/midi',
		'ogg'   => 'audio/ogg',
		'oga'   => 'audio/ogg',
		'm4a'   => 'audio/x-m4a',
		'wav'   => 'audio/wav',
		'wma'   => 'audio/x-ms-wma',
		// video
		'avi'   => 'video/x-msvideo',
		'dv'    => 'video/x-dv',
		'mp4'   => 'video/mp4',
		'mpeg'  => 'video/mpeg',
		'mpg'   => 'video/mpeg',
		'mov'   => 'video/quicktime',
		'wm'    => 'video/x-ms-wmv',
		'flv'   => 'video/x-flv',
		'mkv'   => 'video/x-matroska',
		'webm'  => 'video/webm',
		'ogv'   => 'video/ogg',
		'ogm'   => 'video/ogg'
		);
	protected $separator = DIRECTORY_SEPARATOR;
	protected $onlyMimes = array();
	protected $removed = array();
	protected $cache = array();
	protected $dirsCache = array();
	//
	protected function init() {
		return true;
	}	
	protected function configure() {
		// set thumbnails path
		$path = $this->options['tmbPath'];
		if ($path) {
			if (!file_exists($path)) {
				if (@mkdir($path)) {
					chmod($path, $this->options['tmbPathMode']);
				} else {
					$path = '';
				}
			} 
			
			if (is_dir($path) && is_readable($path)) {
				$this->tmbPath = $path;
				$this->tmbPathWritable = is_writable($path);
			}
		}

		// set image manipulation library
		$type = preg_match('/^(imagick|gd|auto)$/i', $this->options['imgLib'])
			? strtolower($this->options['imgLib'])
			: 'auto';

		if (($type == 'imagick' || $type == 'auto') && extension_loaded('imagick')) {
			$this->imgLib = 'imagick';
		} else {
			$this->imgLib = function_exists('gd_info') ? 'gd' : '';
		}
		
	}
	public function driverId() {
		return $this->driverId;
	}
	public function id() {
		return $this->id;
	}
	public function debug() {
		return array(
			'id'         => $this->id(),
			'name'       => strtolower(substr(get_class($this), strlen('elfinderdriver'))),
			'mimeDetect' => $this->mimeDetect,
			'imgLib'     => $this->imgLib
		);
	}
	public function mount(array $opts) {
		if (!isset($opts['path']) || $opts['path'] === '') {
			return false;
		}
		
		$this->options = array_merge($this->options, $opts);
		$this->id = $this->driverId.(!empty($this->options['id']) ? $this->options['id'] : elFinder::$volumesCnt++).'_';
		$this->root = $this->_normpath($this->options['path']);
		$this->separator = isset($this->options['separator']) ? $this->options['separator'] : DIRECTORY_SEPARATOR;
		
		// default file attribute
		$this->defaults = array(
			'read'    => isset($this->options['defaults']['read'])  ? !!$this->options['defaults']['read']  : true,
			'write'   => isset($this->options['defaults']['write']) ? !!$this->options['defaults']['write'] : true,
			'locked'  => false,
			'hidden'  => false
		);

		// root attributes
		$this->attributes[] = array(
			'pattern' => '~^'.preg_quote(DIRECTORY_SEPARATOR).'$~',
			'locked'  => true,
			'hidden'  => false
		);
		// set files attributes
		if (!empty($this->options['attributes']) && is_array($this->options['attributes'])) {
			
			foreach ($this->options['attributes'] as $a) {
				// attributes must contain pattern and at least one rule
				if (!empty($a['pattern']) || count($a) > 1) {
					$this->attributes[] = $a;
				}
			}
		}

		if (!empty($this->options['accessControl'])) {
			if (is_string($this->options['accessControl']) 
			&& function_exists($this->options['accessControl'])) {
				$this->access = $this->options['accessControl'];
			} elseif (is_array($this->options['accessControl']) 
			&& count($this->options['accessControl']) > 1 
			&& is_object($this->options['accessControl'][0])
			&& method_exists($this->options['accessControl'][0], $this->options['accessControl'][1])) {
				$this->access = array($this->options['accessControl'][0], $this->options['accessControl'][1]);
			}
		}
		
		$this->today     = mktime(0,0,0, date('m'), date('d'), date('Y'));
		$this->yesterday = $this->today-86400;
		
		// debug($this->attributes);
		if (!$this->init()) {
			return false;
		}
		
		// check some options is arrays
		$this->uploadAllow = isset($this->options['uploadAllow']) && is_array($this->options['uploadAllow'])
			? $this->options['uploadAllow']
			: array();
			
		$this->uploadDeny = isset($this->options['uploadDeny']) && is_array($this->options['uploadDeny'])
			? $this->options['uploadDeny']
			: array();

		if (is_string($this->options['uploadOrder'])) { // telephat_mode on, compatibility with 1.x
			$parts = explode(',', isset($this->options['uploadOrder']) ? $this->options['uploadOrder'] : 'deny,allow');
			$this->uploadOrder = array(trim($parts[0]), trim($parts[1]));
		} else { // telephat_mode off
			$this->uploadOrder = $this->options['uploadOrder'];
		}
			
		if (!empty($this->options['uploadMaxSize'])) {
			$size = ''.$this->options['uploadMaxSize'];
			$unit = strtolower(substr($size, strlen($size) - 1));
			$n = 1;
			switch ($unit) {
				case 'k':
					$n = 1024;
					break;
				case 'm':
					$n = 1048576;
					break;
				case 'g':
					$n = 1073741824;
			}
			$this->uploadMaxSize = intval($size)*$n;
		}
			
		$this->disabled = isset($this->options['disabled']) && is_array($this->options['disabled'])
			? $this->options['disabled']
			: array();
		
		$this->cryptLib   = $this->options['cryptLib'];
		$this->mimeDetect = $this->options['mimeDetect'];

		// find available mimetype detect method
		$type = strtolower($this->options['mimeDetect']);
		$type = preg_match('/^(finfo|mime_content_type|internal|auto)$/i', $type) ? $type : 'auto';
		$regexp = '/text\/x\-(php|c\+\+)/';
		
		if (($type == 'finfo' || $type == 'auto') 
		&& class_exists('finfo')
		&& preg_match($regexp, array_shift(explode(';', @finfo_file(finfo_open(FILEINFO_MIME), __FILE__))))) {
			$type = 'finfo';
			$this->finfo = finfo_open(FILEINFO_MIME);
		} elseif (($type == 'mime_content_type' || $type == 'auto') 
		&& function_exists('mime_content_type')
		&& preg_match($regexp, array_shift(explode(';', mime_content_type(__FILE__))))) {
			$type = 'mime_content_type';
		} else {
			$type = 'internal';
		}
		$this->mimeDetect = $type;

		if ($this->mimeDetect == 'internal' && !self::$mimetypesLoaded) {
			self::$mimetypesLoaded = true;
			$this->mimeDetect = 'internal';
			$file = false;
			if (!empty($this->options['mimefile']) && file_exists($this->options['mimefile'])) {
				$file = $this->options['mimefile'];
			} elseif (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'mime.types')) {
				$file = dirname(__FILE__).DIRECTORY_SEPARATOR.'mime.types';
			} elseif (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mime.types')) {
				$file = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mime.types';
			}

			if ($file && file_exists($file)) {
				$mimecf = file($file);

				foreach ($mimecf as $line_num => $line) {
					if (!preg_match('/^\s*#/', $line)) {
						$mime = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
						for ($i = 1, $size = count($mime); $i < $size ; $i++) {
							if (!isset(self::$mimetypes[$mime[$i]])) {
								self::$mimetypes[$mime[$i]] = $mime[0];
							}
						}
					}
				}
			}
		}

		$this->rootName = empty($this->options['alias']) ? $this->_basename($this->root) : $this->options['alias'];
		$root = $this->stat($this->root);
		
		if (!$root) {
			return $this->setError('Root folder does not exists.');
		}
		if (!$root['read'] && !$root['write']) {
			return $this->setError('Root folder has not read and write permissions.');
		}
		
		// debug($root);
		
		if ($root['read']) {
			// check startPath - path to open by default instead of root
			if ($this->options['startPath']) {
				$start = $this->stat($this->options['startPath']);
				if (!empty($start)
				&& $start['mime'] == 'directory'
				&& $start['read']
				&& empty($start['hidden'])
				&& $this->_inpath($this->options['startPath'], $this->root)) {
					$this->startPath = $this->options['startPath'];
					if (substr($this->startPath, -1, 1) == $this->options['separator']) {
						$this->startPath = substr($this->startPath, 0, -1);
					}
				}
			}
		} else {
			$this->options['URL']     = '';
			$this->options['tmbURL']  = '';
			$this->options['tmbPath'] = '';
			// read only volume
			array_unshift($this->attributes, array(
				'pattern' => '/.*/',
				'read'    => false
			));
		}
		$this->treeDeep = $this->options['treeDeep'] > 0 ? (int)$this->options['treeDeep'] : 1;
		$this->tmbSize  = $this->options['tmbSize'] > 0 ? (int)$this->options['tmbSize'] : 48;
		$this->URL      = $this->options['URL'];
		if ($this->URL && preg_match("|[^/?&=]$|", $this->URL)) {
			$this->URL .= '/';
		}

		$this->tmbURL   = !empty($this->options['tmbURL']) ? $this->options['tmbURL'] : '';
		if ($this->tmbURL && preg_match("|[^/?&=]$|", $this->tmbURL)) {
			$this->tmbURL .= '/';
		}
		
		$this->nameValidator = is_string($this->options['acceptedName']) && !empty($this->options['acceptedName']) 
			? $this->options['acceptedName']
			: '';

		$this->_checkArchivers();
		// manual control archive types to create
		if (!empty($this->options['archiveMimes']) && is_array($this->options['archiveMimes'])) {
			foreach ($this->archivers['create'] as $mime => $v) {
				if (!in_array($mime, $this->options['archiveMimes'])) {
					unset($this->archivers['create'][$mime]);
				}
			}
		}
		
		// manualy add archivers
		if (!empty($this->options['archivers']['create']) && is_array($this->options['archivers']['create'])) {
			foreach ($this->options['archivers']['create'] as $mime => $conf) {
				if (strpos($mime, 'application/') === 0 
				&& !empty($conf['cmd']) 
				&& isset($conf['argc']) 
				&& !empty($conf['ext'])
				&& !isset($this->archivers['create'][$mime])) {
					$this->archivers['create'][$mime] = $conf;
				}
			}
		}
		
		if (!empty($this->options['archivers']['extract']) && is_array($this->options['archivers']['extract'])) {
			foreach ($this->options['archivers']['extract'] as $mime => $conf) {
				if (substr($mime, 'application/') === 0 
				&& !empty($cons['cmd']) 
				&& isset($conf['argc']) 
				&& !empty($conf['ext'])
				&& !isset($this->archivers['extract'][$mime])) {
					$this->archivers['extract'][$mime] = $conf;
				}
			}
		}

		$this->configure();
		// echo $this->uploadMaxSize;
		// echo $this->options['uploadMaxSize'];
		return $this->mounted = true;
	}
	public function umount() {
	}
	public function error() {
		return $this->error;
	}
	public function setMimesFilter($mimes) {
		if (is_array($mimes)) {
			$this->onlyMimes = $mimes;
		}
	}
	public function root() {
		return $this->encode($this->root);
	}
	public function defaultPath() {
		return $this->encode($this->startPath ? $this->startPath : $this->root);
	}
	public function options($hash) {
		return array(
			'path'          => $this->_path($this->decode($hash)),
			'url'           => $this->URL,
			'tmbUrl'        => $this->tmbURL,
			'disabled'      => $this->disabled,
			'separator'     => $this->separator,
			'copyOverwrite' => intval($this->options['copyOverwrite']),
			'archivers'     => array(
				'create'  => array_keys($this->archivers['create']),
				'extract' => array_keys($this->archivers['extract'])
			)
		);
	}
	public function commandDisabled($cmd) {
		return in_array($cmd, $this->disabled);
	}
	public function mimeAccepted($mime, $mimes = array(), $empty = true) {
		$mimes = !empty($mimes) ? $mimes : $this->onlyMimes;
		if (empty($mimes)) {
			return $empty;
		}
		return $mime == 'directory'
			|| in_array('all', $mimes)
			|| in_array('All', $mimes)
			|| in_array($mime, $mimes)
			|| in_array(substr($mime, 0, strpos($mime, '/')), $mimes);
	}
	public function isReadable() {
		$stat = $this->stat($this->root);
		return $stat['read'];
	}
	public function copyFromAllowed() {
		return !!$this->options['copyFrom'];
	}
	public function path($hash) {
		return $this->_path($this->decode($hash));
	}
	public function realpath($hash) {
		$path = $this->decode($hash);
		return $this->stat($path) ? $path : false;
	}
	public function removed() {
		return $this->removed;
	}
	public function resetRemoved() {
		$this->removed = array();
	}
	public function closest($hash, $attr, $val) {
		return ($path = $this->closestByAttr($this->decode($hash), $attr, $val)) ? $this->encode($path) : false;
	}
	public function file($hash) {
		$path = $this->decode($hash);
		
		return ($file = $this->stat($path)) ? $file : $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		
		if (($file = $this->stat($path)) != false) {
			if ($realpath) {
				$file['realpath'] = $path;
			}
			return $file;
		}
		return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
	}
	public function dir($hash, $resolveLink=false) {
		if (($dir = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_DIR_NOT_FOUND);
		}

		if ($resolveLink && !empty($dir['thash'])) {
			$dir = $this->file($dir['thash']);
		}
		
		return $dir && $dir['mime'] == 'directory' && empty($dir['hidden']) 
			? $dir 
			: $this->setError(elFinder::ERROR_NOT_DIR);
	}
	public function scandir($hash) {
		if (($dir = $this->dir($hash)) == false) {
			return false;
		}
		
		return $dir['read']
			? $this->getScandir($this->decode($hash))
			: $this->setError(elFinder::ERROR_PERM_DENIED);
	}
	public function ls($hash) {
		if (($dir = $this->dir($hash)) == false || !$dir['read']) {
			return false;
		}
		
		$list = array();
		$path = $this->decode($hash);
		
		foreach ($this->getScandir($path) as $stat) {
			if (empty($stat['hidden']) && $this->mimeAccepted($stat['mime'])) {
				$list[] = $stat['name'];
			}
		}

		return $list;
	}
	public function tree($hash='', $deep=0, $exclude='') {
		$path = $hash ? $this->decode($hash) : $this->root;
		
		if (($dir = $this->stat($path)) == false || $dir['mime'] != 'directory') {
			return false;
		}
		
		$dirs = $this->gettree($path, $deep > 0 ? $deep -1 : $this->treeDeep-1, $this->decode($exclude));
		array_unshift($dirs, $dir);
		return $dirs;
	}
	public function parents($hash) {
		if (($current = $this->dir($hash)) == false) {
			return false;
		}

		$path = $this->decode($hash);
		$tree = array();
		
		while ($path && $path != $this->root) {
			$path = $this->_dirname($path);
			$stat = $this->stat($path);
			if (!empty($stat['hidden']) || !$stat['read']) {
				return false;
			}
			
			array_unshift($tree, $stat);
			if ($path != $this->root) {
				foreach ($this->gettree($path, 0) as $dir) {
					if (!in_array($dir, $tree)) {
						$tree[] = $dir;
					}
				}
			}
		}

		return $tree ? $tree : array($current);
	}
	public function tmb($hash) {
		$path = $this->decode($hash);
		$stat = $this->stat($path);
		
		if (isset($stat['tmb'])) {
			return $stat['tmb'] == "1" ? $this->createTmb($path, $stat) : $stat['tmb'];
		}
		return false;
	}
	public function size($hash) {
		return $this->countSize($this->decode($hash));
	}
	public function open($hash) {
		if (($file = $this->file($hash)) == false
		|| $file['mime'] == 'directory') {
			return false;
		}
		
		return $this->_fopen($this->decode($hash), 'rb');
	}
	public function close($fp, $hash) {
		$this->_fclose($fp, $this->decode($hash));
	}
	public function mkdir($dst, $name) {
		if ($this->commandDisabled('mkdir')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($dst);
		$dst  = $this->_joinPath($path, $name);
		$stat = $this->stat($dst);
		if (!empty($stat)) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		$this->clearcache();
		return ($path = $this->_mkdir($path, $name)) ? $this->stat($path) : false;
	}
	public function mkfile($dst, $name) {
		if ($this->commandDisabled('mkfile')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($dst);

		if ($this->stat($this->_joinPath($path, $name))) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		$this->clearcache();
		return ($path = $this->_mkfile($path, $name)) ? $this->stat($path) : false;
	}
	public function rename($hash, $name) {
		if ($this->commandDisabled('rename')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME, $name);
		}
		
		if (!($file = $this->file($hash))) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if ($name == $file['name']) {
			return $file;
		}
		
		if (!empty($file['locked'])) {
			return $this->setError(elFinder::ERROR_LOCKED, $file['name']);
		}
		
		$path = $this->decode($hash);
		$dir  = $this->_dirname($path);
		$stat = $this->stat($this->_joinPath($dir, $name));
		if ($stat) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		
		if (!$this->_move($path, $dir, $name)) {
			return false;
		}
		
		if (!empty($stat['tmb']) && $stat['tmb'] != "1") {
			$this->rmTmb($stat['tmb']);
		}
		
		$path = $this->_joinPath($dir, $name);

		$this->clearcache();
		return $this->stat($path);
	}
	public function duplicate($hash, $suffix='copy') {
		if ($this->commandDisabled('duplicate')) {
			return $this->setError(elFinder::ERROR_COPY, '#'.$hash, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_COPY, elFinder::ERROR_FILE_NOT_FOUND);
		}

		$path = $this->decode($hash);
		$dir  = $this->_dirname($path);

		return ($path = $this->copy($path, $dir, $this->uniqueName($dir, $this->_basename($path), ' '.$suffix.' '))) == false
			? false
			: $this->stat($path);
	}
	public function upload($fp, $dst, $name, $tmpname) {
		if ($this->commandDisabled('upload')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}

		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		$mime = $this->mimetype($this->mimeDetect == 'internal' ? $name : $tmpname); 
		if ($mime == 'unknown' && $this->mimeDetect == 'internal') {
			$mime = elFinderVolumeDriver::mimetypeInternalDetect($name);
		}

		// logic based on http://httpd.apache.org/docs/2.2/mod/mod_authz_host.html#order
		$allow  = $this->mimeAccepted($mime, $this->uploadAllow, null);
		$deny   = $this->mimeAccepted($mime, $this->uploadDeny,  null);
		$upload = true; // default to allow
		if (strtolower($this->uploadOrder[0]) == 'allow') { // array('allow', 'deny'), default is to 'deny'
			$upload = false; // default is deny
			if (!$deny && ($allow === true)) { // match only allow
				$upload = true;
			}// else (both match | no match | match only deny) { deny }
		} else { // array('deny', 'allow'), default is to 'allow' - this is the default rule
			$upload = true; // default is allow
			if (($deny === true) && !$allow) { // match only deny
				$upload = false;
			} // else (both match | no match | match only allow) { allow }
		}
		if (!$upload) {
			return $this->setError(elFinder::ERROR_UPLOAD_FILE_MIME);
		}

		if ($this->uploadMaxSize > 0 && filesize($tmpname) > $this->uploadMaxSize) {
			return $this->setError(elFinder::ERROR_UPLOAD_FILE_SIZE);
		}

		$dstpath = $this->decode($dst);
		$test    = $this->_joinPath($dstpath, $name);
		
		$file = $this->stat($test);
		$this->clearcache();
		
		if ($file) { // file exists
			if ($this->options['uploadOverwrite']) {
				if (!$file['write']) {
					return $this->setError(elFinder::ERROR_PERM_DENIED);
				} elseif ($file['mime'] == 'directory') {
					return $this->setError(elFinder::ERROR_NOT_REPLACE, $name);
				} 
				$this->remove($file);
			} else {
				$name = $this->uniqueName($dstpath, $name, '-', false);
			}
		}
		
		$w = $h = 0;
		if (strpos($mime, 'image') === 0 && ($s = getimagesize($tmpname))) {
			$w = $s[0];
			$h = $s[1];
		}
		// $this->clearcache();
		if (($path = $this->_save($fp, $dstpath, $name, $mime, $w, $h)) == false) {
			return false;
		}
		
		

		return $this->stat($path);
	}
	public function paste($volume, $src, $dst, $rmSrc = false) {
		$err = $rmSrc ? elFinder::ERROR_MOVE : elFinder::ERROR_COPY;
		
		if ($this->commandDisabled('paste')) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_PERM_DENIED);
		}

		if (($file = $volume->file($src, $rmSrc)) == false) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_FILE_NOT_FOUND);
		}

		$name = $file['name'];
		$errpath = $volume->path($src);
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError($err, $errpath, elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write'] || !$file['read']) {
			return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}

		$destination = $this->decode($dst);

		if (($test = $volume->closest($src, $rmSrc ? 'locked' : 'read', $rmSrc))) {
			return $rmSrc
				? $this->setError($err, $errpath, elFinder::ERROR_LOCKED, $volume->path($test))
				: $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}

		$test = $this->_joinPath($destination, $name);
		$stat = $this->stat($test);
		$this->clearcache();
		if ($stat) {
			if ($this->options['copyOverwrite']) {
				// do not replace file with dir or dir with file
				if (!$this->isSameType($file['mime'], $stat['mime'])) {
					return $this->setError(elFinder::ERROR_NOT_REPLACE, $this->_path($test));
				}
				// existed file is not writable
				if (!$stat['write']) {
					return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
				}
				// existed file locked or has locked child
				if (($locked = $this->closestByAttr($test, 'locked', true))) {
					return $this->setError(elFinder::ERROR_LOCKED, $this->_path($locked));
				}
				// remove existed file
				if (!$this->remove($test)) {
					return $this->setError(elFinder::ERROR_REPLACE, $this->_path($test));
				}
			} else {
				$name = $this->uniqueName($destination, $name, ' ', false);
			}
		}
		
		// copy/move inside current volume
		if ($volume == $this) {
			$source = $this->decode($src);
			// do not copy into itself
			if ($this->_inpath($destination, $source)) {
				return $this->setError(elFinder::ERROR_COPY_INTO_ITSELF, $path);
			}
			$method = $rmSrc ? 'move' : 'copy';
			
			return ($path = $this->$method($source, $destination, $name)) ? $this->stat($path) : false;
		}
		
		
		// copy/move from another volume
		if (!$this->options['copyTo'] || !$volume->copyFromAllowed()) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($path = $this->copyFrom($volume, $src, $destination, $name)) == false) {
			return false;
		}
		
		if ($rmSrc) {
			if ($volume->rm($src)) {
				$this->removed[] = $file;
			} else {
				return $this->setError(elFinder::ERROR_MOVE, $errpath, elFinder::ERROR_RM_SRC);
			}
		}
		return $this->stat($path);
	}
	public function getContents($hash) {
		$file = $this->file($hash);
		
		if (!$file) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if ($file['mime'] == 'directory') {
			return $this->setError(elFinder::ERROR_NOT_FILE);
		}
		
		if (!$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		return $this->_getContents($this->decode($hash));
	}
	public function putContents($hash, $content) {
		if ($this->commandDisabled('edit')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!($file = $this->file($hash))) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		$this->clearcache();
		return $this->_filePutContents($path, $content) ? $this->stat($path) : false;
	}
	public function extract($hash) {
		if ($this->commandDisabled('extract')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		$archiver = isset($this->archivers['extract'][$file['mime']])
			? $this->archivers['extract'][$file['mime']]
			: false;
			
		if (!$archiver) {
			return $this->setError(elFinder::ERROR_NOT_ARCHIVE);
		}
		
		$path   = $this->decode($hash);
		$parent = $this->stat($this->_dirname($path));

		if (!$file['read'] || !$parent['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		$this->clearcache();
		return ($path = $this->_extract($path, $archiver)) ? $this->stat($path) : false;
	}
	public function archive($hashes, $mime) {
		if ($this->commandDisabled('archive')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}

		$archiver = isset($this->archivers['create'][$mime])
			? $this->archivers['create'][$mime]
			: false;
			
		if (!$archiver) {
			return $this->setError(elFinder::ERROR_ARCHIVE_TYPE);
		}
		
		$files = array();
		
		foreach ($hashes as $hash) {
			if (($file = $this->file($hash)) == false) {
				return $this->error(elFinder::ERROR_FILE_NOT_FOUND, '#'+$hash);
			}
			if (!$file['read']) {
				return $this->error(elFinder::ERROR_PERM_DENIED);
			}
			$path = $this->decode($hash);
			if (!isset($dir)) {
				$dir = $this->_dirname($path);
				$stat = $this->stat($dir);
				if (!$stat['write']) {
					return $this->error(elFinder::ERROR_PERM_DENIED);
				}
			}
			
			$files[] = $this->_basename($path);
		}
		
		$name = (count($files) == 1 ? $files[0] : 'Archive').'.'.$archiver['ext'];
		$name = $this->uniqueName($dir, $name, '');
		$this->clearcache();
		return ($path = $this->_archive($dir, $files, $name, $archiver)) ? $this->stat($path) : false;
	}
	public function resize($hash, $width, $height, $x, $y, $mode = 'resize', $bg = '', $degree = 0) {
		if ($this->commandDisabled('resize')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write'] || !$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!$this->canResize($path, $file)) {
			return $this->setError(elFinder::ERROR_UNSUPPORT_TYPE);
		}

		switch($mode) {
			
			case 'propresize':
				$result = $this->imgResize($path, $width, $height, true, true);
				break;

			case 'crop':
				$result = $this->imgCrop($path, $width, $height, $x, $y);
				break;

			case 'fitsquare':
				$result = $this->imgSquareFit($path, $width, $height, 'center', 'middle', ($bg ? $bg : $this->options['tmbBgColor']));
				break;

			case 'rotate':
				$result = $this->imgRotate($path, $degree, ($bg ? $bg : $this->options['tmbBgColor']));
				break;

			default:
				$result = $this->imgResize($path, $width, $height, false, true);
				break;
		}

		if ($result) {
			if (!empty($file['tmb']) && $file['tmb'] != "1") {
				$this->rmTmb($file['tmb']);
			}
			$this->clearcache();
			return $this->stat($path);
		}
		
   		return false;
	}
	public function rm($hash) {
		return $this->commandDisabled('rm')
			? array(elFinder::ERROR_ACCESS_DENIED)
			: $this->remove($this->decode($hash));
	}
	public function search($q, $mimes) {
		return $this->doSearch($this->root, $q, $mimes);
	}
	public function dimensions($hash) {
		if (($file = $this->file($hash)) == false) {
			return false;
		}
		
		return $this->_dimensions($this->decode($hash), $file['mime']);
	}
	protected function setError($error) {
		
		$this->error = array();
		
		foreach (func_get_args() as $err) {
			if (is_array($err)) {
				$this->error = array_merge($this->error, $err);
			} else {
				$this->error[] = $err;
			}
		}
		
		// $this->error = is_array($error) ? $error : func_get_args();
		return false;
	}
	protected function encode($path) {
		if ($path !== '') {

			// cut ROOT from $path for security reason, even if hacker decodes the path he will not know the root
			$p = $this->_relpath($path);
			// if reqesting root dir $path will be empty, then assign '/' as we cannot leave it blank for crypt
			if ($p === '')	{
				$p = DIRECTORY_SEPARATOR;
			}

			// TODO crypt path and return hash
			$hash = $this->crypt($p);
			// hash is used as id in HTML that means it must contain vaild chars
			// make base64 html safe and append prefix in begining
			$hash = strtr(base64_encode($hash), '+/=', '-_.');
			// remove dots '.' at the end, before it was '=' in base64
			$hash = rtrim($hash, '.'); 
			// append volume id to make hash unique
			return $this->id.$hash;
		}
	}
	protected function decode($hash) {
		if (strpos($hash, $this->id) === 0) {
			// cut volume id after it was prepended in encode
			$h = substr($hash, strlen($this->id));
			// replace HTML safe base64 to normal
			$h = base64_decode(strtr($h, '-_.', '+/='));
			// TODO uncrypt hash and return path
			$path = $this->uncrypt($h); 
			// append ROOT to path after it was cut in encode
			return $this->_abspath($path);//$this->root.($path == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR.$path); 
		}
	}
	protected function crypt($path) {
		return $path;
	}
	protected function uncrypt($hash) {
		return $hash;
	}
	protected function nameAccepted($name) {
		if ($this->nameValidator) {
			if (function_exists($this->nameValidator)) {
				$f = $this->nameValidator;
				return $f($name);
			}
			return preg_match($this->nameValidator, $name);
		}
		return true;
	}
	public function uniqueName($dir, $name, $suffix = ' copy', $checkNum=true) {
		$ext  = '';

		if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
			$ext  = '.'.$m[1];
			$name = substr($name, 0,  strlen($name)-strlen($m[0]));
		} 
		
		if ($checkNum && preg_match('/('.$suffix.')(\d*)$/i', $name, $m)) {
			$i    = (int)$m[2];
			$name = substr($name, 0, strlen($name)-strlen($m[2]));
		} else {
			$i     = 1;
			$name .= $suffix;
		}
		$max = $i+100000;

		while ($i <= $max) {
			$n = $name.($i > 0 ? $i : '').$ext;

			if (!$this->stat($this->_joinPath($dir, $n))) {
				$this->clearcache();
				return $n;
			}
			$i++;
		}
		return $name.md5($dir).$ext;
	}
	protected function attr($path, $name, $val=false) {
		if (!isset($this->defaults[$name])) {
			return false;
		}
		
		
		$perm = null;
		
		if ($this->access) {
			if (is_array($this->access)) {
				$obj    = $this->access[0];
				$method = $this->access[1];
				$perm   = $obj->{$method}($name, $path, $this->options['accessControlData'], $this);
			} else {
				$func = $this->access;
				$perm = $func($name, $path, $this->options['accessControlData'], $this);
			}
			
			if ($perm !== null) {
				return !!$perm;
			}
		}
		
		for ($i = 0, $c = count($this->attributes); $i < $c; $i++) {
			$attrs = $this->attributes[$i];
			$p = $this->separator.$this->_relpath($path);
			if (isset($attrs[$name]) && isset($attrs['pattern']) && preg_match($attrs['pattern'], $p)) {
				$perm = $attrs[$name];
			} 
		}
		
		return $perm === null ? $this->defaults[$name] : !!$perm;
	}
	protected function stat($path) {
		return isset($this->cache[$path])
			? $this->cache[$path]
			: $this->updateCache($path, $this->_stat($path));
	}
	protected function updateCache($path, $stat) {
		if (empty($stat) || !is_array($stat)) {
			return $this->cache[$path] = array();
		}

		$stat['hash'] = $this->encode($path);

		$root = $path == $this->root;
		
		if ($root) {
			$stat['volumeid'] = $this->id;
			if ($this->rootName) {
				$stat['name'] = $this->rootName;
			}
		} else {
			if (empty($stat['name'])) {
				$stat['name'] = $this->_basename($path);
			}
			if (empty($stat['phash'])) {
				$stat['phash'] = $this->encode($this->_dirname($path));
			}
		}
		
		// fix name if required
		if ($this->options['utf8fix'] && $this->options['utf8patterns'] && $this->options['utf8replace']) {
			$stat['name'] = json_decode(str_replace($this->options['utf8patterns'], $this->options['utf8replace'], json_encode($stat['name'])));
		}
		
		
		if (empty($stat['mime'])) {
			$stat['mime'] = $this->mimetype($stat['name']);
		}
		
		// @todo move dateformat to client
		$stat['date'] = isset($stat['ts'])
			? $this->formatDate($stat['ts'])
			: 'unknown';
			
		if (!isset($stat['size'])) {
			$stat['size'] = 'unknown';
		}	

		$stat['read']  = intval($this->attr($path, 'read', isset($stat['read']) ? !!$stat['read'] : false));
		$stat['write'] = intval($this->attr($path, 'write', isset($stat['write']) ? !!$stat['write'] : false));
		if ($root) {
			$stat['locked'] = 1;
		} elseif ($this->attr($path, 'locked', !empty($stat['locked']))) {
			$stat['locked'] = 1;
		} else {
			unset($stat['locked']);
		}

		if ($root) {
			unset($stat['hidden']);
		} elseif ($this->attr($path, 'hidden', !empty($stat['hidden'])) 
		|| !$this->mimeAccepted($stat['mime'])) {
			$stat['hidden'] = $root ? 0 : 1;
		} else {
			unset($stat['hidden']);
		}
		
		if ($stat['read'] && empty($stat['hidden'])) {
			
			if ($stat['mime'] == 'directory') {
				// for dir - check for subdirs

				if ($this->options['checkSubfolders']) {
					if (isset($stat['dirs'])) {
						if ($stat['dirs']) {
							$stat['dirs'] = 1;
						} else {
							unset($stat['dirs']);
						}
					} elseif (!empty($stat['alias']) && !empty($stat['target'])) {
						$stat['dirs'] = isset($this->cache[$stat['target']])
							? intval(isset($this->cache[$stat['target']]['dirs']))
							: $this->_subdirs($stat['target']);
						
					} elseif ($this->_subdirs($path)) {
						$stat['dirs'] = 1;
					}
				} else {
					$stat['dirs'] = 1;
				}
			} else {
				// for files - check for thumbnails
				$p = isset($stat['target']) ? $stat['target'] : $path;
				if ($this->tmbURL && !isset($stat['tmb']) && $this->canCreateTmb($p, $stat)) {
					$tmb = $this->gettmb($p, $stat);
					$stat['tmb'] = $tmb ? $tmb : 1;
				}
				
			}
		}
		
		if (!empty($stat['alias']) && !empty($stat['target'])) {
			$stat['thash'] = $this->encode($stat['target']);
			unset($stat['target']);
		}

		return $this->cache[$path] = $stat;
	}
	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();

		foreach ($this->_scandir($path) as $p) {
			if (($stat = $this->stat($p)) && empty($stat['hidden'])) {
				$this->dirsCache[$path][] = $p;
			}
		}	
	}
	protected function clearcache() {
		$this->cache = $this->dirsCache = array();
	}
	protected function mimetype($path) {
		$type = '';
		
		if ($this->mimeDetect == 'finfo') {
			$type = @finfo_file($this->finfo, $path); 
		} elseif ($type == 'mime_content_type') {
			$type = mime_content_type($path);
		} else {
			$type = elFinderVolumeDriver::mimetypeInternalDetect($path);
		}
		
		$type = explode(';', $type);
		$type = trim($type[0]);
		
		if ($type == 'application/x-empty') {
			// finfo return this mime for empty files
			$type = 'text/plain';
		} elseif ($type == 'application/x-zip') {
			// http://elrte.org/redmine/issues/163
			$type = 'application/zip';
		}
		
		return $type == 'unknown' && $this->mimeDetect != 'internal'
			? elFinderVolumeDriver::mimetypeInternalDetect($path)
			: $type;
		
	}
	static protected function mimetypeInternalDetect($path) {
		$pinfo = pathinfo($path); 
		$ext   = isset($pinfo['extension']) ? strtolower($pinfo['extension']) : '';
		return isset(elFinderVolumeDriver::$mimetypes[$ext]) ? elFinderVolumeDriver::$mimetypes[$ext] : 'unknown';
		
	}
	protected function countSize($path) {
		$stat = $this->stat($path);

		if (empty($stat) || !$stat['read'] || !empty($stat['hidden'])) {
			return 'unknown';
		}
		
		if ($stat['mime'] != 'directory') {
			return $stat['size'];
		}
		
		$subdirs = $this->options['checkSubfolders'];
		$this->options['checkSubfolders'] = true;
		$result = 0;
		foreach ($this->getScandir($path) as $stat) {
			$size = $stat['mime'] == 'directory' && $stat['read'] 
				? $this->countSize($this->_joinPath($path, $stat['name'])) 
				: $stat['size'];
			if ($size > 0) {
				$result += $size;
			}
		}
		$this->options['checkSubfolders'] = $subdirs;
		return $result;
	}
	protected function isSameType($mime1, $mime2) {
		return ($mime1 == 'directory' && $mime1 == $mime2) || ($mime1 != 'directory' && $mime2 != 'directory');
	}
	protected function closestByAttr($path, $attr, $val) {
		$stat = $this->stat($path);
		
		if (empty($stat)) {
			return false;
		}
		
		$v = isset($stat[$attr]) ? $stat[$attr] : false;
		
		if ($v == $val) {
			return $path;
		}

		return $stat['mime'] == 'directory'
			? $this->childsByAttr($path, $attr, $val) 
			: false;
	}
	protected function childsByAttr($path, $attr, $val) {
		foreach ($this->_scandir($path) as $p) {
			if (($_p = $this->closestByAttr($p, $attr, $val)) != false) {
				return $_p;
			}
		}
		return false;
	}
	protected function getScandir($path) {
		$files = array();
		
		!isset($this->dirsCache[$path]) && $this->cacheDir($path);

		foreach ($this->dirsCache[$path] as $p) {
			if (($stat = $this->stat($p)) && empty($stat['hidden'])) {
				$files[] = $stat;
			}
		}

		return $files;
	}
	protected function gettree($path, $deep, $exclude='') {
		$dirs = array();
		
		!isset($this->dirsCache[$path]) && $this->cacheDir($path);

		foreach ($this->dirsCache[$path] as $p) {
			$stat = $this->stat($p);
			
			if ($stat && empty($stat['hidden']) && $path != $exclude && $stat['mime'] == 'directory') {
				$dirs[] = $stat;
				if ($deep > 0 && !empty($stat['dirs'])) {
					$dirs = array_merge($dirs, $this->gettree($p, $deep-1));
				}
			}
		}

		return $dirs;
	}	
	protected function doSearch($path, $q, $mimes) {
		$result = array();

		foreach($this->_scandir($path) as $p) {
			$stat = $this->stat($p);

			if (!$stat) { // invalid links
				continue;
			}

			if (!empty($stat['hidden']) || !$this->mimeAccepted($stat['mime'])) {
				continue;
			}
			
			$name = $stat['name'];

			if ($this->stripos($name, $q) !== false) {
				$stat['path'] = $this->_path($p);
				if ($this->URL && !isset($stat['url'])) {
					$stat['url'] = $this->URL . str_replace($this->separator, '/', substr($p, strlen($this->root) + 1));
				}
				
				$result[] = $stat;
			}
			if ($stat['mime'] == 'directory' && $stat['read'] && !isset($stat['alias'])) {
				$result = array_merge($result, $this->doSearch($p, $q, $mimes));
			}
		}
		
		return $result;
	}
	protected function copy($src, $dst, $name) {
		$srcStat = $this->stat($src);
		$this->clearcache();
		
		if (!empty($srcStat['thash'])) {
			$target = $this->decode($srcStat['thash']);
			$stat   = $this->stat($target);
			$this->clearcache();
			return $stat && $this->_symlink($target, $dst, $name)
				? $this->_joinPath($dst, $name)
				: $this->setError(elFinder::ERROR_COPY, $this->_path($src));
		} 
		
		if ($srcStat['mime'] == 'directory') {
			$test = $this->stat($this->_joinPath($dst, $name));
			
			if (($test && $test['mime'] != 'directory') || !$this->_mkdir($dst, $name)) {
				return $this->setError(elFinder::ERROR_COPY, $this->_path($src));
			}
			
			$dst = $this->_joinPath($dst, $name);
			
			foreach ($this->getScandir($src) as $stat) {
				if (empty($stat['hidden'])) {
					$name = $stat['name'];
					if (!$this->copy($this->_joinPath($src, $name), $dst, $name)) {
						return false;
					}
				}
			}
			$this->clearcache();
			return $dst;
		} 

		return $this->_copy($src, $dst, $name) 
			? $this->_joinPath($dst, $name) 
			: $this->setError(elFinder::ERROR_COPY, $this->_path($src));
	}
	protected function move($src, $dst, $name) {
		$stat = $this->stat($src);
		$stat['realpath'] = $src;
		$this->clearcache();
		
		if ($this->_move($src, $dst, $name)) {
			$this->removed[] = $stat;
			return $this->_joinPath($dst, $name);
		}
		
		return $this->setError(elFinder::ERROR_MOVE, $this->_path($src));
	}
	protected function copyFrom($volume, $src, $destination, $name) {
		
		if (($source = $volume->file($src)) == false) {
			return $this->setError(elFinder::ERROR_COPY, '#'.$src, $volume->error());
		}
		
		$errpath = $volume->path($src);
		
		if (!$this->nameAccepted($source['name'])) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_INVALID_NAME);
		}
				
		if (!$source['read']) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		if ($source['mime'] == 'directory') {
			$stat = $this->stat($this->_joinPath($destination, $name));
			$this->clearcache();
			if ((!$stat || $stat['mime'] != 'directory') && !$this->_mkdir($destination, $name)) {
				return $this->setError(elFinder::ERROR_COPY, $errpath);
			}
			
			$path = $this->_joinPath($destination, $name);
			
			foreach ($volume->scandir($src) as $entr) {
				if (!$this->copyFrom($volume, $entr['hash'], $path, $entr['name'])) {
					return false;
				}
			}
			
		} else {
			$mime = $source['mime'];
			$w = $h = 0;
			if (strpos($mime, 'image') === 0 && ($dim = $volume->dimensions($src))) {
				$s = explode('x', $dim);
				$w = $s[0];
				$h = $s[1];
			}
			
			if (($fp = $volume->open($src)) == false
			|| ($path = $this->_save($fp, $destination, $name, $mime, $w, $h)) == false) {
				$fp && $volume->close($fp, $src);
				return $this->setError(elFinder::ERROR_COPY, $errpath);
			}
			$volume->close($fp, $src);
		}
		
		return $path;
	}
	protected function remove($path, $force = false) {
		$stat = $this->stat($path);
		$stat['realpath'] = $path;
		if (!empty($stat['tmb']) && $stat['tmb'] != "1") {
			$this->rmTmb($stat['tmb']);
		}
		$this->clearcache();
		
		if (empty($stat)) {
			return $this->setError(elFinder::ERROR_RM, $this->_path($path), elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$force && !empty($stat['locked'])) {
			return $this->setError(elFinder::ERROR_LOCKED, $this->_path($path));
		}
		
		if ($stat['mime'] == 'directory') {
			foreach ($this->_scandir($path) as $p) {
				$name = $this->_basename($p);
				if ($name != '.' && $name != '..' && !$this->remove($p)) {
					return false;
				}
			}
			if (!$this->_rmdir($path)) {
				return $this->setError(elFinder::ERROR_RM, $this->_path($path));
			}
			
		} else {
			if (!$this->_unlink($path)) {
				return $this->setError(elFinder::ERROR_RM, $this->_path($path));
			}
		}

		$this->removed[] = $stat;
		return true;
	}
	protected function tmbname($stat) {
		return $stat['hash'].$stat['ts'].'.png';
	}
	protected function gettmb($path, $stat) {
		if ($this->tmbURL && $this->tmbPath) {
			// file itself thumnbnail
			if (strpos($path, $this->tmbPath) === 0) {
				return basename($path);
			}

			$name = $this->tmbname($stat);
			if (file_exists($this->tmbPath.DIRECTORY_SEPARATOR.$name)) {
				return $name;
			}
		}
		return false;
	}
	protected function canCreateTmb($path, $stat) {
		return $this->tmbPathWritable 
			&& strpos($path, $this->tmbPath) === false // do not create thumnbnail for thumnbnail
			&& $this->imgLib 
			&& strpos($stat['mime'], 'image') === 0 
			&& ($this->imgLib == 'gd' ? $stat['mime'] == 'image/jpeg' || $stat['mime'] == 'image/png' || $stat['mime'] == 'image/gif' : true);
	}
	protected function canResize($path, $stat) {
		return $this->canCreateTmb($path, $stat);
	}
	protected function createTmb($path, $stat) {
		if (!$stat || !$this->canCreateTmb($path, $stat)) {
			return false;
		}

		$name = $this->tmbname($stat);
		$tmb  = $this->tmbPath.DIRECTORY_SEPARATOR.$name;

		// copy image into tmbPath so some drivers does not store files on local fs
		if (($src = $this->_fopen($path, 'rb')) == false) {
			return false;
		}

		if (($trg = fopen($tmb, 'wb')) == false) {
			$this->_fclose($src, $path);
			return false;
		}

		while (!feof($src)) {
			fwrite($trg, fread($src, 8192));
		}

		$this->_fclose($src, $path);
		fclose($trg);

		$result = false;
		
		$tmbSize = $this->tmbSize;
		
  		if (($s = getimagesize($tmb)) == false) {
			return false;
		}
    
    	if ($s[0] <= $tmbSize && $s[1]  <= $tmbSize) {
     	   $result = $this->imgSquareFit($tmb, $tmbSize, $tmbSize, 'center', 'middle', $this->options['tmbBgColor'], 'png' );

	    } else {

	    	if ($this->options['tmbCrop']) {
        
        		/* Resize and crop if image bigger than thumbnail */
	        	if (!(($s[0] > $tmbSize && $s[1] <= $tmbSize) || ($s[0] <= $tmbSize && $s[1] > $tmbSize) ) || ($s[0] > $tmbSize && $s[1] > $tmbSize)) {
    				$result = $this->imgResize($tmb, $tmbSize, $tmbSize, true, false, 'png');
	        	}

				if (($s = getimagesize($tmb)) != false) {
					$x = $s[0] > $tmbSize ? intval(($s[0] - $tmbSize)/2) : 0;
					$y = $s[1] > $tmbSize ? intval(($s[1] - $tmbSize)/2) : 0;
					$result = $this->imgCrop($tmb, $tmbSize, $tmbSize, $x, $y, 'png');
				}

    		} else {
        		$result = $this->imgResize($tmb, $tmbSize, $tmbSize, true, true, $this->imgLib, 'png');
        		$result = $this->imgSquareFit($tmb, $tmbSize, $tmbSize, 'center', 'middle', $this->options['tmbBgColor'], 'png' );
      		}

		}
		if (!$result) {
			unlink($tmb);
			return false;
		}

		return $name;
	}
  	protected function imgResize($path, $width, $height, $keepProportions = false, $resizeByBiggerSide = true, $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}

    	$result = false;
		list($size_w, $size_h) = array($width, $height);
    	if ($keepProportions == true) {
      		list($orig_w, $orig_h, $new_w, $new_h) = array($s[0], $s[1], $width, $height);
      		/* Calculating image scale width and height */
      		$xscale = $orig_w / $new_w;
      		$yscale = $orig_h / $new_h;
      		/* Resizing by biggest side */
			if ($resizeByBiggerSide) {
		        if ($orig_w > $orig_h) {
					$size_h = $orig_h * $width / $orig_w;
					$size_w = $width;
        		} else {
          			$size_w = $orig_w * $height / $orig_h;
          			$size_h = $height;
				}
			} else {
        		if ($orig_w > $orig_h) {
          			$size_w = $orig_w * $height / $orig_h;
          			$size_h = $height;
		        } else {
					$size_h = $orig_h * $width / $orig_w;
					$size_w = $width;
				}
			}
    	}
		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {

					return false;
				}
				$img->resizeImage($size_w, $size_h, Imagick::FILTER_LANCZOS, true);
				$result = $img->writeImage($path);
				return $result ? $path : false;
				break;
			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}
				if ($img &&  false != ($tmp = imagecreatetruecolor($size_w, $size_h))) {
					if (!imagecopyresampled($tmp, $img, 0, 0, 0, 0, $size_w, $size_h, $s[0], $s[1])) {
							return false;
					}
					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}
					imagedestroy($img);
					imagedestroy($tmp);
					return $result ? $path : false;
				}
				break;
		}
		return false;
  	}
   	protected function imgCrop($path, $width, $height, $x, $y, $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}
		$result = false;
		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {
					return false;
				}
				$img->cropImage($width, $height, $x, $y);
				$result = $img->writeImage($path);
				return $result ? $path : false;
				break;
			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}
				if ($img &&  false != ($tmp = imagecreatetruecolor($width, $height))) {
					if (!imagecopy($tmp, $img, 0, 0, $x, $y, $width, $height)) {
						return false;
					}
					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}
					imagedestroy($img);
					imagedestroy($tmp);
					return $result ? $path : false;
				}
				break;
		}
		return false;
	}
		protected function imgSquareFit($path, $width, $height, $align = 'center', $valign = 'middle', $bgcolor = '#0000ff', $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}
		$result = false;
		/* Coordinates for image over square aligning */
		$y = ceil(abs($height - $s[1]) / 2); 
		$x = ceil(abs($width - $s[0]) / 2);
    
		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {
					return false;
				}

				$img1 = new Imagick();
				$img1->newImage($width, $height, new ImagickPixel($bgcolor));
				$img1->setImageColorspace($img->getImageColorspace());
				$img1->setImageFormat($destformat != null ? $destformat : $img->getFormat());
				$img1->compositeImage( $img, imagick::COMPOSITE_OVER, $x, $y );
				$result = $img1->writeImage($path);
				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				if ($img &&  false != ($tmp = imagecreatetruecolor($width, $height))) {

					if ($bgcolor == 'transparent') {
						list($r, $g, $b) = array(0, 0, 255);
					} else {
						list($r, $g, $b) = sscanf($bgcolor, "#%02x%02x%02x");
					}

					$bgcolor1 = imagecolorallocate($tmp, $r, $g, $b);
						
					if ($bgcolor == 'transparent') {
						$bgcolor1 = imagecolortransparent($tmp, $bgcolor1);
					}

					imagefill($tmp, 0, 0, $bgcolor1);

					if (!imagecopy($tmp, $img, $x, $y, 0, 0, $s[0], $s[1])) {
						return false;
					}

					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}

					imagedestroy($img);
					imagedestroy($tmp);

					return $result ? $path : false;
				}
				break;
		}
		return false;
	}
	protected function imgRotate($path, $degree, $bgcolor = '#ffffff', $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}
		$result = false;

		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {
					return false;
				}

				$img->rotateImage(new ImagickPixel($bgcolor), $degree);
				$result = $img->writeImage($path);
				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				$degree = 360 - $degree;
				list($r, $g, $b) = sscanf($bgcolor, "#%02x%02x%02x");
				$bgcolor = imagecolorallocate($img, $r, $g, $b);
				$tmp = imageRotate($img, $degree, (int)$bgcolor);

				if ($destformat == 'jpg' || ($destformat == null && $s['mime'] == 'image/jpeg')) {
					$result = imagejpeg($tmp, $path, 100);
				} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
					$result = imagegif($tmp, $path, 7);
				} else {
					$result = imagepng($tmp, $path, 7);
				}
				imageDestroy($img);
				imageDestroy($tmp);
				return $result ? $path : false;
				break;
		}
		return false;
	}
	protected function procExec($command , array &$output = null, &$return_var = -1, array &$error_output = null) {

		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin
			1 => array("pipe", "w"),  // stdout
			2 => array("pipe", "w")   // stderr
		);

		$process = proc_open($command, $descriptorspec, $pipes, null, null);

		if (is_resource($process)) {

			fclose($pipes[0]);

			$tmpout = '';
			$tmperr = '';

			$output = stream_get_contents($pipes[1]);
			$error_output = stream_get_contents($pipes[2]);

			fclose($pipes[1]);
			fclose($pipes[2]);
			$return_var = proc_close($process);
		}
		return $return_var;
	}
	protected function rmTmb($tmb) {
		$tmb = $this->tmbPath.DIRECTORY_SEPARATOR.$tmb;
		file_exists($tmb) && @unlink($tmb);
		clearstatcache();
	}
	protected function formatDate($ts) {
		if ($ts > $this->today) {
			return 'Today '.date($this->options['timeFormat'], $ts);
		}
		
		if ($ts > $this->yesterday) {
			return 'Yesterday '.date($this->options['timeFormat'], $ts);
		} 
		
		return date($this->options['dateFormat'], $ts);
	}
	protected function stripos($haystack , $needle , $offset = 0) {
		if (function_exists('mb_stripos')) {
			return mb_stripos($haystack , $needle , $offset);
		} else if (function_exists('mb_strtolower') && function_exists('mb_strpos')) {
			return mb_strpos(mb_strtolower($haystack), mb_strtolower($needle), $offset);
		} 
		return stripos($haystack , $needle , $offset);
	}
	abstract protected function _dirname($path);
	abstract protected function _basename($path);
	abstract protected function _joinPath($dir, $name);
	abstract protected function _normpath($path);
	abstract protected function _relpath($path);
	abstract protected function _abspath($path);
	abstract protected function _path($path);
	abstract protected function _inpath($path, $parent);
	abstract protected function _stat($path);
	abstract protected function _subdirs($path);
	abstract protected function _dimensions($path, $mime);
	abstract protected function _scandir($path);
	abstract protected function _fopen($path, $mode="rb");
	abstract protected function _fclose($fp, $path='');
	abstract protected function _mkdir($path, $name);
	abstract protected function _mkfile($path, $name);
	abstract protected function _symlink($source, $targetDir, $name);
	abstract protected function _copy($source, $targetDir, $name);
	abstract protected function _move($source, $targetDir, $name);
	abstract protected function _unlink($path);
	abstract protected function _rmdir($path);
	abstract protected function _save($fp, $dir, $name, $mime, $w, $h);
	abstract protected function _getContents($path);
	abstract protected function _filePutContents($path, $content);
	abstract protected function _extract($path, $arc);
	abstract protected function _archive($dir, $files, $name, $arc);
	abstract protected function _checkArchivers();
	
} // END class
