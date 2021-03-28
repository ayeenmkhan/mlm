<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$fileterms = "kycterms.html";
// $fileterms = SURL . "/common/terms.html";

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
        // var_dump($fileterms);
        // var_dump($termscon);exit;
    if (file_put_contents($fileterms, $termskyc)) {
        $_SESSION['dotoaster'] = "toastr.success('KYC Terms & Condition content updated successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('KYC Terms & Condition content not updated <strong>Please try again!</strong>', 'Warning');";
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}

$termskyc = (file_exists($fileterms)) ? file_get_contents($fileterms) : '';
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-exclamation-circle"></i> <?php echo myvalidate($LANG['a_termskyc']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <form method="post" action="index.php" id="startform">
                    <input type="hidden" name="hal" value="termskyc">

                    <div class="card-header">
                        <h4>Page Content</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">Please write here your site terms and condition, privacy policy, refund policy, etc.</p>

                        <div class="form-group">
                            <textarea class="form-control rowsize-md" name="termskyc" id="summernotemaxi" required><?php echo isset($termskyc) ? $termskyc : ''; ?></textarea>
                        </div>

                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                            <i class="fa fa-fw fa-undo"></i> Reset
                        </button>
                        <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                        </button>
                        <input type="hidden" name="dosubmit" value="1">

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
