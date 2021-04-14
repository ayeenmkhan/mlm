<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$lwide_menu = $ldeep_menu = '';
for ($i = 0; $i <= 5; $i++) {
    $lvelmax = ($i > 0) ? $i : 'Unilevel';
    $isselected = ($i == $bpprow['maxwidth']) ? "selected" : '';
    $lwide_menu .= "<option value='{$i}' {$isselected}>{$lvelmax}";
}

for ($i = 1; $i <= 18; $i++) {
    $lvelmax = $i;
    $isselected = ($i == $bpprow['maxdepth']) ? "selected" : '';
    $ldeep_menu .= "<option value='{$i}' {$isselected}>{$lvelmax}";
}

$ifrolluptoarr = array(0, 1);
$ifrollupto_cek = radiobox_opt($ifrolluptoarr, $bpprow['ifrollupto']);
$spilloverarr = array(0, 1);
$spillover_cek = radiobox_opt($spilloverarr, $bpprow['spillover']);
$planstatusarr = array(0, 1);
$planstatus_cek = radiobox_opt($planstatusarr, $bpprow['planstatus']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $paymupdate = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

    $basedata = array(
        'pay_emailname' => mystriptag($pay_emailname),
        'pay_emailaddr' => mystriptag($pay_emailaddr, 'email'),
        'currencysym' => base64_encode($currencysym),
        'currencycode' => $currencycode,
        'maxwidth' => intval($maxwidth),
        'maxdepth' => intval($maxdepth),
    );

    $data = array(
        'ppname' => mystriptag($ppname),
        'planinfo' => mystriptag($planinfo),
        'limitref' => intval($limitref),
        'ifrollupto' => intval($ifrollupto),
        'minref2getcm' => $minref2getcm,
        'spillover' => intval($spillover),
        'cmdrlist' => $cmdrlist,
        'cmlist' => $cmlist,
        'cmlist_two' => $cmlist_two,
        'cmlist_three' => $cmlist_three,
        'cmlist_four' => $cmlist_four,
        'cmlist_five' => $cmlist_five,
        'cmlist_six' => $cmlist_six,
        'cmlist_seven' => $cmlist_seven,
        'cmlist_eight' => $cmlist_eight,
        'rwlist' => $rwlist,
        'level_bages' => $level_bages,
        'planstatus' => intval($planstatus),
        'plantoken' => $bpprow['plantoken'],
    );

    $condition = ' AND ppid = "' . $didId . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_payplans WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        $update1 = $db->update(DB_TBLPREFIX . '_baseplan', $basedata, array('bpid' => $didId));
        $update2 = $db->update(DB_TBLPREFIX . '_payplans', $data, array('ppid' => $didId));
        if ($update1 || $update2) {
            $datadt = array(
                'paymupdate' => $paymupdate,
            );
            $update = $db->update(DB_TBLPREFIX . '_payplans', $datadt, array('ppid' => $didId));
            $_SESSION['dotoaster'] = "toastr.success('Configuration updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_baseplan', $basedata);
        $insert = $db->insert(DB_TBLPREFIX . '_payplans', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Configuration added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Configuration not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

$iconstatusplanstr = ($bpprow['planstatus'] == 1) ? "<i class='fa fa-check text-success' data-toggle='tooltip' title='Program Status is Enable'></i>" : "<i class='fa fa-times text-danger' data-toggle='tooltip' title='Program Status is Disable'></i>";
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-gem"></i> <?php echo myvalidate($LANG['a_payplan']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Settings</h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($iconstatusplanstr); ?>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-tab1" data-toggle="tab" href="#bpptab1" role="tab" aria-controls="program" aria-selected="true">Program</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab2" data-toggle="tab" href="#bpptab2" role="tab" aria-controls="structure" aria-selected="true">Structure</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab3" data-toggle="tab" href="#bpptab3" role="tab" aria-controls="commission" aria-selected="false">Commission</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab4" data-toggle="tab" href="#bpptab4" role="tab" aria-controls="others" aria-selected="false">Others</a>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="bpidform">
                    <input type="hidden" name="hal" value="payplancfg">

                    <div class="card-header">
                        <h4>Packages</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">
                            <div class="tab-pane fade show active" id="bpptab1" role="tabpanel" aria-labelledby="config-tab1">
                                <div class="form-group">
                                    <label for="ppname">Program Name</label>
                                    <input type="text" name="ppname" id="ppname" class="form-control" value="<?php echo isset($bpprow['ppname']) ? $bpprow['ppname'] : ''; ?>" placeholder="Program Name" required>
                                </div>

                                <div class="form-group">
                                    <label for="planinfo">Program Description</label>
                                    <textarea class="form-control rowsize-sm" name="planinfo" id="planinfo" placeholder="Program Description"><?php echo isset($bpprow['planinfo']) ? $bpprow['planinfo'] : ''; ?></textarea>
                                </div>

                               

                                <div class="form-row">
                                    
                                    <div class="form-group col-md-6">
                                        <label for="selectgroup-pills">Program Status</label>
                                        <div class="selectgroup selectgroup-pills">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="planstatus" value="0" class="selectgroup-input"<?php echo myvalidate($planstatus_cek[0]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="planstatus" value="1" class="selectgroup-input"<?php echo myvalidate($planstatus_cek[1]); ?>>
                                                <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="bpptab2" role="tabpanel" aria-labelledby="config-tab2">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="maxwidth">Level Width</label>
                                        <div class="input-group">
                                            <select name="maxwidth" id="maxwidth" class="form-control select2">
                                                <?php echo myvalidate($lwide_menu); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="maxdepth">Level Depth</label>
                                        <div class="input-group">
                                            <select name="maxdepth" id="maxdepth" class="form-control select2">
                                                <?php echo myvalidate($ldeep_menu); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="currencysym">Currency Symbol</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-coins"></i></div>
                                            </div>
                                            <input type="text" name="currencysym" id="currencysym" class="form-control" value="<?php echo isset($bpprow['currencysym']) ? $bpprow['currencysym'] : '$'; ?>" placeholder="$" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="currencycode">Currency Code</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-money-bill-wave"></i></div>
                                            </div>
                                            <input type="text" name="currencycode" id="currencycode" class="form-control" value="<?php echo isset($bpprow['currencycode']) ? $bpprow['currencycode'] : 'USD'; ?>" placeholder="USD" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="pay_emailname">Sender Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                                            </div>
                                            <input type="text" name="pay_emailname" id="pay_emailname" class="form-control" value="<?php echo isset($bpprow['pay_emailname']) ? $bpprow['pay_emailname'] : ''; ?>" placeholder="Sender Name">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="pay_emailaddr">Sender Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                                            </div>
                                            <input type="email" name="pay_emailaddr" id="pay_emailaddr" class="form-control" value="<?php echo isset($bpprow['pay_emailaddr']) ? $bpprow['pay_emailaddr'] : ''; ?>" placeholder="Sender Email Address" required>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="bpptab3" role="tabpanel" aria-labelledby="config-tab3">
                            <!--     <div class="form-group">
                                    <label for="cmdrlist">Personal Referral Commission</label>
                                    <input type="text" name="cmdrlist" id="cmdrlist" class="form-control" value="<?php echo isset($bpprow['cmdrlist']) ? $bpprow['cmdrlist'] : ''; ?>" placeholder="Personal referral commission">
                                </div> -->
                            <div class="form-row">
                                
                                <div class="form-group col-md-6">
                                    <label for="cmlist">1st Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist" id="cmlist" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist']) ? $bpprow['cmlist'] : ''; ?></textarea>
                                </div>  
                                 <div class="form-group col-md-6">
                                    <label for="cmlist">2nd Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_two" id="cmlist_two" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_two']) ? $bpprow['cmlist_two'] : ''; ?></textarea>
                                </div> 
                              
                                <div class="form-group col-md-6">
                                    <label for="cmlist">3rd Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_three" id="cmlist_three" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_three']) ? $bpprow['cmlist_three'] : ''; ?></textarea>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="cmlist">4th Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_four" id="cmlist_four" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_four']) ? $bpprow['cmlist_four'] : ''; ?></textarea>
                                </div>
                         
                                <div class="form-group col-md-6">
                                    <label for="cmlist">5th Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_five" id="cmlist_five" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_five']) ? $bpprow['cmlist_five'] : ''; ?></textarea>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="cmlist">6th Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_six" id="cmlist_six" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_six']) ? $bpprow['cmlist_six'] : ''; ?></textarea>
                                </div>
                        
                                <div class="form-group col-md-6">
                                    <label for="cmlist">7th Package  Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_seven" id="cmlist_seven" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_seven']) ? $bpprow['cmlist_seven'] : ''; ?></textarea>
                                </div>   
                                <div class="form-group col-md-6">
                                    <label for="cmlist">8th Package Commission</label>
                                    <textarea class="form-control rowsize-sm" name="cmlist_eight" id="cmlist_eight" placeholder="Commission list, separated with comma"><?php echo isset($bpprow['cmlist_eight']) ? $bpprow['cmlist_eight'] : ''; ?></textarea>
                                
                            </div>
                            </div>

                            <!--     <div class="form-group">
                                    <label for="rwlist">Level Complete Reward</label>
                                    <textarea class="form-control rowsize-sm" name="rwlist" id="rwlist" placeholder="Reward value, separated with comma"><?php echo isset($bpprow['rwlist']) ? $bpprow['rwlist'] : ''; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="rwlist">Level Complete Bages</label>
                                    <textarea class="form-control rowsize-sm" name="level_bages" id="level_bages" placeholder="Bages name, separated with comma"><?php echo isset($bpprow['level_bages']) ? $bpprow['level_bages'] : ''; ?></textarea>
                                </div> -->

                            </div>

                            <div class="tab-pane fade" id="bpptab4" role="tabpanel" aria-labelledby="config-tab4">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="limitref">Max Personal Referral</label>
                                        <div class="input-group">
                                            <input type="number" name="limitref" id="limitref" class="form-control" value="<?php echo isset($bpprow['limitref']) ? $bpprow['limitref'] : ''; ?>" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="minref2getcm">Minimum Personal Referral for Spillover</label>
                                        <div class="input-group">
                                            <input type="text" name="minref2getcm" id="minref2getcm" class="form-control" value="<?php echo isset($bpprow['minref2getcm']) ? $bpprow['minref2getcm'] : ''; ?>" placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Spillover Option</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="spillover" value="0" class="selectgroup-input"<?php echo myvalidate($spillover_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-people-carry"></i> First Complete</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="spillover" value="1" class="selectgroup-input"<?php echo myvalidate($spillover_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-street-view"></i> Spread Evenly</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Roll-up member placement</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="ifrollupto" value="0" class="selectgroup-input"<?php echo myvalidate($ifrollupto_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-user"></i> Company (without Sponsor)</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="ifrollupto" value="1" class="selectgroup-input"<?php echo myvalidate($ifrollupto_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-user-secret"></i> Next Sponsor</span>
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
