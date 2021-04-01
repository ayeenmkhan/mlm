<?php

if (!defined('OK_LOADME')) {
    die("<title>Error!</title><body>No such file or directory.</body>");
}

function read_file_size($size) {
    if (intval($size) == 0) {
        return("0 Bytes");
    }
    $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
}

function dborder_arr($tblarr, $tblsel, $tblsrt) {
    $curqryurl = $_SERVER['REQUEST_URI'];
    if ((strpos($curqryurl, "_stbel=") !== false)) {
        $rtblsrt = ($tblsrt == 'up') ? "down" : "up";
        $curqryurl = str_replace("_stbel={$tblsel}", "_stbel=^", $curqryurl);
    } else {
        $curqryx = (false !== strpos($_SERVER['REQUEST_URI'], '?')) ? "&" : "?";
        $curqryurl .= $curqryx . "_stbel=^&_stype=down";
    }

    $tblarrlink = array();
    foreach ($tblarr as $key => $value) {
        if ($tblsel == $value) {
            $curqryurlgo = str_replace("_stype={$tblsrt}", "_stype={$rtblsrt}", $curqryurl);
            $curqryurlgo = str_replace("_stbel=^", "_stbel={$value}", $curqryurlgo);
            $curfontaw = ($tblsrt != 'up') ? "fa fa-fw fa-long-arrow-alt-down" : "fa fa-fw fa-long-arrow-alt-up";
        } else {
            $curqryurlgo = str_replace("_stbel=^", "_stbel={$value}", $curqryurl);
            $curfontaw = "fa fa-fw fa-arrows-alt-v";
        }
        $tblarrlink[$value] = "<a href='{$curqryurlgo}'><i class='{$curfontaw}'></i></a>";
    }
    return $tblarrlink;
}

function get_optionvals($options, $var = '') {
    $varsvals = ($var == '') ? array() : '';
    $options = str_replace('|, , |', '|,|', trim($options));
    $options = str_replace('|,, |', '|,|', trim($options));
    $options = str_replace('|, |', '|,|', $options);
    $varvals = explode('|,|', $options);
    $vvcount = count($varvals);
    $varfound = 0;
    for ($i = 0; $i < $vvcount; $i++) {
        $varsvalsx = str_replace('|,', '|', $varvals[$i]);
        if ($i == 0)
            $varsvalsx = substr($varsvalsx, 1);
        if ($i == $vvcount - 1)
            $varsvalsx = substr($varsvalsx, 0, -1);
        $vals = explode(':', $varsvalsx);
        if ($var != '' && $vals[0] != $var)
            continue;
        if ($var != '' && $vals[0] == $var)
            $varfound = 1;
        $val = str_replace($vals[0] . ':', '', $varsvalsx);
        ($var == '') ? $varsvals[$vals[0]] = $val : $varsvals = $val;
    }
    if ($var != '' && $varfound != 1)
        $varsvals = false;
    return $varsvals;
}

function add_optionvals($options, $var = '', $val = '') {
    if (get_optionvals($options, $var) === false) {
        $options = ($options == '') ? '|' . $var . ':' . $val . '|' : $options . ', |' . $var . ':' . $val . '|';
    }
    return $options;
}

function put_optionvals($options, $var = '', $val = '') {
    if ($var != '') {
        $options = add_optionvals($options, $var, $val);
        if (strpos($options, '|' . $var . ':' . $val . '|') === false) {
            $optionx = str_replace('|, |', '|,|', $options);
            $varvals = explode('|,|', $optionx);
            $vvcount = count($varvals);
            for ($i = 0; $i < $vvcount; $i++) {
                $oldval = str_replace('|,', '|', $varvals[$i]);
                if ($i == 0)
                    $oldval = substr($oldval, 1);
                if ($i == $vvcount - 1)
                    $oldval = substr($oldval, 0, -1);
                $vals = explode(':', $oldval);
                if ($var != '' && $vals[0] != $var)
                    continue;
                $newval = $vals[0] . ':' . $val;
                $options = str_replace($oldval, $newval, $options);
                break;
            }
        }
    }
    return $options;
}

function select_opt($valarr, $valsel = '', $tostr = 0) {
    if ($tostr != 0) {
        $selopt = $valarr[$valsel];
    } else {
        $selopt = ($valsel == '') ? "<option selected>-</option>" : "<option disabled>-</option>";
        foreach ($valarr as $key => $value) {
            if ($value == '') {
                continue;
            }
            $selopt .= ($key == $valsel) ? "<option value='{$key}' selected>{$value}</option>" : "<option value='{$key}'>{$value}</option>";
        }
    }
    return $selopt;
}

function checkbox_opt($value, $targetval = 1, $tostr = 0) {
    if ($tostr != 0) {
        $cekopt = ($value == $targetval) ? "Yes" : "No";
    } else {
        $cekopt = ($value == $targetval) ? " checked" : "";
    }
    return $cekopt;
}

function radiobox_opt($valuearr, $targetval = 1) {
    $cekopt = array();
    foreach ($valuearr as $key => $value) {
        $cekopt[$key] = ($value == $targetval) ? ' checked="checked"' : '';
    }
    return $cekopt;
}

function getpasshash($str) {
    $strmd = md5($str);
    return password_hash($strmd, PASSWORD_DEFAULT);
}

function redir_to($redir = '') {
    $refredir = $_SERVER["HTTP_REFERER"];
    $redirto = ($redir == '') ? $refredir : "index.php?hal=" . $redir;
    return $redirto;
}

