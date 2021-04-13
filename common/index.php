<?php

include_once('init.loader.php');

// reset member
if ($FORM['prkey'] != '') {
    $seskey = base64_decode($FORM['prkey']);
    $_SESSION['pr_key'] = $seskey;
    redirpageto('reset-password.php');
    exit;
}

// Ajs Get Value
if ($FORM['agv'] != '') {

    $agvarr = explode('-', $FORM['agv'], 2);
    $key = $agvarr[0];
    $value = mystriptag($agvarr[1]);

    if ($key == 'un2i' || $key == 'unex') {
        // username to member info
        $condition = ' AND username = "' . $value . '"';
    } elseif ($key == 'id2i') {
        // id to member info
        $condition = ' AND id = "' . $value . '"';
    } elseif ($key == 'em2i') {
        // email to member info
        $condition = ' AND email = "' . $value . '"';
    } else {
        
    }

    if ($condition != '') {
        // username is exist?
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', $condition . " LIMIT 1");
        $mbrRow = array();
        foreach ($row as $value) {
            $mbrRow = array_merge($mbrRow, $value);
        }
        if ($key == 'unex') {
            if ($mbrRow['id'] > 0 || $value == '') {
                // if username NOT available
                echo "<i class='far fa-times-circle fa-fw text-danger'></i>";
            } else {
                // otherwise
                echo "<i class='far fa-check-circle fa-fw text-success'></i>";
            }
        } else {
            if ($mbrRow['id'] > 0) {
                // display member info
                echo "<span class='text-primary'><strong>{$mbrRow['username']}</strong><br />{$mbrRow['firstname']} {$mbrRow['lastname']} ({$mbrRow['email']})</span>";
            } else {
                echo "<span class='text-primary'><strong>Administrator</strong>";
                //echo "<i class='far fa-question-circle fa-fw text-warning'></i>";
            }
        }
    }

    exit;
}