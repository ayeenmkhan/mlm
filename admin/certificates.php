<?php
include_once('../common/init.loader.php');

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=courses');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}

$delId = intval($FORM['delId']);

if (isset($delId) and $delId != "") {

    $hasdel = md5($delId . date("dH"));
    if ($FORM['hash'] == $hasdel) {
        $db->delete(DB_TBLPREFIX . '_certificate_notif', array('id' => $delId));
        $_SESSION['dotoaster'] = "toastr.success('Record deleted successfully!', 'Success');";
    } else {
        $_SESSION['dotoaster'] = "toastr.error('Record deleted failed!', 'Error');";
    }

    $redirto = redir_to($FORM['redir']);
    header('location: ' . $redirto);
    exit;
}


//Main queries
// echo "SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $sqlshort . $pages->limit . "";
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_certificate_notif");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_certificate_notif");

?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-users"></i> Manage Course Certificate Requests</h1>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="d-block d-sm-none">
                            &nbsp;
                        </div>
                       <!--  <div>
                            <a href="javascript:;" data-href="addCourse.php?redir=courses" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add Module" class="openPopup btn btn-dark"><i class="fa fa-fw fa-book"></i> Add New Module</a>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="userlist">
    </form>

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

    <div class="table-responsive" id="dvData">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" nowrap>Full Name</th>
                    <th scope="col" nowrap>Usename</th>
                    <th scope="col" nowrap>Course Name</th>
                    <th scope="col" >Text</th>
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

                        $stremail = (strlen($val['email']) > 21) ? substr($val['email'], 0, 18) . '...' : $val['email'];

                        $overview = "<label>Info</label><div>" . $val['adminfo'] . "</div>";
                        $mbrimgval = ($val['mbr_image']) ? $val['mbr_image'] : $cfgrow['mbr_defaultimage'];
                        $mbrimgvalstr = "<img alt='?' src='{$mbrimgval}'class='img-fluid float-left mr-3 rounded-circle img-thumbnail' width='96'>";
                        
                        // $cname=getbundleNameByID($val['course_id']);
                        ?>
                        <tr>

                            <td scope="row"><?php echo myvalidate($s); ?></td>
                            <td data-toggle="tooltip" title="" nowrap><?php echo $val['fname']; ?></td> 
                            <td data-toggle="tooltip" title="" nowrap><?php echo $val['user_id']; ?></td> 
                             <td data-toggle="tooltip" title="" nowrap><?php echo $val['course_id']; ?></td> '
                            <td><?php echo $val['text']; ?></td>
                            <td align="center" nowrap>
                                <!-- <a href="javascript:;" data-href="edituser.php?editId=<?php echo myvalidate($val['id']); ?>&redir=userlist" data-poptitle="<i class='fa fa-fw fa-edit'></i> Update Member #<?php echo myvalidate($val['id']); ?>" class="btn btn-sm btn-success openPopup" data-toggle="tooltip" title="Update <?php echo myvalidate($val['username']); ?>"><i class="fa fa-fw fa-edit"></i></a> -->
                               
                                <a href="javascript:;" data-href="certificates.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['id']); ?>&redir=certificates" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="" data-popmsg="Are you sure want to delete this Request ?" data-toggle="tooltip" ><i class="far fa-fw fa-trash-alt"></i></a>
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
