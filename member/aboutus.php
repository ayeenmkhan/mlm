<?php
include_once('../common/init.loader.php');

$page_header = "Member CP Login";

include('../common/pub.header.php');
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
		filter: contrast(90%);
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

	.paddingTB60 {padding:60px 0px 60px 0px;}
	.gray-bg {background: #f4f6f9 !important;}
	.about-title h1 {color: #535353; font-size:45px;font-weight:600;}
	.about-title span {color: #f74551; font-size:45px;font-weight:700;}
	.about-title h3 {color: #535353; font-size:23px;margin-bottom:24px;}
	.about-title p {color: #414040;line-height: 1.8;margin: 0 0 15px; font-family: "Muli", "Poppins", Arial;}
	.about-paddingB {padding-bottom: 12px;}
	.about-img {padding-left: 57px;}

	/* Social Icons */
	.about-icons {margin:48px 0px 48px 0px ;}
	.about-icons i{margin-right: 10px;padding: 0px; font-size:35px;color:#323232;box-shadow: 0 0 3px rgba(0, 0, 0, .2);}
	.about-icons li {margin:0px;padding:0;display:inline-block;}
	#social-fb:hover {color: #3B5998;transition:all .001s;}
	#social-tw:hover {color: #4099FF;transition:all .001s;}
	#social-gp:hover {color: #d34836;transition:all .001s;}
	#social-em:hover {color: #f39c12;transition:all .001s;}
</style>

<header>
	<div class="logo-section">
		<button id="mobile-hamburger-menu" type="button"
			onclick="document.querySelector('nav').style.transform='translateX(0)';">
			<span class="hamburger-icon-small-line"></span>
			<span class="hamburger-icon-big-line"></span>
			<span class="hamburger-icon-small-line"></span>
		</button>
		<a class="navbar-brand" href="<?= SURL ?>/member/login.php" id="header-logo">
			<img id="header-logo-image" src="../assets/image/logo_new.png" alt="#" style="max-height:80px">
		</a>
	</div>
	<nav class="">
		<div class="mobile-icon">
			<span onclick="document.querySelector('nav').style.transform='translateX(-100%)';">&times;</span>
			<a class="navbar-brand" href="<?= SURL ?>/member/login.php">
				<img id="header-logo-image" src="../assets/image/logo_new.png" alt="">
			</a>
		</div>
		<a class="" href="">About Us</a>
		<a class="" href="contactus.php">Contact Us</a>
		<a class="btn btn-danger" href="register.php">Sign up</a>
	</nav>
</header>
<section class="section">
		<div class="about-section paddingTB60 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-md-7 col-sm-6">
						<div class="about-title clearfix">
							<h1>About <span>Fast-earning</span></h1>
							<h3>Grow with us</h3>
							<p class="about-paddingB">Welcome to Fast Earning, your number one source for earning a extra bit
								using your network. We're dedicated to providing you the best of
								Multi-level-marketing services, with a focus on growth.</p>
<p>We're working to provide you a platform to turn your network into a reliable income source. We hope you enjoy our service as much as we enjoy offering them 
to you.If you have any
							questions or comments, please don't hesitate to <a href="../common/contactus.php">Contact
								us.</p>

							</div>
						</div>
					</div>
					<div class="col-md-5 col-sm-6">
						<!-- <div class="about-img">
								<img src="https://devitems.com/preview/appmom/img/mobile/2.png" alt="">
							</div> -->
					</div>
				</div>
			</div>
		</div>
</section>
<?php
include('../common/pub.footer.php');
