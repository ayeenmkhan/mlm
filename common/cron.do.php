<?php

// file execute by page load
if (!defined('OK_LOADME')) {
    die("^-^ DODODO");
}

$nowdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
$lastdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']) - 60);
$mw = $bpprow['ma' . 'xwi' . 'dth'];
$md = $bpprow['m' . 'axde' . 'pth'];
if ($cfgrow['cronts'] < $lastdatetm) {
    /* ========= */
    /*  Do Task  */
    /* ========= */

    // process commission
    dotrxwallet();

    // delete old session
    dellog_sess();

    // check expired member
    do_expmbr();

    // update cron
    $data = array(
        'cronts' => $nowdatetm,
    );
    $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
}

/*To reset the Bounus Counter after one day*/
$current_date= date('Y-m-d H:i:s');
$start_date = new DateTime($cfgrow['counter_cron']);
$since_start = $start_date->diff(new DateTime($current_date));
// echo $since_start->days.' days total<br>';
// // echo $since_start->y.' years<br>';
// echo $since_start->m.' months<br>';
// echo $since_start->d.' days<br>';
// // echo $since_start->h.' hours<br>';
// echo $since_start->i.' minutes<br>';
// echo $since_start->s.' seconds<br>';
 $days= $since_start->days;
 // var_dump($hours);exit();
if($days>=1){

    $data = array(
        'counter_cron' => $current_date,
    );
    $dataCounter = array(
        'counter' => 0,
    );
    // echo $query = DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId);
    // echo "<pre>";print_r($query);exit();
 $response= cronjobupdate($data,$dataCounter,$didId);
// var_dump($response);exit();
}

$bpprow['ma' . 'xwi' . 'dth'] = ($mw > 4) ? 3 + 2 : $mw;
$bpprow['m' . 'axde' . 'pth'] = ($md > 17) ? 15 + 3 : $md;
