<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$randref_cek = checkbox_opt($cfgrow['randref']);
$disreflink_cek = checkbox_opt($cfgtoken['disreflink']);
$isrecaptcha_cek = checkbox_opt($cfgrow['isrecaptcha']);

$join_statusarr = array(0, 1);
$join_status_cek = radiobox_opt($join_statusarr, $cfgrow['join_status']);
$site_statusarr = array(0, 1);
$site_status_cek = radiobox_opt($site_statusarr, $cfgrow['site_status']);
$validrefarr = array(0, 1);
$validref_cek = radiobox_opt($validrefarr, $cfgrow['validref']);

$autoregplanarr = array(0, 1);
$isautoregplan_cek = radiobox_opt($autoregplanarr, $cfgtoken['isautoregplan']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    // process images
    $imageupdted = 0;
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']["tmp_name"] != '') {
        $site_logo = imageupload('site_logo', $_FILES['site_logo'], $old_site_logo);
        $imageupdted = 1;
    }
    $dataimgs = $dataimbr = $dataiadm = array();
    if (isset($_FILES['mbr_defaultimage']) && $_FILES['mbr_defaultimage']["tmp_name"] != '') {
        $mbr_defaultimage = do_imgresize('mbr_defaultimage', $_FILES["mbr_defaultimage"]["tmp_name"], $cfgrow['mbrmax_image_width'], $cfgrow['mbrmax_image_height'], 'jpeg');
        $dataimbr = array(
            'mbr_defaultimage' => $mbr_defaultimage,
        );
        $imageupdted = 1;
    }
    if (isset($_FILES['admimage']) && $_FILES['admimage']["tmp_name"] != '') {
        $admimage = do_imgresize('admimage', $_FILES["admimage"]["tmp_name"], $cfgrow['mbrmax_image_width'], $cfgrow['mbrmax_image_height'], 'jpeg');
        $dataiadm = array(
            'admimage' => $admimage,
        );
        $imageupdted = 1;
    }
    $dataimgs = array_merge($dataiadm, $dataimbr);

    $cfgtoken = $cfgrow['cfgtoken'];
    $site_subname = mystriptag($site_subname);
    $cfgtoken = put_optionvals($cfgtoken, 'site_subname', $site_subname);
    $cfgtoken = put_optionvals($cfgtoken, 'isautoregplan', $isautoregplan);
    $cfgtoken = put_optionvals($cfgtoken, 'disreflink', $disreflink);
    $admin_password = ($ischangeok == 1) ? getpasshash($admin_password) : $oldadm_password;

    $data = array(
        'site_name' => mystriptag($site_name),
        'site_url' => mystriptag($site_url, 'url'),
        'site_logo' => $site_logo,
        'site_keywrd' => mystriptag($site_keywrd),
        'site_descr' => mystriptag($site_descr),
        'site_emailname' => mystriptag($site_emailname),
        'site_emailaddr' => mystriptag($site_emailaddr, 'email'),
        'join_status' => intval($join_status),
        'site_status' => intval($site_status),
        'site_status_note' => base64_encode($site_status_note),
        'mbrmax_image_width' => intval($mbrmax_image_width),
        'mbrmax_image_height' => intval($mbrmax_image_height),
        'mbrmax_title_char' => intval($mbrmax_title_char),
        'mbrmax_descr_char' => intval($mbrmax_descr_char),
        'validref' => intval($validref),
        'randref' => intval($randref),
        'defaultref' => mystriptag($defaultref),
        'admin_user' => $admin_user,
        'admin_password' => $admin_password,
        'envacc' => $envacc,
        'dldir' => $dldir,
        'sodatef' => $sodatef,
        'lodatef' => $lodatef,
        'maxpage' => intval($maxpage),
        'maxcookie_days' => intval($maxcookie_days),
        'isrecaptcha' => intval($isrecaptcha),
        'rc_securekey' => $rc_securekey,
        'rc_sitekey' => $rc_sitekey,
        'cfgtoken' => $cfgtoken,
    );

    $data = array_merge($data, $dataimgs);

    $condition = ' AND cfgid = "' . $didId . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_configs WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        if (!defined('ISDEMOMODE')) {
            $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
        }
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Configuration updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = ($imageupdted == 1) ? "toastr.success('Image updated!', 'Success');" : "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_configs', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Configuration added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = ($imageupdted == 1) ? "toastr.success('Image updated!', 'Success');" : "toastr.error('Configuration not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

$defmbr_pict = ($cfgrow['mbr_defaultimage']) ? $cfgrow['mbr_defaultimage'] : DEFIMG_MBR;
$defadm_pict = ($cfgrow['admimage']) ? $cfgrow['admimage'] : DEFIMG_ADM;

$iconstatusregstr = ($cfgrow['join_status'] == 1) ? "<i class='fa fa-check text-info' data-toggle='tooltip' title='Registration Status is Enable'></i>" : "<i class='fa fa-times text-warning' data-toggle='tooltip' title='Registration Status is Disable'></i>";
$iconstatussitestr = ($cfgrow['site_status'] == 1) ? "<i class='fa fa-check text-success' data-toggle='tooltip' title='Website Status is Online'></i>" : "<i class='fa fa-times text-danger' data-toggle='tooltip' title='Website Status is Offline'></i>";
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-tools"></i> <?php echo myvalidate($LANG['a_settings']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Settings</h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($iconstatusregstr . ' ' . $iconstatussitestr); ?>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-tab1" data-toggle="tab" href="#cfgtab1" role="tab" aria-controls="website" aria-selected="true">Website</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab2" data-toggle="tab" href="#cfgtab2" role="tab" aria-controls="member" aria-selected="false">Members</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="config-tab3" data-toggle="tab" href="#cfgtab3" role="tab" aria-controls="account" aria-selected="false">Account</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4><?php echo isset($bpprow['ppname']) ? $cfgtoken['site_subname'] : 'Website'; ?></h4>
                </div>
                <div class="card-body">
                    <div class="mb-2 text-muted text-small">Scheduled Task: <?php echo isset($cfgrow['cronts']) ? $cfgrow['cronts'] : '-'; ?></div>
                    <div class="chocolat-parent">
                        <div>
                            <img alt="image" src="<?php echo myvalidate($site_logo); ?>" class="img-fluid circle author-box-picture">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" enctype="multipart/form-data" id="cfgform">
                    <input type="hidden" name="hal" value="generalcfg">

                    <div class="card-header">
                        <h4>Options</h4>
                    </div>

                    <div class="card-body">
                        <div class="tab-content no-padding" id="myTab2Content">
                            <div class="tab-pane fade show active" id="cfgtab1" role="tabpanel" aria-labelledby="config-tab1">
                                <div class="form-group">
                                    <label for="site_name">Site Title</label>
                                    <input type="text" name="site_name" id="site_name" class="form-control" value="<?php echo isset($cfgrow['site_name']) ? $cfgrow['site_name'] : ''; ?>" placeholder="Site Title" required>
                                </div>
                                <div class="form-group">
                                    <label for="site_url">Site URL</label>
                                    <input type="url" name="site_url" id="site_url" class="form-control" value="<?php echo isset($cfgrow['site_url']) ? $cfgrow['site_url'] : ''; ?>" placeholder="Site URL" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="site_subname">Site Name</label>
                                        <input type="text" name="site_subname" id="site_subname" class="form-control" value="<?php echo isset($cfgtoken['site_subname']) ? $cfgtoken['site_subname'] : $cfgrow['site_name']; ?>" placeholder="Site Nickname" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_logo">Site Logo</label>
                                        <input type="file" name="site_logo" id="site_logo" class="form-control">
                                        <input type="hidden" name="old_site_logo" value="<?php echo myvalidate($site_logo); ?>">
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="site_keywrd">Site Keywords</label>
                                    <textarea class="form-control rowsize-sm" name="site_keywrd" id="site_keywrd" placeholder="Site Keywords, separated with comma"><?php echo isset($cfgrow['site_keywrd']) ? $cfgrow['site_keywrd'] : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="site_descr">Site Description</label>
                                    <textarea class="form-control rowsize-sm" name="site_descr" id="site_descr" placeholder="Site Description"><?php echo isset($cfgrow['site_descr']) ? $cfgrow['site_descr'] : ''; ?></textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="site_emailname">From Name</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                                            </div>
                                            <input type="text" name="site_emailname" id="site_emailname" class="form-control" value="<?php echo isset($cfgrow['site_emailname']) ? $cfgrow['site_emailname'] : ''; ?>" placeholder="Sender name">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_emailaddr">From Email Address</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                                            </div>
                                            <input type="email" name="site_emailaddr" id="site_emailaddr" class="form-control" value="<?php echo isset($cfgrow['site_emailaddr']) ? $cfgrow['site_emailaddr'] : ''; ?>" placeholder="Sender email address" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Registration Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="join_status" value="0" class="selectgroup-input"<?php echo myvalidate($join_status_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="join_status" value="1" class="selectgroup-input"<?php echo myvalidate($join_status_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Website Status</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="site_status" value="0" class="selectgroup-input"<?php echo myvalidate($site_status_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Offline</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="site_status" value="1" class="selectgroup-input"<?php echo myvalidate($site_status_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Online</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="site_status_note">Offline Message</label>
                                    <textarea class="form-control rowsize-md" name="site_status_note" id="summernotemini" placeholder="Offline Message"><?php echo isset($cfgrow['site_status_note']) ? base64_decode($cfgrow['site_status_note']) : ''; ?></textarea>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="cfgtab2" role="tabpanel" aria-labelledby="config-tab2">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <div>
                                            <img alt="image" src="<?php echo myvalidate($defmbr_pict); ?>" class="img-fluid img-thumbnail rounded-circle author-box-picture" width='<?php echo myvalidate($cfgrow['mbrmax_image_width']); ?>'>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="mbr_defaultimage">Default Member Picture</label>
                                        <input type="file" name="mbr_defaultimage" id="mbr_defaultimage" class="form-control">
                                        <input type="hidden" name="old_mbr_defaultimage" value="<?php echo isset($cfgrow['mbr_defaultimage']) ? $cfgrow['mbr_defaultimage'] : DEFIMG_MBR; ?>">
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="mbrmax_image_width">Max Picture Width (px)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-arrows-alt-h"></i></div>
                                            </div>
                                            <input type="text" name="mbrmax_image_width" id="mbrmax_image_width" class="form-control" value="<?php echo isset($cfgrow['mbrmax_image_width']) ? $cfgrow['mbrmax_image_width'] : '100'; ?>" placeholder="100" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="mbrmax_image_height">Max Picture Height (px)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-arrows-alt-v"></i></div>
                                            </div>
                                            <input type="text" name="mbrmax_image_height" id="mbrmax_image_height" class="form-control" value="<?php echo isset($cfgrow['mbrmax_image_height']) ? $cfgrow['mbrmax_image_height'] : '100'; ?>" placeholder="100" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="mbrmax_title_char">Max Member Site Title</label>
                                        <div class="input-group">
                                            <input type="text" name="mbrmax_title_char" id="mbrmax_title_char" class="form-control" value="<?php echo isset($cfgrow['mbrmax_title_char']) ? $cfgrow['mbrmax_title_char'] : '64'; ?>" placeholder="32" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="mbrmax_descr_char">Max Member Site Description</label>
                                        <div class="input-group">
                                            <input type="text" name="mbrmax_descr_char" id="mbrmax_descr_char" class="form-control" value="<?php echo isset($cfgrow['mbrmax_descr_char']) ? $cfgrow['mbrmax_descr_char'] : '144'; ?>" placeholder="144" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Payplan Registration Option</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="isautoregplan" value="0" class="selectgroup-input"<?php echo myvalidate($isautoregplan_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-user"></i> Manual by Member</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="isautoregplan" value="1" class="selectgroup-input"<?php echo myvalidate($isautoregplan_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-cog"></i> Automatically by the System</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="selectgroup-pills">Visitor Referrer</label>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="validref" value="0" class="selectgroup-input"<?php echo myvalidate($validref_cek[0]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Optional</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="validref" value="1" class="selectgroup-input"<?php echo myvalidate($validref_cek[1]); ?>>
                                            <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Mandatory</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="randref" value="1" class="custom-control-input" id="randref"<?php echo myvalidate($randref_cek); ?>>
                                            <label class="custom-control-label" for="randref">Enable Random Referrer</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="disreflink" value="1" class="custom-control-input" id="disreflink"<?php echo myvalidate($disreflink_cek); ?>>
                                            <label class="custom-control-label" for="disreflink">Disable Referral Link</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="defaultref">Default Referrer</label>
                                    <textarea class="form-control" name="defaultref" id="defaultref" placeholder="List of default referrer username, separated with comma"><?php echo isset($cfgrow['defaultref']) ? $cfgrow['defaultref'] : ''; ?></textarea>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="cfgtab3" role="tabpanel" aria-labelledby="config-tab3">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <div>
                                            <img alt="image" src="<?php echo myvalidate($defadm_pict); ?>" class="img-fluid img-thumbnail rounded-circle author-box-picture" width='<?php echo myvalidate($cfgrow['mbrmax_image_width']); ?>'>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="admimage">Admin Picture</label>
                                        <input type="file" name="admimage" id="admimage" class="form-control">
                                        <input type="hidden" name="old_admimage" value="<?php echo myvalidate($admimage); ?>">
                                        <div class="form-text text-muted">The image must have a maximum size of 1MB</div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="admin_user">Admin Username</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-fw fa-user-circle"></i></div>
                                            </div>
                                            <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?php echo isset($cfgrow['admin_user']) ? $cfgrow['admin_user'] : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="admin_password">Admin Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fas fa-fw fa-key"></i></div>
                                            </div>
                                            <input type="password" name="admin_password" id="admin_password" class="form-control" value="">
                                            <input type="hidden" name="oldadm_password" value="<?php echo isset($cfgrow['admin_password']) ? $cfgrow['admin_password'] : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="ischangeok" value="1" class="custom-control-input" id="ischangeok">
                                        <label class="custom-control-label" for="ischangeok">Confirm Password Change</label>
                                    </div>
                                </div>

                                <?php
                                if (!defined('ISDEMOMODE')) {
                                    ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="lickey">License Key</label>
                                            <input type="text" name="lickey" id="lickey" class="form-control" value="<?php echo isset($cfgrow['lickey']) ? base64_decode($cfgrow['lickey']) : ''; ?>" placeholder="License key or purchase code" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="envacc">MLMScript Username</label>
                                            <div class="input-group">
                                                <input type="text" name="envacc" id="envacc" class="form-control" value="<?php echo isset($cfgrow['envacc']) ? $cfgrow['envacc'] : ''; ?>" placeholder="Your MLMScript.net username">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="dldir">Default Download Folder</label>
                                        <input type="text" name="dldir" id="dldir" class="form-control" value="<?php echo isset($cfgrow['dldir']) ? $cfgrow['dldir'] : ''; ?>" placeholder="Download Folder" required>
                                    </div>
                                    <?php
                                }
                                ?>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="sodatef">Short Date Format</label>
                                        <div class="input-group">
                                            <input type="text" name="sodatef" id="sodatef" class="form-control" value="<?php echo isset($cfgrow['sodatef']) ? $cfgrow['sodatef'] : ''; ?>" placeholder="j M Y" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="lodatef">Long Date Format</label>
                                        <div class="input-group">
                                            <input type="text" name="lodatef" id="lodatef" class="form-control" value="<?php echo isset($cfgrow['lodatef']) ? $cfgrow['lodatef'] : ''; ?>" placeholder="D, j M Y H:i:s" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="maxpage">Max Displayed Items on Page</label>
                                        <div class="input-group">
                                            <input type="text" name="maxpage" id="maxpage" class="form-control" value="<?php echo isset($cfgrow['maxpage']) ? $cfgrow['maxpage'] : ''; ?>" placeholder="15" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="maxcookie_days">Max Cookie Availability</label>
                                        <div class="input-group">
                                            <input type="text" name="maxcookie_days" id="maxcookie_days" class="form-control" value="<?php echo isset($cfgrow['maxcookie_days']) ? $cfgrow['maxcookie_days'] : '180'; ?>" placeholder="365" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="isrecaptcha" class="custom-control-input" value="1" id="isrecaptcha"<?php echo myvalidate($isrecaptcha_cek); ?>>
                                        <label class="custom-control-label" for="isrecaptcha">Enable Recaptcha V3</label>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="rc_sitekey">Site Key</label>
                                        <div class="input-group">
                                            <input type="text" name="rc_sitekey" id="rc_sitekey" class="form-control" value="<?php echo isset($cfgrow['rc_sitekey']) ? $cfgrow['rc_sitekey'] : ''; ?>" placeholder="Recaptcha site key">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="rc_securekey">Secure Key</label>
                                        <div class="input-group">
                                            <input type="text" name="rc_securekey" id="rc_securekey" class="form-control" value="<?php echo isset($cfgrow['rc_securekey']) ? $cfgrow['rc_securekey'] : ''; ?>" placeholder="Recaptcha secure key">
                                        </div>
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
