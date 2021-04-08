<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}
$thisyear = date("Y");
$site_subname = ($cfgtoken['site_subname'] != '') ? "<a href='#'>{$cfgtoken['site_subname']}</a>" : '';

$page_content = <<<INI_HTML
                <div class="simple-footer">
                    <div> <div class="bullet"></div> <a target="_blank" 
href="https://www.termsandconditionsgenerator.com/live.php?token=fTOviCHzZmyxCB2zm9G7abLE3jjcOsYO" 
>Terms&Conditions</a> | <a href="https://www.privacypolicygenerator.info/live.php?token=phtCcnc793UoOr5hCtjq6a0qfznCQPv9" target="_blank"> Privacy Policy 
</a></div>
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
