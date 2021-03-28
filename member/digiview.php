<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

if (isset($FORM['pgid'])) {
    $pgid = mystriptag($FORM['pgid']);
    $row = $db->getAllRecords(DB_TBLPREFIX . '_pages', '*', ' AND pgid = "' . $pgid . '"');
    $pgcntrow = array();
    foreach ($row as $value) {
        $pgcntrow = array_merge($pgcntrow, $value);
    }

    if (!iscontentmbr($pgcntrow['pgavalon'], $mbrstr) || $pgcntrow['pgstatus'] != 1) {
        $pgcntrow['pgtitle'] = "We couldn't find any data";
        $pgcntrow['pgsubtitle'] = $pgcntrow['pgcontent'] = '';
    } else {
        $pgcntrow['pgsubtitle'] = base64_decode($pgcntrow['pgsubtitle']);
        $pgcntrow['pgcontent'] = base64_decode($pgcntrow['pgcontent']);
    }
}

$msgListData = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 ");

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);

    $pgid = preg_replace('/[^\w-]/', '', $pgid);

    $pgavalon = put_optionvals($pgavalon, 'mbr0', intval($pgavalonmbr0));
    $pgavalon = put_optionvals($pgavalon, 'mbpp0', intval($pgavalonmbpp0));
    $pgavalon = put_optionvals($pgavalon, 'mbr1', intval($pgavalonmbr1));
    $pgavalon = put_optionvals($pgavalon, 'mbpp1', intval($pgavalonmbpp1));

    $data = array(
        'pgid' => $pgid,
        'pgmenu' => $pgmenu,
        'pgtitle' => $pgtitle,
        'pgsubtitle' => base64_encode($pgsubtitle),
        'pgcontent' => base64_encode($pgcontent),
        'pgavalon' => $pgavalon,
        'pgppids' => $pgppids,
        'pgstatus' => intval($pgstatus),
        'pgorder' => intval($pgorder),
    );

    $condition = ' AND pgid LIKE "' . $pgid . '" ';
    $sql = $db->getRecFrmQry("SELECT * FROM " . DB_TBLPREFIX . "_pages WHERE 1 " . $condition . "");
    if (count($sql) > 0) {
        $update = $db->update(DB_TBLPREFIX . '_pages', $data, array('pgid' => $pgid));
        if ($update) {
            $_SESSION['dotoaster'] = "toastr.success('Custom content updated successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.warning('You did not change anything!', 'Info');";
        }
    } else {
        $insert = $db->insert(DB_TBLPREFIX . '_pages', $data);
        if ($insert) {
            $_SESSION['dotoaster'] = "toastr.success('Custom content added successfully!', 'Success');";
        } else {
            $_SESSION['dotoaster'] = "toastr.error('Custom content not added <strong>Please try again!</strong>', 'Warning');";
        }
    }

    //header('location: index.php?hal=' . $hal);
    redirpageto("index.php?hal={$hal}&pgid={$pgid}");
    exit;
}

$noviewpage = <<<INI_HTML
                <div class="empty-state">
                    <div class="empty-state-icon bg-info">
                        <i class="fas fa-question"></i>
                    </div>
                    <h2>We couldn't find any page</h2>
                    <p class="lead">
                        Sorry, we can't find any content for you :(
                    </p>
                </div>
INI_HTML;
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-window-restore"></i> <?php echo myvalidate($LANG['a_digicontent']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4><?php echo myvalidate($LANG['g_content']); ?></h4>
                </div>
                <form method="get">
                    <div class="card-body">
                        <div class="form-group">
                            <?php
                            if (count($msgListData) > 0) {
                                $numpage = 0;
                                foreach ($msgListData as $val) {
                                    if (!iscontentmbr($val['pgavalon'], $mbrstr) || $val['pgstatus'] != 1) {
                                        continue;
                                    }
                                    $strsel = ($FORM['pgid'] == $val['pgid']) ? ' selected' : '';
                                    $pagelink = "index.php?hal=digiview&pgid={$val['pgid']}";
                                    ?>
                                    <button type="button" class="btn btn-info mt-2" onclick="location.href = '<?php echo myvalidate($pagelink); ?>'"><?php echo isset($val['pgmenu']) ? $val['pgmenu'] : '?'; ?></button>
                                    <?php
                                    $numpage++;
                                }
                                if ($numpage < 1) {
                                    echo "No Record(s) Found!";
                                } else {
                                    $noviewpage = '<i class="fa fa-fw fa-long-arrow-alt-left"></i> '.$LANG['m_clicklefttocnt'];
                                }
                            }
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <div class="card-header">
                    <h4><?php echo myvalidate($pgcntrow['pgtitle']); ?> </h4>
                    <span style="
    position: relative;left: 500px;">Posted Date: <?php  echo date('d/m/Y',strtotime($pgcntrow['date_time'])); ?></span>
                </div>

                <div class="card-body">
                    <p class="text-muted"><?php echo ($FORM['pgid'] != '') ? "<div class='section-title mt-2'>{$pgcntrow['pgsubtitle']}</div>" : $noviewpage; ?></p>

                    <?php
                    if ($FORM['pgid'] != '') {
                        echo isset($pgcntrow['pgcontent']) ? $pgcntrow['pgcontent'] : '';
                    }
                    ?>
                    <a href="<?php echo $pgcntrow['file_path']?>" target="_blank" >Open File</a>
                </div>

            </div>
        </div>
    </div>
</div>
