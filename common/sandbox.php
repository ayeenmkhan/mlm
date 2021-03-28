<?php

include_once('init.loader.php');

function doipnbox($txmpid, $payamount, $paygate, $txbatch, $redirurl, $ipnreturn = '', $skipamount = 0) {
    global $db, $cfgrow, $bpprow;
// echo "<pre>";print_r($_POST);
    $package = $_POST['package'];
    $defredirurl = $cfgrow['site_url'] . '/' . MBRFOLDER_NAME;
    $redirurl = ($redirurl != '') ? $redirurl : $defredirurl;
    $redirurl = ($redirurl == '-HTTPREF-') ? $_SERVER['HTTP_REFERER'] : $redirurl;

    $txtmstamp = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
    $sb_txmpidarr = explode('-', $txmpid);
    $txid = $sb_txmpidarr[0];
    $mpid = $sb_txmpidarr[1];

// echo "<pre>";print_r($bpprow);exit();
    // get member details
    $mbrstr = getmbrinfo('', '', $mpid);

    // get transaction details
    $condition = ' AND txid = "' . $txid . '" ';
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
    $trxstr = array();
    foreach ($row as $value) {
        $trxstr = array_merge($trxstr, $value);
    }

    // remove proof of payment file
    $proofimg = get_optionvals($trxstr['txtoken'], 'proofimg');
    if ($proofimg) {
        $proofimgfile = INSTALL_PATH . '/assets/imagextra/' . $proofimg;
        if (file_exists($proofimgfile)) {
            unlink($proofimgfile);
            $trxstr['txtoken'] = put_optionvals($trxstr['txtoken'], 'proofimg', '');
            $data = array(
                'txtoken' => $trxstr['txtoken'],
            );
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $trxstr['txid']));
        }
    }

    if (get_optionvals($trxstr['txtoken'], 'isapproved') == 1) {
        if ($ipnreturn) {
            die($ipnreturn);
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('Payment previously has been approved!', 'Info');";
            redirpageto($redirurl);
            exit;
        }
    }

    $txpaytype = $paygate;
    $txamount = $payamount;
    $txbatch = ($txbatch == '') ? strtoupper(date("DmdH-is")) . $mpid : $txbatch;
    $reg_expd = (floatval($bpprow['expday']) > 0 && $mbrstr['reg_date'] > $mbrstr['reg_expd']) ? $mbrstr['reg_date'] : $mbrstr['reg_expd'];

    // is the trx exist [error...]
    $sqlstr = "SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE txfromid = '{$mbrstr['id']}' AND txppid = '{$mbrstr['mppid']}' AND ((txpaytype = '{$txpaytype}' AND txbatch = '{$txbatch}') OR txstatus = '0')";
    $sql = $db->getRecFrmQry($sqlstr);
    if (floatval($bpprow['expday']) > 0 && count($sql) < 1) {
        $data = array(
            'txdatetm' => $txtmstamp,
            'txfromid' => $mbrstr['id'],
            'txamount' => $txamount,
            'txmemo' => 'Renewal fee',
            'txppid' => $mbrstr['mppid'],
            'txtoken' => "|RENEW:{$mbrstr['mpid']}|, |PREVEXP:{$reg_expd}|",
        );
        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);
        $newtrxid = $db->lastInsertId();

        // get recent transaction details
        $condition = ' AND txid = "' . $newtrxid . '" ';
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
        $trxstr = array();
        foreach ($row as $value) {
            $trxstr = array_merge($trxstr, $value);
        }
    }
    // ---

    if (strpos($trxstr['txtoken'], '|RENEW:') !== false) {
        $expdarr = get_actdate($bpprow['expday'], $reg_expd);
        $reg_expd = $expdarr['next'];

        $mptoken = $mbrstr['mptoken'];
        $renewx = intval(get_optionvals($mptoken, 'renewx')) + 1;
        $mptoken = put_optionvals($mptoken, 'renewx', $renewx);
        $mptoken = put_optionvals($mptoken, 'istrial', '0');
    }

    // echo '<pre>';print_r($txamount);exit();
    if ($txamount <= $txamount || $skipamount == 1) {
        // member
        $data = array(
            'reg_expd' => $reg_expd,
            'mpstatus' => 1,
            'mptoken' => $mptoken,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrplans', $data, array('mpid' => $mpid));

        // transaction
        $txtoken = ($update) ? put_optionvals($trxstr['txtoken'], 'isapproved', 1) : $trxstr['txtoken'];
        $amountadjt = $txamount - $trxstr['txamount'];
        $txadminfo = ($amountadjt != 0) ? 'Payment processor fee: ' . $amountadjt . chr(13) . $trxstr['txadminfo'] : $trxstr['txadminfo'];
        $data = array(
            'txpaytype' => $txpaytype,
            'txamount' => $txamount,
            'txbatch' => $txbatch,
            'txtmstamp' => $txtmstamp,
            'txtoken' => $txtoken,
            'txstatus' => 1,
            'txadminfo' => $txadminfo,
        );
        $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $trxstr['txid']));

        // process commission
        if ($update) {
            // personal referral commission list
            $refstr = getmbrinfo($mbrstr['idref']);
            $reflist = dosprlist($refstr['mpid'], $refstr['sprlist'], $mbrstr['mpdepth']);
            $getcmlist = getcmlist($refstr['mpid'], $reflist, $bpprow['cmdrlist'], $mbrstr);
            addcmlist('Referrer Commission', 'PREF', $getcmlist, $mbrstr);

            // level commission list
            if($package=='1'){
                $commission_percentage= $bpprow['cmlist'];
            }if($package=='2'){
                $commission_percentage= $bpprow['cmlist_two'];
            }if($package=='3'){
                $commission_percentage= $bpprow['cmlist_three'];
            }if($package=='4'){
                $commission_percentage= $bpprow['cmlist_four'];
            }

            $sprstr = getmbrinfo($mbrstr['idspr']);
            $getcmlist = getcmlist($sprstr['mpid'], $mbrstr['sprlist'], $commission_percentage, $mbrstr);
            addcmlist('Level Commission', 'TIER', $getcmlist, $mbrstr);

            // level complete reward list
            dolvldone($mbrstr);
        }

        if ($ipnreturn) {
            echo $ipnreturn;
        } else {
            $_SESSION['dotoaster'] = "toastr.success('Payment has been successfully approved!', 'Success');";
            redirpageto($redirurl);
            exit;
        }
    } else {
        die('Invalid Amount');
    }
}

