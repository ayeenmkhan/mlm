<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if ($bpprow['maxwidth'] == 0) {
    $menuactive['genealogyview'] = " style='display:none;'";
    if ($FORM['hal'] == 'genealogyview') {
        $pagefile = 'dashboard.php';
    }
}
$minimenu="";
if($FORM['hal'] == 'loadcourses'){
    $minimenu="sidebar-mini";
}

$modalcontent = file_get_contents(SURL . "/admin/kycterms.html");

$mbrimgfile = ($mbrstr['mbr_image']) ? $mbrstr['mbr_image'] : $cfgrow['mbr_defaultimage'];

$paymentStatus= getuserPaymentStatusID($mbrstr['id']);
$paystaus= $paymentStatus[0]['mpstatus'];

// echo "<pre>";print_r($mbrstr);exit;
if($mbrstr['ewallet']>='1000.00' && $mbrstr['ewallet']<'3000.00'){

    $LEVEL= '<span class="badge badge-primary font-weight-bold">Level One</span>';
}
if($mbrstr['ewallet']>='3000.00' && $mbrstr['ewallet']<'5000.00'){

    $LEVEL= '<span class="badge badge-secondary font-weight-bold">Level Two</span>';
}
if($mbrstr['ewallet']>='5000.00' && $mbrstr['ewallet']<'8000.00'){

    $LEVEL= '<span class="badge badge-success font-weight-bold">Level Three</span>';
}
if($mbrstr['ewallet']>='8000.00' && $mbrstr['ewallet']<'10000.00'){

    $LEVEL= '<span class="badge badge-danger font-weight-bold">Level Four</span>';
}
if($mbrstr['ewallet']>='10000.00' && $mbrstr['ewallet']<'15000.00'){

    $LEVEL= '<span class="badge badge-warning font-weight-bold">Level Five</span>';
}
if($mbrstr['ewallet']>='15000.00' && $mbrstr['ewallet']<'20000.00'){

    $LEVEL= '<span class="badge badge-info font-weight-bold">Level Six</span>';
}
if($mbrstr['ewallet']>='20000.00' && $mbrstr['ewallet']<'40000.00'){

    $LEVEL= '<span class="badge badge-light font-weight-bold">Level Seven</span>';
}
if($mbrstr['ewallet']>='40000.00'){

    $LEVEL= '<span class="badge badge-dark">LEVEL Eight</span>';
}
// if($paystaus==1){
//     $coursesmenu=  <<<INI_HTML
//                          <li{$menuactive['digiload']}><a class="nav-link" href="index.php?hal=digiload"><i class="fas fa-book"></i> <span>{$LANG['m_digiload']}</span></a></li>
//         <li{$menuactive['digiview']}><a class="nav-link" href="index.php?hal=digiview"><i class="fas fa-window-restore"></i><span>{$LANG['m_digiview']}</span></a></li>
// INI_HTML;
// }

$banusmenu = '';
if ($mbrstr['ewallet'] > 1) {
    $banusmenu = <<<INI_HTML
                             <li{$menuactive['getstarted']}><a class="nav-link" href="index.php?hal=getstarted"><i class="fas fa-funnel-dollar"></i><span>Earn Bonuses</span></a></li> 
                INI_HTML;
}


if ($mbrstr['mpid'] > 0) {
    $mbractive_menu = <<<INI_HTML
                            <li class="menu-header">Account</li>
                            <li{$menuactive['userlist']}><a class="nav-link" href="index.php?hal=userlist"><i class="fas fa-users"></i><span>{$LANG['m_userlist']}</span></a></li>
                            <li{$menuactive['historylist']}><a class="nav-link" href="index.php?hal=historylist"><i class="fas fa-cash-register"></i> <span>{$LANG['m_historylist']}</span></a></li>
                            <li{$menuactive['withdrawreq']}><a class="nav-link" href="index.php?hal=withdrawreq"><i class="fas fa-hand-holding-usd"></i> <span>{$LANG['a_withdrawpayout']}</span></a></li>
                            <li{$menuactive['genealogyview']}><a class="nav-link" href="index.php?hal=genealogyview"><i class="fas fa-sitemap"></i> <span>{$LANG['m_genealogyview']}</span></a></li>
INI_HTML;
}

