<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if (defined('ISDEMOMODE')) {
    die();
}

// debug only, truncate member records
if ($FORM['dbtest'] == 'truncated' && $payrow['testpayon'] == 1 && !defined('ISDEMOMODE')) {
    $db->getRecFrmQryStr("TRUNCATE " . DB_TBLPREFIX . "_transactions");
    $db->getRecFrmQryStr("TRUNCATE " . DB_TBLPREFIX . "_points");
    $db->getRecFrmQryStr("TRUNCATE " . DB_TBLPREFIX . "_mbrs");
    $db->getRecFrmQryStr("TRUNCATE " . DB_TBLPREFIX . "_mbrplans");

    $db->getRecFrmQryStr("DELETE FROM " . DB_TBLPREFIX . "_paygates WHERE 1 AND pgidmbr > '0'");
    $db->getRecFrmQryStr("ALTER TABLE " . DB_TBLPREFIX . "_paygates AUTO_INCREMENT = 2");

    redirpageto('index.php?hal=dashboard');
    exit;
}

if (isset($FORM['dosubmit']) && $FORM['dosubmit'] == '1') {
    extract($FORM);

    $baseArr = ($isreuse == 1) ? array('myruname' => $myruname, 'addlic' => '1') : array('rfname' => $rfname, 'rlname' => $rlname, 'remail' => $remail, 'runame' => $runame);
    $dataArr = array_merge($baseArr, array('lickey' => $cfgrow['lickey'], 'do' => 'reg'));

    $initurl = "https://www.mlmscript.net/~enverifykey/api.php";
    $response = getdocurl($initurl, $dataArr);
    $arrResponse = $response['data'];

    if ($arrResponse['isvalid'] != 1) {
        $_SESSION['errmsg'] = $arrResponse['errmsg'];
    } else {
        $envacc = ($arrResponse['username']) ? $arrResponse['username'] : $cfgrow['envacc'];
        $lichash = $arrResponse['lichash'];
        $data = array(
            'envacc' => $envacc,
            'licstatus' => $arrResponse['licstatus'],
            'lichash' => $lichash,
        );
        $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => '1'));
    }
    redirpageto('index.php?hal=updates');
    exit;
}

$cfgtoken = get_optionvals($cfgrow['cfgtoken']);
$updateinfostr = ($cfgtoken['cnvnum'] > $cfgrow['softversion']) ? '<span class="badge badge-success">New version ' . $cfgtoken['cnvnum'] . ' is available!</span>' : '<a href="javascript:;" onclick="getinitdo(\'../common/init.loader.php\', \'vnum\')"><span id="newverstr" class="badge badge-info">No new version available!</span></a><span id="newvernum" class="badge badge-success"></span>';

$errmsg = $_SESSION['errmsg'];
$errmsgstr = ($errmsg) ? showalert('danger', 'Erroer', $errmsg) : showalert('warning', 'Optional', "You can also register your license manually from the <a href='https://www.mlmscript.net/join' target='_blank' data-toggle='tooltip' title='Register to MLMScript.net'>MLMScript.net</a> site.");
$_SESSION['errmsg'] = '';

$admin_content = <<<INI_HTML
<div class="section-header">
    <h1><i class="fa fa-fw fa-briefcase-medical text-success"></i> {$LANG['a_updates']}</h1>
</div>
INI_HTML;
echo myvalidate($admin_content);
?>

<div class="row">
    <?php
    if ($payrow['testpayon'] == 1) {
        ?>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Reset Database</h4>
                </div>
                <div class="card-body">
                    Use the button below to reset or purge your current test records. Please note! This process cannot be reversed.
                </div>
                <div class="card-footer bg-whitesmoke text-md-right">
                    <a href="javascript:;" data-href="index.php?hal=updates&dbtest=truncated" class="btn btn-danger bootboxconfirm" data-poptitle="Purge Test Records" data-popmsg="<p>Are you sure want to purge the member and transaction records?</p><p><span class='badge badge-danger'><i class='fa fa-exclamation-triangle'></i> This action cannot be undone!</span></p>" data-toggle="tooltip" title="Purge Test Records"><i class="far fa-fw fa-trash-alt"></i> Purge Test Records</a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>License Info</h4>
            </div>
            <div class="card-body">
                <span class='text-small text-muted'>License Key</span>
                <h6 class="summary"><?php echo myvalidate(base64_decode($cfgrow['lickey'])); ?></h6>
                <span class='text-small text-muted'>License Date</span>
                <h6 class="summary">25 November 2020</h6>
                <span class='text-small text-muted'>Version</span>
                <h6 class="summary"><?php echo myvalidate($cfgrow['softversion']); ?></h6>
                <span class='text-small text-muted'>Installation Date</span>
                <h6 class="summary">25 November 2020</h6>
                <span class='text-small text-muted'>Have a Question?</span>
                <h6 class="summary text-muted">Please feel free to ask <a href="https://www.icchatva.com" target="_blank">here</a>.</h6>
                <span class='text-small text-muted'>Need additional features or custom programming services?</span>
                <h6 class="summary text-muted">Please feel free to <a href="https://www.icchatva.com" target="_blank">contact us</a>.</h6>
            </div>
            <div class="card-footer bg-whitesmoke">
                <?php echo myvalidate($updateinfostr); ?>
            </div>
        </div>
    </div>

    

</div>
