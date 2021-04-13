<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$topmbrsopt = '';
$topmbrs = array();
$row = $db->getAllRecords(DB_TBLPREFIX . '_mbrplans', '*', ' AND idspr = "0"');
foreach ($row as $value) {
    $mbrstr = getmbrinfo($value['idmbr']);
    $isselected = ($FORM['loadId'] == $mbrstr['idmbr']) ? ' selected' : '';
    $topmbrsopt .= "<option value='{$mbrstr['idmbr']}'{$isselected}>{$mbrstr['id']}. {$mbrstr['firstname']} {$mbrstr['lastname']} ({$mbrstr['username']} - {$mbrstr['idmbr']})</option>";
}

$displaygen = ($FORM['loadId'] > 0) ? $displaygen = "<script type='text/javascript'>new Treant(chart_config);</script>" : '';
?>

<link rel="stylesheet" href="../assets/fellow/treant/Treant.css">
<link rel="stylesheet" href="../assets/fellow/treant/simple-scrollbar.css">
<link rel="stylesheet" href="../assets/fellow/treant/perfect-scrollbar.css">

<div class="section-header">
    <h1><i class="fa fa-fw fa-sitemap"></i> <?php echo myvalidate($LANG['m_genealogyview']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Member Genealogy</h4>
                </div>
                <div class="card-body">
                    <form method="get" action="index.php">
                        <input type="hidden" name="hal" value="genealogylist">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Frontend Member</span>
                                </div>
                                <select name='loadId' class="custom-select" id="inputGroupSelect04">
                                    <option selected>-</option>
                                    <?php echo myvalidate($topmbrsopt); ?>
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" type="button">Load</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="genchart" id="genviewer">
                        <div class="empty-state" data-height="400">
                            <div class="empty-state-icon bg-info">
                                <i class="fas fa-question"></i>
                            </div>
                            <h2>Sorry, we can't find any data</h2>
                            <p class="lead">
                                To get rid of this message, register a new member and select it from the above dropdown list.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/fellow/treant/raphael.js"></script>
<script src="../assets/fellow/treant/Treant.js"></script>
<script src="../assets/fellow/treant/jquery.mousewheel.js"></script>
<script src="../assets/fellow/treant/perfect-scrollbar.js"></script>
<script src="loadgentree.php?loadId=<?php echo myvalidate($FORM['loadId']); ?>"></script>

<?php echo myvalidate($displaygen); ?>