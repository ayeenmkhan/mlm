<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$_SESSION['redirto'] = redir_to($FORM['redir']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    if (!dumbtoken($dumbtoken)) {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
        $redirval = "?res=errtoken";
        redirpageto($redirval);
        exit;
    }

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    // if new username exist, keep using old username
    $condition = ' AND username LIKE "' . $FORM['username'] . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        // do nothing
        $_SESSION['dotoaster'] = "toastr.warning('Record not added <strong>Username exist!</strong>', 'Warning');";
    } else {
        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $password = ($password1 != '') ? getpasshash($password1) : $password;
        $data = array(
            'in_date' => $in_date,
            'firstname' => mystriptag($firstname),
            'lastname' => mystriptag($lastname),
            'ewallet' => mystriptag($package),
            'username' => mystriptag($username, 'user'),
            'email' => mystriptag($email, 'email'),
            'password' => $password,
        );
        $insert = $db->insert(DB_TBLPREFIX . '_mbrs', $data);
        $newmbrid = $db->lastInsertId();
        // $newmbrid = 7;

        if ($newmbrid > 0) {
            include_once('../common/mailer.do.php');

            // send welcome email
            $cntaddarr['fullname'] = $firstname . ' ' . $lastname;
            $cntaddarr['login_url'] = $cfgrow['site_url'] . "/" . MBRFOLDER_NAME;
            $cntaddarr['rawpassword'] = $password2;
            delivermail('mbr_reg', $newmbrid, $cntaddarr);

            // register to membership
            $mbrstr = getmbrinfo($newmbrid);
            $refstr = getmbrinfo($unref, 'username');
            // regmbrplans($mbrstr, $refstr['mpid'], $bpprow['ppid']);
            regmbrplans($mbrstr, $refstr['mpid'], $bpprow['ppid'],$package,$package_type);
        }

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Record added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Record not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    header('location: ' . $redirto);
    exit;
}
?>

<div class="row">
    <div class="col-md-12">

        <p class="text-primary">Fields with <span class="text-danger">*</span> are mandatory!</p>

        <form method="post" action="adduser.php" oninput='password1.setCustomValidity(password2.value != password1.value ? "Passwords do not match." : "")'>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Referrer Username</label>
                    <input type="text" name="unref" id="unref" class="form-control" value="" placeholder="Enter referrer username" onBlur="checkMember('un2i', this.value, '')">
                </div>
                <div class="form-group col-md-4">
                    <label>Referrer Name</label>
                    <div id="resultGetMbr">?</div>
                </div> 
                <div class="form-group col-md-4">
                    <label>Select Package Plan<span class="text-danger">*</span></label>
                <select class="form-control" name="package" id="package" onchange="selectPackageType(this.value)" required>
                               <option value="">Select</option>
                               <option value="1000.00">1,000</option>
                               <option value="5000.00">5,000</option>
                               <option value="10000.00">10,000</option>
                               <option value="20000.00">20,000</option>
                   </select>

                   <input type="hidden" name="package_type" id="package_type" value="">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo myvalidate($LANG['g_firstname']); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" id="firstname" class="form-control" value="" placeholder="Enter member first name" required>
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo myvalidate($LANG['g_lastname']); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" id="lastname" class="form-control" value="" placeholder="Enter member last name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-user"></i></div>
                        </div>
                        <input type="text" name="username" id="username" class="form-control" value="" placeholder="Enter member name" required>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label>Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fa fa-fw fa-envelope"></i></div>
                        </div>
                        <input type="email" name="email" id="email" class="form-control" value="" placeholder="Enter member email" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <input type="hidden" name="password" value="<?php echo isset($rowstr['password']) ? $rowstr['password'] : ''; ?>">
                <div class="form-group col-md-6">
                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password2" id="password2" value="" placeholder="Enter member password" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Password Confirm <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password1" id="password1" value="" placeholder="Confirm member password">
                </div>
            </div>

            <div class="text-md-right">
                <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> Submit
                </button>
                <input type="hidden" name="dosubmit" value="1">
                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
            </div>

        </form>

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