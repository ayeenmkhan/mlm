<?php

include_once('../common/init.loader.php');

$seskey = verifylog_sess('member');
if ($seskey == '') {
    die('o o p s !');
}

function getMyDataChart($mbrstr, $days, $format = 'd/m') {
    global $db;

    $m = date("m");
    $de = date("d");
    $y = date("Y");
    $allarr = $dateArray = $datearr = $refarr = $ernarr = array();
    for ($i = 0; $i <= $days - 1; $i++) {
        $dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
        $datearr[] = date('Y-m-d', mktime(0, 0, 0, $m, ($de - $i), $y));
    }

    foreach ($datearr as $key => $value) {
        $condition = " AND sprlist LIKE '%:{$mbrstr['mpid']}|%' AND reg_date LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
        $refarr[] = intval($row[0]['totref']);
    }
    foreach ($datearr as $key => $value) {
        $condition = " AND txtoid = '{$mbrstr['id']}' AND txstatus = '1' AND (txtoken LIKE '%|LCM:%' OR txtoken LIKE '%|WALT:IN|%') AND txdatetm LIKE '%{$value}%'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'SUM(txamount) as sumearn', $condition);
        $ernarr[] = floatval($row[0]['sumearn']);
    }
    $allarr['dref'] = array_reverse($refarr);
    $allarr['dern'] = array_reverse($ernarr);
    $allarr['days'] = array_reverse($dateArray);
    return $allarr;
}

$seskey = verifylog_sess('member');
if ($seskey == '') {
    $datarr[] = array();
    echo json_encode($datarr);
    die();
}
$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');

$mbrstr = getmbrinfo($username, 'username');
$getallarr = getMyDataChart($mbrstr, 7, "l\nj M");

$datarr['days'] = $getallarr['days'];
$datarr['dref'] = array(64, 37, 50, 30, 43, 27, 48);
$datarr['dern'] = array(530, 302, 430, 640, 387, 270, 488);

echo json_encode($getallarr);
//echo json_encode($datarr);
