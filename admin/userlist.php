<?php
// require_once('export.php');

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=userlist');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}

$condition = '';

if (isset($FORM['name']) and $FORM['name'] != "") {
    $condition .= ' AND (firstname LIKE "%' . $FORM['name'] . '%" OR lastname LIKE "%' . $FORM['name'] . '%") ';
}
if (isset($FORM['username']) and $FORM['username'] != "") {
    $condition .= ' AND username LIKE "%' . $FORM['username'] . '%" ';
}
if (isset($FORM['email']) and $FORM['email'] != "") {
    $condition .= ' AND email LIKE "%' . $FORM['email'] . '%" ';
}
if (isset($FORM['status']) and $FORM['status'] != "") {
    $condition .= ' AND mpstatus LIKE "%' . $FORM['status'] . '%" ';
}
if (isset($FORM['kyc']) and $FORM['kyc'] != "") {
    if($FORM['kyc']==0){
    $condition .= ' OR email="" OR phone="" OR adhar_card="" OR pan_card="" ';
}else{
    $condition .= ' AND email!="" AND phone!="" AND adhar_card!="" AND pan_card!="" ';
}
}

if ($_SESSION['filterid']) {
    $filterid = intval($_SESSION['filterid']);
    $condition .= " AND sprlist LIKE '%:$filterid|%' ";
    $btnclorclear = 'btn-danger';
    $filterusrstr = getmbrinfo('', '', $filterid);
    $clearfilterusrstr = " filter for member ({$filterusrstr['username']})";
} else {
    $btnclorclear = 'btn-warning';
    $clearfilterusrstr = "";
}

//$condition = str_replace(array("'"), '', $condition);

$tblshort_arr = array("in_date", "username", "email");
$tblshort = dborder_arr($tblshort_arr, $FORM['_stbel'], $FORM['_stype']);
if ($FORM['_stbel'] != '' && (in_array($FORM['_stbel'], $tblshort_arr))) {
    $sqlshort = ($FORM['_stype'] == 'up') ? " ORDER BY {$FORM['_stbel']} DESC " : " ORDER BY {$FORM['_stbel']} ASC ";
} else {
    $sqlshort = " ORDER BY id DESC ";
}

//Main queries
// echo "SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $sqlshort . $pages->limit . "";
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $sqlshort . $pages->limit . "");

// echo "<pre>";
//  print_r($userData);exit;
  $array = array();
// if($_GET['submit']=='export'){
// header("Location: export.php");
        $result= $data;
                 
           // $array = array();

            $i=0;
            $sr=1;
            foreach ($userData as $fkey => $res) {
                    $temp_array=Array();
                    $status='';
                    if($res['mpstatus']=='0'){
                        $status="InActive";
                    }
                    else if ($res['mpstatus']=='1') {
                        $status="Active";
                    } 
                    else if ($res['mpstatus']=='2') {
                        $status="Expired";
                    } 
                    else if ($res['mpstatus']=='3') {
                        $status="Pending";
                    } else if ($res['mpstatus']=='4') {
                        $status="Unregistered";
                    }
                    $temp_array[]= $sr;
                    $temp_array[]= $res['firstname']." ".$res['lastname'];
                    $temp_array[]= $res['username'];
                    $temp_array[]= $res['email'];
                    $temp_array[]= $res['phone'];
                    $temp_array[]= $res['adhar_card'];
                    $temp_array[]= $res['pan_card'];
                    $temp_array[]= $status;
                    $temp_array[]= $res['level_bage'];
                // $j++;
                $array[$i]=$temp_array;
                // }
                
                $sr++;
                $i++;
            }
    // $exportData= json_encode($array);
 //            echo "<pre>";
 // print_r($array);exit;
// export_report($userData);

// }

?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-users"></i> Manage Member</h1>
</div>

