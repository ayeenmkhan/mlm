<?php
include_once('../common/init.loader.php');

$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    $firstname = mystriptag($firstname);
    $lastname = mystriptag($lastname);
    $username = mystriptag($username, 'user');
    $email = mystriptag($email, 'email');

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    $isrecapv3 = 1;
    if ($cfgrow['isrecaptcha'] == 1 && isset($FORM['g-recaptcha-response'])) {
        $secret = $cfgrow['rc_securekey'];
        $response = $FORM['g-recaptcha-response'];
        $remoteIp = $_SERVER['REMOTE_ADDR'];
        // call curl to POST request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $response, 'remoteip' => $remoteIp), '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);

        // verify the response
        if ($arrResponse["success"] == '1' && $arrResponse["score"] >= 0.5) {
            // valid submission
        } else {
            $isrecapv3 = 0;
        }
    }

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $username . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");

    if ($isrecapv3 == 0) {
        $_SESSION['show_msg'] = showalert('warning', 'Error!', 'Recaptcha failed, please try it again!');
        $redirval = "?res=rcapt";
    } elseif (count($sql) > 0) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', 'Username already exist!');
        $redirval = "?res=exist";
    } else {

        if (!dumbtoken($dumbtoken)) {
            $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval);
            exit;
        }

        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $passwordconfirm = filter_var($passwordconfirm, FILTER_SANITIZE_STRING);

        $passres = passmeter($password);
        if ($password != $passwordconfirm) {
            $_SESSION['show_msg'] = showalert('danger', 'Password Mismatch', 'Both entered passwords must be the same. Please try it again!');
            $redirval = "?res=errpass";
        } elseif ($passres == 1) {
            $password = getpasshash($password);
            $data = array(
                'in_date' => $in_date,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'payment_id' => $payment_id,
                'email' => $email,
                'password' => $password,
            );
            if (validatemail($email, $firstname . " " . $lastname)) {
                // $insert = $db->insert(DB_TBLPREFIX . '_mbrs', $data);
                $newmbrid = $db->lastInsertId();

                $_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';

                if ($insert) {
                    require_once('../common/mailer.do.php');

                    // send welcome email
                    $cntaddarr['fullname'] = $firstname . ' ' . $lastname;
                    $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
                    $cntaddarr['rawpassword'] = $passwordconfirm;
                    delivermail('mbr_reg', $newmbrid, $cntaddarr);

                    // if ($cfgtoken['isautoregplan'] == 1) {
                    // register to membership
                    if($unref!=''){
                        $refrer_username=$unref;
                    }else{
                        $refrer_username=$username;
                    }
                    $mbrstr = getmbrinfo($newmbrid);
                    $refstr = getmbrinfo($refrer_username, 'username');
                    regmbrplans($mbrstr, $refstr['mpid'], $bpprow['ppid']);
                    // }

                    addlog_sess($username, 'member');
                    $redirval = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
                } else {
                    $redirval = "?res=errsql";
                }
            }
            // ******************


        } else {
            $_SESSION['show_msg'] = showalert('warning', 'Password Hint', $passres);
            $redirval = "?res=errpass";
        }
    }
    redirpageto($redirval);
    exit;
}

$modalcontent = file_get_contents(SURL . "/admin/terms.html");
$refbystr = ($sesref['username'] != '') ? "<div class='card-header-action'><span class='badge badge-info'>| {$sesref['username']}</span></div>" : '';

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
<style>
    header {
        display: flex;
        justify-content: space-between;
        padding: 2rem;
    }

    .hamburger-icon-small-line {
        height: 3px;
        background-color: #333;
        width: 20px;
    }

    .hamburger-icon-big-line {
        height: 3px;
        background-color: #333;
        width: 25px;
    }

    nav {
        display: flex;
        color: #333;
        align-items: center;
        padding: .5rem .8rem;
        transition: transform ease-in-out 500ms;
    }

    nav a {
        color: #565960;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
        padding: .5rem;
        margin-right: 1rem;
        font-weight: 700;
    }

    a:hover,
    a.btn:hover {
        color: #565960;
        filter: contrast(150%);
        text-decoration: none;
    }

    a.btn {
        font-family: Poppins__subset, Poppins, Verdana, sans-serif;
        text-transform: uppercase;
        border-radius: 25px;
        background-color: #F74551;
        color: #fff;
        border: none;
        padding: .5rem 2rem;
        font-size: 14px;
        line-height: 1.5;
        font-weight: 700;
        transition: all .5s ease-in-out;
        margin-left: 1rem;
        box-shadow: none;
    }


    @media (min-width:767px) {
        .logo-section button {
            display: none;
        }

        .mobile-icon {
            display: none;
        }
    }

    @media (max-width:767px) {
        .logo-section {
            display: flex;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }

        .logo-section button {
            height: 20px;
            width: 22px;
            display: flex;
            flex-direction: column;
            background: transparent;
            border: 0;
            justify-content: space-between;
            padding: 0;
            margin: 2rem;
            margin-left: 5px;
            z-index: 9;
            outline: none;
        }

        nav {
            position: fixed;
            top: -2px;
            left: 0;
            width: clamp(300px, 60%, 90%);
            height: 100vh;
            background-color: #35383e;
            z-index: 99;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 2rem;
            color: #eee;
            transform: translateX(-100%);
        }

        nav a {
            color: #eee;
            line-height: 2;
            margin-bottom: 1rem;
        }

        .mobile-icon {
            display: flex;
            justify-content: space-between;
            width: calc(100% + 3rem);
            margin-left: -1rem;
            margin-right: -1rem;
            align-items: center;
        }

        .mobile-icon span {
            font-size: 2rem;
            cursor: pointer;
        }

        .mobile-icon img {
            height: 30px;
        }
    }
