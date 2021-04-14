<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if (isset($FORM['getId']) and $FORM['getId'] != "") {
    // Get member details
    $rowstr = getmbrinfo($FORM['getId']);
    if ($rowstr['id'] < 1) {
        redirpageto('index.php?hal=userlist?err');
        exit;
    }
    // echo "<pre>";print_r($rowstr);exit();
    // get transaction details
    $condition = ' AND txtoken LIKE "%|REG:' . $rowstr['mpid'] . '|%" ';
    $row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', '*', $condition);
    $trxstr = array();
    foreach ($row as $value) {
        $trxstr = array_merge($trxstr, $value);
    }

    $mbr_sosmed = get_optionvals($rowstr['mbr_sosmed']);
    $mbr_twitter = $mbr_sosmed['mbr_twitter'];
    $mbr_facebook = $mbr_sosmed['mbr_facebook'];

    $status_arr = array('0' => 'Inactive', '1' => 'Active', '2' => 'Limited', '3' => 'Pending');
    $statusstr = select_opt($status_arr, $rowstr['mbrstatus'], 1);

    $mbr_imagestr = ($rowstr['mbr_image']) ? $rowstr['mbr_image'] : $cfgrow['mbr_defaultimage'];

    $countrystr = select_opt($country_array, $rowstr['country'], 1);
    $countrystr = strtolower($countrystr);
    $countrystr = ucwords($countrystr);

    $mbrsite_catstr = select_opt($webcategory_array, $rowstr['mbrsite_cat'], 1);

    $showsite_cekicon = ($rowstr['showsite'] == 1) ? '<i class="fa fa-fw fa-check-circle text-success"></i>' : '<i class="fa fa-fw fa-times-circle text-danger"></i>';
    $optinme_cekstr = checkbox_opt($rowstr['optinme'], $rowstr['optinme'], 1);

    $statusactstr = '';
    if ($rowstr['mpid'] < 1) {
        // echo "<pre>";print_r($rowstr['mpid']);
        $markstatus = "<span class='alert alert-dark'>UNREGISTERED</span>";
    } else {
        if ($rowstr['mpstatus'] == 1) {
            $markstatus = "<span class='alert alert-success'>ACTIVE</span>";
        } else {
            $markstatus = "<span class='alert alert-secondary'>INACTIVE</span>";
            $txmpid = $trxstr['txid'] . '-' . $rowstr['mpid'];
            $tottestpay = $rowstr['reg_fee'];
            $package = $rowstr['package'];
            $paybatch = strtoupper(date("DmdH-is")) . $rowstr['mpid'];
            $statusactstr = <<<INI_HTML
                <form method="post" action="../common/sandbox.php" id="dopayform">
                    <input type="hidden" name="sb_type" value="payreg">
                    <input type="hidden" name="sb_txmpid" value="{$txmpid}">
                    <input type="hidden" name="sb_amount" value="{$tottestpay}">
                    <input type="hidden" name="package" value="{$package}">
                    <input type="hidden" name="sb_batch" value="{$paybatch}">
                    <input type="hidden" name="sb_label" value="Admin Approval">
                    <input type="hidden" name="sb_success" value="-HTTPREF-">
                    <button type="submit" name="dopay" value="1" id="dopay" class="btn btn-sm btn-danger mt-4 bootboxformconfirm" data-form="dopayform" data-poptitle="Username: {$rowstr['username']}" data-popmsg="Are you sure want to approve this member?">
                        Manual Approval
                    </button>
                </form>
INI_HTML;
        }
    }

    if ($rowstr['mbrstatus'] != 1) {
        $markstatus .= "<span class='alert alert-danger' data-toggle='tooltip' title='Account status is not Active!'><i class='fa fa-fw fa-exclamation-triangle'></i></span>";
    }

    if (strpos($rowstr['mbrsite_img'], 'imgsrc') === false) {
        $imgdata = ($rowstr['mbrsite_img'] == '') ? DEFIMG_SITE : $rowstr['mbrsite_img'];
        $siteimgstr = "<img src='{$imgdata}' width='100%' />";
    } else {
        $imgdata = file_get_contents($rowstr['mbrsite_img']);
        $siteimgstr = "<img src='data:image/jpeg;base64,{$imgdata}' width='100%' />";
    }
    // $condition = " AND sprlist LIKE '%|{$rowstr['mpid']}:%' OR sprlist LIKE '%:{$rowstr['mpid']}|%'";
    $condition = " AND sprlist LIKE '%:{$rowstr['mpid']}|%'";
    $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
    $myreftotal = $row[0]['totref'];

    $backpage = ($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "index.php?hal=userlist";

    $rowrefstr = getmbrinfo('', '', $rowstr['idref']);
    $rowsprstr = getmbrinfo('', '', $rowstr['idspr']);
} else {
    redirpageto('index.php?hal=dashboard');
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-user-circle"></i> <?php echo myvalidate($LANG['g_memberprofile']); ?></h1>
</div>

<div class="section-body">

    <div class="row mt-sm-4">
        <div class="col-12 col-md-12 col-lg-6">

            <div class="card d-none d-md-block">
                <div class="card-header">
                    <h4>Referrer</h4>
                    <div class="card-header-action">
                        <a data-collapse="#ref-collapse" class="btn btn-icon btn-secondary" href="#"><i class="fas fa-minus"></i></a>
                    </div>
                </div>
                <div class="collapse show" id="ref-collapse">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-5 col-12">
                                <label>Username</label>
                                <h6><?php echo myvalidate($rowrefstr['username']); ?></h6>
                            </div>
                            <div class="form-group col-md-7 col-12">
                                <label><?php echo myvalidate($LANG['g_name']); ?></label>
                                <h6><?php echo myvalidate($rowrefstr['firstname'] . ' ' . $rowrefstr['lastname']); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Email</label>
                                <h6><?php echo myvalidate($rowrefstr['email']); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card profile-widget">
                <div class="profile-widget-header">
                    <img alt="image" src="<?php echo myvalidate($mbr_imagestr); ?>" class="rounded-circle img-thumbnail profile-widget-picture">
                    <div class="profile-widget-items">
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label"><?php echo myvalidate($LANG['g_referrals']); ?></div>
                            <div class="profile-widget-item-value"><?php echo myvalidate($myreftotal); ?></div>
                        </div>
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label">Wallet</div>
                            <div class="profile-widget-item-value"><?php echo number_format($rowstr['ewallet'], 2); ?></div>
                        </div>
                    </div>
                </div>
                <div class="profile-widget-description">
                    <div class="profile-widget-name"><?php echo myvalidate($rowstr['username']); ?> <div class="text-muted d-inline font-weight-normal">
                            <div class="slash"></div> <?php echo formatdate($rowstr['in_date'], 'dt'); ?>
                        </div>
                    </div>
                    <div class="mt-4"><?php echo base64_decode($rowstr['mbr_intro']); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <a href="javascript:;" onclick="location.href = 'index.php?hal=historylist&dohal=filter&doval=<?php echo myvalidate($rowstr['id']); ?>'" class="btn btn-sm btn-block btn-round btn-info" data-toggle="tooltip" title="<?php echo myvalidate($LANG['g_historylist']); ?>"><i class="fa fa-fw fa-vote-yea"></i> History</a>
                </div>
                <div class="col-sm-12 col-md-4">
                    <a href="javascript:;" onclick="location.href = 'index.php?hal=userlist&dohal=filter&doval=<?php echo myvalidate($rowstr['mpid']); ?>'" class="btn btn-sm btn-block btn-round btn-info" data-toggle="tooltip" title="<?php echo myvalidate($LANG['g_referrallist']); ?>"><i class="fa fa-fw fa-user-friends"></i> Referral</a>
                </div>
                <div class="col-sm-12 col-md-4">
                    <a href="javascript:;" onclick="location.href = 'index.php?hal=genealogylist&loadId=<?php echo myvalidate($rowstr['mpid']); ?>'" class="btn btn-sm btn-block btn-round btn-info" data-toggle="tooltip" title="Genealogy Structure"><i class="fa fa-fw fa-sitemap"></i> Structure</a>
                </div>
            </div>
            <div class="d-block d-sm-none">
                &nbsp;
            </div>

            <article class="article mt-4">
                
                <div class="article-details">
                    <div>
                        <h5 class="text-center">Member Status</h5>
                        <br>
                    </div>
                    <div class='article-cta mt-4'>
                        <?php echo myvalidate($markstatus . $statusactstr); ?>
                    </div>
                    <br>
                </div>
            </article>

        </div>

        <div class="col-12 col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Sponsor</h4>
                    <div class="card-header-action">
                        <a data-collapse="#spr-collapse" class="btn btn-icon btn-secondary" href="#"><i class="fas fa-minus"></i></a>
                    </div>
                </div>
                <div class="collapse show" id="spr-collapse">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-5 col-12">
                                <label>Username</label>
                                <h6><?php echo myvalidate($rowsprstr['username']); ?></h6>
                            </div>
                            <div class="form-group col-md-7 col-12">
                                <label><?php echo myvalidate($LANG['g_name']); ?></label>
                                <h6><?php echo myvalidate($rowsprstr['firstname'] . ' ' . $rowsprstr['lastname']); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Email</label>
                                <h6><?php echo myvalidate($rowsprstr['email']); ?></h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <form method="post" class="needs-validation" novalidate="">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_accoverview']); ?></h4>
                        <div class="card-header-action">
                            <a class="btn btn-icon btn-primary" href="../<?php echo myvalidate(MBRFOLDER_NAME); ?>/login.php?ucpunlock=<?php echo myvalidate($rowstr['username']); ?>" data-toggle="tooltip" title="Login as member" target="_blank"><i class="fas fa-unlock-alt"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6 col-12">
                                <label><?php echo myvalidate($LANG['g_firstname']); ?></label>
                                <h6><?php echo myvalidate($rowstr['firstname']); ?></h6>
                            </div>
                            <div class="form-group col-md-6 col-12">
                                <label><?php echo myvalidate($LANG['g_lastname']); ?></label>
                                <h6><?php echo myvalidate($rowstr['lastname']); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Email</label>
                                <h6><?php echo myvalidate($rowstr['email']); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Phone</label>
                                <h6><?php echo myvalidate($rowstr['phone']); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Address</label>
                                <h6><?php echo myvalidate($rowstr['address']); ?> <?php echo myvalidate($rowstr['state']); ?></h6>
                                <h6><?php echo myvalidate($countrystr); ?></h6>
                            </div>
                        </div>

                        <?php if($admSess['sestype'] === 'admin') {?>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Bank Name</label>
                                <h6><?php echo myvalidate(base64_decode($rowstr['bankname'])); ?></h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Account Number</label>
                                <h6><?php echo myvalidate(base64_decode($rowstr['accountnum'])); ?></h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Bank IFSC</label>
                                <h6><?php echo myvalidate(base64_decode($rowstr['bankifsc'])); ?></h6>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row">
                            <div class="form-group col-12">
                                <label>Subscribe to notifications</label>
                                <h6><?php echo myvalidate($optinme_cekstr); ?></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12"><?php
                                                if ($mbr_twitter) {
                                                ?>
                                    <span class="badge badge-success">
                                        <i class="fab fa-fw fa-twitter"></i> <?php echo myvalidate($mbr_twitter); ?>
                                    </span>
                                <?php
                                                }
                                                if ($mbr_facebook) {
                                ?>
                                    <span class="badge badge-success">
                                        <i class="fab fa-fw fa-facebook-f"></i> <?php echo myvalidate($mbr_facebook); ?>
                                    </span>
                                <?php
                                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-whitesmoke text-right">
                        <a href="javascript:;" onclick="location.href = '<?php echo myvalidate($backpage); ?>'" class="btn btn-warning" data-toggle="tooltip" title="Back"><i class="fa fa-fw fa-undo-alt"></i> Back</a>
                        <a href="javascript:;" data-href="edituser.php?editId=<?php echo myvalidate($rowstr['id']); ?>&redir=getuser" data-poptitle="<i class='fa fa-fw fa-edit'></i> Edit Member" class="btn btn-success openPopup" data-toggle="tooltip" title="Edit <?php echo myvalidate($rowstr['username']); ?>"><i class="fa fa-fw fa-edit"></i> Update</a>
                    </div>
            </div>
            </form>
        </div>
    </div>

</div>
</div>