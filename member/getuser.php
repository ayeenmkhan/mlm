<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if (isset($FORM['getId']) and $FORM['getId'] != "") {
    // Get member details
    $rowstr = getmbrinfo($FORM['getId']);
    if (strpos($rowstr['sprlist'], ":{$mbrstr['mpid']}|") === false) {
        redirpageto('index.php?hal=userlist?err');
        exit;
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

    if ($rowstr['mpid'] < 1) {
        $markstatus = "<span class='alert alert-dark'>UNREGISTERED</span>";
    } else {
        if ($rowstr['mpstatus'] == 1) {
            $markstatus = "<span class='alert alert-success'>ACTIVE</span>";
        } else {
            $markstatus = "<span class='alert alert-secondary'>INACTIVE</span>";
        }
    }

    if ($rowstr['mbrstatus'] != 1) {
        $markstatus .= "<span class='alert alert-danger' data-toggle='tooltip' title='Account status is not Active!'><i class='fa fa-fw fa-exclamation-triangle'></i></span>";
    }

    $condition = " AND sprlist LIKE '%:{$rowstr['mpid']}|%'";
    $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
    $myreftotal = $row[0]['totref'];

    $backpage = ($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "index.php?hal=userlist";

    $rowrefstr = getmbrinfo('', '', $rowstr['idref']);
    $rowsprstr = getmbrinfo('', '', $rowstr['idspr']);
} else {
    header("Location: index.php?hal=dashboard");
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-user-circle"></i> <?php echo myvalidate($LANG['g_memberprofile']); ?></h1>
</div>

<div class="section-body">

    <div class="row mt-sm-4">
        <div class="col-12 col-md-12 col-lg-6">
            <div class="card profile-widget">
                <div class="profile-widget-header">
                    <img alt="image" src="<?php echo myvalidate($mbr_imagestr); ?>" class="rounded-circle profile-widget-picture">
                    <div class="profile-widget-items">
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label"><?php echo myvalidate($LANG['g_hits']); ?></div>
                            <div class="profile-widget-item-value"><?php echo myvalidate($rowstr['hits']); ?></div>
                        </div>
                        <div class="profile-widget-item">
                            <div class="profile-widget-item-label"><?php echo myvalidate($LANG['g_referrals']); ?></div>
                            <div class="profile-widget-item-value"><?php echo myvalidate($myreftotal); ?></div>
                        </div>
                    </div>
                </div>
                <div class="profile-widget-description">
                    <div class="profile-widget-name"><?php echo myvalidate($rowstr['username']); ?> <div class="text-muted d-inline font-weight-normal"><div class="slash"></div> <?php echo formatdate($rowstr['in_date'], 'dt'); ?></div></div>
                    <?php echo base64_decode($rowstr['mbr_intro']); ?>
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
                    <a href="javascript:;" onclick="location.href = 'index.php?hal=genealogyview&loadId=<?php echo myvalidate($rowstr['mpid']); ?>'" class="btn btn-sm btn-block btn-round btn-info" data-toggle="tooltip" title="Genealogy Structure"><i class="fa fa-fw fa-sitemap"></i> Structure</a>
                </div>
            </div>
            <div class="d-block d-sm-none">
                &nbsp;
            </div>

            <article class="article mt-4">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-title">
                        <h2 class="badge badge-primary"><?php echo ($bpprow['ppname']) ? myvalidate($bpprow['ppname']) : $cfgrow['site_name']; ?> - <?php echo ($bpprow['regfee'] > 0) ? myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']) : 'FREE'; ?></h2>
                    </div>
                </div>
                <div class="article-details">
                    <div><?php echo ($bpprow['planinfo']) ? myvalidate($bpprow['planinfo']) : '-'; ?></div>
                    <div class='article-cta mt-4'>
                        <?php echo myvalidate($markstatus); ?>
                    </div>
                </div>
            </article>

        </div>

        <div class="col-12 col-md-12 col-lg-6">
            <div class="card">
                <form method="post" class="needs-validation" novalidate="">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_accoverview']); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6 col-12">
                                <label>Referrer</label>
                                <h6><a href="index.php?hal=getuser&getId=<?php echo myvalidate($rowrefstr['id']); ?>" data-toggle="tooltip" title="<?php echo myvalidate($rowrefstr['firstname'] . ' ' . $rowrefstr['lastname']); ?>"><?php echo myvalidate($rowrefstr['username']); ?></a></h6>
                            </div>
                            <div class="form-group col-md-6 col-12">
                                <label>Sponsor</label>
                                <h6><a href="index.php?hal=getuser&getId=<?php echo myvalidate($rowsprstr['id']); ?>" data-toggle="tooltip" title="T<?php echo myvalidate($rowsprstr['firstname'] . ' ' . $rowsprstr['lastname']); ?>"><?php echo myvalidate($rowsprstr['username']); ?></a></h6>
                            </div>
                        </div>
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
                        <div class="row">
                            <div class="form-group col-12">
                                <label>Website <?php echo myvalidate($showsite_cekicon); ?></label>
                                <div class="text-muted font-weight-normal"><?php echo myvalidate($mbrsite_catstr); ?></div>
                                <h6><a href="<?php echo myvalidate($rowstr['mbrsite_url']); ?>" target="_blank"><?php echo myvalidate($rowstr['mbrsite_title']); ?></a></h6>
                                <div class="text-muted form-text">
                                    <?php echo base64_decode($rowstr['mbrsite_desc']); ?>
                                </div>
                            </div>
                        </div>
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
                    </div>
            </div>
            </form>
        </div>
    </div>

</div>
</div>
