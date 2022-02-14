<?php
/**
 * @package Matomo
 * @copyright Copyright 2021-2022 webchills (www.webchills.at)
 * @based on piwikecommerce 2012 by Stephan Miller
 * @copyright Copyright 2003-2022 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: matomo.php 2022-02-14 11:11:40Z webchills $
 */

require('includes/application_top.php');

?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta charset="<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" href="includes/stylesheet.css">
<link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<style>
        html, body { 
            margin: 0; 
            padding: 0; 
            height: 100%; 
        }
        iframe {
            position: absolute;  
            height: 100%;
            width: 99%;
            border: none;
            box-sizing: border-box; 
            -moz-box-sizing: border-box; 
            -webkit-box-sizing: border-box;
        }        
</style>
<script src="includes/menu.js"></script>
<script src="includes/general.js"></script>
<script>
function init() {
cssjsmenu('navbar');
if (document.getElementById) {
var kill = document.getElementById('hoverJS');
kill.disabled = true;
}
}
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<div class="container-fluid">
<!-- body //-->
<table border="0" width="99%" cellspacing="2" cellpadding="2">
    <tr>
        <td colspan="7">
        <h1><?php echo HEADING_TITLE_MATOMO; ?></h1>
        </td>
    </tr>
    <tr>
        <td><iframe id="matomoframe" src="https://<?php echo MATOMO_URL; ?>/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite=<?php echo MATOMO_ID; ?>&period=<?php echo MATOMO_REPORT_PERIOD; ?>&date=<?php echo MATOMO_REPORT_DATE; ?>&token_auth=<?php echo MATOMO_TOKEN_AUTH; ?>" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="100%"></iframe></td>
    </tr>
</table>
<script language="javascript" src="includes/javascript/iframeResizer.min.js"></script>
</div>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>