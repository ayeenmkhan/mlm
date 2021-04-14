<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$condition = '';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', 'COUNT(*) as totref', $condition);
$myregtotal = $row[0]['totref'];

$condition = ' AND mpstatus = "0" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
$myreftotal = $row[0]['totref'];

$condition = ' AND txtoid = "0" AND txstatus = "1" AND txtoken LIKE "%|REG:%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
// $myincometotal = floatval($row[0]['totincome']);
$myincometotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $myincometotal= $myincometotal+$x;
}
// ---

$condition = ' AND txtoid = "0" AND txfromid > "0" AND txstatus = "1" AND txtoken NOT LIKE "%|WIDR:%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
// $mytxintotal = floatval($row[0]['totincome']);

$mytxintotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $mytxintotal= $mytxintotal+$x;
}
$condition = ' AND txtoid > "0" AND txfromid = "0" AND txstatus = "1" AND txtoken NOT LIKE "%|WIDR:%" ';
$row = $db->getAllRecords(DB_TBLPREFIX . '_transactions', 'txamount', $condition);
// $mytxouttotal = floatval($row[0]['totincome']);
$mytxouttotal = 0;
foreach ($row as $key => $value) {
    $x = str_replace( ',', '', $value['txamount']);

    $mytxouttotal= $mytxouttotal+$x;
}
$totincome = $mytxintotal - $mytxouttotal;

$condition = ' AND ((txtoid = "0" AND txfromid > "0") OR (txtoid > "0" AND txfromid = "0")) AND txstatus = "1" AND txtoken NOT LIKE "%|WIDR:%" ';
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
$mytottrx = count($sql);

// ---

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
        $regbadge_text = "Blocked";
        break;
    default:
        $regbadge_class = "badge-default";
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
} else {
    $myplanstatus = "<a href='index.php?hal=planreg' class='btn btn-primary btn-round'>{$LANG['g_register']}</a>";
}

// ---

$recenttrx = '';
$condition = "";
$limitrecent = intval($cfgrow['maxpage'] / 3);
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . " ORDER BY txid DESC LIMIT " . $limitrecent);
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $sestime = strtotime($val['reg_utctime']);
        $timejoin = time_since($sestime);
        $dlnimgfile = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
        $val['fullname'] = $val['firstname'] . ' ' . $val['lastname'];

        $fafontbadge = '';
        switch ($val['txstatus']) {
            case "1":
                $fafontbadge .= "fa-check text-primary";
                break;
            case "2":
                $fafontbadge .= "fa-exclamation text-warning";
                break;
            case "3":
                $fafontbadge .= "fa-times text-danger";
                break;
            default:
                $fafontbadge .= "fa-question text-light";
        }

        $recenttrx .= "<ul class='list-unstyled list-unstyled-border'>
                                <li class='media'>
                                    <i class='fa fa-2x fa-fw {$fafontbadge} mr-2'></i>
                                    <div class='media-body'>
                                        <div class='media-right'>{$bpprow['currencysym']}{$val['txamount']} {$bpprow['currencycode']}</div>
                                        <div class='media-title'>{$val['txmemo']}</div>
                                        <div class='float-right text-muted text-small'>{$val['txbatch']}</div>
                                        <div class='text-muted text-small'>{$val['txdatetm']}</div>
                                    </div>
                                </li>
                            </ul>";
    }
} else {
    $recenttrx = '<div class="text-center mt-4 text-muted">
                        <div>
                            <i class="fa fa-3x fa-question-circle"></i>
                        </div>
                        <div>No Record Found</div>
                   </div>';
}

// ---

$recentwdr = '';
$condition = " AND txtoken LIKE '%|WIDR:%'";
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions LEFT JOIN " . DB_TBLPREFIX . "_mbrs ON txtoid = id WHERE 1 " . $condition . " ORDER BY txstatus, txdatetm DESC LIMIT 9");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $sestime = strtotime($val['txdatetm']);
        $timereq = time_since($sestime);
        $usrwdrstr = getmbrinfo($val['txfromid']);
        $dlnimgfile = ($usrwdrstr['mbr_image']) ? $usrwdrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];
        $usrwdrstr['fullname'] = $usrwdrstr['firstname'] . ' ' . $usrwdrstr['lastname'];
        switch ($val['txstatus']) {
            case "1":
                $badgestatus = "<span class='badge badge-secondary' data-toggle='tooltip' title='Processing'><i class='fa fa-fw fa-check'></i></span>";
                break;
            case "2":
                $badgestatus = "<span class='badge badge-info' data-toggle='tooltip' title='Verified'><i class='fa fa-fw fa-user'></i></span>";
                break;
            default:
                $badgestatus = "<span class='badge badge-light' data-toggle='tooltip' title='Pending'><i class='fa fa-fw fa-question'></i></span>";
        }
        $stremail = (strlen($usrwdrstr['email']) > 16) ? substr($usrwdrstr['email'], 0, 14) . '...' : $usrwdrstr['email'];
        $recentwdr .= "<li class='media'>
                            <img class='mr-3 rounded-circle' width='48' src='{$dlnimgfile}' alt='avatar'>
                            <div class='media-body'>
                                <div class='float-right text-small text-success'>{$timereq} ago</div>
                                <div class='media-title'>{$bpprow['currencysym']}{$val['txamount']}</div>
                                <div class='float-right media-title'>{$badgestatus}</div>
                                <span class='text-small text-muted'>
                                    <div><a href='index.php?hal=getuser&getId={$val['id']}' data-toggle='tooltip' title='{$usrwdrstr['fullname']}'>{$usrwdrstr['username']}</a></div>
                                    <div data-toggle='tooltip' title='{$usrwdrstr['email']}'>{$stremail}</div>
                                </span>
                            </div>
                       </li>";
    }
} else {
    $recentwdr = '<div class="text-center mt-4 text-muted">
                        <div>
                            <i class="fa fa-3x fa-question-circle"></i>
                        </div>
                        <div>No Record Found</div>
                   </div>';
}

