<?php
include_once('../common/init.loader.php');

$page_header = "Member CP Login";

if ($FORM['ucpunlock'] != '') {
    $seskey = verifylog_sess('admin');
    if ($seskey != '') {
        addlog_sess($FORM['ucpunlock'], 'member');
        $redirval = "index.php?hal=dashboard";
        $waitme = 0;
    } else {
        $_SESSION['show_msg'] = showalert('danger', 'Error!', 'Invalid login session');
        $redirval = "?res=errucp";
        $waitme = mt_rand(9, 33);
        echo "<div class='text-center text-small'>Verifying, please wait... <i class='fas fa-cog fa-spin'></i></div>";
    }
    redirpageto($redirval, $waitme);
    exit;
}

if (verifylog_sess('member') != '') {
    redirpageto('index.php?hal=dashboard');
    exit;
}

if (isset($FORM['dosubmit']) && $FORM['dosubmit'] == '1') {
    extract($FORM);

    $username = mystriptag($username, 'user');

    // Get member details
    $rowstr = getmbrinfo($username, 'username');

    if ($rowstr['id'] > 0 && $rowstr['mbrstatus'] <= 3 && password_verify(md5($password), $rowstr['password'])) {
        if (!dumbtoken($dumbtoken)) {
            $_SESSION['show_msg'] = showalert('danger', 'Error!', $LANG['g_invalidtoken']);
            $redirval = "?res=errtoken";
            redirpageto($redirval, 1);
            exit;
        }

        addlog_sess($username, 'member', $remember);
        redirpageto('index.php?hal=dashboard');
        exit;
    } else {
        $_SESSION['show_msg'] = showalert('danger', 'Invalid Login', 'Username and Password are case sensitive. Please try it again.');
        redirpageto('login.php?err=' . $username);
        exit;
    }
}

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';

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
</style>
<header>
    <div class="logo-section">
        <button id="mobile-hamburger-menu" type="button" onclick="document.querySelector('nav').style.transform='translateX(0)';">
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
            <a class="navbar-brand" href="https://immortalsuccess.com/">
                <img id="header-logo-image" src="../assets/image/logo_defaultimage.png for mobile menu.png" alt="">
            </a>
        </div>
        <a class="" href="aboutus.php">About Us</a>
        <a class="" href="contactus.php">Contact Us</a>
        <a class="btn btn-danger" href="register.php">Sign up</a>
    </nav>
</header>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                <!-- <div class="login-brand">
                    <img src="< ?php echo myvalidate($site_logo); ?>" alt="logo" width="250" class="shadow-light" style="
    border-radius: 25%!important;">
                    <div>< ?php echo myvalidate($cfgrow['site_name']); ?></div>
                </div> -->

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4>Login</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate="">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                                <div class="invalid-feedback">
                                    Please fill in your username
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-block">
                                    <label for="password" class="control-label">Password</label>
                                    <div class="float-right">
                                        <a href="forgot-password.php" class="text-small">
                                            <?php echo myvalidate($LANG['g_forgotpass']); ?>?
                                        </a>
                                    </div>
                                </div>
                                <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                <div class="invalid-feedback">
                                    please fill in your password
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                    <label class="custom-control-label" for="remember-me"><?php echo myvalidate($LANG['g_rememberme']); ?></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                    Login
                                </button>
                                <input type="hidden" name="dosubmit" value="1">
                                <input type="hidden" name="dumbtoken" value="<?php echo myvalidate($_SESSION['dumbtoken']); ?>">
                            </div>
                        </form>
                        <div class="mt-2 text-muted text-center">
                            <?php echo myvalidate($LANG['g_donothaveacc']); ?> <a href="register.php"><?php echo myvalidate($LANG['g_createacc']); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include('../common/pub.footer.php');
