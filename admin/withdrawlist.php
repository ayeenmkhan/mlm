<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=withdrawlist');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}

$condition = " AND txtoken LIKE '%|WIDR:%'";

if (isset($FORM['txbatch']) and $FORM['txbatch'] != "") {
    $condition .= ' AND txbatch LIKE "%' . $FORM['txbatch'] . '%" ';
}
if (isset($FORM['txmemo']) and $FORM['txmemo'] != "") {
    $condition .= ' AND txmemo LIKE "%' . $FORM['txmemo'] . '%" ';
}
if (isset($FORM['txadminfo']) and $FORM['txadminfo'] != "") {
    $condition .= ' AND (txtoken LIKE "%' . $FORM['txtoken'] . '%" OR txadminfo LIKE "%' . $FORM['txadminfo'] . '%") ';
}

if ($_SESSION['filterid']) {
    $filterid = intval($_SESSION['filterid']);
    $condition .= " AND (txfromid = '$filterid' OR txtoid = '$filterid') ";
    $btnclorclear = 'btn-danger';
} else {
    $btnclorclear = 'btn-warning';
}

//$condition = str_replace(array("'"), '', $condition);

$tblshort_arr = array("txdatetm", "txbatch", "txamount");
$tblshort = dborder_arr($tblshort_arr, $FORM['_stbel'], $FORM['_stype']);
if ($FORM['_stbel'] != '' && (in_array($FORM['_stbel'], $tblshort_arr))) {
    $sqlshort = ($FORM['_stype'] == 'up') ? " ORDER BY {$FORM['_stbel']} DESC " : " ORDER BY {$FORM['_stbel']} ASC ";
} else {
    $sqlshort = " ORDER BY txid DESC ";
}

//Main queries
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . $sqlshort . $pages->limit . "");
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-hand-holding-usd"></i> <?php echo myvalidate($LANG['g_withdrawreq']); ?></h1>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <i class="fa fa-fw fa-search"></i> Find Withdrawal
                </h4>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_transactionid']); ?></label>
                            <input type="text" name="txbatch" id="txbatch" class="form-control" value="<?php echo isset($FORM['txbatch']) ? $FORM['txbatch'] : '' ?>" placeholder="Transaction ID">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_description']); ?></label>
                            <input type="text" name="txmemo" id="txmemo" class="form-control" value="<?php echo isset($FORM['txmemo']) ? $FORM['txmemo'] : '' ?>" placeholder="Transaction description">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_keyword']); ?></label>
                            <input type="txadminfo" name="txadminfo" id="txadminfo" class="form-control" value="<?php echo isset($FORM['txadminfo']) ? $FORM['txadminfo'] : '' ?>" placeholder="Enter transaction keyword">
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="float-md-right">
                            <a href="index.php?hal=withdrawlist&dohal=clear" class="btn <?php echo myvalidate($btnclorclear); ?>"><i class="fa fa-fw fa-redo"></i> Clear</a>
                            <button type="submit" name="submit" value="search" id="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> Search</button>
                        </div>
                        <div class="d-block d-sm-none">
                            &nbsp;
                        </div>
                        <div>
                            <a href="javascript:;" data-href="dowithdraw.php?redir=withdrawlist" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add Withdrawal" class="openPopup btn btn-dark"><i class="fa fa-fw fa-user-plus"></i> Add Withdrawal</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="withdrawlist">
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

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['txdatetm']); ?>Date</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['txbatch']); ?>Transaction ID</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['txamount']); ?>Amount</th>
                    <th scope="col" nowrap></th>
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
                        $hasdel = md5($val['txid'] . date("dH"));

                        if ($val['txstatus'] == 2) {
                            $bletmark = '<span class="bullet text-warning"></span>';
                        } elseif ($val['txstatus'] == 1) {
                            $bletmark = '<span class="bullet text-success"></span>';
                        } else {
                            $bletmark = '<span class="bullet text-muted"></span>';
                        }

                        switch ($val['txstatus']) {
                            case "1":
                                $badgestatus = "<span class='badge badge-secondary'><i class='fa fa-fw fa-spinner'></i> Processing</span>";
                                break;
                            case "3":
                                $badgestatus = "<span class='badge badge-success'><i class='fa fa-fw fa-check'></i> Completed</span>";
                                break;
                            case "2":
                                $badgestatus = "<span class='badge badge-info'><i class='fa fa-fw fa-user'></i> Verified</span>";
                                break;
                            default:
                                $badgestatus = "<span class='badge badge-light'><i class='fa fa-fw fa-question'></i> Pending</span>";
                        }

                        $mbrstr = getmbrinfo($val['txfromid']);
                        $txpaytype = base64_decode($mbrstr[$val['txpaytype']]);

                        $overview = "<label>Info</label><div>" . $val['adminfo'] . "</div>";
                        ?>
                        <tr>

                            <th scope="row"><?php echo myvalidate($s); ?></th>
                            <td data-toggle="tooltip" title="<?php echo myvalidate($val['txdatetm']); ?>"><?php echo formatdate($val['txdatetm']); ?></td>
                            <td><?php echo ($val['txbatch']) ? myvalidate($val['txbatch']) : '-'; ?></td>
                            <td class="text-right"><?php echo myvalidate($val['txamount'] . $bletmark); ?></td>
                            <td class="text-center"><?php echo myvalidate($badgestatus); ?></td>
                            <td align="center" nowrap>
                                <a href="javascript:;"
                                   class="btn btn-sm btn-secondary"
                                   data-html="true"
                                   data-toggle="popover"
                                   data-trigger="hover"
                                   data-placement="left" 
                                   title="<?php echo myvalidate($val['txtmstamp']); ?>"
                                   data-content="<div>Recipient: <?php echo myvalidate($mbrstr['username']); ?></div><div>Account: <?php echo myvalidate($txpaytype); ?></div><div class='mt-2'><?php echo myvalidate($val['txmemo']); ?></div><div class='mt-2'><?php echo myvalidate($val['txadminfo']); ?></div>">
                                    <i class="far fa-fw fa-question-circle"></i>
                                </a>
                                <a href="javascript:;" data-href="dowithdraw.php?editId=<?php echo myvalidate($val['txid']); ?>&redir=withdrawlist" data-poptitle="<i class='fa fa-fw fa-edit'></i> Update Transaction History" class="btn btn-sm btn-success openPopup" data-toggle="tooltip" title="Update <?php echo myvalidate($val['txbatch']); ?>"><i class="fa fa-fw fa-edit"></i></a>
                                <a href="javascript:;" data-href="dowithdraw.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['txid']); ?>&redir=withdrawlist" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="Transaction ID: <?php echo myvalidate($val['txid']) . ' ' . myvalidate($val['txbatch']); ?>" data-popmsg="Are you sure want to delete this transaction history?<br /><em>The amount <strong><?php echo myvalidate($val['txamount']); ?></strong> will reverse to the <strong><?php echo myvalidate($mbrstr['username']); ?></strong> account.</em>" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['txbatch']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
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
