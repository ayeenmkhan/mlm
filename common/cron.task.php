<?php

// file execute by system cron schedule
include_once('config.php');
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

// -----

include_once('db.class.php');
include_once('sys.func.php');
include_once('value.list.php');
include_once('en.lang.php');

$LANG = array_merge((array) $LANG, (array) $lang);

$dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
$pdo = "";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$db = new Database($pdo);

// load site configuration

$didId = 1;

// settings
$row = $db->getAllRecords(DB_TBLPREFIX . '_configs', '*', ' AND cfgid = "' . $didId . '"');
$cfgrow = array();
foreach ($row as $value) {
    $cfgrow = array_merge($cfgrow, $value);
}
$langloadf = INSTALL_PATH . '/common/lang/' . $cfgrow['langiso'] . '.lang.php';
if (file_exists($langloadf)) {
    $TEMPLANG = $LANG;
    include_once($langloadf);
    $LANG = array_filter($LANG);
    $LANG = array_merge($TEMPLANG, $LANG);
}

$bpprow = array();

// baseplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_baseplan', '*', ' AND bpid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
// payplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*', ' AND ppid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
$bpprow['currencysym'] = base64_decode($bpprow['currencysym']);

// load cron do
include_once('cron.do.php');

// end vars
$row = $value = '';
