<?php
include_once('../common/init.loader.php');

$hal = mystriptag($FORM['hal']);
$pagefile = ($avalmemberpage_array[$hal] == 1) ? $hal . '.php' : 'dashboard.php';

$menuactive = array();
foreach ($avalmemberpage_array as $key => $value) {
    $menuactive[$key] = ($key == $hal) ? ' class="active"' : '';
}

$seskey = verifylog_sess('member');
if ($seskey == '') {
    // force login for empty session
    redirpageto('login.php?err=expiry');
    exit;
}

$sesRow = getlog_sess($seskey);
$username = get_optionvals($sesRow['sesdata'], 'un');

// Get member details
$mbrstr = getmbrinfo($username, 'username');
$mbrstr['fullname'] = $mbrstr['firstname'] . ' ' . $mbrstr['lastname'];

if ($mbrstr['id'] < 1) {
    // force logout for unknown username
    redirpageto("logout.php?un={$username}&err=notfound");
    exit;
}
if (($mbrstr['mbrstatus'] < 1 && ($hal == 'planreg' || $hal == 'planpay')) || $mbrstr['mbrstatus'] == 3) {
    $pagefile = 'dashboard.php';
}
if ($mbrstr['mpid'] < 1 && in_array($hal, array("userlist", "historylist", "withdrawreq", "genealogyview", "getuser"))) {
    $pagefile = 'dashboard.php';
}

// Get sponsor details
$sprstr = getmbrinfo($mbrstr['idspr']);

// session time interval
$logtimeago = time_since($sesRow['sestime']);
//
/* // debug
  echo 'MBR-INDEX-DEBUG = ' . dosprlist(4, '|1:1|, |2:0|', 3);
  die();
  // */
//
// -----
// process download
if ($FORM['dlfn'] && $FORM['dlid'] > 0 && $FORM['l'] == md5($cfgrow['dldir'] . $FORM['dlid'] . date("md"))) {
    $flid = intval($FORM['dlid']);
    $condition = ' AND flid = "' . $flid . '" ';
    $row = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_files WHERE 1 " . $condition . "");
    $filRow = array();
    foreach ($row as $value) {
        $filRow = array_merge($filRow, $value);
    }

    // count download
    $fldlcount = $filRow['fldlcount'] + 1;
    $data = array(
        'fldlcount' => $fldlcount,
    );
    $update = $db->update(DB_TBLPREFIX . '_files', $data, array('flid' => $flid));
    // process download

    $mtype = "application/force-download";
    dodlfile($filRow['flpath'], $FORM['dlfn'], $mtype);
}

// load my language
$langloadf = INSTALL_PATH . '/common/lang/' . $mbrstr['mylang'] . '.lang.php';
if (file_exists($langloadf)) {
    $TEMPLANG = $LANG;
    include_once($langloadf);
    $LANG = array_filter($LANG);
    $LANG = array_merge($TEMPLANG, $LANG);
    $TEMPLANG = '';
}

// language list
$langliststr = '';
foreach ($langlistarr as $key => $value) {
    $langicon = ($key == $LANG['lang_iso']) ? "fa-check" : "fa-minus";
    $langliststr .= "<a href='index.php?hal={$FORM['hal']}&lang={$key}&langdt={$_SESSION['dumbtoken']}' class='dropdown-item has-icon'><i class='fas {$langicon}'></i> {$value}</a>";
}

// update language
if ($langlistarr[$FORM['lang']] != '' && dumbtoken($dumbtoken)) {
    $httpurlref = $_SERVER['HTTP_REFERER'];
    $langiso = strtolower(mystriptag($FORM['lang']));
    $update = $db->update(DB_TBLPREFIX . '_mbrs', array('mylang' => $langiso), array('id' => $mbrstr['id']));
    header("Location: {$httpurlref}");
    exit;
}

// -----
include_once('mbrheader.php');
?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <?php
        if ($cfgrow['site_status'] != 1) {
            redirpageto('offline.php');
            exit;
        } elseif (file_exists($pagefile)) {
            include ($pagefile);
        } else {
            include ("nofile.php");
        }
        ?>
    </section>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="...">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
            </div>
        </div>

    </div>
</div>

<?php
include_once('mbrfooter.php');

