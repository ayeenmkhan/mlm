<!DOCTYPE html>
<html lang="en">
<head>
	<title>Contact V14</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="contact_res/css/util.css">
	<link rel="stylesheet" type="text/css" href="contact_res/css/main.css">
<!--===============================================================================================-->
</head>
<body>


	<div class="container-contact100">
		<div class="wrap-contact100">
			<form class="contact100-form validate-form" method="POST" action="contactus.php">
				<span class="contact100-form-title">
					Contact Us
				</span>
				
				<?php 
				    if(isset($_POST['submit'])){
				        $_SESSION['succmsg'] = "Enquiry Received";
				        
				    }
				?>
    
                <h5 style="text-align: center;color: #4fab4f;margin: 0 auto 1em;"><?= $_SESSION['succmsg'] ?></h5>
                <?php 
				    if(isset( $_SESSION['succmsg'])){
				        unset( $_SESSION['succmsg']);
				    }
				?>
				<label class="label-input100" for="first-name">Your Name *</label>
				<div class="wrap-input100 rs1 validate-input">
					<input id="first-name" class="input100" type="text" name="first-name" placeholder="First name">
					<span class="focus-input100"></span>
				</div>
				<div class="wrap-input100 rs1 validate-input">
					<input class="input100" type="text" name="last-name" placeholder="Last name">
					<span class="focus-input100"></span>
				</div>

				<label class="label-input100" for="email">Email Address *</label>
				<div class="wrap-input100 validate-input">
					<input id="email" class="input100" type="text" name="email" placeholder="Eg. example@email.com">
					<span class="focus-input100"></span>
				</div>

				<label class="label-input100" for="phone">Phone Number</label>
				<div class="wrap-input100">
					<input id="phone" class="input100" type="text" name="phone" placeholder="Eg. +1 800 000000">
					<span class="focus-input100"></span>
				</div>

				<label class="label-input100" for="message">Message *</label>
				<div class="wrap-input100 validate-input">
					<textarea id="message" class="input100" name="message" placeholder="Please enter your comments..."></textarea>
					<span class="focus-input100"></span>
				</div>

				<div class="container-contact100-form-btn">
					<button class="contact100-form-btn" type="submit" name="submit">
						<span>
							Submit
							<i class="zmdi zmdi-arrow-right m-l-8"></i>
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>



<!--===============================================================================================-->
	<script src="contact_res/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/vendor/bootstrap/js/popper.js"></script>
	<script src="contact_res/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/vendor/daterangepicker/moment.min.js"></script>
	<script src="contact_res/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="contact_res/js/main.js"></script>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>

</body>
</html>
