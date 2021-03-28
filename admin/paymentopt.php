<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$paypalonarr = array(0, 1);
$paypalon_cek = radiobox_opt($paypalonarr, $payrow['paypalon']);
$paypal4usr_cek = checkbox_opt($payrow['paypal4usr']);

$coinpaymentsonarr = array(0, 1);
$coinpaymentson_cek = radiobox_opt($coinpaymentsonarr, $payrow['coinpaymentson']);
$coinpayments4usr_cek = checkbox_opt($payrow['coinpayments4usr']);

$manualpayonarr = array(0, 1);
$manualpayon_cek = radiobox_opt($manualpayonarr, $payrow['manualpayon']);
$manualpay4usr_cek = checkbox_opt($payrow['manualpay4usr']);

$testpayonarr = array(0, 1);
$testpayon_cek = radiobox_opt($testpayonarr, $payrow['testpayon']);
$testpay4usr_cek = checkbox_opt($payrow['testpay4usr']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $data = array(
        'paypalon' => intval($paypalon),
        'paypalfee' => $paypalfee,
        'paypalacc' => base64_encode($paypalacc),
        'paypal4usr' => intval($paypal4usr),
        'coinpaymentson' => intval($coinpaymentson),
        'coinpaymentsfee' => $coinpaymentsfee,
        'coinpaymentsmercid' => base64_encode($coinpaymentsmercid),
        'coinpaymentsipnkey' => base64_encode($coinpaymentsipnkey),
        'coinpayments4usr' => intval($coinpayments4usr),
        'manualpayon' => intval($manualpayon),
        'manualpaybtn' => $manualpaybtn,
        'manualpayfee' => $manualpayfee,
        'manualpayname' => mystriptag($manualpayname),
        'manualpayipn' => base64_encode($manualpayipn),
        'manualpay4usr' => intval($manualpay4usr),
        'testpayon' => intval($testpayon),
        'testpayfee' => $testpayfee,
        'testpaylabel' => $testpaylabel,
        'testpay4usr' => intval($testpay4usr),
    );

    $condition = " AND paygid = '{$didId}' ";
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_paygates WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        $update = $db->update(DB_TBLPREFIX . '_paygates', $data, array('paygid' => $didId));
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Payment options updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_paygates', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Payment options added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Payment options not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

$iconstatuspaystr = ($payrow['paypalon'] == 1 || $payrow['coinpaymentson'] == 1 || $payrow['manualpayon'] == 1) ? "<i class='fa fa-check text-success' data-toggle='tooltip' title='Payment Option is Available'></i>" : "<i class='fa fa-times text-danger' data-toggle='tooltip' title='Payment Option is Unavailable'></i>";
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-money-bill-wave"></i> <?php echo myvalidate($LANG['a_payment']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Gateway</h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($iconstatuspaystr); ?>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="config-paypal" data-toggle="tab" href="#paypaypal" role="tab" aria-controls="paypal" aria-selected="false">RazorPay</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-coinpayments" data-toggle="tab" href="#paycoinpayments" role="tab" aria-controls="coinpayments" aria-selected="false">CoinPayments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="config-cash" data-toggle="tab" href="#paycash" role="tab" aria-controls="cash" aria-selected="true">Cash and Bank</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-test" data-toggle="tab" href="#paytest" role="tab" aria-controls="test" aria-selected="false">System Test</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="payform">
                    <input type="hidden" name="hal" value="paymentopt">

                    <div class="card-header">
                        <h4>Options</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">

                            <div class="tab-pane fade" id="paypaypal" role="tabpanel" aria-labelledby="config-paypal">
                                <!-- <p class="text-muted">Use this gateway option to accept payment using PayPal.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>  -->

                       <!--          <div class="form-group">
                                    <label for="paypalacc">PayPal Account</label>
                                    <input type="text" name="paypalacc" id="paypalacc" class="form-control" value="<?php echo isset($payrow['paypalacc']) ? base64_decode($payrow['paypalacc']) : ''; ?>" placeholder="PayPal Email Address">
                                </div>

                                <div class="form-group">
                                    <label for="paypalfee">Gateway Fee</label>
                                    <input type="text" name="paypalfee" id="paypalfee" class="form-control" value="<?php echo isset($payrow['paypalfee']) ? $payrow['paypalfee'] : '0'; ?>" placeholder="Additional fee">
                                </div> -->

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalon" value="0" class="selectgroup-input"<?php echo myvalidate($paypalon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="paypalon" value="1" class="selectgroup-input"<?php echo myvalidate($paypalon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="paypal4usr" value="1" class="custom-switch-input"<?php echo myvalidate($paypal4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paycoinpayments" role="tabpanel" aria-labelledby="config-coinpayments">
                                <p class="text-muted">Use this gateway option to accept payment using CoinPayments.</p>
                                <p class="text-muted text-small"><em>In order to make this payment method working properly, make sure your current currency <strong> <?php echo myvalidate($bpprow['currencycode']); ?></strong> is supported by this payment option.</em></p>

                                <div class="form-group">
                                    <label for="coinpaymentsmercid">Merchant ID</label>
                                    <input type="text" name="coinpaymentsmercid" id="coinpaymentsmercid" class="form-control" value="<?php echo isset($payrow['coinpaymentsmercid']) ? base64_decode($payrow['coinpaymentsmercid']) : ''; ?>" placeholder="CoinPayments Merchant ID">
                                </div>

                                <div class="form-group">
                                    <label for="coinpaymentsipnkey">IPN Secret</label>
                                    <input type="password" name="coinpaymentsipnkey" id="coinpaymentsipnkey" class="form-control" value="<?php echo isset($payrow['coinpaymentsipnkey']) ? base64_decode($payrow['coinpaymentsipnkey']) : ''; ?>" placeholder="CoinPayments IPN Secret">
                                </div>

                                <div class="form-group">
                                    <label for="coinpaymentsfee">Gateway Fee</label>
                                    <input type="text" name="coinpaymentsfee" id="coinpaymentsfee" class="form-control" value="<?php echo isset($payrow['coinpaymentsfee']) ? $payrow['coinpaymentsfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="coinpaymentson" value="0" class="selectgroup-input"<?php echo myvalidate($coinpaymentson_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="coinpaymentson" value="1" class="selectgroup-input"<?php echo myvalidate($coinpaymentson_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="coinpayments4usr" value="1" class="custom-switch-input"<?php echo myvalidate($coinpayments4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade show active" id="paycash" role="tabpanel" aria-labelledby="config-cash">
                                <p class="text-muted">Cash, bank transfer, and other offline or manual payment methods. Use the following tags to display dynamic contents:</p>
                                <ul>
                                    <li><strong>[[currencysym]]</strong> = Currency symbol (<?php echo myvalidate($bpprow['currencysym']); ?>).</li>
                                    <li><strong>[[currencycode]]</strong> = Currency code (<?php echo myvalidate($bpprow['currencycode']); ?>).</li>
                                    <li><strong>[[feeamount]]</strong> = Payment gateway fee.</li>
                                    <li><strong>[[amount]]</strong> = Registration amount.</li>
                                    <li><strong>[[totamount]]</strong> = Total amount need to pay.</li>
                                    <li><strong>[[payplan]]</strong> = Membership name.</li>
                                </ul>

                                <div class="form-group">
                                    <label for="manualpayname">Payment Name</label>
                                    <input type="text" name="manualpayname" id="manualpayname" class="form-control" value="<?php echo isset($payrow['manualpayname']) ? $payrow['manualpayname'] : 'Cash or Bank Transfer'; ?>" placeholder="Cash or Bank Transfer">
                                </div>
                                <div class="form-group">
                                    <label for="manualpayipn">Payment Instructions</label>
                                    <textarea class="form-control rowsize-md" name="manualpayipn" id="summernotemini" placeholder="Enter the payment instructions here."><?php echo isset($payrow['manualpayipn']) ? base64_decode($payrow['manualpayipn']) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="manualpayfee">Gateway Fee</label>
                                    <input type="text" name="manualpayfee" id="manualpayfee" class="form-control" value="<?php echo isset($payrow['manualpayfee']) ? $payrow['manualpayfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="manualpayon" value="0" class="selectgroup-input"<?php echo myvalidate($manualpayon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="manualpayon" value="1" class="selectgroup-input"<?php echo myvalidate($manualpayon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Member Gateway Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="manualpay4usr" value="1" class="custom-switch-input"<?php echo myvalidate($manualpay4usr_cek); ?>>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Allow member to use this payment gateway option</span>
                                    </label>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="paytest" role="tabpanel" aria-labelledby="config-test">
                                <p class="text-muted">Use this gateway option for testing and to simulate member payment.</p>

                                <div class="form-group">
                                    <label for="testpaylabel">Payment Name</label>
                                    <input type="text" name="testpaylabel" id="testpaylabel" class="form-control" value="<?php echo isset($payrow['testpaylabel']) ? $payrow['testpaylabel'] : 'Test Payment'; ?>" placeholder="Gateway Name">
                                </div>

                                <div class="form-group">
                                    <label for="testpayfee">Gateway Fee</label>
                                    <input type="text" name="testpayfee" id="testpayfee" class="form-control" value="<?php echo isset($payrow['testpayfee']) ? $payrow['testpayfee'] : '0'; ?>" placeholder="Additional fee">
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Gateway Status (Debug Mode)</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testpayon" value="0" class="selectgroup-input"<?php echo myvalidate($testpayon_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="testpayon" value="1" class="selectgroup-input"<?php echo myvalidate($testpayon_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Reset
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                        </button>
                        <input type="hidden" name="dosubmit" value="1">
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
