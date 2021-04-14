<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$_SESSION['redirto'] = redir_to($FORM['redir']);

if (isset($FORM['delId']) and $FORM['delId'] != "") {
    $hasdel = md5($FORM['delId'] . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_transactions', array('txid' => $FORM['delId']));
        $_SESSION['dotoaster'] = "toastr.success('Record deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Record deleted failed!', 'Error');";
    }

    header('location: ' . $_SESSION['redirto']);
    $_SESSION['redirto'] = '';
    exit;
}

$editId = intval($FORM['editId']);
$txpaytype_menu = select_opt($avalpaymentopt_array);

if (isset($editId) and $editId != "") {
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', ' AND txid="' . $editId . '"');
    $rowstr = array();
    foreach ($row as $value) {
        $rowstr = array_merge($rowstr, $value);
    }

    $_SESSION['redirto'] = redir_to($FORM['redir']);

    $txpaytype_menu = select_opt($avalpaymentopt_array, $rowstr['txpaytype']);

    $txstatusarr = array(0, 1, 2, 3);
    $txstatus_cek = radiobox_opt($txstatusarr, $rowstr['txstatus']);

    $payfrom = getusernameid($rowstr['txfromid'], 'username');
    $payto = getusernameid($rowstr['txtoid'], 'username');
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    $editId = intval($editId);

    if (!dumbtoken($dumbtoken)) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        $redirval = "?res=errtoken";
        redirpageto($redirval);
        exit;
    }

    $data = array(
        'txpaytype' => $txpaytype,
        'txfromid' => $txfromid,
        'txtoid' => $txtoid,
        'txamount' => $txamount,
        'txmemo' => mystriptag($txmemo),
        'txbatch' => $txbatch,
        'txstatus' => $txstatus,
        'txadminfo' => mystriptag($txadminfo),
    );

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    if (isset($editId) and $editId > 0) {
        // if update transaction history
        $condition = ' AND txid = "' . $editId . '" ';
    } else {
        // if new transaction history exist, keep using old txbatch
        $condition = ' AND txbatch LIKE "' . $txbatch . '" ';
    }
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        if ($editId > 0) {
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $editId));
            if ($update) {
                $_SESSION['dotoaster'] = "toastr.success('Record updated successfully!', 'Success');";
            } else {
                $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
            }
        } else {
            // do nothing
            $_SESSION['dotoaster'] = "toastr.warning('Record not added <strong>Transaction history exist!</strong>', 'Warning');";
        }
    } else {
        $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $data_add = array(
            'txdatetm' => $txdatetm,
            'txtmstamp' => $txdatetm,
        );

        $data = array_merge($data, $data_add);
        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Record added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Record not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    header('location: ' . $redirto);
    exit;
}
?>

<div class="row">
    <div class="col-md-12">

        <p class="text-primary">Fields with <span class="text-danger">*</span> are mandatory!</p>

        <form method="post" action="dohistory.php">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Payment From ID <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="txfromid" id="txfromid" class="form-control" value="<?php echo isset($rowstr['txfromid']) ? $rowstr['txfromid'] : ''; ?>" placeholder="Enter payer ID" onBlur="checkMember('id2i', this.value, '1')" required>
                </div>
                <div class="form-group col-md-8">
                    <label>From Member</label>
                    <div id="resultGetMbr1"><?php echo isset($payfrom) ? "<span class='text-primary'><strong>{$payfrom}</strong></span>" : '?'; ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Payment To ID <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="txtoid" id="txtoid" class="form-control" value="<?php echo isset($rowstr['txtoid']) ? $rowstr['txtoid'] : ''; ?>" placeholder="Enter payee ID" onBlur="checkMember('id2i', this.value, '2')" required>
                </div>
                <div class="form-group col-md-8">
                    <label>To Member</label>
                    <div id="resultGetMbr2"><?php echo isset($payto) ? "<span class='text-primary'><strong>{$payto}</strong></span>" : '?'; ?></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Amount <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-money-bill-wave"></i></div>
                        </div>
                        <input type="text" name="txamount" id="txamount" class="form-control" value="<?php echo isset($rowstr['txamount']) ? $rowstr['txamount'] : ''; ?>" placeholder="Payment amount" required>
                    </div>
                </div>
                <div class="form-group col-md-8">
                    <label><?php echo myvalidate($LANG['g_transactionid']); ?></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-receipt"></i></div>
                        </div>
                        <input type="text" name="txbatch" id="txbatch" class="form-control" value="<?php echo isset($rowstr['txbatch']) ? $rowstr['txbatch'] : ''; ?>" placeholder="Enter transaction id">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Payment Type</label>
                    <select name="txpaytype" id="txpaytype" class="form-control select1" required>
                        <?php echo myvalidate($txpaytype_menu); ?>
                    </select>
                </div>
                <div class="form-group col-md-8">
                    <label for="selectgroup-pills">Status</label>
                    <div class="selectgroup selectgroup-pills">
                        <label class="selectgroup-item">
                            <input type="radio" name="txstatus" value="0" class="selectgroup-input"<?php echo myvalidate($txstatus_cek[0]); ?>>
                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-question-circle"></i> Unpaid</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="txstatus" value="1" class="selectgroup-input"<?php echo myvalidate($txstatus_cek[1]); ?>>
                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Paid</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="txstatus" value="2" class="selectgroup-input"<?php echo myvalidate($txstatus_cek[2]); ?>>
                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-user-circle"></i> OnHold</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="txstatus" value="3" class="selectgroup-input"<?php echo myvalidate($txstatus_cek[3]); ?>>
                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Cancel</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label><?php echo myvalidate($LANG['g_description']); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="txmemo" id="txmemo" class="form-control" value="<?php echo isset($rowstr['txmemo']) ? $rowstr['txmemo'] : ''; ?>" placeholder="Transaction details" required>
                </div>
                <div class="form-group col-md-8">
                    <label>Note</label>
                    <textarea class="form-control" name="txadminfo" id="txadminfo" placeholder="Transaction note, available for administrator only"><?php echo isset($rowstr['txadminfo']) ? $rowstr['txadminfo'] : ''; ?></textarea>
                </div>
            </div>

            <div class="text-md-right">
                <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> Submit
                </button>
                <input type="hidden" name="editId" value="<?php echo myvalidate($editId); ?>">
                <input type="hidden" name="dosubmit" value="1">
                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
            </div>

        </form>

    </div>

</div>
