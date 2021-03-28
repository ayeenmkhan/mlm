<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$_SESSION['redirto'] = redir_to($FORM['redir']);

if (isset($FORM['delId']) and $FORM['delId'] != "") {
    $hasdel = md5($FORM['delId'] . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_bundle_package', array('id' => $FORM['delId']));
        $db->delete(DB_TBLPREFIX . '_courses', array('bundle_id' => $FORM['delId']));
        $_SESSION['dotoaster'] = "toastr.success('Bundle deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Bundle deleted failed!', 'Error');";
    }

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    header('location: ' . $redirto);
    exit;
}

$filetypenow = 'file';
if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    $editId = intval($editId);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';
    // upload file
    if ($_FILES['flpath'] && $_FILES["flpath"]["size"] < 1100000) {
       
        // valid extensions
        $valid_extensions = array('png','pdf','jpg','jpeg');
        $fname = $_FILES['flpath']['name'];
        $flpath = SURL. '/assets/imagextra/';
         // var_dump($fname);
        // get uploaded file's extension
        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        // check's valid format
        // if (in_array($ext, $valid_extensions)) {
        //     $flpath = $cfgrow['dldir'] . '/'.$fname;
        //     move_uploaded_file($_FILES['flpath']['tmp_name'], $flpath);
        // }
    }

    $flupdate = date('Y-m-d', time() + (3600 * $cfgrow['time_offset']));
    $flimage = imageupload('flimage_' . md5($_FILES['flpath']['name']),$_FILES['flpath'],$old_flimage);

    $data = array(
        'image_name' => $flimage,
        'bundle_name' => $bundle_name,
        'description' => $description,
    );
    // var_dump($data);exit;

        $insert = $db->insert(DB_TBLPREFIX . '_bundle_package', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Bundle added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Bundle not added <strong>Please try again!</strong>', 'Warning');";
        }
    
    header('location: ' . $redirto);
    exit;
}
?>

<?php
if (defined('ISDEMOMODE')) {
    ?>
    <div class="row">
        <div class="col-md-12">
            <p class="text-danger">Sorry, this feature is disabled in demo mode!</p>
        </div>
    </div>
    <?php
    die();
}
?>
<div class="row">
    <div class="col-md-12">

        <p class="text-primary">Fields with <span class="text-danger">*</span> are mandatory!</p>

        <form method="post" action="addBundle.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Course Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="bundle_name" id="bundle_name" class="form-control" value="<?php echo isset($rowstr['bundle_name']) ? $rowstr['bundle_name'] : ''; ?>" placeholder="File name" required>
                    </div>
                 
                </div>
                  <div class="form-group col-md-6">
                    <label>Image<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="<?php echo myvalidate($filetypenow); ?>" name="flpath" id="flpath" class="form-control" value="<?php echo isset($rowstr['flpath']) ? $rowstr['flpath'] : ''; ?>" placeholder="File location" required>
                    </div>
                    <div class="form-text text-muted">The file must have a maximum size of 1Mb</div>
                </div>  
                <div class="form-group col-md-6">
                    <label>Description<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <textarea class="form-control" name="description" placeholder="Description..." required><?php echo isset($rowstr['description']) ? $rowstr['description'] : ''; ?></textarea>
                    </div>
                </div>

           
            </div>


            <div class="text-md-right">
                <a href="javascript:;" class="btn btn-secondary" data-dismiss="modal"><i class="far fa-fw fa-times-circle"></i> Cancel</a>
                <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                    <i class="fa fa-fw fa-plus-circle"></i> Submit
                </button>
                <input type="hidden" name="editId" value="<?php echo myvalidate($editId); ?>">
                <input type="hidden" name="dosubmit" value="1">
            </div>

        </form>

    </div>

</div>