function myvalidate($myodata) {
    return $myodata;
}

function getCoursesNameByID($course_id){
     global $db, $cfgrow;
$data = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses WHERE id='".$course_id."' ");
    return $data[0]['course_name'];
}
function getCourseVideoByID($course_id){
     global $db, $cfgrow;
$data = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_course_content WHERE video_id='".$course_id."' ");
    return $data[0]['video_code'];
}
function getbundleNameByID($bundle_id){
     global $db, $cfgrow;
$data = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_bundle_package WHERE id='".$bundle_id."' ");
    // var_dump($data);
    return $data;
}
function getuserNameByID($username){
     global $db, $cfgrow;
$data = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs WHERE username='".$username."' ");
    // var_dump($data);
    return $data;
}
function getuserPaymentStatusID($id){
     global $db, $cfgrow;
$data = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE idmbr='".$id."' ");
    // var_dump($data);
    return $data;
}

function mystriptag($mysdata, $filter = 'string') {
    if ($filter == 'email') {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_EMAIL);
    } elseif ($filter == 'url') {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_URL);
    } else {
        $mysdata = filter_var($mysdata, FILTER_SANITIZE_STRING);
    }
    if ($filter == 'user') {
        $mysdata = preg_replace("/[^A-Za-z0-9]/", '', $mysdata);
    }
    return strip_tags($mysdata);
}

function imageupload($outfname, $fileimg, $oldimg = '') {
    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif','pdf');

    $newimg = $oldimg;
    $path = '../assets/imagextra/';
    if ($fileimg) {
        $img = $fileimg['name'];
        $tmp = $fileimg['tmp_name'];
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        $final_image = $outfname . '.' . $ext;
        // check's valid format
        if (in_array($ext, $valid_extensions)) {
            if ($oldimg != '' && file_exists($oldimg) && strpos($oldimg, '/imagextra/') !== false) {
                unlink($oldimg);
            }
            $path = $path . strtolower($final_image);
            if (move_uploaded_file($tmp, $path)) {
                $newimg = $path;
            }
        }
    }
    // var_dump($newimg);exit;
    return $newimg;
}

function readfile_chunked($filename, $retbytes = true) {
    $chunksize = 2 * (1024 * 1024);
    $buffer = '';
    $cnt = 0;

    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        echo myvalidate($buffer);
        ob_flush();
        flush();
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt;
    }
    return $status;
}

function dodlfile($file_path, $file_name, $mtype) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Type: $mtype");
    header("Content-Disposition: attachment; filename=\"$file_name\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($file_path));

    //@readfile($file_path);
    readfile_chunked($file_path);
}

function badgembrplanstatus($statusid, $mpstatus = 0) {
    $statusbadge = '';
    // switch ($statusid) {
    //     case "1":
    //         $statusbadge .= "<span class='badge badge-success'>Active</span>";
    //         break;
    //     case "2":
    //         $statusbadge .= "<span class='badge badge-warning'>Limited</span>";
    //         break;
    //     case "3":
    //         $statusbadge .= "<span class='badge badge-danger'>Pending</span>";
    //         break;
    //     default:
    //         $statusbadge .= "<span class='badge badge-light'>Inactive</span>";
    // }
    switch ($mpstatus) {
        case "0":
            $statusbadge .= "<span class='badge badge-light' data-toggle='tooltip' title='Registered only'><i class='fa fa-fw fa-user'></i></span>";
            break;
        case "1":
            $statusbadge .= "<span class='badge badge-success' data-toggle='tooltip' title='Active'><i class='fa fa-fw fa-check'></i></span>";
            break;
        case "2":
            $statusbadge .= "<span class='badge badge-warning' data-toggle='tooltip' title='Expired'><i class='fa fa-fw fa-exclamation'></i></span>";
            break;
        case "3":
            $statusbadge .= "<span class='badge badge-danger' data-toggle='tooltip' title='Pending'><i class='fa fa-fw fa-times'></i></span>";
            break;
        default:
            $statusbadge .= "<span class='badge badge-light' data-toggle='tooltip' title='Unregistered'><i class='fa fa-fw fa-question'></i></span>";
    }
    return $statusbadge;
}
function kycfunction($email,$phone,$adhar,$pancard) {
    $statusbadge = '';
    if ($email!='' && $phone!='' && $adhar!='' && $pancard!='') {
            $statusbadge .= "<span class='badge badge-success'><i class='fa fa-fw fa-check'></i></span>";
    }else{
            $statusbadge .= "<span class='badge badge-danger'><i class='fa fa-fw fa-question'></i></span>";
    }
    return $statusbadge;
}

