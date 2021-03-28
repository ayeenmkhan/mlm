<?php
include_once('../common/init.loader.php');

header("Refresh: 300; URL=index.php");
include('../common/pub.header.php');

if ($cfgrow['site_status'] == 1 && $_SERVER['HTTP_REFERER']) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$show_msg = $_SESSION['show_msg'];
$_SESSION['show_msg'] = '';
?>
<section class="section">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
                <div class="login-brand">
                    <img src="<?php echo myvalidate($site_logo); ?>" alt="logo" width="100" class="shadow-light rounded-circle">
                </div>

                <?php echo myvalidate($show_msg); ?>

                <div class="card card-danger">
                    <div class="card-body">
                        <?php
                        echo showalert('danger', 'Oops!', base64_decode($cfgrow['site_status_note']));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include('../common/pub.footer.php');
