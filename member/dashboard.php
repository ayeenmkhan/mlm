<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

//Agree KYC Terms and condition;
$strmbr_intro = base64_decode($mbrstr['mbr_intro']);
$mbr_sosmed = get_optionvals($mbrstr['mbr_sosmed']);
$mbrstr['mbr_twitter'] = $mbr_sosmed['mbr_twitter'];
$mbrstr['mbr_facebook'] = $mbr_sosmed['mbr_facebook'];

$country_array = array_map('strtolower', $country_array);
$country_array = array_map('ucwords', $country_array);
$country_menu = select_opt($country_array, $mbrstr['country']);

$mbrsite_cat_menu = select_opt($webcategory_array, $mbrstr['mbrsite_cat']);

$optinmearr = array(0, 1);
$optinme_cek = radiobox_opt($optinmearr, $mbrstr['optinme']);
$showsitearr = array(0, 1);
$showsite_cek = radiobox_opt($showsitearr, $mbrstr['showsite']);
$is_agree=$mbrstr['is_agree'];
$mbrimgstr = ($mbrstr['mbr_image']) ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];


if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);
    // if password change

    $data = array(
        'is_agree' => 1,
    );

    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $mbrstr['id']));

    if ($update0 || $update || $update1 || $insert) {
        $_SESSION['dotoaster'] = "toastr.success('Record updated successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
    }

    redirpageto('index.php?hal=dashboard' . $hal);
    exit;
}
if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == 'resend') {

    extract($FORM);
    // var_dump($FORM);exit;
    // if password change
    $toemail= $user_email;
    $receiverName= $username;
    require_once('../common/mailer.do.php');
    validatemail($toemail,$receiverName,$user_id);
    $_SESSION['dotoaster'] = "toastr.success('Email verification sent successfully!', 'Success');";
    redirpageto('index.php?hal=dashboard' . $hal);
    exit;
}

$condition = ' AND sprlist LIKE "%:' . $mbrstr['mpid'] . '|%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
$myreftotal = $row[0]['totref'];

$condition = ' AND txtoid = "' . $mbrstr['id'] . '" AND txstatus = "1" AND txtoken LIKE "%|LCM:%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
$myincometotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $myincometotal= $myincometotal+$x;
}


// ---

$condition = ' AND txtoid = "' . $mbrstr['id'] . '" AND txstatus = "1" AND txtoken NOT LIKE "%|REG:%" AND (txtoken LIKE "%|LCM:%" OR txtoken LIKE "%|WALT:IN|%") ';

$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
// $mytxintotal = floatval($row[0]['totincome']);
$mytxintotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $mytxintotal= $mytxintotal+$x;
}

$condition = ' AND txfromid = "' . $mbrstr['id'] . '" AND txstatus = "1" AND txtoken NOT LIKE "%|REG:%" AND (txtoken LIKE "%|WIDR:OUT|%" OR txtoken LIKE "%|WALT:OUT|%") ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
// $mytxouttotal = floatval($row[0]['totincome']);
$mytxouttotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $mytxouttotal= $mytxouttotal+$x;
}

$condition = ' AND (txtoid = "' . $mbrstr['id'] . '" OR txfromid = "' . $mbrstr['id'] . '") AND txstatus = "1" AND txtoken NOT LIKE "%|REG:%" ';
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
$mytottrx = count($sql);

$mydiftrx = floatval($mytxintotal - $mytxouttotal - $mbrstr['ewallet']);

// echo $mytxintotal;
// echo $mytxouttotal;
// echo $mbrstr['ewallet'];
// ---

$mbrimgstr = ($mbrstr['mbr_image']) ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];

switch ($mbrstr['mbrstatus']) {
    case "1":
        $regbadge_class = "badge-success";
        $regbadge_text = "Active";
        break;
    case "2":
        $regbadge_class = "badge-warning";
        $regbadge_text = "Limited";
        break;
    case "3":
        $regbadge_class = "badge-danger";
        $regbadge_text = "Pending";
        break;
    default:
        $regbadge_class = "badge-light";
        $regbadge_text = "Inactive";
}
$myregstatus = "<div class='badge {$regbadge_class}'>{$regbadge_text}</div>";

