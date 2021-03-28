<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$admin_content = <<<INI_HTML
<div class="section-header">
    <h1><i class="fa fa-fw fa-exclamation-triangle text-danger"></i> {$LANG['g_pagenotfound']}</h1>
</div>

<div class="section-body">
    <a href="index.php?hal=dashboard" class="btn btn-info">{$LANG['g_continue']} <i class="fa fa-fw fa-undo"></i></a>
</div>
INI_HTML;
echo myvalidate($admin_content);
