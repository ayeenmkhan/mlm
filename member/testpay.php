<?php
include_once('../common/init.loader.php');

$page_header = $LANG['g_registration'];
include('../common/pub.header.php');

?>
<style type="text/css">
    .razorpay-payment-button{
        display: none;
    }
</style>
    <form action="" method="POST">
    <!-- Note that the amount is in paise = 50 INR -->
    <script
        src="https://checkout.razorpay.com/v1/checkout.js"
        data-key="rzp_test_aV1hAUNXE73CKh"
        data-amount="100000"
        
        data-name="immortalsuccess.com"
        data-description="immortalsuccess Registeration with RazorPay"
        data-image="http://doliyastore.com/bundle-package/assets/imagextra/site_logo.jpg"
        data-prefill.name="<?php echo $_SESSION['firstname'].' '.$_SESSION['lastname'];?>"
        data-prefill.email="support@razorpay.com"
        data-prefill.contact=""
        data-theme.color="#F37254"
    ></script>
    <input type="hidden" value="Hidden Element" name="hidden">
    </form>
 

 <?php
$_SESSION['firstname'] = $_SESSION['lastname'] = $_SESSION['username'] = $_SESSION['email'] = '';
include('../common/pub.footer.php');
?>
    <script>
    $(window).on('load', function() {
        console.log("Page load function call");
     jQuery('.razorpay-payment-button').click();
    });
  </script>