</style>
<header>
    <div class="logo-section">
        <button id="mobile-hamburger-menu" type="button" onclick="document.querySelector('nav').style.transform='translateX(0)';">
            <span class="hamburger-icon-small-line"></span>
            <span class="hamburger-icon-big-line"></span>
            <span class="hamburger-icon-small-line"></span>
        </button>
        <a class="navbar-brand" href="#" id="header-logo">
            <img id="header-logo-image" src="../assets/image/logo_defaultimage.png" alt="" style="max-height:30px">
        </a>
    </div>
    <nav class="">
        <div class="mobile-icon">
            <span onclick="document.querySelector('nav').style.transform='translateX(-100%)';">&times;</span>
            <a class="navbar-brand" href="#">
                <img id="header-logo-image" src="../assets/image/logo_defaultimage.png for mobile menu.png" alt="">
            </a>
        </div>
        <a class="" href="#">About Us</a>
        <a class="" href="#">Contact Us</a>
        <a class="btn btn-danger" href="login.php">Log in</a>
    </nav>
</header>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <!-- <div class="login-brand">
                    <img src="< ?php echo myvalidate($site_logo); ?>" alt="logo" width="250" class="shadow-light" style="
    border-radius: 25%!important;">
                    <div>< ?php echo myvalidate($cfgrow['site_name']); ?></div>
                </div> -->

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4><?php echo myvalidate($LANG['g_register']); ?></h4>
                        <?php echo myvalidate($refbystr); ?>

                    </div>
                    <!--   <style type="text/css">
                            .svelte-ohbfj8{
                                position: relative;
                                left: 460px; }
                        </style> -->
                    <!--     <form><script src="https://checkout.razorpay.com/v1/payment-button.js" data-payment_button_id="pl_FoLOOCsQF5w5lR"> </script> </form> -->
                    <!--     <form><script src="https://checkout.razorpay.com/v1/payment-button.js" data-payment_button_id="pl_FyExvVYpQJeTou"> </script> </form> -->

                    <div class="card-body">
                        <!-- <small>Before Register into system kindly Pay registeration fee By click RazorPay Button and Copy Payment ID</small> -->
                        <?php
                        if ($cfgrow['join_status'] != 1) {
                            echo showalert('danger', 'Oops!', 'Due to the system maintenance, currently we do not accept new registration!');
                        } elseif ($cfgrow['validref'] == 1 && $sesref['id'] < 1) {
                            echo showalert('warning', 'Oops!', 'You are not allowed to register without a valid referrer!');
                        } else {
                            if ($cfgrow['isrecaptcha'] == 1) {
                                echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                            }
                        ?>
                            <form method="POST" action="registerUser.php" class="needs-validation" id="regmbrform">
                                <?php if (!$sesref['username']) : ?>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Did you have any refer code <span class="text-danger">*</span></label>
                                            <label for="yes"><input type="radio" name="refer" value="yes" onclick="document.querySelector('#referSection').style.display='flex'">&nbsp;&nbsp;Yes</label>
                                            <label for="no"><input type="radio" name="refer" value="no" onclick="document.querySelector('#referSection').style.display='none'">&nbsp;&nbsp;No</label>
                                                                              </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row" id="referSection" <?php if (!$sesref['username']) {
                                                                        echo "style='display:none;'";
                                                                    } ?>>
                                    <!-- <div class="form-group col-md-4">
                                 <label>Payment ID <span class="text-danger">*</span></label>
                                 <input type="text" name="payment_id" id="payment_id" class="form-control" value="" placeholder="Enter Payment ID" required>
                                </div>  -->
                                    <div class="form-group col-md-4">
                                        <label>Referrer Username <span class="text-danger">*</span></label>
                                        <input type="text" name="unref" id="unref" class="form-control" value="<?php echo $sesref['username']; ?>" placeholder="Enter referrer username" onBlur="checkRefreeMember('un2i', this.value, '')">
                                    </div>
                                    <div class="form-group col-md-4 ">
                                        <label>Referrer Name</label>
                                        <div id="resultRefree">?</div>
                                    </div>

                                       
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                            <label>Package Plan <span class="text-danger">*</span></label>
                                                <select class="form-control" name="package" id="package" onchange="selectPackageType(this.value)">
                                                   <option value="">Select</option>
                                                   <option value="1000.00">1,000</option>
                                                   <option value="5000.00">5,000</option>
                                                   <option value="10000.00">10,000</option>
                                                   <option value="20000.00">20,000</option>
                                                </select>
                                     <input type="hidden" name="package_type" id="package_type" value="">
                                        </div>
                                    <div class="form-group col-md-4">
                                        <label for="firstname"><?php echo myvalidate($LANG['g_firstname']); ?> <span class="text-danger">*</span></label>
                                        <input id="firstname" type="text" class="form-control" name="firstname" value="<?php echo myvalidate($_SESSION['firstname']); ?>" autofocus required>
                                        <div class="invalid-feedback">
                                            Please fill in your first name
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="lastname"><?php echo myvalidate($LANG['g_lastname']); ?><span class="text-danger">*</span></label>
                                        <input id="lastname" type="text" class="form-control" name="lastname" value="<?php echo myvalidate($_SESSION['lastname']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please fill in your last name
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input id="email" type="email" class="form-control" name="email" value="<?php echo myvalidate($_SESSION['email']); ?>" required>
                                        <div class="invalid-feedback">
                                            Please fill in your valid email address
                                        </div>
                                    </div>
                              
                                    <div class="form-group col-md-4">
                                        <label for="username">Username <span id="resultGetMbr" class="text-danger">*</span></label>
                                        <input id="username" type="text" class="form-control" name="username" value="<?php echo myvalidate($_SESSION['username']); ?>" onBlur="checkMember('unex', this.value, '')" required>
                                        <div class="invalid-feedback">
                                            Please choose your username
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="password" class="d-block">Password <span class="text-danger">*</span></label>
                                        <input id="password" type="password" class="form-control" data-indicator="pwindicator" name="password" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="passwordconfirm" class="d-block">Password Confirm</label>
                                        <input id="password2" type="password" class="form-control" name="passwordconfirm">
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="agree" value="1" class="custom-control-input" id="agree" required>
                                        <label class="custom-control-label" for="agree"><?php echo myvalidate($LANG['g_agreeterms']); ?><a href="javascript:;" data-toggle="modal" data-target="#myModalterm"><i class="fas fa-fw fa-question-circle"></i></a></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button data-sitekey="<?php echo myvalidate($cfgrow['rc_sitekey']); ?>" data-callback='onSubmit' class="btn btn-primary btn-lg btn-block g-recaptcha">
                                        Register
                                    </button>
                                    <input type="hidden" name="dosubmit" value="1">
                                    <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                                </div>

                            </form>

                        <?php
                            if ($cfgrow['isrecaptcha'] == 1) {
                                $isrecaptcha_content = <<<INI_HTML
                                    <script type="text/javascript">
                                        function onSubmit(token) {
                                            document.getElementById('regmbrform').submit();
                                        }
                                    </script>
INI_HTML;
                                echo myvalidate($isrecaptcha_content);
                            }
                        }
                        ?>
                        <div class="mt-4 text-muted text-center">
                            <?php echo myvalidate($LANG['g_haveacc']); ?> <a href="login.php">Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="myModalterm" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo myvalidate($LANG['g_termscon']); ?></h5>
            </div>
            <div class="modal-body">
                <div class="text-muted"><?php echo myvalidate($modalcontent); ?></div>
                <!--   <div class="text-muted"><p><b>Checklist for a Terms and Conditions</b></p><ul><li>Rules for use</li><li>Data protection</li><li>Payment terms</li><li>Refund policy</li><li>Termination</li><li>Liability disclaimers</li><li>Others</li></ul></div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function selectPackageType(value){

        // alert(value);
        if(value=='1000.00'){
            $('#package_type').val(1)
        }if(value=='5000.00'){
            $('#package_type').val(2)
        }if(value=='10000.00'){
            $('#package_type').val(3)
        }if(value=='20000.00'){
            $('#package_type').val(4)
        }

    }
</script>

<?php
$_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';
include('../common/pub.footer.php');
