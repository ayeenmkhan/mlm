<?php
include_once('../common/init.loader.php');
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

    $sqlshort = " ORDER BY id DESC ";
if (isset($FORM['delId']) and $FORM['delId'] != "") {
    $hasdel = md5($FORM['delId'] . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_feedback', array('id' => $FORM['delId']));
        $_SESSION['dotoaster'] = "toastr.success('Course Feedback deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Course Feedback deleted failed!', 'Error');";
    }

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';

    header('location: ' .SURL.'/admin/index.php?hal=feedback');
    exit;
}

//Main queries
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_feedback WHERE 1 ");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_feedback WHERE 1 " . $sqlshort . $pages->limit . "");
// var_dump($userData);
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-cloud-download-alt"></i> Digital Course Feedback</h1>
</div>

<div class="section-body">

    <div class="clearfix"></div>

    <hr>
    <div class="clearfix"></div>

    <div class="row marginTop">
        <div class="col-sm-12 paddingLeft pagerfwt">
            <?php if ($pages->items_total > 0) { ?>
                <div class="row">
                    <div class="col-md-7">
                        <?php echo myvalidate($pages->display_pages()); ?>
                    </div>
                    <div class="col-md-5 text-right">
                        <span class="d-none d-md-block">
                            <?php echo myvalidate($pages->display_items_per_page()); ?>
                            <?php echo myvalidate($pages->display_jump_menu()); ?>
                            <?php echo myvalidate($pages->items_total()); ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" nowrap>Username</th>
                    <th scope="col" nowrap>Course Name</th>
                    <th scope="col" nowrap>Modoule Name</th>
                    <th scope="col" nowrap>Comment</th>
                    <th scope="col" nowrap>Video</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($userData) > 0) {
                    $pgnow = ($FORM['page'] > 1) ? $FORM['page'] - 1 : 0;
                    $s = ($FORM['ipp'] > 0) ? $pgnow * $FORM['ipp'] : $pgnow * $cfgrow['maxpage'];
                    foreach ($userData as $val) {
                        $s++;
                        $hasdel = md5($val['id'] . date("dH"));

                        $strfname = basename($val['flpath']);
                        $overview = "<label>Info</label><div>" . $val['adminfo'] . "</div>";
                        $flimage = ($val['flimage']) ? $val['flimage'] : DEFIMG_FILE;
                        ?>
                        <tr>

                            <th scope="row"><?php echo myvalidate($s); ?></th>
                            <td>
                                <span data-toggle="tooltip" title="">
                                    <?php echo myvalidate($val['username']); ?>
                                </span>
                            </td>
                            <td>
                                <?php $course= getbundleNameByID($val['course_id']); ?>
                                <span data-toggle="tooltip">
                                    <?php echo $course[0]['bundle_name']; ?>
                                </span>
                            </td>

                            <td><?php echo getCoursesNameByID($val['module_id']); ?></td>
                            <td><?php echo $val['feedback']; ?></td>
                            <td><?php echo base64_decode(getCourseVideoByID($val['video_id'])); ?></td>
                            <td align="center" nowrap>
                            
                                <a href="javascript:;" data-href="feedback.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['id']); ?>&redir=feedback" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="User Feedback" data-popmsg="Are you sure want to delete this?" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['title']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
                            </td>

                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center mt-4 text-muted">
                                <div>
                                    <i class="fa fa-3x fa-question-circle"></i>
                                </div>
                                <div>No Record Found</div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="clearfix"></div>

    <div class="row marginTop">
        <div class="col-sm-12 paddingLeft pagerfwt">
            <?php if ($pages->items_total > 0) { ?>
                <div class="row">
                    <div class="col-md-7">
                        <?php echo myvalidate($pages->display_pages()); ?>
                    </div>
                    <div class="col-md-5 text-right">
                        <span class="d-none d-md-block">
                            <?php echo myvalidate($pages->display_items_per_page()); ?>
                            <?php echo myvalidate($pages->display_jump_menu()); ?>
                            <?php echo myvalidate($pages->items_total()); ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

</div>

