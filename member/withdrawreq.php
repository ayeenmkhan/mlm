<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}
if (isset($FORM['canel_req']) and $FORM['canel_req'] == '1') {
    extract($FORM);
        $redirto = $_SESSION['redirto'];
        $_SESSION['redirto'] = '';

        // deduct wallet
        $ewallet = $mbrstr['ewallet'] + $req_amount;
        $data = array(
            'ewallet' => $ewallet,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $user_id));

    $delete=$db->delete(DB_TBLPREFIX . '_transactions', array('txid' => $tansaction_id,'txstatus' => 0));
    
    
      if ($delete) {
            $_SESSION['dotoaster'] = "toastr.success('Withdrawal request have been Cancelled successfully!', 'Success');";
        } else {
        $_SESSION['dotoaster'] = "toastr.error('Withdrawal cancel request failed <strong>Please try again!</strong>', 'Warning');";
        }
    redirpageto('index.php?hal=withdrawreq');
    exit;

}
if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);
    /*Check wallet amount minimum of 1000 INR should have to remain in account*/
    if($mbrstr['ewallet'] > '1000.00'){

    if ( $txamount > 0 && $txamount <= $mbrstr['ewallet']) {
        $redirto = $_SESSION['redirto'];
        $_SESSION['redirto'] = '';

        // deduct wallet
        $ewallet = $mbrstr['ewallet'] - $txamount;
        if($ewallet >='1000'){
        $data = array(
            'ewallet' => $ewallet,
        );
        $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));


        $txadminfo = 'Payout To: ';
        $row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND pgidmbr = "' . $mbrstr['id'] . '"');
        $mbrpaystr = array();
        foreach ($row as $value) {
            $mbrpaystr = array_merge($mbrpaystr, $value);
        }
        $txadminfo .= base64_decode($mbrpaystr[$txpaytype]);

        // add withdraw request
        $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $data = array(
            'txdatetm' => $txdatetm,
            // 'txpaytype' => $txpaytype,
            'txpaytype' => 'Manual Request',
            'txfromid' => $mbrstr['id'],
            'txtoid' => 0,
            'txamount' => $txamount,
            'txmemo' => $LANG['g_withdrawstr'],
            'txppid' => $mbrstr['mppid'],
            'txtoken' => '|WIDR:OUT|',
            'txstatus' => 0,
            'txadminfo' => $txadminfo,
        );

        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Withdrawal request have been submited successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Withdrawal request failed <strong>Please try again!</strong>', 'Warning');";
        }
    }else{
         $_SESSION['dotoaster'] = "toastr.error('Withdrawal request failed <strong>Insufficient funds remaining, Maintain Min 1,000 INR in your account!</strong>', 'Warning');";
    }
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Withdrawal request failed <strong>Please try again!</strong>', 'Warning');";
    }
}else{
     $_SESSION['dotoaster'] = "toastr.error('Withdrawal request failed <strong>Insufficient funds!</strong>', 'Error');";
}

    redirpageto('index.php?hal=withdrawreq');
    exit;
}

if ($mbrstr['ewallet'] < 0) {
    $balanceclor = ' text-danger';
} elseif ($mbrstr['ewallet'] > 0) {
    $balanceclor = ' text-info';
}

$btnwidrdis = ($mbrstr['ewallet'] <= 0) ? " disabled" : '';

$condition = " AND txtoken LIKE '%|WIDR:%' AND txtoid = '0' AND txfromid = '{$mbrstr['id']}'";
$withdrawlist = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . " LIMIT 12");


$conditions = ' AND sprlist LIKE "%:' . $mbrstr['mpid'] . '|%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $conditions);
$myreftotal = $row[0]['totref'];
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-hand-holding-usd"></i> <?php echo myvalidate($LANG['g_withdrawreq']); ?></h1>
</div>