function dotxsuspend($txmpid, $suspendbatch, $addtoken) {
    global $db, $cfgrow, $bpprow;

    if ($suspendbatch != 'cancel') {
        $txtmstamp = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $sb_txmpidarr = explode('-', $txmpid);
        $txid = $sb_txmpidarr[0];
        $mpid = $sb_txmpidarr[1];

        // get transaction details
        $condition = ($suspendbatch != '') ? ' AND txbatch = "' . $suspendbatch . '" ' : ' AND txid = "' . $txid . '" ';
        $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
        $trxstr = array();
        foreach ($row as $value) {
            $trxstr = array_merge($trxstr, $value);
        }

        if ($trxstr['txstatus'] != '3') {
            $txtoken = $trxstr['txtoken'] . ', ' . $addtoken;
            $data = array(
                'txtmstamp' => $txtmstamp,
                'txtoken' => $txtoken,
                'txstatus' => 3,
            );
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $trxstr['txid']));
        }
    }
}

if ($FORM['sb_type'] == 'payreg') {
    $txmpid = $FORM['sb_txmpid'];
    $payamount = $FORM['sb_amount'];
    $paybatch = $FORM['sb_batch'];
    $paygate = $FORM['sb_label'];
    $redirurl = $FORM['sb_success'];
    doipnbox($txmpid, $payamount, $paygate, $paybatch, $redirurl);
}

if ($FORM['custom'] != '' && $FORM['mc_currency'] == $bpprow['currencycode']) {
    $txmpid = $FORM['custom'];
    $skipamount = 0;
    if ($FORM['txn_type'] == 'web_accept') {
        $payamount = $FORM['mc_gross'];
        $paybatch = $FORM['txn_id'];
    } else {
        if ($FORM['txn_type'] == 'subscr_signup') {
            $payamount = ($FORM['mc_amount1'] == '') ? $FORM['mc_amount3'] : $FORM['mc_amount1'];
            $skipamount = 1;
        } else {
            $payamount = $FORM['mc_amount3'];
        }
        $paybatch = $FORM['subscr_id'];
    }
    $paygate = 'PayPal';

    require('paypal.ipn.php');
    $ipn = new PaypalIPN();
    $verified = $ipn->verifyIPN();

    if ($verified) {
        if ($payamount < 0 || $FORM['txn_type'] == 'subscr_cancel' || $FORM['txn_type'] == 'subscr_eot') {
            $suspendbatch = ($payamount < 0) ? $paybatch : 'cancel';
            $payment_status = ($FORM['payment_status']) ? $FORM['payment_status'] : $FORM['txn_type'];
            dotxsuspend($txmpid, $suspendbatch, "|payment_status:{$payment_status}|, |amount:{$payamount}|");
        } else {
            doipnbox($txmpid, $payamount, $paygate, $paybatch, '', 'OK', $skipamount);
        }
    }
}

if ($FORM['invoice'] != '') {
    $txmpid = $FORM['invoice'];
    $payamount = $FORM['amount1'];
    $paygate = 'CoinPayments';

    $hmac_pass = 1;
    $merchant_id = base64_decode($payrow['coinpaymentsmercid']);
    $coinpaymentsipnkey = base64_decode($payrow['coinpaymentsipnkey']);

    $merchant = isset($FORM['merchant']) ? $FORM['merchant'] : '';
    if ($merchant != $merchant_id) {
        $hmac_pass = 0;
    }

    $request = file_get_contents('php://input');
    $hmac = hash_hmac("sha512", $request, $coinpaymentsipnkey);
    if ($coinpaymentsipnkey && $hmac_pass == 1 && $hmac != $_SERVER['HTTP_HMAC']) {
        $hmac_pass = 0;
    }

    // received_confirms = 2 for Funds received and confirmed, sending to you shortly
    if ($hmac_pass == 1 && $FORM['received_confirms'] == '2') {
        doipnbox($txmpid, $payamount, $paygate, $FORM['txn_id'], '', 'IPN OK');
    }
}