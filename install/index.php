<?php
include('../common/config.php');
include_once('../common/db.class.php');
include_once('../common/sys.func.php');

$FORM = array_merge((array) $FORM, (array) $_REQUEST);
session_start();

$sversion = 'v1.2.2';

function find_SQL_Version() {
    if (function_exists('mysqli_get_client_info') || function_exists('mysql_get_client_info')) {
        $mysql_get_clinfo = (phpversion() >= 5.5) ? mysqli_get_client_info() : mysql_get_client_info();
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $mysql_get_clinfo, $version);
        return $version[0];
    } else {
        return '?';
    }
}

function dostepredir($dostep) {
    $_SESSION['dostep'] = intval($dostep);
    redirpageto($_SERVER['PHP_SELF']);
    exit;
}

$page_header = "Installation Wizard";
$page_content = <<<INI_HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{$page_header}</title>
        <meta name="author" content="MLMScript.net">

        <link rel="shortcut icon" type="image/png" href="../assets/image/favicon.png"/>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/fellow/fontawesome5121/css/all.min.css">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="../assets/css/pace-theme-minimal.css">

        <!-- Template CSS -->
        <link rel="stylesheet" href="../assets/css/fontmuli.css">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/components.css">
        <link rel="stylesheet" href="../assets/css/custom.css">

    </head>

    <body>
        <div id="app">
INI_HTML;
echo myvalidate($page_content);

if (defined('INSTALL_PATH') && $_SESSION['dostep'] < 4) {
    $_SESSION['dostep'] = '';
}

$formval = $_SESSION;
$instep = $_SESSION['dostep'];

$markstep1 = $markstep2 = $markstep3 = '';
if ($instep == 4) {
    $markstep1 = $markstep2 = $markstep3 = $markstep4 = " wizard-step-active";
} elseif ($instep == 3) {
    $markstep1 = $markstep2 = $markstep3 = " wizard-step-active";
} elseif ($instep == 2) {
    $markstep1 = $markstep2 = " wizard-step-active";
} else {
    $markstep1 = " wizard-step-active";
}