function getCourseContentByID($course_id){
    global $db, $cfgrow;
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_course_content WHERE course_id='".$course_id."'");
    return $userData;
}
function getCoursesModuleByID($course_id){
    global $db, $cfgrow;
$userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_courses WHERE bundle_id='".$course_id."'");
    return $userData;
}
function updateBonusCounter($counter,$wallet,$user_id){
    global $db, $cfgrow;

    $data = array(
        'counter' => $counter,
        'ewallet'=> $wallet
    );
$userData = $db->update(DB_TBLPREFIX . '_mbrs', $data, array('id' => $user_id));
    return $userData;
}
// function to get ip address
function get_userip() {
    $ip = false;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

function redirpageto($destinationurl, $delay = 0) {
    echo "<meta http-equiv='refresh' content='{$delay};url={$destinationurl}'>";
    exit;
}

function formatdate($datetimestr, $type = 'd') {
    global $cfgrow;

    $dtformat = ($type == 'd') ? $cfgrow['sodatef'] : $cfgrow['lodatef'];
    return date($dtformat, strtotime($datetimestr));
}

function addlog_sess($username, $type = 'system', $rememberme = '') {
    global $db, $cfgrow;

    $userip = get_userip();
    $sesdata = put_optionvals('', 'un', $username);
    $sesdata = put_optionvals($sesdata, 'ip', $userip);

    $sestime = time() + (3600 * $cfgrow['time_offset']);
    $seskey = getpasshash($username . '|' . $userip);

    $data = array(
        'sestype' => $type,
        'sesdata' => $sesdata,
        'sestime' => intval($sestime),
        'seskey' => $seskey,
    );

    $sesRow = getlog_sess($seskey);
    if ($sesRow['sesid'] < 1) {
        $db->insert(DB_TBLPREFIX . '_sessions', $data);
    } else {
        $db->update(DB_TBLPREFIX . '_sessions', $data, array('sesid' => $sesRow['sesid']));
    }

    $_SESSION[$cfgrow['md5sess'] . $type] = $seskey;
    if ($rememberme == 1) {
        setcookie($cfgrow['md5sess'] . $type, $seskey, time() + (3600 * 72));
    } else {
        setcookie($cfgrow['md5sess'] . $type, '', time() - (3600 * $cfgrow['time_offset']));
    }
    return $seskey;
}

function getlog_sess($seskey, $isupdate = '') {
    global $db, $cfgrow;

    $condition = ' AND seskey = "' . $seskey . '" ';
    $row = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_sessions WHERE 1 " . $condition . "");
    $sesRow = array();
    foreach ($row as $value) {
        $sesRow = array_merge($sesRow, $value);
    }

    // update time
    if ($sesRow['sesid'] > 0 && $isupdate == 1) {
        $sestime = time() + (3600 * $cfgrow['time_offset']);
        $data = array(
            'sestime' => intval($sestime),
        );
        $db->update(DB_TBLPREFIX . '_sessions', $data, array('sesid' => $sesRow['sesid']));
    }
    return $sesRow;
}

function dellog_sess($type = '') {
    global $db, $cfgrow;

    if ($type != '') {
        // delete type session
        $seskey = ($_SESSION[$cfgrow['md5sess'] . $type] ? $_SESSION[$cfgrow['md5sess'] . $type] : $_COOKIE[$cfgrow['md5sess'] . $type]);
        if ($seskey != '') {
            $db->delete(DB_TBLPREFIX . '_sessions', array('seskey' => $seskey));

            $_SESSION[$cfgrow['md5sess'] . $type] = '';
            setcookie($cfgrow['md5sess'] . $type, '', time() - (3600 * $cfgrow['time_offset']));
        }
    } else {
        // delete old sessions
        $sqlarr = array();
        $tmintvarr = array("system" => (3600 * 6), "visitor" => 60, "admin" => (3600 * 12), "member" => (3600 * 72));
        foreach ($tmintvarr as $key => $value) {
            $sestime = time() - $value;
            $sqlarr[] = "(sestype = '{$key}' AND sestime < {$sestime})";
        }
        $sqladd = implode(' OR ', $sqlarr);
        $condition = "AND ({$sqladd})";
        $db->deleteQry("DELETE FROM " . DB_TBLPREFIX . "_sessions WHERE 1 " . $condition);
    }
}

function verifylog_sess($type = 'system', $isupdate = '') {
    global $cfgrow;

    $hasil = '';
    $seskey = ($_SESSION[$cfgrow['md5sess'] . $type] ? $_SESSION[$cfgrow['md5sess'] . $type] : $_COOKIE[$cfgrow['md5sess'] . $type]);

    $userip = get_userip();
    $sesRow = getlog_sess($seskey, $isupdate);
    $username = get_optionvals($sesRow['sesdata'], 'un');
    $_SESSION['username']=$username;
    if (password_verify(md5($username . '|' . $userip), $seskey)) {
        $hasil = $seskey;
    } else {
        dellog_sess($seskey);
    }
    return $hasil;
}

function time_since($sestime) {
    global $cfgrow;

    $since = time() + (3600 * $cfgrow['time_offset']) - $sestime;
    $chunks = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'minute'),
        array(1, 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
    return $print;
}

function showalert($type, $title, $message) {

    $faiconarr = array("info" => "lightbulb", "success" => "check-circle", "warning" => "question-circle", "danger" => "times-circle", "secondary" => "bell", "light" => "bell", "dark" => "bell", "primary" => "bell");
    $faicon = $faiconarr[$type];

    $alert_content = <<<INI_HTML
                <div class="alert alert-{$type} alert-dismissible alert-has-icon show fade">
                    <div class="alert-icon"><i class="far fa-{$faicon} fa-fw"></i></div>
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                        <div class="alert-title">{$title}</div>
                        {$message}
                    </div>
                </div>
INI_HTML;

    return $alert_content;
}

function getmbrinfo($id, $bfield = 'id', $mpid = 0) {
    global $db;

    $userRow = array();

    if ($id != '') {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', ' AND ' . $bfield . ' = "' . $id . '"');
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', ' AND idmbr = "' . $userRow['id'] . '"');
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
    }

    // plan member
    if ($mpid > 0) {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', ' AND mpid = "' . $mpid . '"');
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
        if ($id == '') {
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', ' AND id = "' . $userRow['idmbr'] . '"');
            foreach ($row as $value) {
                $userRow = array_merge($userRow, $value);
            }
        }
    }

    // payment options
    if ($userRow['id'] > 0) {
        $row = $db->getAllRecords(DB_TBLPREFIX . '_paygates', '*', ' AND pgidmbr = "' . $userRow['id'] . '"');
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
    }

    $userRow['username'] = ($userRow['username'] == '') ? 'Administrator' : $userRow['username'];

    return $userRow;
}

function getusernameid($srcval, $targetstr = 'id') {
    global $db;

    if ($srcval < 1) {
        $userRow[$targetstr] = 'Administrator';
    } else {
        if ($targetstr == 'id') {
            $sqlwhere = "username LIKE '{$srcval}'";
        } else {
            $sqlwhere = "id = '{$srcval}'";
        }

        $userRow = array();
        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrs', '*', ' AND ' . $sqlwhere);
        foreach ($row as $value) {
            $userRow = array_merge($userRow, $value);
        }
    }

    return $userRow[$targetstr];
}

function parsenotify($cntarr, $msg) {
    foreach ((array) $cntarr as $key => $value) {
        $msg = str_replace("[[{$key}]]", $value, $msg);
    }

    // add custom parse
    $msg = str_replace("[[fullname]]", $cntarr['firtname'] . ' ' . $cntarr['lastname'], $msg);

    return $msg;
}

function printlog($idstr = '', $err = '') {
    global $cfgrow;

    if (defined('ISPRINTLOG')) {
        $datetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $myfile = file_put_contents('printlog.log', "[{$datetm}][{$idstr}] {$err}" . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $myfile;
    }
}

function passmeter($password) {
    $uppercase = preg_match('#[A-Z]#', $password);
    $lowercase = preg_match('#[a-z]#', $password);
    $number = preg_match('#[0-9]#', $password);
    $specialChars = preg_match('#[^\w]#', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return 'Password should include at least one upper and lower case letters, one number, one special character, and should be at least 8 characters in length.';
    } else {
        return 1;
    }
}

function dosprlist($mpid, $sprlist, $mpdepth) {
    $sprlist = str_replace(' ', '', $sprlist);
    $sprlistarr = explode(',', $sprlist);
    $pos = 2;
    $mpid = intval($mpid);
    $newsprlist = array("|1:{$mpid}|");
    foreach ($sprlistarr as $key => $value) {
        $valarr = explode(':', $value);
        $sprval = intval(str_replace('|', '', $valarr[1]));
        $newsprlist[] = "|{$pos}:{$sprval}|";
        $pos++;
    }
    $newsprlist = array_slice($newsprlist, 0, $mpdepth);

    $newsprout = implode(', ', $newsprlist);
    return $newsprout;
}

function getsprlistid($tier, $sprlist) {
    $mpid = 0;
    $sprlist = str_replace(array(' ', '|'), '', $sprlist);
    $sprlistarr = explode(',', $sprlist);
    foreach ($sprlistarr as $key => $value) {
        $valarr = explode(':', $value);
        if (intval($valarr[0]) == $tier) {
            $mpid = intval($valarr[1]);
            break;
        }
    }
    return $mpid;
}

function getamount($cm, $regfee, $mrank = 0) {
    if (floatval($regfee) <= 0) {
        $resamount = (strpos($cm, '%') !== false) ? 0 : $cm;
    } else {
        $resamount = (strpos($cm, '%') !== false) ? $cm * $regfee / 100 : $cm;
    }
    return number_format((float) $resamount, DECIMAL_POINT);
}

function getcmlist($mpid, $sprlist, $cmlist, $mbrstr = array()) {
    $sprcmlist = array();

    $reg_fee = $mbrstr['reg_fee'];
    $mpdepth = $mbrstr['mpdepth'];

    $sprlistarr = explode(',', str_replace(array(' ', '|'), '', $sprlist));
    $cmlistarr = explode(',', str_replace(' ', '', $cmlist));
    for ($i = 0; $i < $mpdepth; $i++) {
        $valarr = explode(':', $sprlistarr[$i]);
        $sprval = intval($valarr[1]);
        if ($sprval < 1) {
            break;
        }
        $sprcm = getamount($cmlistarr[$i], $reg_fee);
        $sprcmlist[$sprval] = $sprcm;
    }

    return $sprcmlist;
}

function addcmlist($memo, $tokencode, $getcmlist = array(), $mbrstr = array()) {
    global $db, $cfgrow, $bpprow;

    // require_once(INSTALL_PATH . '/common/mailer.do.php');
    $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

    $cmcount = 1;
    foreach ((array) $getcmlist as $key => $value) {
        if ($key > 0 && $value > 0) {
            $cmcountstr = (strpos($tokencode, 'TIER') !== false) ? " [{$cmcount}]" : '';
            $sprstr = getmbrinfo('', '', $key);
            $data = array(
                'txdatetm' => $reg_utctime,
                'txtoid' => $sprstr['id'],
                'txamount' => $value,
                'txmemo' => $memo . $cmcountstr,
                'txppid' => $mbrstr['mppid'],
                'txtoken' => "|SRCIDMBR:{$mbrstr['id']}|, |SRCLVPOS:{$cmcount}|, |LCM:{$tokencode}|",
            );
            $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);

            if ($sprstr['id'] > 0) {
                $cntaddarr['ncm_memo'] = $memo . $cmcountstr;
                $cntaddarr['ncm_amount'] = $bpprow['currencysym'] . $value . ' ' . $bpprow['currencycode'];
                $cntaddarr['dln_username'] = $mbrstr['username'];
                // delivermail('mbr_newcm', $sprstr['id'], $cntaddarr);
            }
        }
        $cmcount++;
    }
}
function addlevelbage($memo, $tokencode, $getcmlist = array(), $mbrstr = array()) {
    global $db, $cfgrow, $bpprow;

    // require_once(INSTALL_PATH . '/common/mailer.do.php');
    $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

    $cmcount = 1;
    foreach ((array) $getcmlist as $key => $value) {
        $sprstr = getmbrinfo('', '', $key);
        if ($key > 0 ) {
            $data = array(
                'level_bage' => $value,
            );
            $insert = $db->update(DB_TBLPREFIX . '_mbrs', $data,array('id' => $sprstr['id']));
        }
        $cmcount++;
    }
}

function dolvldone($mbrstr, $mppid = 1) {
    global $db, $cfgrow, $bpprow;

    for ($i = 1; $i <= $mbrstr['mpdepth']; $i++) {
        $mpid = getsprlistid($i, $mbrstr['sprlist']);
        if ($mpid < 1) {
            break;
        } else {
            $sprtag = "|{$i}:{$mpid}|";
            $condition = " AND sprlist LIKE '%{$sprtag}%' AND mpstatus != '0'";
            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
            $myreftotal = $row[0]['totref'];

            $ix = $i;
            if (pow($mbrstr['mpwidth'], $ix) == $myreftotal) {
                $sprstr = getmbrinfo('', '', $mpid);
                $condition = ' AND txtoid = "' . $sprstr['id'] . '" AND txppid = "' . $mppid . '" AND txtoken LIKE "' . "%|LCM:FRWD{$ix}|%" . '" ';
                $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 " . $condition . "");
                if (count($sql) < 1) {
                    $iy = $ix - 1;
                    $rwlistarr = explode(',', str_replace(' ', '', $bpprow['rwlist']));
                    $getcmlist = array($sprstr['mpid'] => $rwlistarr[$iy]);  

                    //Level bage
                    $lbglistarr = explode(',', str_replace(' ', '', $bpprow['level_bages']));
                    $lbggetcmlist = array($sprstr['mpid'] => $lbglistarr[$iy]);
                    
                    addcmlist("Level Reward", "FRWD{$ix}", $getcmlist, $mbrstr);
                    addlevelbage("Level Bage", "FRWD{$ix}", $lbggetcmlist, $mbrstr);
                }
            }
        }
    }
}

function regmbrplans($mbrstr = array(), $refmpid = 0, $ppid = 1,$package="",$package_type="") {
    global $db, $cfgrow, $bpprow;
    $regPackege=$package;
    $resultarr = array();
    $refstr = getmbrinfo('', '', $refmpid);

    $mppid = intval($ppid);
    $idref = intval($refstr['id']);
    $idmbr = $mbrstr['id'];

    $condition = ' AND idmbr = "' . $idmbr . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition . "");
    if ($bpprow['planstatus'] == 1 && count($sql) < 1) {
        $reg_date = date('Y-m-d', time() + (3600 * $cfgrow['time_offset']));
        $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $reg_ip = get_userip();

        $mpstatus = ($regPackege <= 0) ? 1 : 0;
        $reg_expd = $reg_date;

        $expday = floatval($bpprow['expday']);
        if ($expday > 0) {
            $expdarr = get_actdate($bpprow['expday']);
            $reg_expd = $expdarr['next'];
        }

        $rprmpid = getmpidflow($refmpid);
        $sprstr = getmbrinfo('', '', $rprmpid);
        $idspr = intval($sprstr['id']);

        $sprlist = dosprlist($sprstr['mpid'], $sprstr['sprlist'], $sprstr['mpdepth']);

        $hostspr = 0;
        $idhostmbr = 0;

        $data = array(
            'idhostmbr' => $idhostmbr,
            'idmbr' => $idmbr,
            'mppid' => $mppid,
            'isdefault' => 1,
            'reg_date' => $reg_date,
            'reg_expd' => $reg_expd,
            'reg_utctime' => $reg_utctime,
            'reg_ip' => $reg_ip,
            // 'reg_fee' => $bpprow['regfee'],
            'reg_fee' => $regPackege,
            'package' => $package_type,
            'mpstatus' => $mpstatus,
            'hostspr' => $hostspr,
            'idref' => $idref,
            'idspr' => $idspr,
            'sprlist' => $sprlist,
            'mpwidth' => $bpprow['maxwidth'],
            'mpdepth' => $bpprow['maxdepth'],
        );

        // echo "<pre>";print_r($data);exit;
        $insert = $db->insert(DB_TBLPREFIX . '_mbrplans', $data);
        $newmbrplanid = $db->lastInsertId();
        $resultarr['mpid'] = $newmbrplanid;

        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Registration processed successfully!', 'Success');";

            // add transaction records
            if ($bpprow['regfee'] > 0) {
                $data = array(
                    'txdatetm' => $reg_utctime,
                    'txfromid' => $idmbr,
                    // 'txamount' => $bpprow['regfee'],
                    'txamount' => $regPackege,
                    'txmemo' => 'Registration fee',
                    'txppid' => $mppid,
                    'txtoken' => "|REG:$newmbrplanid|",
                );
                $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);
                $newtrxid = $db->lastInsertId();
                $resultarr['txid'] = $newtrxid;
            }

            // send new referral signup
            if ($idspr > 0) {
                // require_once(INSTALL_PATH . '/common/mailer.do.php');
                $cntaddarr['dln_fullname'] = $mbrstr['firstname'] . " " . $mbrstr['lastname'];
                $cntaddarr['dln_username'] = $mbrstr['username'];
                // delivermail('mbr_newdl', $idspr, $cntaddarr);
            }
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Registration processed failed. <strong>Please try again!</strong>', 'Warning');";
        }

        return $resultarr;
    }
}

