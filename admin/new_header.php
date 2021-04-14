<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$admimage = ($cfgrow['admimage']) ? $cfgrow['admimage'] : DEFIMG_ADM;

if ($bpprow['maxwidth'] == 0) {
    $menuactive['genealogylist'] = " style='display:none;'";
}

$updatebtnstr = (!defined('ISDEMOMODE')) ? '<a href="index.php?hal=updates" class="btn btn-success btn-lg btn-block btn-icon-split"><i class="fas fa-rocket"></i> ' . $LANG['a_updates'] . '</a>' : '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{$cfgrow['site_name']} {$LANG['g_admincp']}</title>

        <meta name="description" content="{$cfgrow['site_descr']}">
        <meta name="keywords" content="{$cfgrow['site_keywrd']}">
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.png"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">
        <link rel="stylesheet" href="../assets/css/toastr.min.css">
        <link rel="stylesheet" href="../assets/css/select2.min.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>
        <script src="../assets/js/toastr.min.js"></script>
        <script src="../assets/js/select2.full.min.js"></script>
        <script src="../assets/js/bootbox.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>

        <!-- include summernote css/js -->
        <link href="../assets/css/summernote-bs4.css" rel="stylesheet">
        <script src="../assets/js/summernote-bs4.min.js"></script>

    </head>

    <body>
        <div id="app">
            <div class="main-wrapper">
                <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <div class="mr-auto">
                        <ul class="navbar-nav mr-3">
                            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                            <li><a href="{$cfgrow['site_url']}" class="nav-link nav-link-lg" data-toggle="tooltip" title="{$cfgrow['site_url']}" target="_blank"><i class="fas fa-globe-asia"></i></a></li>
                        </ul>
                    </div>
                    {$tplstr['demo_mode_warn']}{$tplstr['debug_mode_warn']}
                    <ul class="navbar-nav navbar-right">

                        <li class="dropdown dropdown-list-toggle">
                            <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
                                <div class="d-sm-none badge badge-light"><span class="text-uppercase">{$LANG['lang_iso']}</span></div>
                                <div class="d-none d-sm-block badge badge-light">{$translation_str}</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-title">Language</div>
                                {$langliststr}
                            </div>
                        </li>


                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <img alt="image" src="{$admimage}" class="rounded-circle mr-1">
                                <div class="d-sm-none d-lg-inline-block"><span class="text-capitalize">{$cfgrow['admin_user']}</span> </div></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-title">Logged in {$logtimeago}</div>
                                <a href="index.php?hal=generalcfg#cfgtab3" class="dropdown-item has-icon">
                                    <i class="far fa-user-circle"></i> Profile
                                </a>
                                <a href="index.php?hal=generalcfg#cfgtab1" class="dropdown-item has-icon">
                                    <i class="fas fa-cogs"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item has-icon text-danger">
                                    <i class="fas fa-door-open"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="main-sidebar">
                    <aside id="sidebar-wrapper">
                        <div class="sidebar-brand">
                            <a href="index.php">{$LANG['g_admincp']}</a>
                        </div>
                        <div class="sidebar-brand sidebar-brand-sm">
                            <a href="index.php">{$LANG['g_admincpinit']}</a>
                        </div>
                        <ul class="sidebar-menu">
                            <li class="menu-header">Main</li>
                            <li{$menuactive['dashboard']}><a class="nav-link" href="index.php?hal=dashboard"><i class="fas fa-chart-line"></i><span>{$LANG['g_dashboard']}</span></a></li>

                            <li class="menu-header">Member</li>
                            <li{$menuactive['userlist']}><a class="nav-link" href="index.php?hal=userlist"><i class="fas fa-users"></i><span>{$LANG['a_managemember']}</span></a></li>
                            <li{$menuactive['historylist']}><a class="nav-link" href="index.php?hal=historylist"><i class="fas fa-cash-register"></i> <span>{$LANG['a_historylist']}</span></a></li>
                            <li{$menuactive['withdrawlist']}><a class="nav-link" href="index.php?hal=withdrawlist"><i class="fas fa-hand-holding-usd"></i> <span>{$LANG['a_withdrawlist']}</span></a></li>
                            <li{$menuactive['genealogylist']}><a class="nav-link" href="index.php?hal=genealogylist"><i class="fas fa-sitemap"></i> <span>{$LANG['a_genealogylist']}</span></a></li>

                            <li class="menu-header">Utility</li>
                            <li{$menuactive['getstart']}><a class="nav-link" href="index.php?hal=getstart"><i class="fas fa-flag-checkered"></i> <span>{$LANG['a_getstart']}</span></a></li>
                            <li{$menuactive['digifile']}><a class="nav-link" href="index.php?hal=digifile"><i class="fas fa-cloud-download-alt"></i> <span>{$LANG['a_digifile']}</span></a></li>
                            <li{$menuactive['digicontent']}><a class="nav-link" href="index.php?hal=digicontent"><i class="fas fa-window-restore"></i><span>{$LANG['a_digicontent']}</span></a></li>
                            <li{$menuactive['termscon']}><a class="nav-link" href="index.php?hal=termscon"><i class="fas fa-exclamation-circle"></i> <span>{$LANG['a_termscon']}</span></a></li>
                            <li{$menuactive['termskyc']}><a class="nav-link" href="index.php?hal=termskyc"><i class="fas fa-exclamation-circle"></i> <span>{$LANG['a_termskyc']}</span></a></li>

                            <li class="menu-header">Setting</li>
                            <li{$menuactive['notifylist']}><a class="nav-link" href="index.php?hal=notifylist"><i class="fas fa-bullhorn"></i><span>{$LANG['a_notifylist']}</span></a></li>
                            <li{$menuactive['generalcfg']}><a class="nav-link" href="index.php?hal=generalcfg"><i class="fas fa-tools"></i> <span>{$LANG['a_settings']}</span></a></li>
                            <li{$menuactive['payplancfg']}><a class="nav-link" href="index.php?hal=payplancfg"><i class="fas fa-gem"></i><span>{$LANG['a_payplan']}</span></a></li>
                            <li{$menuactive['paymentopt']}><a class="nav-link" href="index.php?hal=paymentopt"><i class="fas fa-money-bill-wave"></i><span>{$LANG['a_payment']}</span></a></li>
                            <li{$menuactive['languagelist']}><a class="nav-link" href="index.php?hal=languagelist"><i class="fas fa-flag"></i><span>{$LANG['a_languagelist']}</span></a></li>
                        </ul>

                        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                            {$updatebtnstr}
                            <a href="logout.php" class="btn btn-danger btn-lg btn-block btn-icon-split">
                                <i class="fas fa-door-open"></i> Logout
                            </a>
                        </div>
                    </aside>
                </div>