if (intval($mbrstr['mpid']) > 0) {
    $myplanpay = '';
    switch ($mbrstr['mpstatus']) {
        case "1":
            $badge_class = "badge-success";
            $badge_text = "Active";
            break;
        case "2":
            $badge_class = "badge-warning";
            $badge_text = "Expire";
            break;
        case "3":
            $badge_class = "badge-danger";
            $badge_text = "Pending";
            break;
        default:
            $badge_class = "badge-primary";
            $badge_text = "";
            $myplanpay = "<a href='index.php?hal=planpay' class='btn btn-danger btn-round'>Make Payment</a>";
    }
    $myplanstatus = "<div class='badge {$badge_class}'>{$badge_text}</div>" . $myplanpay;
    $reg_date = formatdate($mbrstr['reg_date']);
    $regsince = "<span class='text-muted'>Registered Since</span> {$reg_date}";
} else {
    $myplanstatus = "<a href='index.php?hal=planreg' class='btn btn-primary btn-round'>{$LANG['g_register']}</a>";
    $regsince = '';
}

// ---

$sprstr = getmbrinfo($mbrstr['idspr']);
$sprstr['fullname'] = $sprstr['firstname'] . ' ' . $sprstr['lastname'];
$sprimgstr = ($sprstr['mbr_image']) ? $sprstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
$spremailstr = (strlen($sprstr['email']) > 23) ? substr($sprstr['email'], 0, 20) . '...' : $sprstr['email'];
$sprphonestr = ($sprstr['phone']) ? $sprstr['phone'] : '-';
$sprcountrystr = ucwords(strtolower($country_array[$sprstr['country']]));
$sprstatusstr = badgembrplanstatus($sprstr['mbrstatus'], $sprstr['mpstatus']);
$spraboutstr = ($sprstr['mbr_intro']) ? "<blockquote class='text-small'>" . base64_decode($sprstr['mbr_intro']) . "</blockquote>" : '';

// ---

$recentrefl = '';
$condition = " AND sprlist LIKE '%:{$mbrstr['mpid']}|%' AND mppid = '{$mbrstr['mppid']}' AND id != {$mbrstr['id']}";
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . " ORDER BY mpid DESC LIMIT 9");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $sestime = strtotime($val['reg_utctime']);
        $timejoin = time_since($sestime);
        $dlnimgfile = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
        $val['fullname'] = $val['firstname'] . ' ' . $val['lastname'];
        $stremail = (strlen($val['email']) > 24) ? substr($val['email'], 0, 21) . '...' : $val['email'];
        $recentrefl .= "<li class='media'>
                            <img class='mr-3 rounded-circle' width='48' src='{$dlnimgfile}' alt='avatar'>
                            <div class='media-body'>
                                <div class='float-right text-small text-success'>{$timejoin} ago</div>
                                <div class='media-title'>{$val['username']}</div>
                                <span class='text-small text-muted'><div>{$val['fullname']}</div><div data-toggle='tooltip' title='{$val['email']}'>{$stremail}</div></span>
                            </div>
                       </li>";
    }
} else {
    $recentrefl = '<div class="text-center mt-4 text-muted">
                        <div>
                            <i class="fa fa-3x fa-question-circle"></i>
                        </div>
                        <div>No Record Found</div>
                   </div>';
}

$expdatestr = ($mbrstr['reg_expd'] > $mbrstr['reg_date']) ? 'Expiration: ' . formatdate($mbrstr['reg_expd']) : '';




?>
<style type="text/css">
      #info {
        position: relative;
        left: 64%;
    }

@media only screen and (min-width: 1025px) and (max-width: 1560px) {
    #info {
        position: relative !important;
        left: 60% !important;
    }
}
@media only screen and (min-width: 992px) and (max-width: 1024px) {
    #info {
        position: relative !important;
        left: 51% !important;
    }
}
@media only screen and (min-width: 576px) and (max-width: 800px) {
    #info {
        position: relative !important;
        left: 46% !important;
    }
}
    @media only screen and (max-width: 800px) {
    #info {
        position: relative;
        left: 64%;
    }
}   
/* @media only screen and (max-width: 800px) {
    #info {
        position: relative;
        left: 50%;
    }
}*/


</style>
<div class="section-header">
     <h1><i class="fa fa-fw fa-chart-line"></i> <?php echo myvalidate($LANG['g_dashboardtitle']); ?></h1>