function iscontentmbr($options, $mbrstr) {
    $hasil = true;
    $avalfor = get_optionvals($options);

    if ($avalfor['mbr'] == 1) {
        if ($avalfor['mbpp1'] != 1 && $mbrstr['mpstatus'] == 1) {
            $hasil = false;
        }
        if ($avalfor['mbpp0'] != 1 && $mbrstr['mpstatus'] != 1) {
            $hasil = false;
        }
    }
    return $hasil;
}

function dotrxwallet($limit = 25) {
    global $db, $cfgrow, $bpprow;

    $ListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE 1 AND txfromid = '0' AND txtoid > '0' AND txstatus = '0' AND txtoken NOT LIKE '%|WIDR:%' LIMIT {$limit}");
    if (count($ListData) > 0) {
        $numcount = $ewallet = 0;
        $txtmstamp = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        foreach ($ListData as $val) {

            $txbatch = 'WLN' . date("mdH-is");
            $txtoken = $val['txtoken'] . ', |WALT:IN|';

            $data = array(
                'txpaytype' => 'system',
                'txbatch' => $txbatch,
                'txtmstamp' => $txtmstamp,
                'txtoken' => $txtoken,
                'txstatus' => 1,
            );
            $update = $db->update(DB_TBLPREFIX . '_transactions', $data, array('txid' => $val['txid']));
               // Remove comma(s) from the string
            $x = str_replace( ',', '', $val['txamount']);
            $mbrstr = getmbrinfo($val['txtoid']);
            $ewallet = $mbrstr['ewallet'] + $x;

            // $mbrstr = getmbrinfo($val['txtoid']);
            // $ewallet = $mbrstr['ewallet'] + $val['txamount'];
            $update = $db->update(DB_TBLPREFIX . '_mbrs', array('ewallet' => $ewallet), array('id' => $mbrstr['id']));

            $numcount++;
            if ($numcount < 1) {
                break;
            }
        }
    }
}

