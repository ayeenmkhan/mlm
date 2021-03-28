<?php
include_once('../common/init.loader.php');

if (verifylog_sess('admin') == '') {
    die('o o p s !');
}

$_SESSION['redirto'] = redir_to($FORM['redir']);

if (isset($FORM['delId']) and $FORM['delId'] != "") {
    $hasdel = md5($FORM['delId'] . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_course_content', array('id' => $FORM['delId']));
        $_SESSION['dotoaster'] = "toastr.success('Course Content deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Course Content deleted failed!', 'Error');";
    }

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    header('location: ' . $redirto);
    exit;
}

$editId = intval($FORM['editId']);

$filetypenow = 'file';
$courses = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses WHERE 1");
$bundle = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_bundle_package");
$flavalto_menu = select_opt($avalpaymentopt_array);

if (isset($editId) and $editId != "") {
    $row = $db->getAllRecords(DB_TBLPREFIX . '_files', '*', ' AND flid = "' . $editId . '"');
    $rowstr = array();
    foreach ($row as $value) {
        $rowstr = array_merge($rowstr, $value);
    }

    $_SESSION['redirto'] = redir_to($FORM['redir']);
    $filetypenow = 'text';

    $flavalto_menu = select_opt($avalpaymentopt_array, $rowstr['flavalto']);

    $flstatusarr = array(0, 1);
    $flstatus_cek = radiobox_opt($flstatusarr, $rowstr['flstatus']);
}

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);
    $editId = intval($editId);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

 

    $data = array(
        'course_id' => $course_id,
        'title' => $title,
        'video_code' => base64_encode($video),
        'video_id' => $video_id,
    );

        $insert = $db->insert(DB_TBLPREFIX . '_course_content', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Content added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Content not added <strong>Please try again!</strong>', 'Warning');";
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

        <form method="post" action="doupfile.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Course Module</label>
                    <select name="course_id" id="course_id" class="form-control select1">
                        <option value="">Select</option>
                        <?php foreach($bundle as $val){?>
                        <optgroup label="<?php echo $val['bundle_name'];?>">
                       <?php $module=getCoursesModuleByID($val['id']);?>     
                    <?php foreach($module as $res){?>
                        <option value="<?php echo $res['id'];?>"><?php echo $res['course_name'];?></option>
                    <?php }?>
                        </optgroup>
                     <?php }?>
                    </select>'
                </div>
                <div class="form-group col-md-6">
                    <label>Title<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo isset($rowstr['title']) ? $rowstr['title'] : ''; ?>" placeholder="Title" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                   <label for="selectgroup-pills">Insert Video ID</label>
                    <input type="text" value="<?php echo isset($rowstr['video_id']) ? $rowstr['video_id'] : ''; ?>" name="video_id" id="video_id" class="form-control" required placeholder="Inset Video ID">
                </div> 
                <div class="form-group col-md-6">
                    <label for="selectgroup-pills">Embeded Video Code</label>
                    <textarea class="form-control" placeholder="Paste your video Embeded code here..." name="video" id="video"></textarea>
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