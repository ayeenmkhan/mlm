<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

    $sqlshort = " ORDER BY id ASC ";


//Main queries
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_course_content WHERE 1 ");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_course_content WHERE 1 " . $sqlshort . $pages->limit . "");
// var_dump($userData);
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-cloud-download-alt"></i> Digital Course Content</h1>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-header">
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div>
                            <a href="javascript:;" data-href="doupfile.php?redir=digifile" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add Module Content" class="openPopup btn btn-dark"><i class="fa fa-fw fa-upload"></i>Add Module Content</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="digifile">
    </form>

    <hr class="mt-4">
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
                    <th scope="col" nowrap>Module Name</th>
                    <th scope="col" nowrap>Title</th>
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
                                    <?php echo getCoursesNameByID($val['course_id']); ?>
                                </span>
                            </td>
                            <td>
                                <span data-toggle="tooltip" title="<?php echo myvalidate($strfname); ?>">
                                    <?php echo myvalidate($val['title']); ?>
                                </span>
                            </td>
                            <td><?php echo base64_decode($val['video_code']); ?></td>
                            <td align="center" nowrap>
                            
                                <a href="javascript:;" data-href="doupfile.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['id']); ?>&redir=digifile" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="File: <?php echo myvalidate($val['id'] . '. ' . $val['title']); ?>" data-popmsg="Are you sure want to delete this?" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['title']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
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

