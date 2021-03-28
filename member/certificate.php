<?php
include_once('../common/init.loader.php');

$seskey = verifylog_sess('member');
if ($seskey == '') {
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


        $in_date = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $password = ($password1 != '') ? getpasshash($password1) : $password;
        $data = array(
            'user_id' => $username,
            'fname' => $fname,
            'course_id' => mystriptag($course_name),
            'text' => mystriptag($text),
            'created_date' =>$in_date ,
        );
        // var_dump($cfgtoken['isautoregplan']);
        // var_dump($data);exit;
        $insert = $db->insert(DB_TBLPREFIX . '_certificate_notif', $data);
        $newmbrid = $db->lastInsertId();

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Your Request is submitted successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Request not Send <strong>Please try again!</strong>', 'Warning');";
        }
    
    header('location: ' . $redirto);
    exit;
}
?>

<div class="row">
    <div class="col-md-12">
<?php $cname= getbundleNameByID($_SESSION['course_id']); ?>
<?php $username= getuserNameByID($_SESSION['username']); ?>
        <p class="text-primary">Send Request for Certificate <span class="text-danger">*</span> Our team will send certificate to Your registered email ID !</p>

        <form method="post" action="certificate.php">

            <div class="text-md-right">
                <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> Request For Certificate
                </button>
                <input type="hidden" name="dosubmit" value="1">
                <input type="hidden" name="course_name" value=" <?php echo $cname[0]['bundle_name'];?>">
                <input type="hidden" name="username" value=" <?php echo $_SESSION['username'];?>">
                <input type="hidden" name="fname" value="<?php echo $username[0]['firstname']." ".$username[0]['lastname'];?>">
                <input type="hidden" name="text" value="Requested for Course Completion certificate">
                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
            </div>

        </form>

    </div>

</div>
