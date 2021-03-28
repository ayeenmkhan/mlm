<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$isgetstart_cek = checkbox_opt($cfgtoken['isgetstart']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $cfgtoken = put_optionvals($cfgrow['cfgtoken'], 'isgetstart', $isgetstart);

    $data = array(
        'getstart' => base64_encode($getstart),
        'cfgtoken' => $cfgtoken,
    );

    $condition = ' AND cfgid = "' . $didId . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_configs WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Getting started contents updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_configs', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Getting started contents added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Getting started contents not added <strong>Please try again!</strong>', 'Warning');";
        }
    }
    //header('location: index.php?hal=' . $hal);
    redirpageto('index.php?hal=' . $hal);
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-flag-checkered"></i> <?php echo myvalidate($LANG['a_getstart']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <form method="post" action="index.php" id="startform">
                    <input type="hidden" name="hal" value="getstart">

                    <div class="card-header">
                        <h4>Page Content</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">Describe here your program and how it works. You can also provide instructions and tips how to use your site and achieve success with your program.</p>

                        <div class="form-group">
                            <textarea class="form-control rowsize-md" name="getstart" id="summernotemaxi" required><?php echo isset($cfgrow['getstart']) ? base64_decode($cfgrow['getstart']) : ''; ?></textarea>
                        </div>

                    </div>

                    <div class="card-footer bg-whitesmoke text-md-right">
                        <div class="form-group float-left">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="isgetstart" value="1" class="custom-control-input" id="isgetstart"<?php echo myvalidate($isgetstart_cek); ?>>
                                <label class="custom-control-label" for="isgetstart">Enable</label>
                            </div>
                        </div>

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