function adjusttrxwallet($oldamount, $newamount, $idmbr, $txtokenstr = '', $txadminfo = '') {
    global $db, $cfgrow, $bpprow;

    if ($oldamount != $newamount) {
        $txbatch = date("mdH-is");
        if ($oldamount < $newamount) {
            // add
            $txfromid = 0;
            $txtoid = $idmbr;
            $txamount = $newamount - $oldamount;
            $txmemo = "Wallet Credit Correction";
            $txbatch = 'WLN' . $txbatch;
            $txtoken = '|WALT:IN|';
        } else {
            // deduct
            $txfromid = $idmbr;
            $txtoid = 0;
            $txamount = $oldamount - $newamount;
            $txmemo = "Wallet Debit Correction";
            $txbatch = 'WLT' . $txbatch;
            $txtoken = '|WALT:OUT|';
        }

        $txtoken64 = base64_encode($txtokenstr);
        $txtoken = $txtoken . ", |NOTE:{$txtoken64}|";

        $txdatetm = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));
        $data = array(
            'txdatetm' => $txdatetm,
            'txfromid' => $txfromid,
            'txtoid' => $txtoid,
            'txpaytype' => 'system',
            'txamount' => $txamount,
            'txmemo' => $txmemo,
            'txbatch' => $txbatch,
            'txtmstamp' => $txdatetm,
            'txppid' => $mppid,
            'txstatus' => 1,
            'txtoken' => $txtoken,
            'txadminfo' => $txadminfo,
        );
        $insert = $db->insert(DB_TBLPREFIX . '_transactions', $data);
    }
}

