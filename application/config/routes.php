<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['recheck'] = 'Run_advance/recheck';
$route['preprocess'] = 'Run_advance/get_json';
$route['chkpreprocess'] = 'Run_advance/check_run';
$route['readcount'] = 'Run_advance/read_count';
$route['subsample'] = 'Run_advance/run_sub_sample';
$route['chksample'] = 'Run_advance/check_subsample';
$route['analysis'] = 'Run_advance/run_analysis';
$route['chkanalysis']='Run_advance/check_analysis';
$route['checkdesign/(:any)/(:any)'] = 'Run_advance/check_file_design/$1/$2';
$route['checkmetadata/(:any)/(:any)'] = 'Run_advance/check_file_metadata/$1/$2';
$route['upfasta'] = 'Run_advance/check_fasta';
$route['ckprorun'] = 'Run_advance/chk_data_project_run';
$route['chkdir'] = 'Run_advance/check_dirzip';
$route['showimg/(:any)'] = 'Run_advance/on_showimg/$1';
$route['createdesign/(:any)'] ='Run_advance/create_file_design/$1';
$route['createmetadata/(:any)'] ='Run_advance/create_file_metadata/$1';
$route['dowfile/(:any)'] = 'Run_advance/down_zip/$1';
$route['wrtdesign/(:any)/(:any)'] = 'Run_advance/write_design/$1/$2';
$route['wrtmetadata/(:any)/(:any)'] = 'Run_advance/write_metadata/$1/$2';


$route['aum'] = 'Run_owncloud/ex_string';