// ---

$recentrefl = '';
$condition = "";
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . " ORDER BY mpid DESC LIMIT 9");
if (count($userData) > 0) {
    foreach ($userData as $val) {
        $sestime = ($val['mpid'] > 0) ? strtotime($val['reg_utctime']) : strtotime($val['in_date']);
        $timejoin = time_since($sestime);
        $dlnimgfile = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
        $val['fullname'] = $val['firstname'] . ' ' . $val['lastname'];
        switch ($val['mpstatus']) {
            case "0":
                $badgestatus = "<span class='badge badge-light' data-toggle='tooltip' title='Inactive'><i class='fa fa-fw fa-question'></i></span>";
                break;
            case "1":
                $badgestatus = "<span class='badge badge-success' data-toggle='tooltip' title='Active'><i class='fa fa-fw fa-check'></i></span>";
                break;
            case "2":
                $badgestatus = "<span class='badge badge-warning' data-toggle='tooltip' title='Inactive'><i class='fa fa-fw fa-exclamation'></i></span>";
                break;
            case "2":
                $badgestatus = "<span class='badge badge-danger' data-toggle='tooltip' title='Inactive'><i class='fa fa-fw fa-times'></i></span>";
                break;
            default:
                $badgestatus = "<span class='badge badge-secondary' data-toggle='tooltip' title='Registered only'><i class='fa fa-fw fa-user'></i></span>";
        }
        $stremail = (strlen($val['email']) > 16) ? substr($val['email'], 0, 14) . '...' : $val['email'];
        $recentrefl .= "<li class='media'>
                            <img class='mr-3 rounded-circle' width='48' src='{$dlnimgfile}' alt='avatar'>
                            <div class='media-body'>
                                <div class='float-right text-small text-success'>{$timejoin} ago</div>
                                <div class='media-title'><a href='index.php?hal=getuser&getId={$val['id']}'>{$val['username']}</a></div>
                                <div class='float-right media-title'>{$badgestatus}</div>
                                <span class='text-small text-muted'>
                                <div>{$val['fullname']}</div><div data-toggle='tooltip' title='{$val['email']}'>{$stremail}</div>
                                </span>
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
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-chart-line"></i> <?php echo myvalidate($LANG['g_dashboardtitle']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="far fa-address-book"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_registered']); ?> Members</h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($myreftotal); ?>
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
                        <h4>Active Members</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php echo myvalidate($myregtotal); ?>
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
                        <h4>Incoming</h4>
                    </div>
                    <div class="card-body">
                        <?php echo myvalidate($bpprow['currencysym'] . $myincometotal); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_performance']); ?></h4>
                </div>
                <div class="card-body">
                    <canvas id="myChart" height="192"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart1" height="128"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart2" height="128"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Overview</h4>
                </div>
                <div class="card-body">
                    <div class="summary">
                        <div class="summary-info">
                            <h4><span class="text-success"><i class="fas fa-caret-up"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mytxintotal); ?> <span class="text-danger"><i class="fas fa-caret-down"></i></span><?php echo myvalidate($bpprow['currencysym'] . $mytxouttotal); ?></h4>
                            <div class="text-muted">from total <?php echo myvalidate($mytottrx); ?> transactions</div>
                            <h3 class="mt-2"><span class="text-info"></span><?php echo myvalidate($bpprow['currencysym'] . $totincome . ' ' . $bpprow['currencycode']); ?></h3>
                            <div class="d-block mt-2">
                                <a href="index.php?hal=historylist">View Details</a>
                            </div>
                        </div>
                        <div class="summary-item">
                            <h6><span class='text-muted'>Recent <?php echo myvalidate($limitrecent); ?> transactions</span></h6>
                            <?php echo myvalidate($recenttrx); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart3" height="256"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Withdrawal</h4>
                    <div class="card-header-action">
                        <a href="index.php?hal=withdrawlist" class="btn btn-primary" data-toggle="tooltip" title="View All"><i class="fa fa-ellipsis-h"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        <?php echo myvalidate($recentwdr); ?>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Recent Members</h4>
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
<script src="../assets/js/acpchart.js"></script>
