<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=historylist');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}

$condition = " AND txtoken NOT LIKE '%|WIDR:%'";

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
    $filterusrstr = getmbrinfo('', '', $filterid);
    $clearfilterusrstr = " filter for member ({$filterusrstr['username']})";
} else {
    $btnclorclear = 'btn-warning';
    $clearfilterusrstr = "";
}

//$condition = str_replace(array("'"), '', $condition);

$tblshort_arr = array("txdatetm", "txbatch", "txmemo", "txamount");
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

// echo "SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . $sqlshort;
$exportData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . $sqlshort );

// echo "<pre>";
// print_r($exportData);exit();

  $array = array();
// if($_GET['submit']=='export'){
// header("Location: export.php");
        $result= $data;
                 
           // $array = array();

            $i=0;
            $sr=1;
            foreach ($exportData as $fkey => $res) {
                    $temp_array=Array();
                    $user_id='';
                    if($res['txfromid']=='0'){
                        $user_id= $res['txtoid'];
                    }
                    if($res['txtoid']=='0'){
                        $user_id=$res['txfromid'];
                    }
                    $userInfo= getuserInfoByID($user_id);
                    $temp_array[]= $sr;
                    $temp_array[]= $userInfo[0]['firstname']." ".$userInfo[0]['lastname'];
                    $temp_array[]= $userInfo[0]['email'];
                    $temp_array[]= $userInfo[0]['phone'];
                    $temp_array[]= $userInfo[0]['altphone'];
                    $temp_array[]= $res['txamount'];
                    $temp_array[]= $res['txmemo'];
                // $j++;
                $array[$i]=$temp_array;
                // }
                
                $sr++;
                $i++;
            }
            // echo "<pre>";
// print_r($array);
// exit();

