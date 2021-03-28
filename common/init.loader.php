<?php
include_once('config.php');
if (!defined('INSTALL_PATH')) {
    header("Location: ../install");
}
if (!defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}
// -----
include_once('db.class.php');
include_once('navpage.class.php');
include_once('sys.func.php');
include_once('value.list.php');
include_once('en.lang.php');

$FORM = array_merge((array) $FORM, (array) $_REQUEST);
$LANG = array_merge((array) $LANG, (array) $lang);

$dsn = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST . "";
$pdo = "";

try {
    // $pdo = new PDO($dsn, base64_decode(DB_USER), base64_decode(DB_PASSWORD));
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

session_start();
dumbtoken();

$db = new Database($pdo);
$pages = new Paginator();

$tplstr = $cfgrow = $bpprow = $payrow = array();

// load site configuration

$didId = 1;

// settings
$row = $db->getAllRecords(DB_TBLPREFIX . '_configs', '*', ' AND cfgid = "' . $didId . '"');
foreach ($row as $value) {
    $cfgrow = array_merge($cfgrow, $value);
}
$cfgrow['md5sess'] = 'sess_' . md5(INSTALL_PATH) . '_';
$site_logo = ($cfgrow['site_logo']) ? $cfgrow['site_logo'] : DEFIMG_LOGO;
$cfgtoken = get_optionvals($cfgrow['cfgtoken']);
$langlist = base64_decode($cfgtoken['langlist']);
$langlistarr = json_decode($langlist, true);
if (empty(array_filter((array) $langlistarr))) {
    $langlistarr['en'] = 'English';
}

// current date time
$cfgrow['datetimestr'] = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

$langloadf = INSTALL_PATH . '/common/lang/' . $cfgrow['langiso'] . '.lang.php';
if (file_exists($langloadf)) {
    $TEMPLANG = $LANG;
    include_once($langloadf);
    $LANG = array_filter($LANG);
    $LANG = array_merge($TEMPLANG, $LANG);
    $TEMPLANG = '';
}

// baseplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_baseplan', '*', ' AND bpid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}

// payplan
$row = $db->getAllRecords(DB_TBLPREFIX . '_payplans', '*', ' AND ppid = "' . $didId . '"');
foreach ($row as $value) {
    $bpprow = array_merge($bpprow, $value);
}
$bpprow['currencysym'] = base64_decode($bpprow['currencysym']);
$planlogo = ($bpprow['planlogo']) ? $bpprow['planlogo'] : DEFIMG_PLAN;

// paymentgate
$row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND paygid = "' . $didId . '"');
foreach ($row as $value) {
    $payrow = array_merge($payrow, $value);
}

// return latest version
if (isset($FORM['initdo']) and $FORM['initdo'] == 'vnum') {
    echo checknewver();
    exit();
}

// get referrer id
if ($_SESSION['ref_sess_un'] || $_COOKIE['ref_sess_un']) {

    if ($_SESSION['ref_sess_un'] != $_COOKIE['ref_sess_un']) {
        setcookie('ref_sess_un', $_SESSION['ref_sess_un'], time() + (86400 * $cfgrow['maxcookie_days']));
    }

    $ref_sess_un = ($_COOKIE['ref_sess_un']) ? $_COOKIE['ref_sess_un'] : $_SESSION['ref_sess_un'];

    // get member details
    $sesref = getmbrinfo($ref_sess_un, 'username');

    // check for max personal ref
    if ($bpprow['limitref'] > 0) {
        $refcondition = " AND idref = '{$sesref['id']}'";
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $refcondition);
        $myperdltotal = $row[0]['totref'];
        if ($bpprow['limitref'] <= $myperdltotal) {
            $newmpid = getmpidflow($sesref['mpid']);
            $sesref = getmbrinfo('', '', $newmpid);
        }
    }

    if ($cfgtoken['disreflink'] == 1 || $sesref['mpstatus'] == 0 || $sesref['mpstatus'] == 3) {
        $sesref = array();
        $_SESSION['ref_sess_un'] = '';
        setcookie('ref_sess_un', '', time() - 86400);
    }
}

// if rand ref
if ($sesref['id'] < 1 && $cfgrow['randref'] == 1) {
    if ($cfgrow['defaultref'] != '') {
        $refarr = explode(',', str_replace(' ', '', $cfgrow['defaultref']));
        $i = array_rand($refarr);
        $randun = $refarr[$i];
    }
    $condition = ' AND username = "' . $randun . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans LEFT JOIN " . DB_TBLPREFIX . "_mbrs ON idmbr = id WHERE 1 " . $condition . " LIMIT 1");
    if ($randun && count($sql) < 1) {
        $condition = ' AND mbrstatus = "1" AND mpstatus = "1"';
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans LEFT JOIN ' . DB_TBLPREFIX . '_mbrs ON idmbr = id', 'username', $condition);
        $randun = floatval($row[0]['username']);
    }
    // get member details from rand ref
    if ($randun) {
        $sesref = getmbrinfo($randun, 'username');
    }
}

// is demo
if (defined('ISDEMOMODE')) {
    $tplstr['demo_mode_warn'] = "<ul class='navbar-nav'><li><div class='badge badge-danger'>Demo Version</div></li></ul>";
}
// is debug
if ($payrow['testpayon'] == 1) {
    $tplstr['debug_mode_warn'] = "<ul class='navbar-nav'><li><div class='badge badge-danger'>Debug Mode</div></li></ul>";
}

// load cron do
include_once('cron.do.php');
// end vars
$row = $value = '';
