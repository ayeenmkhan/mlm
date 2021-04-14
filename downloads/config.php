<?php

define('OK_LOADME', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// --- start ---

date_default_timezone_set('UTC');
//Live server
define('DB_HOST', 'localhost');
define('DB_USER', 'fitnessd_admin');
define('DB_PASSWORD', 'tl@Pc*Y-_GR*');
define('DB_NAME', 'fitnessd_bundle-package');
define('DB_TBLPREFIX', 'netw');

//Localserver
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASSWORD', '123');
// define('DB_NAME', 'bundle-package');
// define('DB_TBLPREFIX', 'netw');

// define('INSTALL_PATH', 'C:\localhost');
define('INSTALL_PATH', $_SERVER['DOCUMENT_ROOT'].'/bundle-package');
define('SURL','http://'.$_SERVER['HTTP_HOST'].'/bundle-package');
define('DECIMAL_POINT', 2);

define('DEFIMG_LOGO', '../assets/image/logo_defaultimage.png');
define('DEFIMG_PLAN', '../assets/image/plan_defaultimage.jpg');
define('DEFIMG_FILE', '../assets/image/file_defaultimage.jpg');
define('DEFIMG_SITE', '../assets/image/site_defaultimage.jpg');
define('DEFIMG_ADM', '../assets/image/adm_defaultimage.jpg');
define('DEFIMG_MBR', '../assets/image/mbr_defaultimage.jpg');

define('ADMFOLDER_NAME', 'admin');
define('MBRFOLDER_NAME', 'member');
define('UIDFOLDER_NAME', 'id');

//ini_set('log_errors', 'On');
//ini_set('error_log', 'netw_error.log');
