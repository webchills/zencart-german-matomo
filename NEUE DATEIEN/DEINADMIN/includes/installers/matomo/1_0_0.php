<?php
/**
 * @package Matomo
 * @copyright Copyright 2021-2022 webchills (www.webchills.at)
 * @based on piwikecommerce 2012 by Stephan Miller
 * @copyright Copyright 2003-2022 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: 1_0_0.php 2021-02-12 15:11:51Z webchills $
 */
 
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Matomo'
LIMIT 1;");

$db->Execute("INSERT IGNORE INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES
('Matomo Location', 'MATOMO_URL', '', 'Enter the directory of your Matomo installation here.<br/>Omit http:// or https:// at the beginning!<br/>And also leave out a backslash / at the end!<br/>If your Matomo installation runs e.g. under https://www.mydomain.com/matomo/<br/>then enter exactly as follows:<br/>www.mydomain.com/matomo<br/>If your Matomo installation runs e.g. under https://matomo.mydomain.com<br/>then enter exactly as follows:<br/>matomo.mydomain.com', @gid, 1, NOW(), NULL, NULL),
('Matomo Site ID', 'MATOMO_ID', 1, 'Enter here the ID of the website you have created in your Matomo administration for your store', @gid, 2, NOW(), NULL, NULL),
('Matomo Report Period', 'MATOMO_REPORT_PERIOD', 'day', 'Enter the desired report period for your report. Options:day,week,month,year', @gid, 3, NOW(), NULL, NULL),
('Matomo Report Date', 'MATOMO_REPORT_DATE', 'yesterday', 'Enter the desired report day for your report. Options:today,yesterday', @gid, 4, NOW(), NULL, NULL),
('Matomo Token Auth', 'MATOMO_TOKEN_AUTH', '', 'Enter your Matomo token_auth. You have to create a new user in Matomo WITHOUT admin rights and generate a token for this user.', @gid, 5, NOW(), NULL, NULL);");

$db->Execute("REPLACE INTO ".TABLE_CONFIGURATION_LANGUAGE." (configuration_title, configuration_key, configuration_description, configuration_language_id) VALUES
('Matomo URL', 'MATOMO_URL', 'Geben Sie hier das Verzeichnis Ihrer Matomo Installation ein.<br/>Lassen Sie dabei http:// oder https:// am Anfang weg!<br/>Und lassen Sie auch einen Backslash / am Ende weg!<br/>Läuft Ihre Matomo Installation z.B. unter https://www.meinedomain.de/matomo/<br/>dann geben Sie exakt wie folgt an:<br/>www.meinedomain.de/matomo<br/>Läuft Ihre Matomo Installation z.B. unter https://matomo.meinedomain.de<br/>dann geben Sie exakt wie folgt an:<br/>matomo.meinedomain.de',	43),
('Matomo Site ID', 'MATOMO_ID', 'Geben Sie hier die ID der Website an, die Sie in Ihrer Matomo Administration für Ihren Shop angelegt haben',	43),
('Matomo Report Zeitraum', 'MATOMO_REPORT_PERIOD', 'Geben Sie hier den gewünschten Zeitraum für den Report an, der später in der Zen Cart Administration unter Statistiken > Matomo Reports standardmäßig ersichtlich sein wird.<br/>Optionen: Tag (day), Woche (week), Monat (month) oder Jahr (year)',	43),
('Matomo Report Datum', 'MATOMO_REPORT_DATE', 'Geben Sie hier den gewünschten Tag für den Report an, der später in der Zen Cart Administration unter Statistiken > Matomo Reports standardmäßig ersichtlich sein wird.<br/>Optionen: Heute (today) oder Gestern (yesterday)',	43),
('Matomo Token', 'MATOMO_TOKEN_AUTH', 'Geben Sie hier den Token ein, den Sie für den zusätzlichen Matomo User ohne Adminrechte angelegt haben.<br/>Dies ist erforderlich, damit das Matomo Dashboard unter Statistiken > Matomo Reports eingeblendet werden kann. ',	43);");

// delete old configuration/statistics menu
$admin_page = 'configMatomo';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
$admin_page_stats = 'statsMatomo';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page_stats . "' LIMIT 1;");
// add configuration/stats menu
if (!zen_page_key_exists($admin_page)) {
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Matomo'
LIMIT 1;");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('configMatomo','BOX_CONFIGURATION_MATOMO','FILENAME_CONFIGURATION',CONCAT('gID=',@gid),'configuration','Y',@gid)");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('statsMatomo','BOX_REPORTS_MATOMO','FILENAME_MATOMO','','reports','Y',101)");
$messageStack->add('Matomo Modul erfolgreich installiert.', 'success');  
}