<?php
session_start(); 
if(!isset($_SESSION['unox'])) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
error_reporting(0); // Set E_ALL for debuging

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
//
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}
// ** CMSUno
$u = $_SERVER['PHP_SELF'];
$q = explode('/',$u); $u = '/';
foreach($q as $r)
	{
	if($r=='uno') break;
	else if($r!='') $u .= $r.'/';
	}
// ***
$opts = array(
	// 'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
			'path'          => '../../../../files/', // path to files (REQUIRED)
			'URL'           => $u . 'files/', // URL to files (REQUIRED)
			'accessControl' => 'access' // disable and hide dot starting files (OPTIONAL)
		)
	)
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
