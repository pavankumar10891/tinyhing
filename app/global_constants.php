<?php
/* Global constants for site */
define('FFMPEG_CONVERT_COMMAND', '');

define("ADMIN_FOLDER", "admin/");
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', base_path());
define('APP_PATH', app_path());

define("IMAGE_CONVERT_COMMAND", "");
define('WEBSITE_URL', url('/').'/');
define('WEBSITE_JS_URL', WEBSITE_URL . 'js/');
define('WEBSITE_CSS_URL', WEBSITE_URL . 'css/');
define('FRONT_WEBSITE_URL','');
define('WEBSITE_IMG_URL', WEBSITE_URL . 'public/frontend/images/');
define('WEBSITE_UPLOADS_ROOT_PATH', ROOT . DS . 'uploads' .DS );
define('WEBSITE_UPLOADS_URL', WEBSITE_URL . 'uploads/');
define('BLANK_IMAGE_URL', WEBSITE_URL . 'no-img.png');

define('WEBSITE_ADMIN_URL', WEBSITE_URL.ADMIN_FOLDER );
define('WEBSITE_ADMIN_IMG_URL', WEBSITE_ADMIN_URL . 'img/');
define('WEBSITE_ADMIN_JS_URL', WEBSITE_ADMIN_URL . 'js/');
define('WEBSITE_ADMIN_FONT_URL', WEBSITE_ADMIN_URL . 'fonts/');
define('WEBSITE_ADMIN_CSS_URL', WEBSITE_ADMIN_URL . 'css/');

define('SETTING_FILE_PATH', APP_PATH . DS . 'settings.php');

define('CK_EDITOR_URL', WEBSITE_UPLOADS_URL . 'ck_editor_images/');
define('CK_EDITOR_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'ck_editor_images' . DS); 

define('USER_IMAGE_URL', WEBSITE_UPLOADS_URL . 'users/');
define('USER_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'users' . DS);

define('USERAD_IMAGE_URL', WEBSITE_UPLOADS_URL.'userad_images/');
define('USERAD_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .'userad_images'.DS); 

define('MAINTAB_IMAGE_URL', WEBSITE_UPLOADS_URL . 'maintabs/');
define('MAINTAB_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .  'maintabs' . DS); 

define('LANGUAGE_IMAGE_URL', WEBSITE_UPLOADS_URL.'language_images/');
define('LANGUAGE_IMAGE_ROOT_PATH', WEBSITE_UPLOADS_ROOT_PATH .'language_images'.DS);
//////////////// extension 
define('IMAGE_EXTENSION','jpeg,jpg,png,gif,bmp');
define('IMAGE_EXTENSION_DOCUMENTS','jpeg,jpg,png,gif,bmp,pdf,docx,doc,xls,excel');
//URL validation
define('REGEX', '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/');
define('DEV_APP','dev');

$config	=	array();

define('ALLOWED_TAGS_XSS', '<a><strong><b><p><br><i><font><img><h1><h2><h3><h4><h5><h6><span><div><em><table><ul><li><section><thead><tbody><tr><td><figure><article>');

define('ADMIN_ID', 1);
define('SUPER_ADMIN_ROLE_ID', 1);
define('CUSTOMER_ROLE_ID', 2);
Config::set('default_language.folder_code', 'eng');
Config::set('default_language.language_code', '1');
Config::set('default_language.name', 'English');

Config::set("Site.currency", "&#2547;");
Config::set("Site.currencyCode", "$");

Config::set('per_page',array('15'=>'15','20'=>'20','30'=>'30','50'=>'50','100'=>'100'));

Config::set('gender', array('male' => 'Male', 'female' => 'Female'));
Config::set('anrede', array('1' => 'Mr.', '2' => 'Mis', '3'=>'Mrs.'));
Config::set('notification', array(1 => 'Anzeigenstatus', 2 => 'Anzeige endet bald', 3=>'Neue Inserate von "Folge ich"', 4 => 'Neue Inserate in der Region', 5=>'Newsletter'));

define('ACTIVE', 1);
define('INACTIVE', 0);
define('GOOGLE_MAP_API_KEY','AIzaSyCcHmIl7TxP_aefOxG9FxOMWPz2DQYk7hI');
/**Masters */
define('TYPE_CITY','city');
define('TYPE_REGION','region');
define('TYPE_POST_CODE','post_code');

Config::set('seo_type_dropdown',array(
    TYPE_CITY=>'City',
    TYPE_REGION=>'Region',
    TYPE_POST_CODE=>'Post Code',
));
Config::set("Reading.date_format",'m-d-Y');
Config::set("Site.from_email",'owebest01@gmail.com');
Config::set("Site.email",'owebest01@gmail.com');
