<?php
include_once('../common/init.loader.php');

$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    $firstname = mystriptag($firstname);
    $lastname = mystriptag($lastname);
    $username = mystriptag($username, 'user');
    $email = mystriptag($email, 'email');
    $phone = mystriptag($phone, 'phone');

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;

    $isrecapv3 = 1;
    if ($cfgrow['isrecaptcha'] == 1 && isset($FORM['g-recaptcha-response'])) {
        $secret = $cfgrow['rc_securekey'];
        $response = $FORM['g-recaptcha-response'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        // call curl to POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $response, 'remoteip' => $remoteIp), '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);

        // verify the response
        if ($arrResponse["success"] == '1' && $arrResponse["score"] >= 0.5) {
            // valid submission
        } else {
            $isrecapv3 = 0;
        }
    }

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $username . '" OR phone LIKE "'.$phone.'"';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");

    // var_dump($sql);exit;
    if ($isrecapv3 == 0) {
        $_SESSION['show_msg'] = showalert('warning', 'Error!', 'Recaptcha failed, please try it again!');
        $redirval = "?res=rcapt";
    } elseif (count($sql) > 0) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', 'Username / Phone Number already exist!');
        $redirval = SURL . "/member/register.php?res=exist";
    } else {

        // if (!dumbtoken($dumbtoken)) {
        //     $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        //     $redirval = "?res=errtoken";
        //     redirpageto($redirval);
        //     exit;
        // }
        $six_digit_random_number = mt_rand(100000, 999999);

        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $passwordconfirm = filter_var($passwordconfirm, FILTER_SANITIZE_STRING);

        $passres = passmeter($password);
        if ($password != $passwordconfirm) {
            $_SESSION['show_msg'] = showalert('danger', 'Password Mismatch', 'Both entered passwords must be the same. Please try it again!');
            $redirval = SURL . "/member/register.php?res=errpass";
        } elseif ($passres == 1) {
            $password = getpasshash($password);
            $data = array(
                'in_date' => $in_date,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'ewallet' => mystriptag($package),
                'payment_id' => $razorpay_payment_id,
                'email' => $email,
                'phone' => $phone,
                'validation_code' => $six_digit_random_number,
                'password' => $password,
            );
                // var_dump($data);exit();
            $insert = $db->insert(DB_TBLPREFIX . '_mbrs', $data);
            $newmbrid = $db->lastInsertId();
            // $insert = 1;

            $_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] =  $_SESSION['email'] = $_SESSION['phone']='';

            $_SESSION['customer_name']= $firstname . ' ' . $lastname;
            $_SESSION['customer_email']= $email;
            $_SESSION['customer_phone']= $phone;
            if ($insert) {
                //require_once('../common/mailer.do.php');

                // send welcome email
                $cntaddarr['fullname'] = $firstname . ' ' . $lastname;
                $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
                $cntaddarr['rawpassword'] = $passwordconfirm;

                // delivermail('mbr_reg', $newmbrid, $cntaddarr);

                // if ($cfgtoken['isautoregplan'] == 1) {
                    // register to membership
                    if(!isset($unref)){
                        $unref=$username;
                    }else{
                        $unref= $unref;
                    }
                    $mbrstr = getmbrinfo($newmbrid);
                    $refstr = getmbrinfo($unref, 'username');
                    regmbrplans($mbrstr, $refstr['mpid'], $bpprow['ppid'],$package,$package_type);
                // }

                addlog_sess($username, 'member');
                // $redirval = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;

             /*Send Verification code through SMS*/
                $smsResponse= sendVerificationCode($phone,$six_digit_random_number);
                if($smsResponse->status=='success'){
                $amount= (int)$package*100;
                $redirval = SURL . "/member/validate_phone.php?user_id=".$newmbrid."&package=".$amount."";
                
                }else{
                      $condition = "AND id='".$newmbrid."'";
                $db->deleteQry("DELETE FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition);

                $mbrplan_condition = "AND idmbr='".$newmbrid."'";
                $db->deleteQry("DELETE FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition);
                    $_SESSION['show_msg'] = showalert('danger', 'SMS SENDING', 'OTP not sent. Please try it again!');
                    $redirval = SURL . "/member/register.php?res=errpass";
                }
            
                // var_dump($redirval);exit;
            } else {
                $redirval = "?res=errsql";
            }
            // ************************TEST************

        } else {
            $_SESSION['show_msg'] = showalert('warning', 'Password Hint', $passres);
            $redirval = SURL . "/member/register.php?res=errpass";
        }
    }
    // var_dump(expression)
    redirpageto($redirval);
    exit;
}

$modalcontent = file_get_contents(SURL . "/admin/terms.html");
$refbystr = ($sesref['username'] != '') ? "<div class='card-header-action'><span class='badge badge-info'>| {$sesref['username']}</span></div>" : '';

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
