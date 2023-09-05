<?php
//////////////////////////////////////////////////////////////////////
// CONFIGS

// Enable FTP connector netmount
$useFtpNetMount = true;

// Set root path/url
define('ELFINDER_ROOT_PATH', dirname(__FILE__));
define('ELFINDER_ROOT_URL' , dirname($_SERVER['SCRIPT_NAME']));

// Volumes config
// Documentation for connector options:
// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
$opts = array(
	'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
			'path'          => ELFINDER_ROOT_PATH . '/files/', // path to files (REQUIRED)
			'URL'           => ELFINDER_ROOT_URL  . '/files/', // URL to files (REQUIRED)
			'trashHash'     => 't1_Lw',                     // elFinder's hash of trash folder
			'uploadDeny'    => array('all'),                // All Mimetypes not allowed to upload
			'uploadAllow'   => array('image', 'text/plain'),// Mimetype `image` and `text/plain` allowed to upload
			'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
			'accessControl' => 'access'                     // disable and hide dot starting files (OPTIONAL)
		),
		// Trash volume
		array(
			'id'            => '1',
			'driver'        => 'Trash',
			'path'          => ELFINDER_ROOT_PATH . '/files/.trash/',
			'tmbURL'        => ELFINDER_ROOT_URL . '/files/.trash/.tmb/',
			'uploadDeny'    => array('all'),                // Recomend the same settings as the original volume that uses the trash
			'uploadAllow'   => array('image', 'text/plain'),// Same as above
			'uploadOrder'   => array('deny', 'allow'),      // Same as above
			'accessControl' => 'access',                    // Same as above
		)
	),
	'optionsNetVolumes' => array(
		'*' => array(
			'tmbURL'    => ELFINDER_ROOT_URL  . '/files/.tmb',
			'tmbPath'   => ELFINDER_ROOT_PATH . '/files/.tmb',
			'syncMinMs' => 30000
		)
	)
);

//////////////////////////////////////////////////////////////////////
// load composer autoload.php
require './vendor/autoload.php';

// Enable FTP connector netmount
if ($useFtpNetMount) {
	elFinder::$netDrivers['ftp'] = 'FTP';
}

// Required for Dropbox network mount
// Installation by composer
// `composer require kunalvarma05/dropbox-php-sdk`
// Enable network mount
elFinder::$netDrivers['dropbox2'] = 'Dropbox2';
// Dropbox2 Netmount driver need next two settings. You can get at https://www.dropbox.com/developers/apps
// AND reuire regist redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=dropbox2&host=1"
define('ELFINDER_DROPBOX_APPKEY',    'ngqzor1x7ztd8y5');
define('ELFINDER_DROPBOX_APPSECRET', 'zlx8w1perv88r6p');
// ===============================================

// // Required for Google Drive network mount
// // Installation by composer
// // `composer require google/apiclient:^2.0`
// // Enable network mount
// elFinder::$netDrivers['googledrive'] = 'GoogleDrive';
// // GoogleDrive Netmount driver need next two settings. You can get at https://console.developers.google.com
// // AND reuire regist redirect url to "YOUR_CONNECTOR_URL?cmd=netmount&protocol=googledrive&host=1"
// define('ELFINDER_GOOGLEDRIVE_CLIENTID',     '');
// define('ELFINDER_GOOGLEDRIVE_CLIENTSECRET', '');
// // Required case of without composer
// define('ELFINDER_GOOGLEDRIVE_GOOGLEAPICLIENT', '/path/to/google-api-php-client/vendor/autoload.php');
// ===============================================

// // Required for One Drive network mount
// //  * cURL PHP extension required
// //  * HTTP server PATH_INFO supports required
// // Enable network mount
// elFinder::$netDrivers['onedrive'] = 'OneDrive';
// // GoogleDrive Netmount driver need next two settings. You can get at https://dev.onedrive.com
// // AND reuire regist redirect url to "YOUR_CONNECTOR_URL/netmount/onedrive/1"
// define('ELFINDER_ONEDRIVE_CLIENTID',     '');
// define('ELFINDER_ONEDRIVE_CLIENTSECRET', '');
// ===============================================

// // Required for Box network mount
// //  * cURL PHP extension required
// // Enable network mount
// elFinder::$netDrivers['box'] = 'Box';
// // Box Netmount driver need next two settings. You can get at https://developer.box.com
// // AND reuire regist redirect url to "YOUR_CONNECTOR_URL"
// define('ELFINDER_BOX_CLIENTID',     '');
// define('ELFINDER_BOX_CLIENTSECRET', '');
// ===============================================

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume, $isDir, $relpath) {
	return basename($path)[0] === '.'            // if file/folder begins with '.' (dot) with out volume root
			 && strlen($relpath) !== 1
		? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
		:  null;                                 // else elFinder decide it itself
}

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

// end connector