function getmpidflow($mpid) {
    global $db, $cfgrow, $bpprow;

    if (intval($mpid) < 1) {
        return 0;
        exit;
    }

    $sprstr = getmbrinfo('', '', $mpid);
    $maxwideexd = $sprstr['mpwidth'];
    $maxdeepexd = $sprstr['mpdepth'] * 2;

    if ($maxwideexd < 1 || $maxdeepexd < 1) {
        return $mpid;
        exit;
    }

    $mysprlist = "|1:" . $mpid . "|";
    $condition = " AND sprlist LIKE '%{$mysprlist}%'";
    $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $condition);
    $total = intval($row[0]['totref']);

    if ($total >= $sprstr['mpwidth']) {
        $filterstatus = " AND (mpstatus = '1' OR mpstatus = '2')";
        if ($bpprow['spillover'] == 1) {
            $count_subrefsql = ", (SELECT COUNT(*) FROM " . DB_TBLPREFIX . "_mbrplans WHERE idspr = ovrid) as totsubref ";
            $ordby = 'totsubref ASC, reg_utctime ASC, mpid DESC';
        } else {
            $count_subrefsql = '';
            $ordby = 'reg_utctime ASC, mpid DESC, idmbr ASC';
        }

        for ($i = 1; $i <= $maxdeepexd; $i++) {
            $tmpmpid = array();
            $mpidx = "";

            $directsprlist = "|" . $i . ":" . $mpid . "|";
            $condition = " AND sprlist LIKE '%{$directsprlist}%'" . $filterstatus;
            $userData = $db->getRecFrmQry("SELECT mpid as ovrid {$count_subrefsql} FROM " . DB_TBLPREFIX . "_mbrplans WHERE 1 " . $condition . " ORDER BY " . $ordby);
            if (count($userData) > 0) {
                foreach ($userData as $val) {
                    $mpidx = $val['ovrid'];
                    $tmpmpid[] = $mpidx;

                    $subsprlist = "|1:" . $mpidx . "|";
                    $subcondition = " AND sprlist LIKE '%{$subsprlist}%'" . $filterstatus;
                    $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $subcondition);
                    $myreftotal = $row[0]['totref'];

                    if ($myreftotal < $sprstr['mpwidth'] && $mpidx > 0) {
                        if ($bpprow['minref4splovr'] > 0) {
                            $sprrow = getmbrinfo('', '', $mpidx);
                            $refcondition = " AND idref = '{$sprrow['id']}'" . $filterstatus;
                            $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $refcondition);
                            $myperdltotal = $row[0]['totref'];
                            if ($myperdltotal < $bpprow['minref4splovr']) {
                                continue;
                            }
                        }
                        return $mpidx;
                        exit;
                    }
                }
            }
        }

        if ($bpprow['ifrollupto'] == 1) {
            foreach ((array) $tmpmpid as $key => $val) {
                $subsprlist = "|1:" . $val . "|";
                $subcondition = " AND sprlist LIKE '%{$subsprlist}%'" . $filterstatus;
                $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $subcondition);
                $myreftotal = $row[0]['totref'];
                if ($myreftotal < $sprstr['mpwidth']) {
                    if ($bpprow['minref4splovr'] > 0) {
                        $sprrow = getmbrinfo('', '', $val);
                        $refcondition = " AND idref = '{$sprrow['id']}'" . $filterstatus;
                        $row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', 'COUNT(*) as totref', $refcondition);
                        $myperdltotal = $row[0]['totref'];
                        if ($myperdltotal < $bpprow['minref4splovr']) {
                            continue;
                        }
                    }
                    $mpidx = getmpidflow($val);
                    if ($mpidx > 0) {
                        return $mpidx;
                        exit;
                    }
                }
            }
        } else {
            return 0;
        }
    } else {
        return $mpid;
    }
}

