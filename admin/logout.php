<?php

include_once('../common/init.loader.php');

dellog_sess('admin');
$_SESSION['show_msg'] = showalert('success', 'Bye...', $LANG['g_successlogout']);
redirpageto('login.php');
exit;
