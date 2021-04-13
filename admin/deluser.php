<?php

include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$delId = intval($FORM['delId']);

if (isset($delId) and $delId != "") {

    $hasdel = md5($delId . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_mbrs', array('id' => $delId));
        $db->delete(DB_TBLPREFIX . '_mbrplans', array('idmbr' => $delId));
        $_SESSION['dotoaster'] = "toastr.success('Record deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Record deleted failed!', 'Error');";
    }

    $redirto = redir_to($FORM['redir']);
    header('location: ' . $redirto);
    exit;
}