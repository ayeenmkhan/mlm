<?php

include_once('../common/init.loader.php');
// echo "string";exit();
$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    $user_id = $_GET['user_id'];
    // if new username exist, keep using old username
            $data = array(
                'confirm_email' => 1,
            );
            $update = $db->update(DB_TBLPREFIX . '_mbrs', $data,array('id' => $user_id));
            // $newmbrid = $db->lastInsertId();
                 $_SESSION['dotoaster'] = "toastr.success('Email Address Verified Successfuly!', 'Success')";
                 $redirval = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
    // $redirval = $cfgrow['site_url'] . "/member/razorpay.php?user_id=".$newmbrid."";
                // var_dump($redirval);exit;
       
    // var_dump(expression)
    redirpageto($redirval);
    exit;

$modalcontent = file_get_contents(SURL . "/admin/terms.html");
$refbystr = ($sesref['username'] != '') ? "<div class='card-header-action'><span class='badge badge-info'>| {$sesref['username']}</span></div>" : '';

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
