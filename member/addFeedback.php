<?php
include_once('../common/init.loader.php');
// require_once('export.php');
if (!defined('OK_LOADME')) {
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
    $_SESSION['redirto'] = '&video_id='.$video_id.'';
    if($video_id==''){
      $video_id= "30q7n48g4f";
    }
    if($course_id==''){
      $course_id= $_GET['bundle_id'];
    }
    $data = array(
        'course_id' => $course_id,
        'video_id' => $video_id,
        'module_id' => $module_id,
        'username' => $username,
        'feedback' => $feedback,
    );
    // var_dump($data);exit;

        $insert = $db->insert(DB_TBLPREFIX . '_feedback', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Feedback submitted successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Feedback not added <strong>Please try again!</strong>', 'Warning');";
        }
    
    header('location: ' . $redirto.'&video_id='.$video_id);
    exit;
}