function getwebssdata($mbrstr, $url) {
    $mbrid = $mbrstr['id'];
    if (function_exists('curl_init') && intval($mbrid) > 0 && filter_var($url, FILTER_VALIDATE_URL) !== FALSE && $_SESSION['getwebssdata' . $mbrid] == '') {
        $ch = curl_init("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url={$url}&screenshot=true");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $googlepsdata = json_decode($response, true);
        $snap = $googlepsdata['screenshot']['data'];
        $snap = str_replace(['_', '-'], ['/', '+'], $snap);

        if ($snap) {
            $imgtofile = "/assets/imagextra/mbr_imgsrc_{$mbrid}.dat";
            $datfile = INSTALL_PATH . $imgtofile;
            file_put_contents($datfile, $snap, LOCK_EX);
            $_SESSION['getwebssdata' . $mbrid] = 1;
            return $imgtofile;
        }
    }
}

function getdocurl($initurl, $arrdata) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $initurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrdata, '', '&'));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $arrResponse['err'] = curl_error($ch);
    }
    curl_close($ch);
    $arrResponse['data'] = json_decode($response, true);
    return $arrResponse;
}

function dumbtoken($readtoken = '', $expt = 5) {
    if (time() > $_SESSION['dumbtokenexp']) {
        $_SESSION['dumbtokenexp'] = $_SESSION['dumbtoken'] = '';
    }

    if ($readtoken == '') {
        if ($_SESSION['dumbtoken'] != '') {
            return $_SESSION['dumbtoken'];
        } else {
            // 1 hour = 60 seconds * 60 minutes = 3600
            $_SESSION['dumbtokenexp'] = time() + (60 * $expt);
            $_SESSION['dumbtoken'] = bin2hex(openssl_random_pseudo_bytes(24));
            return $_SESSION['dumbtoken'];
        }
    } else {
        if ($_SESSION['dumbtoken'] == $readtoken && time() <= $_SESSION['dumbtokenexp']) {
            return true;
        } else {
            return false;
        }
    }
}

function do_imgresize($targetFile, $originalFile, $newWidth, $newHeight = 0, $ext = '') {

    $info = getimagesize($originalFile);
    $mime = ($ext == '') ? $info['mime'] : "image/{$ext}";

    switch ($mime) {
        case 'image/jpeg':
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/png':
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/gif':
            $image_save_func = 'imagegif';
            $new_image_ext = 'gif';
            break;

        default:
            exit();
    }

    $img = imagecreatefromstring(file_get_contents($originalFile));
    list($width, $height) = getimagesize($originalFile);

    $propHeight = ($height / $width) * $newWidth;
    $newHeight = ($newHeight > 0) ? $newHeight : $propHeight;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $targetFile = '../assets/imagextra/' . $targetFile;

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $newimg = "$targetFile.$new_image_ext";
    $image_save_func($tmp, $newimg);
    return $newimg;
}

/* usage example:
  $resultdate = get_actdate($intvdatetime, $basedate);
  $resultdate['var'] = $intvdatetime type ('H', 'D', 'W', 'M', 'Y', ''=in days)
  $resultdate['var_str'] = $intvdatetime type ('Hour', 'Day', 'Week', 'Month', 'Year', ''=in days)
  $resultdate['val'] = value from the $intvdatetime, example 10 -> 10, 12w -> 12, 4m -> 4, etc;
  $resultdate['val_str'] = value from the $intvdatetime in days, example 10 -> 10, 23h -> 0, 5d -> 5, 2w -> 14, 1m -> 30, etc;
  $resultdate['next'] = $basedate + $intvdatetime;
  $resultdate['prev'] = $basedate - $intvdatetime;
  $resultdate['now'] = $basedate;
  $resultdate['diffdays'] = different (in days) between $basedate and $resultdate['next'];
 */

