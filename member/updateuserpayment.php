<?php

include_once('../common/init.loader.php');
// echo "string";exit();
$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

    extract($FORM);
// var_dump($FORM);exit();
    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    $firstname = mystriptag($firstname);
    $lastname = mystriptag($lastname);
    $username = mystriptag($username, 'user');
    $email = mystriptag($email, 'email');

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    $isrecapv3 = 1;
    // if new username exist, keep using old username

    if ($isrecapv3 == 0) {
        $_SESSION['show_msg'] = showalert('warning', 'Error!', 'Recaptcha failed, please try it again!');
        $redirval = "?res=rcapt";
    } elseif (count($sql) > 0) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', 'Username already exist!');
        $redirval = "?res=exist";
    } else {

            $data = array(
                'payment_id' => $razorpay_payment_id,
            );
            $insert = $db->update(DB_TBLPREFIX . '_mbrs', $data,array('id' => $user_id));
            // $newmbrid = $db->lastInsertId();

            $_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';

            if ($insert) {
                    $_SESSION['customer_name']= $_SESSION['customer_email']='';
                    
                $redirval = SURL . "/" . MBRFOLDER_NAME;
    // $redirval = $cfgrow['site_url'] . "/member/razorpay.php?user_id=".$newmbrid."";
                // var_dump($redirval);exit;
            } else {
                $redirval = "?res=errsql";
            }
       
    }
    // var_dump(expression)
    redirpageto($redirval);
    exit;

$modalcontent = file_get_contents(SURL . "/admin/terms.html");
$refbystr = ($sesref['username'] != '') ? "<div class='card-header-action'><span class='badge badge-info'>| {$sesref['username']}</span></div>" : '';

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
