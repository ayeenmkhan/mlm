<?php
// require_once('export.php');

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
if ($FORM['dohal'] == 'clear') {
    $_SESSION['filterid'] = '';
    redirpageto('index.php?hal=loadcourses');
    exit;
}
if ($FORM['dohal'] == 'filter' && $FORM['doval']) {
    $_SESSION['filterid'] = $FORM['doval'];
}
//Main queries
// echo "SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . $sqlshort . $pages->limit . "";
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses where bundle_id ='".$_GET['bundle_id']."'");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses where bundle_id ='".$_GET['bundle_id']."'");

?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-book"></i> Learning Courses</h1>
</div>

<div class="section-body">


    <hr>
    <div class="clearfix"></div>
  <div class="d-flex" id="wrapper">
<?php $cname= getbundleNameByID($_GET['bundle_id']); ?>
    <!-- Sidebar -->
    <div class="menu-list" id="sidebar-wrapper">
      <div class="sidebar-heading">
        <div>
            <img src="<?php echo $cname[0]['image_name'];?>" width="200px" height="150px">
        </div>
        
        <p class="bundle-title"><?php echo $cname[0]['bundle_name'];?></p>
            
        </div>
      <div class="list-group list-group-flush">
        <!-- <a href="#wistia_rknwvl2cf4" class="list-group-item list-group-item-action bg-light">Make French Toast</a> -->
        <!-- <a href="#wistia_342jss6yh5" class="list-group-item list-group-item-action bg-light">Wistia Team Intro</a> -->
        <ul class="list-unstyled">
        <?php foreach ($userData as $val) {?>
        <li class="sidebar-dropdown">
            <a href="#" class="list-group-item list-group-item-action bg-light">
              <span><?php echo $val['course_name']?></span>
            </a>
            <?php $content= getCourseContentByID($val['id']);?>
            <div class="sidebar-submenu">
              <ul>
                <?php foreach($content as $res){?>
                <li>
                  <a href="#wistia_<?php echo $res['video_id']?>" class=" list-group-item list-group-item-action bg-light"><?php echo $res['title'];?> <i class="fa fa-play-circle" aria-hidden="true"></i></a>
                </li>
            <?php }?>
              </ul>
            </div>
          </li>
      <?php }?>
      </ul>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <div class="container-fluid">
       <div class="wistia_embed wistia_async_30q7n48g4f?autoPlay=true" style="width:100%">&nbsp;</div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->


</div>
  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>
  <!-- example_embed_link.html -->
          <script src="//fast.wistia.net/assets/external/iframe-api-v1.js"></script>
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script>
<script type="text/javascript">
    
    jQuery(function ($) {

    $(".sidebar-dropdown > a").click(function() {
  $(".sidebar-submenu").slideUp(200);
  if (
    $(this)
      .parent()
      .hasClass("active")
  ) {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .parent()
      .removeClass("active");
  } else {
    $(".sidebar-dropdown").removeClass("active");
    $(this)
      .next(".sidebar-submenu")
      .slideDown(200);
    $(this)
      .parent()
      .addClass("active");
  }
});

$("#close-sidebar").click(function() {
  $(".page-wrapper").removeClass("toggled");
});
$("#show-sidebar").click(function() {
  $(".page-wrapper").addClass("toggled");
});


   
   
});
</script>