$errmsg = $_SESSION['errmsg'];
if ($errmsg) {
    if ($instep == 2) {
        $markstep2 = " wizard-step-warning";
    } elseif ($instep == 3) {
        $markstep3 = " wizard-step-warning";
    }
}
$_SESSION['errmsg'] = '';

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    $_SESSION = array_merge($_SESSION, $FORM);
    extract($_SESSION);

    if ($instep == 2) {
        // check database
        $dsn = "mysql:dbname=" . $db_name . ";host=" . $db_host . "";
        $pdo = "";
        try {
            $pdo = new PDO($dsn, $db_username, $db_password);
        } catch (PDOException $e) {
            $_SESSION['errmsg'] = "Connection failed: " . $e->getMessage();
            $dostep = 2;
            dostepredir($dostep);
        }
    } elseif ($instep == 3) {
        // settings
        if ($admin_password != $admin_passwordx) {
            $_SESSION['errmsg'] = "Password do not same, please re-enter correctly!";
            $dostep = 3;
            dostepredir($dostep);
        }
        if (strlen($lickey) < 16) {
            $_SESSION['errmsg'] = "Invalid license key!";
            $dostep = 3;
            dostepredir($dostep);
        }

        // verify license
        $domain = $_SERVER['HTTP_HOST'];
        $domain64 = base64_encode($domain);
        $lickey64 = base64_encode($lickey);

        $arrdata = array('lickey' => $lickey64, 'domain' => $domain64, 'do' => 'install');
        $initurl = "https://www.mlmscript.net/~enverifykey/api.php";
        $response = getdocurl($initurl, $arrdata);
        $arrResponse = $response['data'];


        if ($arrResponse['isvalid'] != 1) {
            $_SESSION['errmsg'] = $arrResponse['errmsg'];
            $dostep = 3;
            dostepredir($dostep);
        }

        // save config file
        $cfgfile = "../common/config.php";
        if ($arrResponse['sqlstr'] && $fh = @fopen($cfgfile, 'w')) {
            $heretimezone = date_default_timezone_get();
            $sql_username_enc = base64_encode($db_username);
            $sql_password_enc = base64_encode($db_password);
            $installbasepath = dirname(dirname(__FILE__));

            $settings_sql = <<<INI_HTML
<?php

define('OK_LOADME', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// --- start ---

date_default_timezone_set('{$heretimezone}');

define('DB_HOST', '{$db_host}');
define('DB_USER', '{$sql_username_enc}');
define('DB_PASSWORD', '{$sql_password_enc}');
define('DB_NAME', '{$db_name}');
define('DB_TBLPREFIX', '{$db_tblprefix}');

define('INSTALL_PATH', '{$installbasepath}');
define('DECIMAL_POINT', 2);

define('DEFIMG_LOGO', '../assets/image/logo_defaultimage.png');
define('DEFIMG_PLAN', '../assets/image/plan_defaultimage.jpg');
define('DEFIMG_FILE', '../assets/image/file_defaultimage.jpg');
define('DEFIMG_SITE', '../assets/image/site_defaultimage.jpg');
define('DEFIMG_ADM', '../assets/image/adm_defaultimage.jpg');
define('DEFIMG_MBR', '../assets/image/mbr_defaultimage.jpg');

define('ADMFOLDER_NAME', 'admin');
define('MBRFOLDER_NAME', 'member');
define('UIDFOLDER_NAME', 'id');

//ini_set('log_errors', 'On');
//ini_set('error_log', '{$db_tblprefix}_error.log');

INI_HTML;

            fwrite($fh, $settings_sql);
            fclose($fh);

            // ---
            require($cfgfile);

            // save basic settings
            $dsn = "mysql:dbname=" . $db_name . ";host=" . $db_host . "";
            $pdo = "";
            try {
                $pdo = new PDO($dsn, $db_username, $db_password);
            } catch (PDOException $e) {
                $_SESSION['errmsg'] = "Connection failed: " . $e->getMessage();
                $dostep = 2;
                dostepredir($dostep);
            }

            // get sql and insert tables
            $sqlbasestr = base64_decode($arrResponse['sqlstr']);
            $sqlbase = json_decode($sqlbasestr, true);

            // execute sql
            $db = new Database($pdo);

            // loop through each line
            foreach ($sqlbase as $line) {
                // skip it if it's a comment
                $line = trim($line);
                $start_character = substr($line, 0, 2);
                if ($start_character == '--' || $start_character == '/*' || $start_character == '//' || $line == '') {
                    continue;
                }
                // add this line to the current segment
                $templine .= $line;
                // if it has a semicolon at the end, it's the end of the query
                if (substr($line, -1, 1) == ';') {
                    // perform the query
                    $templine = preg_replace("/\r|\n/", " ", $templine);
                    $templine = str_replace("#TBLPREFIX#", $db_tblprefix, $templine);
                    $db->getRecFrmQryStr($templine);
                    // reset temp variable to empty
                    $templine = '';
                }
            }

            $geturl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $geturl .= $_SERVER['SERVER_NAME'];
            $geturl .= $_SERVER['REQUEST_URI'];
            $site_url = dirname(dirname($geturl));

            $softversion = ($sversion != '') ? substr($sversion, 1) : $arrResponse['softversion'];

            $data = array(
                'site_name' => $site_name,
                'site_url' => $site_url,
                'dldir' => $installbasepath . '/downloads',
                'admin_user' => $admin_user,
                'admin_password' => getpasshash($admin_password),
                'softversion' => $softversion,
                'lickey' => base64_encode($lickey),
                'site_emailaddr' => $site_emailaddr,
                'installdate' => $arrResponse['installdate'],
                'installhash' => $arrResponse['installhash'],
                'licdate' => $arrResponse['boughtdate'],
            );

            $condition = ' AND cfgid = "1" ';
            $sql = $db->getRecFrmQry("SELECT * FROM " . $db_tblprefix . "_configs WHERE 1 " . $condition . "");
            if (count($sql) > 0) {
                $update = $db->update($db_tblprefix . '_configs', $data, array('cfgid' => '1'));
            } else {
                $insert = $db->insert($db_tblprefix . '_configs', $data);
            }
        } else {
            $_SESSION['errmsg'] = "Cannot write to config file, please try it again.!";
            $dostep = 3;
        }
    } elseif ($instep == 4) {
        // remove installer
        session_destroy();
        redirpageto('../admin');
        exit;
    }

    dostepredir($dostep);
}

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <div class="login-brand">
                    <img src="../assets/image/logo_defaultimage.png" alt="logo" width="100" class="shadow-light rounded-circle">
                    <div>UniMatrix Membership</div>
                </div>

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Installation</h4>
                        <div class='card-header-action'><span class='badge badge-info'><?php echo myvalidate($sversion); ?></span></div>
                    </div>

                    <div class="card-body">

                        <?php
                        if (defined('INSTALL_PATH') && $instep < 1) {
                            echo showalert('info', 'Congratulation!', 'The script has been installed.');
                        } else {
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="wizard-steps">
                                        <div class="wizard-step<?php echo myvalidate($markstep1); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Welcome
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep2); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-database"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Database
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep3); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Settings
                                            </div>
                                        </div>
                                        <div class="wizard-step<?php echo myvalidate($markstep4); ?>">
                                            <div class="wizard-step-icon">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                            <div class="wizard-step-label">
                                                Complete
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <?php
                                    if ($errmsg) {
                                        echo showalert('danger', 'Warning!', $errmsg);
                                    }
                                    ?>
                                    <form method="post" id='installer' class="wizard-content mt-2">
                                        <div class="wizard-pane">
                                            <?php
                                            if ($instep == 4) {
                                                ?>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4>Complete</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>The script has been successfully installed. The Admin CP Username is <strong><?php echo myvalidate($formval['admin_user']); ?></strong>. <a href="../admin/" target="_blank">Click here</a> to log in to the Admin CP and configure the script.</p><p>Please refer to the document for the options and features available in the script.</p>
                                                    </div>
                                                    <div class="card-footer bg-whitesmoke">
                                                        <p>For security reasons, remove the installation folder.</p>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-danger">
                                                            Remove Installation Sessions <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="5">
                                                        <input type="hidden" name="dosubmit" value="1">
                                                    </div>
                                                </div>
                                                <?php
                                            } elseif ($instep == 3) {
                                                $modalcontent = file_get_contents('terms.html');
                                                ?>

                                                <div class="form-group">
                                                    <label for="lickey">License Key</label>
                                                    <input type="text" name="lickey" id="lickey" class="form-control" value="<?php echo isset($formval['lickey']) ? $formval['lickey'] : ''; ?>" placeholder="Script license key or purchase code" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="site_name">Site Title</label>
                                                    <input type="text" name="site_name" id="site_name" class="form-control" value="<?php echo isset($formval['site_name']) ? $formval['site_name'] : ''; ?>" placeholder="Site name or title" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="site_emailaddr">Email Address</label>
                                                    <input type="email" name="site_emailaddr" id="site_emailaddr" class="form-control" value="<?php echo isset($formval['site_emailaddr']) ? $formval['site_emailaddr'] : ''; ?>" placeholder="Your email address" required>
                                                    <h6 class="text-muted text-small"><span class="badge badge-danger">Important!</span> <span class="badge badge-secondary">If you are forgetting your Admin CP login details, use this email address to reset your password.</span></h6>
                                                </div>

                                                <div class="form-group">
                                                    <label for="admin_user">Admin Username</label>
                                                    <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?php echo isset($formval['admin_user']) ? $formval['admin_user'] : ''; ?>" placeholder="Admin username" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="admin_password">Admin Password</label>
                                                    <input type="password" name="admin_password" id="admin_password" class="form-control" value="" placeholder="Admin password" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="admin_passwordx">Confirm Password</label>
                                                    <input type="password" name="admin_passwordx" id="admin_passwordx" class="form-control" value="" placeholder="Confirm admin password" required>
                                                </div>

                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" name="iagree" value="1" class="custom-control-input" id="iagree" required>
                                                        <label class="custom-control-label" for="iagree">I agree with the <a href="javascript:;" data-toggle="modal" data-target="#myModalterm">terms and conditions</a></label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                                            <i class="fa fa-fw fa-undo"></i> Reset
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                                            Next <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="4">
                                                        <input type="hidden" name="dosubmit" value="1">
                                                    </div>
                                                </div>

                                                <?php
                                            } elseif ($instep == 2) {
                                                ?>
                                                <div class="form-group">
                                                    <label for="db_host">DB Host</label>
                                                    <input type="text" name="db_host" id="db_host" class="form-control" value="<?php echo isset($formval['db_host']) ? $formval['db_host'] : 'localhost'; ?>" placeholder="Database host" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="db_name">DB Name</label>
                                                    <input type="text" name="db_name" id="db_name" class="form-control" value="<?php echo isset($formval['db_name']) ? $formval['db_name'] : ''; ?>" placeholder="Database name" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="db_tblprefix">Table Prefix</label>
                                                    <input type="text" name="db_tblprefix" id="db_tblprefix" class="form-control" value="<?php echo isset($formval['db_tblprefix']) ? $formval['db_tblprefix'] : 'netw'; ?>" placeholder="Table prefix" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="db_username">DB Username</label>
                                                    <input type="text" name="db_username" id="db_username" class="form-control" value="<?php echo isset($formval['db_username']) ? $formval['db_username'] : ''; ?>" placeholder="Database username" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="db_password">DB Password</label>
                                                    <input type="password" name="db_password" id="db_password" class="form-control" value="" placeholder="Database password" required>
                                                </div>

                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                                            <i class="fa fa-fw fa-undo"></i> Reset
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                                            Next <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="3">
                                                        <input type="hidden" name="dosubmit" value="1">
                                                    </div>
                                                </div>
                                                <?php
                                            } else {

                                                $dobtnsubmit = 1;
                                                $phpver = phpversion();
                                                if (7.2 <= $phpver) {
                                                    $phpversionstr = "<i class='fa fa-fw fa-check text-success'></i> PHP {$phpver}</li>";
                                                } else {
                                                    $phpversionstr = "<i class='fa fa-fw fa-times text-danger'></i> PHP {$phpver}</li>";
                                                    $dobtnsubmit = '';
                                                }

                                                $sqlver = find_SQL_Version();
                                                if ($sqlver >= 5) {
                                                    $sqlversionstr = "<i class='fa fa-fw fa-check text-success'></i> SQL {$sqlver}</li>";
                                                } else {
                                                    $sqlversionstr = "<i class='fa fa-fw fa-times text-danger'></i> SQL {$sqlver}</li>";
                                                }

                                                if (extension_loaded('ionCube Loader')) {
                                                    $ioncubestr = "<i class='fa fa-fw fa-check text-success'></i> IonCube loader</li>";
                                                } else {
                                                    $ioncubestr = "<i class='fa fa-fw fa-times text-warning'></i> IonCube loader</li>";
                                                }

                                                if (function_exists('curl_init')) {
                                                    $curlstr = "<i class='fa fa-fw fa-check text-success'></i> cUrl function</li>";
                                                } else {
                                                    $curlstr = "<i class='fa fa-fw fa-times text-danger'></i> cUrl function</li>";
                                                    $dobtnsubmit = '';
                                                }

                                                if (function_exists('fsockopen')) {
                                                    $fsockopenstr = "<i class='fa fa-fw fa-check text-success'></i> fSockOpen function</li>";
                                                } else {
                                                    $fsockopenstr = "<i class='fa fa-fw fa-times text-warning'></i> fSockOpen function</li>";
                                                }

                                                if (get_extension_funcs('json')) {
                                                    $jsonstr = "<i class='fa fa-fw fa-check text-success'></i> JSON extension</li>";
                                                } else {
                                                    $jsonstr = "<i class='fa fa-fw fa-times text-danger'></i> JSON extension</li>";
                                                    $dobtnsubmit = '';
                                                }

                                                if (get_extension_funcs('zlib')) {
                                                    $zlibstr = "<i class='fa fa-fw fa-check text-success'></i> zLib extension</li>";
                                                } else {
                                                    $zlibstr = "<i class='fa fa-fw fa-times text-danger'></i> zLib extension</li>";
                                                    $dobtnsubmit = '';
                                                }

                                                if (function_exists('gzencode')) {
                                                    $gzencodestr = "<i class='fa fa-fw fa-check text-success'></i> gzEncode function</li>";
                                                } else {
                                                    $gzencodestr = "<i class='fa fa-fw fa-times text-warning'></i> gzEncode function</li>";
                                                    $dobtnsubmit = '';
                                                }

                                                $btndisablestr = ($dobtnsubmit != 1) ? " disabled" : '';
                                                ?>

                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4>Introduction</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p>Thank you for purchasing our script. Your support and trust in us are much appreciated.</p>
                                                        <p>Please read the <strong>Documentation</strong> and follow the instructions before start the installation.</p>
                                                        <p>All The Best.<br />
                                                            MLMScript.net</p>
                                                    </div>
                                                    <div class="card-footer bg-whitesmoke">
                                                        <p>Server Requirements:</p>
                                                        <ul class="list-unstyled">
                                                            <li><?php echo myvalidate($phpversionstr); ?></li>
                                                            <li><?php echo myvalidate($sqlversionstr); ?></li>
                                                            <li><?php echo myvalidate($ioncubestr); ?></li>
                                                            <li><?php echo myvalidate($curlstr); ?></li>
                                                            <li><?php echo myvalidate($fsockopenstr); ?></li>
                                                            <li><?php echo myvalidate($jsonstr); ?></li>
                                                            <li><?php echo myvalidate($zlibstr); ?></li>
                                                            <li><?php echo myvalidate($gzencodestr); ?></li>
                                                        </ul>
                                                        <p>Click <strong>Start Installation</strong> button to continue.</p>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="text-right">
                                                        <button type="button" class="btn btn-info" onclick="window.open('https://www.mlmscript.net/helpdesk/', '_blank')">
                                                            Help <i class="fa fa-fw fa-question"></i>
                                                        </button>
                                                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary" <?php echo myvalidate($btndisablestr); ?>>
                                                            Start Installation <i class="fas fa-arrow-right"></i>
                                                        </button>
                                                        <input type="hidden" name="dostep" value="2">
                                                        <input type="hidden" name="dosubmit" value="<?php echo myvalidate($dobtnsubmit); ?>">
                                                    </div>
                                                </div>
                                                <?php
                                                // end
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</section>

<?php
$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <div>{$cfgrow['site_name']}</div>
                    Crafted with <i class="fa fa-fw fa-heart"></i> 2020 <div class="bullet"></div> <a href="https://www.codecanyon.net/user/amazego">AmazeGo</a>
                </div>

        <!-- Modal -->
        <div class="modal fade" id="myModalterm" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Terms and Conditions</h5>
                    </div>
                    <div class="modal-body">
                        <small class="text-muted">{$modalcontent}</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

</div>

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>

        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->

   </body>
</html>
INI_HTML;
echo myvalidate($page_content);
