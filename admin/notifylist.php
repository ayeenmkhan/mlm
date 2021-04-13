<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$ntId = intval($FORM['ntId']);
if (isset($ntId) && intval($ntId) > 0) {
    $row = $db->getAllRecords(DB_TBLPREFIX . '_notifytpl', '*', ' AND ntid = "' . $ntId . '"');
    $msgtplrow = array();
    foreach ($row as $value) {
        $msgtplrow = array_merge($msgtplrow, $value);
    }

    $ntoptionsarr = get_optionvals($msgtplrow['ntoptions']);

    $email_statusarr = array(0, 1);
    $email_status_cek = radiobox_opt($email_statusarr, $ntoptionsarr['email']);
    $sms_statusarr = array(0, 1);
    $sms_status_cek = radiobox_opt($sms_statusarr, $ntoptionsarr['sms']);
    $pushmsg_statusarr = array(0, 1);
    $pushmsg_status_cek = radiobox_opt($pushmsg_statusarr, $ntoptionsarr['pushmsg']);
}

$msgListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_notifytpl WHERE 1 ");

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    $ntId = intval($ntId);

    $ntoptions = $msgtplrow['ntoptions'];
    $ntoptions = put_optionvals($ntoptions, 'email', $emailval);
    $ntoptions = put_optionvals($ntoptions, 'sms', $smsval);
    $ntoptions = put_optionvals($ntoptions, 'pushmsg', $pushmsgval);

    $data = array(
        'ntname' => mystriptag($ntname),
        'ntdesc' => mystriptag($ntdesc),
        'ntpid' => intval($ntpid),
        'ntsubject' => mystriptag($ntsubject),
        'nttext' => mystriptag($nttext),
        'nthtml' => $nthtml,
        'ntsms' => $ntsms,
        'ntpush' => $ntpush,
        'ntoptions' => $ntoptions,
        'nttoken' => $nttoken,
    );

    $update = $db->update(DB_TBLPREFIX . '_notifytpl', $data, array('ntid' => $ntId));
    if ($update) {
        $_SESSION['dotoaster'] = "toastr.success('Notification template updated successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto("index.php?hal={$hal}&ntId={$ntId}");
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-bullhorn"></i> <?php echo myvalidate($LANG['a_notifylist']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Notification for</h4>
                </div>
                <form method="get">
                    <div class="card-body">
                        <div class="form-group">
                            <select name="ntId" class="form-control select1">
                                <option value="">-</option>
                                <?php
                                if (count($msgListData) > 0) {
                                    foreach ($msgListData as $val) {
                                        $strsel = ($msgtplrow['ntid'] == $val['ntid']) ? ' selected' : '';
                                        echo "<option value='{$val['ntid']}'{$strsel}>" . $val['ntname'] . "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No Record(s) Found!</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="submit" value="Load" id="load" class="btn btn-info">
                                <i class="fa fa-fw fa-redo"></i> Load
                            </button>
                            <input type="hidden" name="hal" value="notifylist">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" id="msgtplform">
                    <input type="hidden" name="hal" value="notifylist">

                    <div class="card-header">
                        <h4>Contents</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted"><?php echo isset($msgtplrow['ntdesc']) ? $msgtplrow['ntdesc'] : '<i class="fa fa-fw fa-long-arrow-alt-left"></i> Please select the notification template from the drop down list on the left!'; ?></p>

                        <?php
                        if ($msgtplrow['ntid'] > 0) {
                            ?>
                            <div class="form-group">
                                <label for="ntname">Notification Name</label>
                                <input type="text" name="ntname" id="ntname" class="form-control" value="<?php echo isset($msgtplrow['ntname']) ? $msgtplrow['ntname'] : ''; ?>" placeholder="Notification Name" required>
                                <div class="form-text text-muted"><em>Notification Code: <?php echo isset($msgtplrow['ntcode']) ? $msgtplrow['ntcode'] : '?'; ?></em></div>
                            </div>
                            <div class="form-group">
                                <label for="ntdesc"><?php echo myvalidate($LANG['g_description']); ?></label>
                                <textarea class="form-control" name="ntdesc" id="ntdesc" placeholder="Notification Description" required><?php echo isset($msgtplrow['ntdesc']) ? $msgtplrow['ntdesc'] : ''; ?></textarea>
                            </div>

                            <div class="section-title">Email Template</div>
                            <div class="form-group">
                                <label for="ntsubject">Subject</label>
                                <input type="text" name="ntsubject" id="ntsubject" class="form-control" value="<?php echo isset($msgtplrow['ntsubject']) ? $msgtplrow['ntsubject'] : ''; ?>" placeholder="Email Subject" required>
                            </div>

                            <div class="form-group">
                                <label for="nttext">Body Text</label>
                                <textarea class="form-control rowsize-lg" name="nttext" id="nttext" placeholder="Text Message" required><?php echo isset($msgtplrow['nttext']) ? $msgtplrow['nttext'] : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="summernote">Body HTML</label>
                                <textarea class="form-control" name="nthtml" id="summernote" placeholder="HTML Message" required><?php echo isset($msgtplrow['nthtml']) ? $msgtplrow['nthtml'] : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="selectgroup-pills">Email Status</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="emailval" value="0" class="selectgroup-input"<?php echo myvalidate($email_status_cek[0]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="emailval" value="1" class="selectgroup-input"<?php echo myvalidate($email_status_cek[1]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                    </label>
                                </div>
                            </div>

                            <?php
                        }
                        ?>

                    </div>

                    <?php
                    if ($msgtplrow['ntid'] > 0) {
                        ?>
                        <div class="card-footer bg-whitesmoke text-md-right">
                            <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                <i class="fa fa-fw fa-undo"></i> Reset
                            </button>
                            <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                            </button>
                            <input type="hidden" name="ntId" value="<?php echo myvalidate($ntId); ?>">
                            <input type="hidden" name="dosubmit" value="1">
                        </div>
                        <?php
                    }
                    ?>

                </form>

            </div>
        </div>
    </div>
</div>