<div class="section-body">

    <form method="get">
        <div class="card card-primary">
            <div class="card-header">
                <h4>
                    <i class="fa fa-fw fa-search"></i> Find Member
                </h4>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label><?php echo myvalidate($LANG['g_firstname']); ?></label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($FORM['name']) ? $FORM['name'] : '' ?>" placeholder="Enter member name">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($FORM['username']) ? $FORM['username'] : '' ?>" placeholder="Enter member username">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="useremail" class="form-control" value="<?php echo isset($FORM['email']) ? $FORM['email'] : '' ?>" placeholder="Enter member email">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Status</label>
                           <select class="form-control" name="status" id="status">
                               <option value="">Select</option>
                               <option value="1"<?php if(isset($_GET['status']) and $_GET['status']=='1'){echo "selected";}?>>Active</option>
                               <option value="0"<?php if(isset($_GET['status']) and $_GET['status']=='0'){echo "selected";}?>>Registerd Only</option>
                               <option value="2"<?php if(isset($_GET['status']) and $_GET['status']=='2'){echo "selected";}?>>Expired</option>
                               <option value="3"<?php if(isset($_GET['status']) and $_GET['status']=='3'){echo "selected";}?>>Pending</option>
                               <option value="4"<?php if(isset($_GET['status']) and$_GET['status']=='4'){echo "selected";}?>>Unregistered</option>
                           </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Kyc</label>
                           <select class="form-control" name="kyc" id="kyc">
                               <option value="">Select</option>
                               <option value="1"<?php if ($_GET['kyc']=='1'){echo "selected";}?>>Complete</option>
                               <option value="0"<?php if($_GET['kyc']=='0'){echo "selected";}?>>Incomplete</option>
                           </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-whitesmoke">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="float-md-right">
                            <a href="index.php?hal=userlist&dohal=clear" class="btn <?php echo myvalidate($btnclorclear); ?>"><i class="fa fa-fw fa-redo"></i> Clear<?php echo myvalidate($clearfilterusrstr); ?></a>
                            <button type="submit" name="submit" value="search" id="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> Search</button> 
                            <button type="button" name="submit" onclick="exportToCsv()" value="export" id="btnExport" class="btn btn-secondary"><i class="fa fa-file-export"></i> Export</button>
                        </div>
                        <div class="d-block d-sm-none">
                            &nbsp;
                        </div>
                        <div>
                            <a href="javascript:;" data-href="adduser.php?redir=userlist" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Add Member" class="openPopup btn btn-dark"><i class="fa fa-fw fa-user-plus"></i> Add Member</a>
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
                    <th scope="col" nowrap>Level Bages</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['in_date']); ?>Date</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['username']); ?>Username</th>
                    <th scope="col" nowrap><?php echo myvalidate($tblshort['email']); ?>Email</th>
                    <th scope="col" class="text-center">Payment ID</th>
                    <th scope="col" class="text-center">KYC</th>
                    <th scope="col" class="text-center">Status</th>
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
                            <td scope="row"><?php if($val['ewallet']>='1000.00' && $val['ewallet']<'5000.00'){?><span class="badge badge-secondary 
font-weight-bold">Level One</span><?php }else if($val['ewallet']>='5000.00' && $val['ewallet']<'10000.00'){?> <span class="badge badge-warning 
font-weight-bold">Level Two</span> <?php 
}else if($val['ewallet']>='10000.00' && $val['ewallet']<'20000.00'){?> <span class="badge badge-info font-weight-bold">Level Three</span> <?php }
                                else if($val['ewallet']>='20000.00'){?><span class="badge badge-primary font-weight-bold">Level Four</span><?php }?></td>
                            <td data-toggle="tooltip" title="<?php echo myvalidate($val['in_date']); ?>" nowrap><?php echo formatdate($val['in_date']); ?></td>
                            <td data-toggle='tooltip' title='<?php echo myvalidate($val['firstname']) . ' ' . myvalidate($val['lastname']); ?>'><?php echo myvalidate($val['username']); ?></td>
                            <td data-toggle='tooltip' title='<?php echo myvalidate($val['email']); ?>'><?php echo myvalidate($stremail); ?></td>
                           <td align="center" nowrap> <?php  echo $val['payment_id']; ?></td>
                            <td align="center" nowrap> <?php  echo kycfunction($val['email'],$val['phone'],$val['adhar_card'],$val['pan_card']); ?></td>
                           
                            <td align="center" nowrap> <?php  echo badgembrplanstatus($val['mbrstatus'], $val['mpstatus']); ?></td>
                            <td align="center" nowrap>
                                <a href="javascript:;"
                                   class="btn btn-sm btn-secondary"
                                   data-html="true"
                                   data-toggle="popover"
                                   data-trigger="hover"
                                   data-placement="left" 
                                   title="<?php echo strtoupper($val['id'] . '. ' . $val['username']); ?>"
                                   data-content="<?php echo myvalidate($mbrimgvalstr); ?>
                                   <div class='mt-2'><?php echo myvalidate($val['adminfo']); ?></div>
                                   ">
                                    <i class="far fa-fw fa-question-circle"></i>
                                </a>
                                <a href="index.php?hal=getuser&getId=<?php echo myvalidate($val['id']); ?>" class="btn btn-sm btn-info" data-toggle="tooltip" title="View <?php echo myvalidate($val['username']); ?>"><i class="far fa-fw fa-id-badge"></i></a>
                                <a href="javascript:;" data-href="edituser.php?editId=<?php echo myvalidate($val['id']); ?>&redir=userlist" data-poptitle="<i class='fa fa-fw fa-edit'></i> Update Member #<?php echo myvalidate($val['id']); ?>" class="btn btn-sm btn-success openPopup" data-toggle="tooltip" title="Update <?php echo myvalidate($val['username']); ?>"><i class="fa fa-fw fa-edit"></i></a>
                                <a href="javascript:;" data-href="deluser.php?hash=<?php echo myvalidate($hasdel); ?>&delId=<?php echo myvalidate($val['id']); ?>&redir=userlist" class="btn btn-sm btn-danger bootboxconfirm" data-poptitle="Username: <?php echo myvalidate($val['username']); ?>" data-popmsg="Are you sure want to delete this member?" data-toggle="tooltip" title="Delete <?php echo myvalidate($val['username']); ?>"><i class="far fa-fw fa-trash-alt"></i></a>
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
  ["Sr#","Name","Username","Email","Phone","Adhar Card","Pan Card","Status","Level Badge"],
  
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
 x.setAttribute("download","member_data.csv");
 document.body.appendChild(x);
 x.click();
}
</script>
