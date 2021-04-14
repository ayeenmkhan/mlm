<?php

include_once('../common/init.loader.php');
// echo "string";exit();
$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

    // $redirto = $_SESSION['redirto'];
    // $_SESSION['redirto'] = '';
    $user_id = $_GET['user_id'];
    $package = $_GET['package'];
if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    // echo "<pre>";print_r($_POST);
   
    // if new username exist, keep using old username
            $data = array(
                'phone_valid' => 1,
            );
             $updatedata = array(
                'validation_code' => '',
            );
            $update = $db->update(DB_TBLPREFIX . '_mbrs', $data,array('validation_code' => $validation_code));
             $db->update(DB_TBLPREFIX . '_mbrs', $updatedata,array('id' => $user_id));
           // var_dump($update);exit();
            if($update){
                $_SESSION['show_msg'] = showalert('success', 'Success!', 'Phone Number Verified Successfuly!.');
                $redirval = SURL . "/member/razorpay.php?user_id=".$user_id."&package=".$package."";
            }else{
                $condition = "AND id='".$user_id."'";
                $db->deleteQry("DELETE FROM " . DB_TBLPREFIX . "_mbrs WHERE 1 " . $condition);

                $mbrplan_condition = "AND idmbr='".$user_id."'";
                $db->deleteQry("DELETE FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition);

                $_SESSION['show_msg'] = showalert('warning', 'Error!', 'Mobile Verification Failed Please try again.');
                $redirval = SURL . "/member/register.php?res=exist";;
            }
    // $redirval = $cfgrow['site_url'] . "/member/razorpay.php?user_id=".$newmbrid."";
                // var_dump($redirval);exit;
       
    // var_dump(expression)
    redirpageto($redirval);
    exit;
}

?>

<!-- Modal -->
<div class="modal fade pt-5" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
  <div class="modal-dialog modal-sm" role="document">
    <form action="validate_phone.php" method="post">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="loader"></div>
        <div clas="loader-txt">
          <p>Please enter <b>OTP</b> received on your phone. <br><small>to verify your phone number</small></p>
          <input type="text" name="validation_code" class="form-control" placeholder="Your OTP Goes Here">

        </div>
      </div>
      <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
      <input type="hidden" name="package" value="<?php echo $package;?>">
      <input type="hidden" name="dosubmit" value="1">
      <input type="submit" name="otp" value="Submit" class="btn btn-success">
    </div>
</form>
  </div>
</div>
<?php include('../common/pub.footer.php');?>
<script type="text/javascript">
    $(document).ready(function() {
  $(window).on("load", function(e) {

    e.preventDefault();
    $("#loadMe").modal({
      backdrop: "static", //remove ability to close modal with click
      keyboard: false, //remove option to close with keyboard
      show: true //Display loader!
    });
 
  });

});

</script>
