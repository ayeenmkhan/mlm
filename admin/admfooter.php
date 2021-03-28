<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$dotoaster = $_SESSION['dotoaster'];
$_SESSION['dotoaster'] = '';

$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='#'>{$cfgtoken['site_subname']}</a>" : '';
$year= date('Y');
$admin_content = <<<INI_HTML
<footer class="main-footer">
    <div class="d-none d-sm-block footer-left">
        Crafted with <i class="fa fa-fw fa-heart"></i> copyright © systemX {$year} <div class="bullet"></div> {$site_subname}
    </div>
    <div class="footer-right text-sm-left">
        v{$cfgrow['softversion']}
    </div>
</footer>
</div>
</div>

        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->
        <script type="text/javascript">
        toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "preventDuplicates": true,
        "onclick": null
        }
        {$dotoaster}
        </script>

</body>
</html>
INI_HTML;
echo myvalidate($admin_content);
