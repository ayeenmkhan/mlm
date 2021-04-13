<?php
// require_once('export.php');

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=bundlelist');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}

//Main queries
// echo "SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $sqlshort . $pages->limit . "";
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_bundle_package");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_bundle_package");

?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-users"></i> Manage Bundle</h1>
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
                        <div>
                            <a href="javascript:;" data-href="addBundle.php?redir=bundlelist" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add Course" class="openPopup btn btn-dark"><i class="fa fa-fw fa-list"></i> Add Course</a>
                        </div>
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
                    <th scope="col" nowrap>Bundle Name</th>
                    <th scope="col" nowrap>Description</th>
                    <th scope="col" nowrap>Bundle Image</th>
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
                        ?>
                        <tr>

                            <td scope="row"><?php echo myvalidate($s); ?></td>
                            <td data-toggle="tooltip" title="<?php echo myvalidate($val['bundle_name']); ?>" nowrap><?php echo myvalidate($val['bundle_name']); ?></td> 
                             <td nowrap><?php echo myvalidate($val['description']); ?></td>     
                             <td data-toggle="tooltip" title="" nowrap><img src="<?php echo myvalidate($val['image_name']); ?>" width="100" height="100"></td>
                            <td align="center" nowrap>
                                <a href="javascript:;" data-href="addBundle.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['id']); ?>&redir=bundlelist" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="Bundle Name: <?php echo myvalidate($val['bundle_name']); ?>" data-popmsg="Are you sure want to delete this Bundle ?" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['bundle_name']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
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