function get_actdate($intvdatetime, $basedate = '') {
    global $cfgrow;

    $basedate = ($basedate == '') ? date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset'])) : $basedate;
    $arrdate = getdate(strtotime($basedate));
    $istime = (strlen($basedate) > 12 && $arrdate['hours'] != '') ? 'y' : 'n';

    $result = array();
    $intvdatetime = str_replace(" ", "", strtoupper($intvdatetime));
    if (!is_numeric($intvdatetime)) {
        $result['var'] = substr($intvdatetime, -1);
        $result['val'] = str_replace($result['var'], "", $intvdatetime);
        $result['val'] = intval($result['val']);

        switch ($result['var']) {
            case "H":
                $result['var_str'] = 'Hour';
                $result['val_str'] = $result['val'] * 0;
                $strjng = 'hour';
                break;
            case "W":
                $result['var_str'] = 'Week';
                $result['val_str'] = $result['val'] * 7;
                $strjng = 'week';
                break;
            case "M":
                $result['var_str'] = 'Month';
                $result['val_str'] = $result['val'] * 30;
                $strjng = 'month';
                break;
            case "Y":
                $result['var_str'] = 'Year';
                $result['val_str'] = $result['val'] * 365;
                $strjng = 'year';
                break;
            default:
                $result['var_str'] = 'Day';
                $result['val_str'] = $result['val'];
                $strjng = 'day';
        }

        if ($result['val'] > 1)
            $strjng .= 's';
    } else {
        $result['var'] = 'D';
        $result['var_str'] = 'Day';
        $strjng = 'day';
        $result['val'] = $result['val_str'] = intval($intvdatetime);
        if ($result['val'] > 1)
            $strjng .= 's';
    }

    $str_basedate = strtotime($basedate);
    $str_diffdate = $result['val'] . ' ' . $strjng;
    $str_basedate_add = strtotime("+" . $str_diffdate, $str_basedate);
    $str_basedate_les = strtotime("-" . $str_diffdate, $str_basedate);

    if ($istime == 'y') {
        $result['next'] = date("Y-m-d H:i:s", $str_basedate_add);
        $result['prev'] = date("Y-m-d H:i:s", $str_basedate_les);
    } else {
        $result['next'] = date("Y-m-d", $str_basedate_add);
        $result['prev'] = date("Y-m-d", $str_basedate_les);
    }

    $result['now'] = $basedate;
    $dateTimeEnd = $result['next'];
    $dateTimeBegin = $result['now'];

    $timedifference = strtotime($dateTimeEnd) - strtotime($dateTimeBegin);
    $result['diffdays'] = floor($timedifference / 86400);

    return $result;
}

function get_unpaidtxid($mbrstr) {
    global $db;

    $txunpaidrow = $db->getRecFrmQry("SELECT txid FROM " . DB_TBLPREFIX . "_transactions WHERE txfromid = '{$mbrstr['id']}' AND txppid = '{$mbrstr['mppid']}' AND txtoken LIKE '%|RENEW:%' AND txamount > 0 AND txstatus = '0'");
    return $txunpaidrow[0]['txid'];
}

function do_expmbr($limitcheck = 48) {
    global $db, $cfgrow, $bpprow;

    $expday = floatval($bpprow['expday']);
    $graceday = floatval($bpprow['graceday']);
    if ($expday > 0) {
        $reg_prev = date('Y-m-d', time() - (3600 * 24 * 7) + (3600 * $cfgrow['time_offset']));
        $grace_prev = date('Y-m-d', strtotime('-' . $graceday . ' day', strtotime($reg_prev)));
        $reg_utctime = date('Y-m-d H:i:s', time() + (3600 * $cfgrow['time_offset']));

        $condition = " AND mpstatus = '1' AND reg_expd < '{$reg_prev}' LIMIT {$limitcheck}";
        $userData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_mbrs LEFT JOIN " . DB_TBLPREFIX . "_mbrplans ON id = idmbr WHERE 1 " . $condition . "");
        if (count($userData) > 0) {
            foreach ($userData as $val) {
                $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_transactions WHERE txfromid = '{$val['id']}' AND txppid = '{$val['mppid']}' AND txtoken LIKE '%|PREVEXP:{$val['reg_expd']}|%'");
                if ($val['reg_fee'] > 0 && count($sql) < 1) {
                    $data = array(
                        'txdatetm' => $reg_utctime,
                        'txfromid' => $val['id'],
                        'txamount' => $val['reg_fee'],
                        'txmemo' => 'Renewal fee',
                        'txppid' => $val['mppid'],
                        'txtoken' => "|RENEW:{$val['mpid']}|, |PREVEXP:{$val['reg_expd']}|",
                    );
                    $db->insert(DB_TBLPREFIX . '_transactions', $data);
                }
                if ($graceday > 0 && $val['reg_expd'] < $grace_prev && $val['reg_date'] < $val['reg_expd'] && $val['reg_fee'] > 0) {
                    $db->update(DB_TBLPREFIX . '_mbrplans', array('mpstatus' => 2), array('mpid' => $val['mpid']));
                }
            }
        }
    }
}
