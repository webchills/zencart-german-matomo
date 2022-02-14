<?php  
/**
* package Matomo
* @copyright Copyright 2021-2022 webchills (www.webchills.at)
* @based on piwikecommerce 2012 by Stephan Miller
* @copyright Copyright 2003-2021 Zen Cart Development Team
* Zen Cart German Version - www.zen-cart-pro.at
* @copyright Portions Copyright 2003 osCommerce
* @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
* @version $Id: matomo.php 2022-02-14 16:51:40Z webchills $
*/


// checked OK
function log_category($categories_id,$language_id = 0) {
global $db;
if ($language_id == 0) $language_id = $_SESSION['languages_id'];  
$matomocategories = $db->Execute("SELECT categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = " . (int)$categories_id . " AND language_id = " . (int)$language_id);
if ($matomocategories->EOF) return '';    
return '_paq.push([\'setEcommerceView\',productSku = false, productName = false, category = \''.str_replace(array('\'', '"'), '', $matomocategories->fields['categories_name']).'\']);' . "\n";
}

// checked OK
function log_product($products_id,$language_id = 0) {
global $db;      
if ($language_id == 0) $language_id = $_SESSION['languages_id'];  
$matomoproducts = $db->Execute("SELECT p.products_id, p.products_model, pd.products_name, cd.categories_name FROM " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, ". TABLE_CATEGORIES_DESCRIPTION ." cd WHERE p.products_id = pd.products_id AND p2c.categories_id = cd.categories_id AND p.products_id = " . (int)$products_id . " AND pd.language_id =".(int)$language_id." AND cd.language_id =".(int)$language_id);
if ($matomoproducts->EOF) return ''; 
if ($matomoproducts->fields['products_model'] !='') { 
return '_paq.push([\'setEcommerceView\', \''.$matomoproducts->fields['products_model'].'\', \''.str_replace(array('\'', '"'), '', $matomoproducts->fields['products_name']).'\', \''.str_replace(array('\'', '"'), '', $matomoproducts->fields['categories_name']).'\']);' . "\n";
}	else {
return '_paq.push([\'setEcommerceView\', \''.$matomoproducts->fields['products_id'].'\', \''.str_replace(array('\'', '"'), '', $matomoproducts->fields['products_name']).'\', \''.str_replace(array('\'', '"'), '', $matomoproducts->fields['categories_name']).'\']);' . "\n";
} 
} 
  

// checked OK - functionality ok, warning log Undefined variable $string 
function log_cart($matomoproducts,$total,$language_id = 0) {
global $db;  
$string = isset($string) ? $string : '';             
if ($language_id == 0) $language_id = $_SESSION['languages_id']; 
for ($i=0, $n=sizeof($matomoproducts); $i<$n; $i++) {
$matomocategories = $db->Execute("SELECT cd.categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION ." cd, ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE cd.categories_id = p2c.categories_id AND p2c.products_id = " . (int)$matomoproducts[$i]['id'] . " AND cd.language_id =".(int)$language_id);
if ($matomocategories->EOF) return ''; 

if ($matomoproducts[$i]['model'] != ''){ 
$string .= '_paq.push([\'addEcommerceItem\', \''.$matomoproducts[$i]['model'].'\',\''.str_replace(array('\'', '"'), '', $matomoproducts[$i]['name']).'\',\''.str_replace(array('\'', '"'), '', $matomocategories->fields['categories_name']).'\',\''.format_price($matomoproducts[$i]['final_price']).'\',\''.$matomoproducts[$i]['quantity'].'\']);' . "\n";
}	else {
$string .= '_paq.push([\'addEcommerceItem\', \''.$matomoproducts[$i]['id'].'\',\''.str_replace(array('\'', '"'), '', $matomoproducts[$i]['name']).'\',\''.str_replace(array('\'', '"'), '', $matomocategories->fields['categories_name']).'\',\''.format_price($matomoproducts[$i]['final_price']).'\',\''.$matomoproducts[$i]['quantity'].'\']);' . "\n";	
}
}
$string .= '_paq.push([\'trackEcommerceCartUpdate\',\''.format_price($total).'\']);' . "\n";
return $string;
} 

function log_order($insert_id,$order,$matomoproducts,$language_id = 0) {
global $db; 
$string = isset($string) ? $string : '';     
if ($language_id == 0) $language_id = $_SESSION['languages_id']; 
foreach ($matomoproducts as $p) {
if (!is_null($p['products_id'])) {    
$matomo_categories_query = "select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION ." cd, ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE cd.categories_id = p2c.categories_id and p2c.products_id = " . (int)$p['products_id'] . " and cd.language_id =".(int)$language_id;
$matomo_categories = $db->Execute($matomo_categories_query);
$matomo_order_product_query = "select products_id, products_model, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = " . (int)$insert_id . " and products_id = " . (int)$p['products_id'];
$matomo_order_product = $db->Execute($matomo_order_product_query);
if ($matomo_order_product->fields['products_model'] !='') {
$string .= '_paq.push([\'addEcommerceItem\', \''.$matomo_order_product->fields['products_model'].'\',\''.str_replace(array('\'', '"'), '', $p['products_name']).'\',\''.str_replace(array('\'', '"'), '', $matomo_categories->fields['categories_name']).'\',\''.(float)$matomo_order_product->fields['final_price'].'\',\''.$matomo_order_product->fields['products_quantity'].'\']);' . "\n";
} else {
$string .= '_paq.push([\'addEcommerceItem\', \''.$matomo_order_product->fields['products_id'].'\',\''.str_replace(array('\'', '"'), '', $p['products_name']).'\',\''.str_replace(array('\'', '"'), '', $matomo_categories->fields['categories_name']).'\',\''.(float)$matomo_order_product->fields['final_price'].'\',\''.$matomo_order_product->fields['products_quantity'].'\']);' . "\n";
}
}
}
$subtotal_result = $db->Execute("SELECT ROUND(value, 2) subtotal FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_subtotal' AND orders_id = ". $order->fields['orders_id']);
$subtotal = $subtotal_result->fields['subtotal'];
$shipping_result = $db->Execute("SELECT ROUND(value, 2) shipping FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_shipping' AND orders_id = ". $order->fields['orders_id']);
if ($shipping_result->RecordCount() > 0) {
$shipping = $shipping_result->fields['shipping'];
} else {
$shipping = 0.00;
}
$discount_result = $db->Execute("SELECT ROUND(value, 2) discount FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_coupon' AND orders_id = ". $order->fields['orders_id']);
if ($discount_result->RecordCount() > 0) {
$discount = $discount_result->fields['discount'];
} else {
$discount = 0.00;
}
$string .= '_paq.push([\'trackEcommerceOrder\',\''.$insert_id.'\',\''.format_price($order->fields['order_total']).'\',\''.format_price($subtotal).'\',\''.format_price($order->fields['order_tax']).'\',\''.format_price($shipping).'\',\''.format_price($discount).'\']);' . "\n";
return $string;	
} 

function format_price($price) {      
return number_format($price, 2, '.', '');
}

function log_custom_variable($index,$key,$value) {

return '_paq.push([\'setCustomVariable\',\''.$index.'\',\''.$key.'\',\''.$value.'","visit"]);' . "\n";
}