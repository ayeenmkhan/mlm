<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}
$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='#'>{$cfgtoken['site_subname']}</a>" : '';

$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <!--
                    You are not allowed to remove the UniMatrix link unless you have right to do so by own the Extended license or order the Branding Removal license at https://www.mlmscript.net/order
                    -->
                    <div class="text-small">Crafted with <i class="fa fa-fw fa-heart"></i> copyright © systemX {$thisyear} <div class="bullet"></div> {$site_subname}</div>
                </div>
</div>

        <!-- General JS Scripts -->
        <script src="../assets/js/jquery-3.4.1.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.nicescroll.min.js"></script>
        <script src="../assets/js/moment.min.js"></script>
        <script src="../assets/js/pace.min.js"></script>

        <!-- JS Libraies -->
        <script src="../assets/js/stisla.js"></script>

        <!-- Template JS File -->
        <script src="../assets/js/scripts.js"></script>
        <script src="../assets/js/custom.js"></script>

        <!-- Page Specific JS File -->

   </body>
</html>
INI_HTML;
echo myvalidate($page_content);
