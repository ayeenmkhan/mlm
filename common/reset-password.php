<?php
include_once('init.loader.php');

$page_header = $LANG['g_resetpass'];
include('pub.header.php');

if ($_SESSION['pr_key'] == '') {
    $errmsg = showalert('secondary', '', '');
    echo "<div class='row'><div class='col-md-12 text-center'>{$errmsg}</div></div>";
} else {

    if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
        $password = filter_var($FORM['password'], FILTER_SANITIZE_STRING);
        $passwordconfirm = filter_var($FORM['passwordconfirm'], FILTER_SANITIZE_STRING);

        $passres = passmeter($password);
           $sesRow = getlog_sess($_SESSION['pr_key']);
            $dataun = get_optionvals($sesRow['sesdata'], 'un');
            $dataarr = explode('-', $dataun);
             $dataid = $dataarr[1];
        if ($password != $passwordconfirm) {
            $_SESSION['show_msg'] = showalert('danger', 'Password Mismatch', 'Both entered passwords must be the same. Please try it again!');
        } elseif ($passres == 1) {

            if ($sesRow['sesid'] > 0 && $dataarr[0] == 'resetpass') {

               
                $hashedpassword = getpasshash($password);

                if ($dataid > 0) {
                    $data = array(
                        'password' => $hashedpassword,
                    );
                    $update = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $dataid));
                } else {
                    $data = array(
                        'admin_password' => $hashedpassword,
                    );
                    $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
                }

                $db->delete(DB_TBLPREFIX . '_sessions', array('seskey' => $_SESSION['pr_key']));
                // $_SESSION['dotoaster'] = "toastr.success('New password have been applied. You can log in to your account using your new password. Thank you', 'Success');";
                $_SESSION['show_msg'] = showalert('primary', 'Congratulation', 'New password have been applied. You can log in to your back office using your new password. Thank you!');
            } else {
                $_SESSION['show_msg'] = showalert('danger', 'Session Expiry', 'Your request session has been expired, please make a password request again!');
            }
            $_SESSION['pr_key'] = '';
        } else {
            $_SESSION['show_msg'] = showalert('warning', 'Password Hint', $passres);
        }
        if($dataid > 0){
        redirpageto(SURL.'/member/login.php');
        exit;
    }else{
        redirpageto(SURL.'/admin/login.php');
        exit;
    }
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
                        <div class="card-header"><h4>Reset Password</h4></div>

                        <?php
                        if ($_SESSION['show_msg'] == '') {
                            ?>
                            <div class="card-body">
                                <p class="text-muted">Complete the form below to continue</p>
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="password">New Password</label>
                                        <input id="password" type="password" class="form-control pwstrength" name="password" tabindex="2" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="passwordconfirm">Confirm Password</label>
                                        <input id="passwordconfirm" type="password" class="form-control" name="passwordconfirm" tabindex="2" required>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                            Reset Password
                                        </button>
                                        <input type="hidden" name="dosubmit" value="1">
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
}
include('pub.footer.php');
