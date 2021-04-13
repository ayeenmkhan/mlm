<?php
include_once('../common/init.loader.php');

$page_header = $LANG['g_forgotpass'];
include('../common/pub.header.php');

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $email = mystriptag($email, 'email');

    // Get member details
    $rowstr = getmbrinfo($email, 'email');
    $mbrid = $rowstr['id'];

    if ($mbrid > 0) {
        if (!dumbtoken($dumbtoken)) {
            $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval);
            exit;
        }

        $seskey = base64_encode(addlog_sess('resetpass-' . $mbrid));
        $cntaddarr['passwordreset_url'] = $cfgrow['site_url'] . "/common/?prkey=" . $seskey;
        require_once('../common/mailer.do.php');
        resetPasswordmail('mbr_resetpass', $mbrid, $cntaddarr);

        $_SESSION['show_msg'] = showalert('primary', 'Reset Request', 'A reset link has been sent to your email address. Please check it.');
    } else {
        $_SESSION['show_msg'] = showalert('danger', 'Invalid Email', 'Email address is not recognized!.<br />Please try it again.');
    }
    redirpageto('forgot-password.php?em=' . date("ymdHis"));
    exit;
}

$show_msg = $_SESSION['show_msg'];
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                <div class="login-brand">
                    <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100" class="shadow-light rounded-circle">
                    <div><?php echo myvalidate($cfgrow['site_name']); ?></div>
                </div>

                <div class="card card-primary">
                    <div class="card-header"><h4><?php echo myvalidate($LANG['g_forgotpass']); ?></h4></div>

                    <?php
                    if ($_SESSION['show_msg'] == '') {
                        ?>
                        <div class="card-body">
                            <p class="text-muted"><?php echo myvalidate($LANG['g_forgotpassresetlink']); ?></p>
                            <form method="POST">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                        <?php echo myvalidate($LANG['g_forgotpass']); ?>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-block" tabindex="4" onclick="location.href = 'login.php'">
                                        Cancel
                                    </button>
                                    <input type="hidden" name="dosubmit" value="1">
                                    <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                    $_SESSION['show_msg'] = '';
                    ?>

                </div>

                <?php echo myvalidate($show_msg); ?>
            </div>
        </div>
    </div>
</section>
<?php
include('../common/pub.footer.php');