<div class="section-body">

    <form method="post" action="index.php">
        <input type="hidden" name="hal" value="withdrawreq">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <?php echo myvalidate($LANG['g_balance']); ?> <span class="<?php echo myvalidate($balanceclor); ?>"><?php echo myvalidate($bpprow['currencysym'] . $mbrstr['ewallet']); ?></span> <?php echo myvalidate($bpprow['currencycode']); ?>
                </h4>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-12 float-md-right">
                        <blockquote>
                            <p><strong>Pending</strong>: The request has been sent but is not yet processed. <strong>Verified</strong>: The request has passed verification. <strong>Processing</strong>: The request is being processed.</p><strong>Completed</strong>: The request is being processed. Once processed, the funds will be sent to your account.</p>
                        </blockquote>
                    </div>
                    <div class="col-md-6">
                   <!--      <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo myvalidate($LANG['g_account']); ?></span>
                                </div>
                                <select name='txpaytype' class="custom-select" id="inputGroupSelect05">
                                    <option selected>-</option>
                                    <?php
                                    if ($payrow['paypal4usr'] == 1) {
                                        ?>
                                        <option value="paypalacc">PayPal (<?php echo isset($mbrpaystr['paypalacc']) ? base64_decode($mbrpaystr['paypalacc']) : '?'; ?>)</option>
                                        <?php
                                    }
                                    if ($payrow['coinpayments4usr'] == 1) {
                                        ?>
                                        <option value="coinpaymentsmercid">Bitcoin (<?php echo isset($mbrpaystr['coinpaymentsmercid']) ? base64_decode($mbrpaystr['coinpaymentsmercid']) : '?'; ?>)</option>
                                        <?php
                                    }
                                    if ($payrow['manualpay4usr'] == 1) {
                                        ?>
                                        <option value="manualpayipn"><?php echo myvalidate($payrow['manualpayname']); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> -->
                        <?php if($myreftotal>='2'){?>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo myvalidate($LANG['m_withdrawamount']); ?></span>
                                </div>
                                <input type="text" name='txamount' class="form-control">
                            </div>
                        </div>

                        <div class="float-md-right">
                            <a href="index.php?hal=withdrawreq" class="btn btn-danger"><i class="fa fa-fw fa-redo"></i> Clear</a>
                            <button type="submit" name="submit" value="withdraw" id="submit" class="btn btn-primary"<?php echo myvalidate($btnwidrdis); ?>><i class="fa fa-fw fa-donate"></i> Withdraw</button>
                        </div>
                    <?php }else {?>
                            <div class="col-md-12 float-md-right">
                        <blockquote>
                            <p style="color: red;"><strong>Note</strong>: You can send withdrawl request! Only when you add minumum of two member into our platform by using your referal code .</p>
                        </blockquote>
                    </div>
                    <?php }?>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <?php //echo myvalidate($LANG['m_withdrawreqnote']); ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="dosubmit" value="1">
    </form>

    <div class="row">
        <?php
        if (count($withdrawlist) > 0) {
            $numwdr = 0;
            foreach ($withdrawlist as $val) {
                if ($val['txamount'] <= 0) {
                    continue;
                }
                $paybyopt = $avalwithdrawgate_array[$val['txpaytype']];
                $paybyoptstr = $payrow[$paybyopt];

                $headdtbg = 'bg-primary text-light';
                $statusbadge = '';
                switch ($val['txstatus']) {
                    case "1":
                        $headdtbg = 'bg-light';
                        $statusbadge .= "<span class='badge badge-secondary'>Processing</span>";
                        break;
                    case "2":
                        $statusbadge .= "<span class='badge badge-info'>Verified</span>";
                        break; 
                    case "3":
                        $statusbadge .= "<span class='badge badge-success'>Completed</span>";
                        break;
                    default:
                        $statusbadge .= "<span class='badge badge-light'>Pending</span>";
                }
                ?>

                <div class="col-12 col-md-4 col-lg-4">
                    <form action="" method="post">
                    <div class="pricing">
                        <div class="pricing-title <?php echo myvalidate($headdtbg); ?>">
                            <?php echo formatdate($val['txdatetm'], 'dt'); ?>
                        </div>
                        <div class="pricing-padding">
                            <div class="pricing-price">
                                <h4><?php echo myvalidate($bpprow['currencysym'] . $val['txamount'] . ' ' . $bpprow['currencycode']); ?></h4>
                                <div><?php echo myvalidate($paybyoptstr); ?></div>
                            </div>
                            <?php echo myvalidate($statusbadge); ?>
                            <input type="hidden" name="user_id" value="<?php echo $mbrstr['id'];?>">
                            <input type="hidden" name="req_amount" value="<?php echo $val['txamount'];?>">
                            <input type="hidden" name="tansaction_id" value="<?php echo $val['txid'];?>">
                            <input type="hidden" name="canel_req" value="1">
                            <?php if($val['txstatus']==0){?><button type="submit" onclick="return check();" class="btn btn-danger" style="border-radius: 30px;height: 30px;">Cancel</button><?php }?>
                        </div>
                    </div>
                    </form>
                </div>

                <?php
                $numwdr++;
            }
            if ($numwdr < 1) {
                echo "No Record(s) Found!";
            }
        }
        ?>
    </div>

</div>

<script type="text/javascript">
    function check() {
        if(confirm('Are you sure want to cancel the withdrawl request?')){
            return true;
        }else{
            return false;
        }
    }
</script>