$member_content = <<<INI_HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>Fastearning Member</title>

        <meta name="description" content="{$cfgrow['site_descr']}">
        <meta name="keywords" content="{$cfgrow['site_keywrd']}">
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/logo_new.png"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">
        <link rel="stylesheet" href="../assets/css/toastr.min.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/simple-sidebar.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">
        <link rel="stylesheet" href="../assets/css/playlist.css">

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>
        <script src="../assets/js/toastr.min.js"></script>
        <script src="../assets/js/bootbox.min.js"></script>
        
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>

        <!-- include summernote css/js -->
        <link href="../assets/css/summernote-bs4.css" rel="stylesheet">
        <script src="../assets/js/summernote-bs4.min.js"></script>
        
    </head>

    <body class="{$minimenu}">
        <div id="app">
            <div class="main-wrapper">
                <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <div class="mr-auto">
                        <ul class="navbar-nav mr-3">
                            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                        </ul>
                    </div>

                    {$tplstr['demo_mode_warn']}{$tplstr['debug_mode_warn']}
                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" class="nav-link notification-toggle nav-link-lg">
                                <div class="d-sm-none badge badge-light"><span class="text-uppercase">{$LANG['lang_iso']}</span></div>
                                {$LEVEL}
                            </a>
                        </li>
                        
                        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <img alt="image" src="{$mbrimgfile}" class="rounded-circle mr-1">
                                <div class="d-sm-none d-lg-inline-block"><span class="text-capitalize">{$mbrstr['username']}</span></div></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-title">Logged in {$logtimeago}</div>
                                <a href="index.php?hal=accountcfg#cfgtab3" class="dropdown-item has-icon">
                                    <i class="far fa-user-circle"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php?un={$mbrstr['username']}" class="dropdown-item has-icon text-danger">
                                    <i class="fas fa-door-open"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="main-sidebar">
                    <aside id="sidebar-wrapper">
                        <div class="sidebar-brand">
                            <a href="index.php">{$LANG['g_membercp']}</a>
                        </div>
                        <div class="sidebar-brand sidebar-brand-sm">
                            <a href="index.php">{$LANG['g_membercpinit']}</a>
                        </div>
                        <ul class="sidebar-menu">
                            <li class="menu-header">Main</li>
                            <li{$menuactive['dashboard']}><a class="nav-link" href="index.php?hal=dashboard"><i class="fas fa-chart-line"></i><span>{$LANG['g_dashboard']}</span></a></li>
                            
                            {$banusmenu}
                            {$mbractive_menu}                         

                            <li class="menu-header">Setting</li>
                            <li{$menuactive['accountcfg']}><a class="nav-link" href="index.php?hal=accountcfg"><i class="fas fa-user-cog"></i> <span>{$LANG['m_profilecfg']}</span></a></li>
                            <li{$menuactive['feedback']}><a class="nav-link" href="index.php?hal=feedback"><i class="fas fa-life-ring"></i><span>{$LANG['m_feedback']}</span></a></li>
                        </ul>

                        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                            <a href="logout.php?un={$mbrstr['username']}" class="btn btn-danger btn-lg btn-block btn-icon-split">
                                <i class="fas fa-door-open"></i> Logout
                            </a>
                        </div>
                    </aside>
                </div>

                <!-- Modal -->
<div class="modal fade" id="myModalterm" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title ">KYC Agreement</h5>
            </div>
            <form action="index.php" method="post">
            <div class="modal-body">
                <div class="text-muted"> $modalcontent </div>
            </div>
            <div class="modal-footer">
            <input type="hidden" name="dosubmit" value="1">
                <button type="submit" class="btn btn-success">I Agree</button>
            </div>
            </form>
        </div>
    </div>
</div>
INI_HTML;

echo myvalidate($member_content);
