<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$getstartstr = base64_decode($cfgrow['getstart']);
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-flag-checkered"></i> <?php echo myvalidate($LANG['m_getstarted']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <?php echo (strip_tags($getstartstr) != '') ? $getstartstr : '<i class="fa fa-question-circle fa-fw text-danger"></i> Nothing here to see! Please contact us for assistance.'; ?>
                </div>

            </div>
        </div>
    </div>
</div>