?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-cash-register"></i> <?php echo myvalidate($LANG['g_historylist']); ?></h1>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <i class="fa fa-fw fa-search"></i> <?php echo myvalidate($LANG['g_findhistory']); ?>
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
                            <a href="index.php?hal=historylist&dohal=clear" class="btn <?php echo myvalidate($btnclorclear); ?>"><i class="fa fa-fw fa-redo"></i> Clear<?php echo myvalidate($clearfilterusrstr); ?></a>
                            <button type="submit" name="submit" value="search" id="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> Search</button>
                            <button type="button" name="submit" onclick="exportToCsv()" value="export" id="btnExport" class="btn btn-secondary"><i class="fa fa-file-export"></i> Export</button>
                        </div>
                        <div class="d-block d-sm-none">
                            &nbsp;
                        </div>
                        <div>
                            <a href="javascript:;" data-href="dohistory.php?redir=historylist" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add History" class="openPopup btn btn-dark"><i class="fa fa-fw fa-user-plus"></i> Add History</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="hal" value="historylist">
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
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['txmemo']); ?>Description</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['txamount']); ?>Amount</th>
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
                        } elseif ($val['txstatus'] == 3) {
                            $bletmark = '<span class="bullet text-light"></span>';
                        } else {
                            if ($val['txfromid'] == 0) {
                                $bletmark = '<span class="bullet text-danger"></span>';
                            } elseif ($val['txtoid'] == 0) {
                                $bletmark = '<span class="bullet text-success"></span>';
                            } else {
                                $bletmark = '<span class="bullet text-muted"></span>';
                            }
                        }

                        $payfrom = getusernameid($val['txfromid'], 'username');
                        $payto = getusernameid($val['txtoid'], 'username');

                        if (strpos($val['txtoken'], '|NOTE:') !== false) {
                            $notestr = base64_decode(get_optionvals($val['txtoken'], 'NOTE'));
                            $txmemostr = "<span class='text-info'>{$notestr}</span>";
                        } elseif (strpos($val['txtoken'], '|WALT:IN|') !== false) {
                            $txmemostr = 'Wallet Credit';
                        } elseif (strpos($val['txtoken'], '|WALT:OUT|') !== false) {
                            $txmemostr = 'Wallet Debit';
                        } elseif (strpos($val['txtoken'], '|WIDR:') !== false) {
                            $txmemostr = 'Withdrawal';
                        } else {
                            $txmemostr = '';
                        }

                        $proofimg = get_optionvals($val['txtoken'], 'proofimg');
                        $proofimgstr = ($proofimg != '') ? '<a href="javascript:;" data-img="../assets/imagextra/' . $proofimg . '" data-link="getuser" data-id="' . $val['txfromid'] . '" data-poptitle="Proof of Payment: ' . $payfrom . '" class="openPopup text-info" data-toggle="tooltip" title="Proof of Payment: ' . $payfrom . '"><i class="fa fa-receipt fa-fw"></i></a>' : '';

                        $overview = "<label>Info</label><div>" . $val['adminfo'] . "</div>";
                        ?>
                        <tr>

                            <th scope="row"><?php echo myvalidate($s); ?></th>
                            <td data-toggle="tooltip" title="<?php echo myvalidate($val['txdatetm']); ?>"><?php echo formatdate($val['txdatetm']); ?></td>
                            <td><?php echo ($val['txbatch']) ? myvalidate($val['txbatch']) : '-'; ?></td>
                            <td><?php echo myvalidate($val['txmemo'] . ' ' . $proofimgstr); ?></td>
                            <td class="text-right"><?php echo myvalidate($val['txamount'] . $bletmark); ?></td>
                            <td align="center" nowrap>
                                <a href="javascript:;"
                                   class="btn btn-sm btn-secondary"
                                   data-html="true"
                                   data-toggle="popover"
                                   data-trigger="hover"
                                   data-placement="left" 
                                   title="<?php echo myvalidate($val['txid'] . '. ' . $val['txbatch']); ?>"
                                   data-content="<h6><?php echo myvalidate($val['txtmstamp']); ?></h6><div>From: <?php echo myvalidate($payfrom); ?></div><div>To: <?php echo myvalidate($payto); ?></div><div class='mt-2'><?php echo myvalidate($txmemostr); ?></div><div class='mt-2'><?php echo myvalidate($val['txadminfo']); ?></div>">
                                    <i class="far fa-fw fa-question-circle"></i>
                                </a>
                                <a href="javascript:;" data-href="dohistory.php?editId=<?php echo myvalidate($val['txid']); ?>&redir=historylist" data-poptitle="<i class='fa fa-fw fa-edit'></i> Update Transaction History #<?php echo myvalidate($val['txid']); ?>" class="btn btn-sm btn-success openPopup" data-toggle="tooltip" title="Update <?php echo myvalidate($val['txbatch']); ?>"><i class="fa fa-fw fa-edit"></i></a>
                                <a href="javascript:;" data-href="dohistory.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['txid']); ?>&redir=historylist" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="Transaction ID: <?php echo myvalidate($val['txid']) . '-' . myvalidate($val['txbatch']); ?>" data-popmsg="Are you sure want to delete this transaction history?" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['txbatch']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
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

<script type="text/javascript">
exportToCsv = function() {
var Results = [
  ["Sr#","Name","Email","Mobile Number","Alternative Number","Amount","Description"],
  
];
<?php 
    $jsonData = json_encode($array);
?>
var parseData= JSON.parse('<?php echo $jsonData;?>');
for (var x = 0; x < parseData.length; x++) {
    Results.push(parseData[x]);
}
// console.log("Static Array",Results);
  // var pausecontent = new Array();
  //   console.log("Parse Data ",parseData);


  var CsvString = "";
  Results.forEach(function(RowItem, RowIndex) {
    RowItem.forEach(function(ColItem, ColIndex) {
      CsvString += ColItem + ',';
    });
    CsvString += "\r\n";
  });
  CsvString = "data:application/csv," + encodeURIComponent(CsvString);
 var x = document.createElement("A");
 x.setAttribute("href", CsvString );
 x.setAttribute("download","transaction_history.csv");
 document.body.appendChild(x);
 x.click();
}
</script>