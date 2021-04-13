<?php
include_once('../common/init.loader.php');
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

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {
    extract($FORM);

    $redirto = $_SESSION['redirto'];
    $_SESSION['redirto'] = '';
    if($video_id==''){
      $video_id= "30q7n48g4f";
    }
    if($course_id==''){
      $course_id= $_GET['bundle_id'];
    }
    $data = array(
        'course_id' => $course_id,
        'video_id' => $video_id,
        'module_id' => $module_id,
        'username' => $username,
        'feedback' => $feedback,
    );
    // var_dump($data);exit;

        $insert = $db->insert(DB_TBLPREFIX . '_feedback', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Feedback submitted successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Feedback not added <strong>Please try again!</strong>', 'Warning');";
        }
    
    header('location: ' .SURL.'/member/index.php?hal=loadcourses&bundle_id='.$course_id.'&video_id='.$video_id.'');
    exit;
}
$sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses where bundle_id ='".$_GET['bundle_id']."'");
$pages->items_total = count($sql);
$pages->mid_range = 3;
$pages->paginate();

$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses where bundle_id ='".$_GET['bundle_id']."'");

$_SESSION['course_id']= $_GET['bundle_id'];
$cover_course= getCourseContentByID($userData[0]['id']);
$cover_video=$cover_course[0]['video_id'];
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-book"></i> Learning Courses</h1>
    <div id="mobile-view">
    <a href="#" id="open"  onclick="openNav()"  class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
    <a href="#" id="close" onclick="closeNav()" style="display: none"  class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a>
    </div>
</div>

<div class="section-body">


    <hr>
    <div class="clearfix"></div>
<div class="row">
<div class="col-md-2 rowone">
  <div class="d-flex" id="">
<?php $cname= getbundleNameByID($_GET['bundle_id']); ?>
    <!-- Sidebar -->
    <div class="menu-list" id="sidebar-wrapper">
      <div class="sidebar-heading">
        <div>
            <img src="<?php echo $cname[0]['image_name'];?>" width="200px" height="150px">
        </div>
        
        <p class="bundle-title"><?php echo $cname[0]['bundle_name'];?></p>
            
        </div>
          <!-- <div class="progress">
          <div class="progress-bar" style="width:70%; border-radius: 34px">70%</div>
        </div> -->
      <div class="list-group list-group-flush">
      <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <?php  $count=1; foreach ($userData as $val) {?>
    <div class="panel panel-default">
        <div class="" role="tab" id="heading_<?php echo $count;?>">
            <a class="playlistmenu" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $count;?>" aria-expanded="false" aria-controls="collapse_<?php echo $count;?>"><?php echo $val['course_name']?></a>
        </div>
         <?php $content= getCourseContentByID($val['id']);?>
        <div aria-expanded="false" id="collapse_<?php echo $count;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo $count;?>">
            <div class="panel-body">
                <ul class="list-group list-unstyled">
                   <?php  foreach($content as $res){?>
                  <li class="list-group-item d-flex justify-content-between align-items-center course-video"> <i class="fa fa-play-circle"></i><a href="#wistia_<?php echo $res['video_id']?>" onclick='get_video_id("<?php echo $res['video_id'];?>","<?php echo $val['id'];?>");'> <font color="grey"><?php echo $res['title'];?></font></a></li>
                <?php $count++;}?>
                </ul>
            </div>
        </div>
    </div>
  <?php }?>   
</div>
      
      </div>
<div class="certificate">
<a href="javascript:;" class="openPopup btn btn-primary certificate-btn" data-href="certificate.php?redir=loadcourses" data-poptitle="<i class='fa fa-fw fa-plus-circle'></i> Course Completion Certificate">Completed Course</a>
</div>
</div>
</div>
</div>
<!-- </div> -->
    <!-- /#sidebar-wrapper -->
<!-- <div class="row"> -->
<div class="col-md-10">
    <!-- Page Content -->
    <div id="page-content-wrapper">

      <div class="container-fluid">
                  <!--wiesta player script example_embed_link.html -->
<!-- <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script>
       <div class="wistia_embed wistia_async_30q7n48g4f" style="width:100%">&nbsp;</div> -->

       <!-- example_embed_link.html -->
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script>
<div class="wistia_embed wistia_async_<?php echo isset($_GET['video_id']) ? $_GET['video_id'] : $cover_video; ?>" style="height: 670px;">&nbsp;</div>
<!-- <div class="wistia_embed wistia_async_30q7n48g4f" style="height: 670px;width: 1200px;">&nbsp;</div> -->
<!-- <a href="#wistia_30q7n48g4f">Make French Toast</a><br/>
<a href="#wistia_342jss6yh5">Wistia Team Intro</a><br/> -->
  
  </div>
  <form class="form-inline" method="post" action="addFeedback.php" style="margin-top: 1em; padding-left: 20px">
  <label>Feedback:</label>
  <textarea class="form-control feedback" rows="10" cols="50" placeholder="Feedback..." name="feedback"></textarea>
  <input type="hidden" name="video_id" value="" id="video_feed">
  <input type="hidden" name="module_id" value="" id="module_id">
  <input type="hidden" name="course_id" value="<?php echo $_GET['bundle_id'];?>">
  <input type="hidden" name="username" value="<?php echo $_SESSION['username'];?>">
  <input type="hidden" name="dosubmit" value="1">
  <button type="submit" class="btn btn-primary feedback-btn" name="submit" value="submit" id="submit">Send</button>
  <!-- <button type="button" class="btn btn-primary continue-btn">Finish & Continue</button> -->
</form>
</div>
</div>
</div>
<!-- </div> -->
    <!-- /#page-content-wrapper -->


<script>
 function get_video_id(video_id,module_id) {
  // alert(video_id);
   $('#video_feed').val(video_id);
   $('#module_id').val(module_id);
 }
</script>
<script>
 Wistia.playlist("1n6492l8d4", {
  version: "v1",
  theme: "bento",
  videoOptions: {
    volumeControl: true,
    autoPlay: true,
    videoWidth: 640,
    videoHeight: 360,
    videoFoam: true
  },
  media_0_0: {
    autoPlay: false,
    controlsVisibleOnLoad: false
  }
});
</script>

<script type="text/javascript">
  function openNav() {
    $('.rowone').show();
    $('#close').show();
    $('#open').hide();
  }

    function closeNav() {
    $('.rowone').hide();
        $('#open').show();
    $('#close').hide();
  }
</script>