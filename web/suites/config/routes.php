<?php  defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CMS module
 *
 */
// public
$route['report/sitemap.xml'] = 'sitemap/xml';
$route['report/report(/:any)?'] = 'api/load$1';
$route['report(/:any)?'] = 'Suites/modules/report/controllers/api/index$1';
?>