</div>
<div class="section-body">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="far fa-paper-plane"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_hits']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($mbrstr['hits']); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="far fa-handshake"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_referrals']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($myreftotal); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="far fa-money-bill-alt"></i>
                </div>
               

                
                <div class="card-wrap">
                    <div class="card-header">
                        <!-- <h4><?php echo myvalidate($LANG['g_earning']); ?></h4> -->
                        <h4>My Wallet</h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($bpprow['currencysym'] . number_format(@$mbrstr['ewallet'],2)); ?>
                    </div>
                       <i class="fa fa-info-circle fa-6" id="info" data-toggle="tooltip" title="Commissions are being processed after admin approval" aria-hidden="true"  style="font-size: 25px;"></i>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                <div class="alert alert-light alert-has-icon">
                    <div class="alert-icon text-success"><i class="far fa-bell"></i></div>
                    <div class="alert-body text-success">
                        <div class="alert-title">Hello, <?php echo $mbrstr['fullname'];?> </div>
                        <p>Welcome to MLM, We're glad you've Joined Our Platform.</p>
                        <div class="float-right mt-4">
                        
                        </div>
                    </div>
                </div>
            <?php
            $unpaidtxid = get_unpaidtxid($mbrstr);
            $myplanstatusbtn = ($unpaidtxid > 0) ? "<a href='index.php?hal=planpay' class='btn btn-danger btn-round'>Make Payment</a>" : $myplanstatus;

            if (intval($mbrstr['confirm_email']) == 0 ) {
                ?>
             <!--    <div class="alert alert-light alert-has-icon">
                    <div class="alert-icon text-danger"><i class="far fa-bell"></i></div>
                    <div class="alert-body text-danger">
                        <div class="alert-title">Email Address Is not verified!</div>
                        <p>Please verify your email Address By clicking on link that we sent.</p>
                        <div class="float-right mt-4">
                            <form action="" method="post">
                                <input type="hidden" name="usernamee" value="<?php echo $mbrstr['fullname'];?>">
                                <input type="hidden" name="user_email" value="<?php echo $mbrstr['email'];?>">
                                <input type="hidden" name="user_id" value="<?php echo $mbrstr['id'];?>">
                                <input type="submit" name="dosubmit" value="resend" class="btn btn-info btn-sm">
                            </form>
                        </div>
                    </div>
                </div> -->
                <?php
            }
            ?>
        <?php 
                if (intval($mbrstr['mpstatus']) == 0 ) {
            ?>
                    <div class="alert alert-light alert-has-icon">
                    <div class="alert-icon text-info"><i class="far fa-bell"></i></div>
                    <div class="alert-body text-info">
                        <div class="alert-title">Profile verification Pending!</div>
                        <p>The course content will be accessible after payment verification</p>
                    </div>
                </div>
        <?php }?>

            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_accoverview']); ?></h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($myregstatus); ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <ul class="list-unstyled list-unstyled-border">
                            <li class="media">
                                <div class="media-body">
                                    <div class="media-title">
                                        <img class='mr-3 rounded-circle img-responsive' width='<?php echo myvalidate($cfgrow['mbrmax_image_width']); ?>' height='<?php echo myvalidate($cfgrow['mbrmax_image_height']); ?>' src='<?php echo myvalidate($mbrimgstr); ?>' alt='<?php echo myvalidate($mbrstr['username']); ?>'></div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-body">
                                    <div class="text-small"><?php echo myvalidate($LANG['g_registered']); ?></div>
                                    <div class="media-title"><?php echo formatdate($mbrstr['in_date']); ?></div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-body">
                                    <div class="text-small"><?php echo myvalidate($LANG['g_name']); ?></div>
                                    <div class="media-title"><?php echo myvalidate($mbrstr['fullname'] . ' (' . $mbrstr['email'] . ')'); ?></div>
                                </div>
                            </li>
                            <?php
                            if (intval($mbrstr['mpstatus']) == 1 && $cfgtoken['disreflink'] != 1) {
                                ?>
                                <li class="media">
                                    <div class="media-body">
                                        <div class="text-small"><?php echo myvalidate($LANG['g_refurl']); ?></div>
                                        <div class="media-title">
                                            <a href="<?php echo myvalidate($cfgrow['site_url']) . '/' . UIDFOLDER_NAME . '/' . $mbrstr['username']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo myvalidate($cfgrow['site_url']) . '/' . UIDFOLDER_NAME . '/' . $mbrstr['username']; ?>">
                                                <span class="d-none d-sm-block"><?php echo myvalidate($cfgrow['site_url']) . '/' . UIDFOLDER_NAME . '/' . $mbrstr['username']; ?></span>
                                                <span class="d-sm-none"><i class="fa fa-fw fa-link"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <?php
            if (intval($mbrstr['mpstatus']) == 1) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_performance']); ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="182"></canvas>
                    </div>
                </div>
                <?php
            }
            ?>

            <!-- <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_membership']); ?></h4>
                    <div class="card-header-action">
                        <?php echo myvalidate($myplanstatus); ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary">
                        <div class="summary-info">
                            <h4><span class="text-success"><i class="fas fa-caret-up"></i></span><?php echo $bpprow['currencysym']. number_format(@$mytxintotal,2); ?> <span class="text-danger"><i class="fas fa-caret-down"></i></span><?php echo $bpprow['currencysym'] . number_format($mytxouttotal,2); ?> <small><span class="text-warning"><i class="far fa-pause-circle"></i></span><?php echo $bpprow['currencysym'] . number_format(@$mydiftrx,2); ?></small></h4>
                            <div class="text-muted">from total <?php echo myvalidate($mytottrx); ?> transactions</div>
                            <h3 class="mt-2"><span class="text-info"><i class="fas fa-wallet"></i></span><?php echo myvalidate($bpprow['currencysym'] . number_format(@$mbrstr['ewallet']) . ' ' . $bpprow['currencycode']); ?></h3>
                            <div class="d-block mt-2">
                                <a href="index.php?hal=historylist">View Details</a>
                            </div>
                        </div>
                        <div class="summary-item">
                            <h6><?php echo myvalidate($regsince); ?></h6>
                            <ul class="list-unstyled list-unstyled-border">
                                <li class="media">
                                    <a href="index.php?hal=planreg">
                                        <img class="mr-3 rounded" width="50" src="<?php echo myvalidate($planlogo); ?>" alt="Membership">
                                    </a>
                                    <div class="media-body">
                                        <div class="media-right"><?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?></div>
                                        <div class="media-title"><a href="index.php?hal=planreg"><?php echo myvalidate($bpprow['ppname']); ?></a></div>
                                        <h6 class="text-small">
                                            <?php
                                            if ($mbrstr['reg_expd'] < date("Y-m-d")) {
                                                ?>
                                                <span class="badge badge-danger"><?php echo myvalidate($expdatestr); ?></span>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="badge badge-info"><?php echo myvalidate($expdatestr); ?></span>
                                                <?php
                                            }
                                            ?>
                                        </h6>
                                        <div class="text-muted text-small"><?php echo myvalidate($bpprow['planinfo']); ?></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <?php
            if ($mbrstr['idspr'] > 0) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_mysponsor']); ?></h4>
                        <div class="card-header-action">
                            <?php echo myvalidate($sprstatusstr); ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled list-unstyled-border">
                            <li class='media'>
                                <img class='mr-3 rounded-circle' width='48' src='<?php echo myvalidate($sprimgstr); ?>' alt='avatar'>
                                <div class='media-body'>
                                    <div class='float-right text-small text-success'></div>
                                    <div class='media-title'><?php echo myvalidate($sprstr['username']); ?></div>
                                    <span class='text-small text-muted'>
                                        <div><?php echo myvalidate($sprstr['fullname']); ?></div>
                                        <div data-toggle='tooltip' title='<?php echo myvalidate($sprstr['email']); ?>'><i class="fa fa-fw fa-envelope"></i> <?php echo myvalidate($spremailstr); ?></div>
                                        <div><i class="fa fa-fw fa-mobile-alt"></i> <?php echo myvalidate($sprphonestr); ?></div>
                                        <div><?php echo myvalidate($sprcountrystr); ?></div>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <div><?php echo myvalidate($spraboutstr); ?></div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_recentref']); ?></h4>
                    <div class="card-header-action">
                        <a href="index.php?hal=userlist" class="btn btn-primary" data-toggle="tooltip" title="View All"><i class="fa fa-ellipsis-h"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        <?php echo myvalidate($recentrefl); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- Template JS File -->
<script src="../assets/js/chart.min.js"></script>

<!-- Page Specific JS File -->
<script src="../assets/js/ucpchart.js"></script>
<?php if($is_agree==0){?>
<script>
$(document).ready(function(){
        $("#myModalterm").modal('show');
});
</script>
<?php } ?>