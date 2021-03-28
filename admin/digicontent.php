<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$pgid = mystriptag($FORM['pgid']);
if (isset($pgid)) {
    $row = $db->getAllRecords(DB_TBLPREFIX . '_pages', '*', ' AND pgid = "' . $pgid . '"');
    $pgcntrow = array();
    foreach ($row as $value) {
        $pgcntrow = array_merge($pgcntrow, $value);
    }

    if (md5($pgid . '*') == $FORM['del']) {
        $db->delete(DB_TBLPREFIX . '_pages', array('pgid' => $pgid));
        $_SESSION['dotoaster'] = "toastr.success('Custom page deleted successfully!', 'Success');";
        redirpageto('index.php?hal=digicontent');
        exit;
    }

    $pgcntrow['pgsubtitle'] = base64_decode($pgcntrow['pgsubtitle']);
    $pgcntrow['pgcontent'] = base64_decode($pgcntrow['pgcontent']);

    $pgavalon = get_optionvals($pgcntrow['pgavalon']);
    $pgavalonmbpp0_cek = checkbox_opt($pgavalon['mbpp0']);
    $pgavalonmbpp1_cek = checkbox_opt($pgavalon['mbpp1']);

    $pgavalonmbrarr = array(0, 1);
    $pgavalonmbr_cek = radiobox_opt($pgavalonmbrarr, $pgavalon['mbr']);

    $pgstatusarr = array(0, 1);
    $pgstatus_cek = radiobox_opt($pgstatusarr, $pgcntrow['pgstatus']);
}

$msgListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 ");

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);
    // var_dump($_FILES['flpath']);
        // upload file
    if ($_FILES['flpath']) {
       
        // valid extensions
        $valid_extensions = array('png','pdf','jpg','jpeg');
        $fname = $_FILES['flpath']['name'];
        $flpath = SURL. '/assets/imagextra/';
        
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

     var_dump($flimage);
    $pgid = mystriptag($pgid);

    $pgidnew = ($pgidnew != '' && $pgidnew != $pgid) ? $pgidnew : $pgid;
    $pgidnew = preg_replace('/(\W)+/', '', $pgidnew);

    $pgavalon = put_optionvals($pgavalon, 'mbr', intval($pgavalonmbr));
    $pgavalon = put_optionvals($pgavalon, 'mbpp0', intval($pgavalonmbpp0));
    $pgavalon = put_optionvals($pgavalon, 'mbpp1', intval($pgavalonmbpp1));

    $data = array(
        'pgid' => $pgidnew,
        'pgmenu' => mystriptag($pgmenu),
        'pgtitle' => mystriptag($pgtitle),
        'pgsubtitle' => base64_encode(mystriptag($pgsubtitle)),
        'pgcontent' => base64_encode($pgcontent),
        'pgavalon' => $pgavalon,
        'pgppids' => $pgppids,
        'pgstatus' => intval($pgstatus),
        'pgorder' => intval($pgorder),
        'file_path' => $flimage,
    );

    // var_dump($data);exit;

    $condition = ' AND pgid LIKE "' . $pgid . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        $update = $db->update(DB_TBLPREFIX . '_pages', $data, array('pgid' => $pgid));
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Custom content updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_pages', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Custom content added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Custom content not added <strong>Please try again!</strong>', 'Warning');";
        }
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto("index.php?hal={$hal}&pgid={$pgid}");
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-window-restore"></i> <?php echo myvalidate($LANG['a_digicontent']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Title</h4>
                </div>
                <form method="get">
                    <div class="card-body">
                        <div class="form-group">
                            <select name="pgid" class="form-control select1">
                                <option value="">-</option>
                                <?php
                                if (count($msgListData) > 0) {
                                    foreach ($msgListData as $val) {
                                        $strsel = ($FORM['pgid'] == $val['pgid']) ? ' selected' : '';
                                        echo "<option value='{$val['pgid']}'{$strsel}>" . $val['pgmenu'] . "</option>";
                                    }
                                } else {
                                    echo "<option disabled>No Record(s) Found!</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" onclick="location.href = 'index.php?hal=digicontent&pgid=0'">
                                Create New
                            </button>
                            <button type="submit" value="Load" id="load" class="btn btn-info">
                                <i class="fa fa-fw fa-redo"></i> Load
                            </button>
                            <input type="hidden" name="hal" value="digicontent">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" id="msgtplform" enctype="multipart/form-data">
                    <input type="hidden" name="hal" value="digicontent">

                    <div class="card-header">
                        <h4>Contents</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted"><?php echo ($FORM['pgid'] != '') ? '' : '<i class="fa fa-fw fa-long-arrow-alt-left"></i> Please select the Custom Content from the drop down list on the left or click the <strong>Create New</strong> button to add a new custom content!'; ?></p>

                        <?php
                        if ($FORM['pgid'] != '') {
                            if (strlen($FORM['pgid']) < 4) {
                                ?>
                                <div class="form-group">
                                    <label for="pgidnew">Page ID</label>
                                    <input type="text" pattern=".{4,64}" name="pgidnew" id="pgidnew" class="form-control" value="<?php echo isset($pgcntrow['pgid']) ? $pgcntrow['pgid'] : ''; ?>" required>
                                    <div class="form-text text-muted"><em>Use only alphanumeric (a-z, A-Z, 0-9) and minimum 4 characters.</em></div>
                                </div>
                                <?php
                            }
                            ?>
                            <input type="hidden" name="pgid" value="<?php echo myvalidate($pgid); ?>">

                            <div class="form-group">
                                <label for="pgmenu">Bundle Name</label>
                                <input type="text" name="pgmenu" id="pgmenu" class="form-control" value="<?php echo isset($pgcntrow['pgmenu']) ? $pgcntrow['pgmenu'] : ''; ?>" placeholder="Menu Name" required>
                                <?php echo isset($pgcntrow['pgid']) ? "<div class='form-text text-muted'><em>Page ID: {$pgcntrow['pgid']}</em></div>" : ''; ?>
                            </div>

                            <div class="form-group">
                                <label for="pgtitle">Title</label>
                                <input type="text" name="pgtitle" id="pgtitle" class="form-control" value="<?php echo isset($pgcntrow['pgtitle']) ? $pgcntrow['pgtitle'] : ''; ?>" placeholder="Title" required>
                            </div>
                            <div class="form-group">
                                <label for="pgsubtitle">Subtitle</label>
                                <textarea class="form-control rowsize-sm" name="pgsubtitle" id="pgsubtitle" placeholder="Subtitle or brief description" required><?php echo isset($pgcntrow['pgsubtitle']) ? $pgcntrow['pgsubtitle'] : ''; ?></textarea>
                            </div>  
                            <div class="form-group">
                                <label for="pgsubtitle">File</label>
                                <input type="file" name="flpath" class="form-control">
                                <input type="hidden" name="old_flimage" value="<?php echo isset($pgcntrow['file_path']) ? $pgcntrow['file_path'] : ''; ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="summernote">Content</label>
                                <textarea class="form-control" name="pgcontent" id="summernotemaxi" placeholder="Page content" required><?php echo isset($pgcntrow['pgcontent']) ? $pgcntrow['pgcontent'] : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="summernote">Availability</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="pgavalonmbr" value="0" class="selectgroup-input"<?php echo myvalidate($pgavalonmbr_cek[0]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Public (regardless member status)</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="pgavalonmbr" value="1" class="selectgroup-input"<?php echo myvalidate($pgavalonmbr_cek[1]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Registered (active only)</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <div>
                                        <input type="checkbox" name="pgavalonmbpp1" value="1" class="custom-control-input" id="pgavalonmbpp1"<?php echo myvalidate($pgavalonmbpp1_cek); ?>><label class="custom-control-label" for="pgavalonmbpp1">Member active</label>
                                    </div>
                                    <div>
                                        <input type="checkbox" name="pgavalonmbpp0" value="1" class="custom-control-input" id="pgavalonmbpp0"<?php echo myvalidate($pgavalonmbpp0_cek); ?>><label class="custom-control-label" for="pgavalonmbpp0">Member inactive</label>
                                    </div>
                                </div>
                                <input type="hidden" name="pgavalon" value="<?php echo myvalidate($pgcntrow['pgavalon']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="selectgroup-pills">Page Status</label>
                                <div class="selectgroup selectgroup-pills">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="pgstatus" value="0" class="selectgroup-input"<?php echo myvalidate($pgstatus_cek[0]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-times-circle"></i> Disable</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="pgstatus" value="1" class="selectgroup-input"<?php echo myvalidate($pgstatus_cek[1]); ?>>
                                        <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-fw fa-check-circle"></i> Enable</span>
                                    </label>
                                </div>
                            </div>

                            <?php
                        }
                        ?>

                    </div>

                    <?php
                    if ($FORM['pgid'] != '') {
                        ?>
                        <div class="card-footer bg-whitesmoke text-md-right">
                            <?php
                            if ($FORM['pgid'] != '0') {
                                ?>
                                <div class="form-group float-left text-left">
                                    <a href="javascript:;" data-href="index.php?pgid=<?php echo myvalidate($val['pgid']); ?>&hal=digicontent&del=<?php echo md5($val['pgid'] . '*'); ?>" class="btn btn-danger bootboxconfirm" data-poptitle="Page ID: <?php echo myvalidate($val['pgid']); ?>" data-popmsg="Are you sure want to delete this custom page?" data-toggle="tooltip" title="Delete: <?php echo myvalidate($val['pgtitle']); ?>"><i class="far fa-fw fa-trash-alt"></i> Remove</a>
                                </div>
                                <?php
                            }
                            ?>
                            <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                <i class="fa fa-fw fa-undo"></i> Reset
                            </button>
                            <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                            </button>
                            <input type="hidden" name="dosubmit" value="1">
                        </div>
                        <?php
                    }
                    ?>

                </form>

            </div>
        </div>
    </div>
</div>
