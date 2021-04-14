<?php
include_once('../common/init.loader.php');

$hal = mystriptag($FORM['hal']);
// var_dump($avaladminpage_array);exit;
$pagefile = ($avaladminpage_array[$hal] == 1) ? $hal . '.php' : 'dashboard.php';

$menuactive = array();
foreach ($avaladminpage_array as $key => $value) {
    $menuactive[$key] = ($key == $hal) ? ' class="active"' : '';
}

$seskey = verifylog_sess('admin');
if ($seskey == '') {
    redirpageto('login.php');
    exit;
}
$admSess = getlog_sess($seskey);

$logtimeago = time_since($admSess['sestime']);

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
    $update = $db->update(DB_TBLPREFIX . '_configs', array('langiso' => $langiso), array('cfgid' => $didId));
    header("Location: {$httpurlref}");
    exit;
}
// -----

include_once('admheader.php');
?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <?php
        if (file_exists($pagefile)) {
            include ($pagefile);
        } else {
            include ("nofile.php");
        }
        ?>
    </section>
</div>

<!-- Modal ayeeen -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="...">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <!-- Modal content ayaeen-->
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
include_once('admfooter.php');
