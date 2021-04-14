<?php

include_once('../common/config.php');
if (!defined('INSTALL_PATH') || !defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}

include_once('../common/db.class.php');
$dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
$pdo = "";
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$db = new Database($pdo);

// -----

session_start();

// get referrer username
$refun = preg_replace("/[^A-Za-z0-9]/", '', $_REQUEST['ref']);

if ($_SESSION['ref_sess_un'] != $refun) {
    $mbrhits = $db->getRecFrmQry("SELECT id, hits FROM " . DB_TBLPREFIX . "_mbrs WHERE username = '{$refun}' AND mbrstatus = '1'");
    $id = $mbrhits[0]['id'];
    $hits = $mbrhits[0]['hits'] + 1;
    $db->update(DB_TBLPREFIX . '_mbrs', array('hits' => $hits), array('id' => $id));
}
$_SESSION['ref_sess_un'] = $refun;
header('Location: ../member/register.php');
exit;
