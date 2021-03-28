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
    <h1><i class="fa fa-fw fa-box"></i>My Courses</h1>
</div>

<div class="section-body">

    <hr>

    <div class="clearfix"></div>

 

    <div class="clearfix"></div>

    <div class="">
<div class="row">
<?php foreach($userData as $res){?>     
<div class="col-md-3">
<div class="card" style="width: 18rem;">
  <img src="<?php echo $res['image_name']?>" class="card-img-top" alt="..." style="height: 208px;">
  <div class="card-body">
    <h5 class="card-title"><?php echo $res['bundle_name'];?></h5>
    <p class="card-text"><?php echo $res['description'];?></p>
   <!--  <div class="progress" style="margin-bottom: 20px;">
          <div class="progress-bar course-bar" style="width:70%; border-radius: 34px">70%</div>
    </div> -->
    <a href="<?php echo SURL ?>/member/index.php?hal=loadcourses&bundle_id=<?php echo $res['id']?>" class="btn btn-secondary btn-sm start-course">Start Course</a>
  </div>
</div>
</div>
<?php }?>
</div>
    </div>

    <div class="clearfix"></div>



</div>
