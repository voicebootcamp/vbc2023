<?php
/*------------------------------------------------------------------------
# osappschedule.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
define('DS',DIRECTORY_SEPARATOR);
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
$document = JFactory::getDocument();
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/omnipay.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/payment.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payment.php';
require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payments.php';
require_once JPATH_COMPONENT."/helpers/pane.php";
require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/helper.php";
OSBHelper::loadMedia();
jimport('joomla.html.parameter');
jimport('joomla.filesystem.folder');
//Include files from classes folder
$dir = JFolder::files(JPATH_COMPONENT."/classes");
if(count($dir) > 0)
{
	for($i=0;$i<count($dir);$i++)
	{
		require_once(JPATH_COMPONENT."/classes".DS.$dir[$i]);
	}
}

$dir = JFolder::files(JPATH_COMPONENT."/helpers");
if(count($dir) > 0){
	for($i=0;$i<count($dir);$i++){
		if($dir[$i]!= "ipn_log.txt"){
			require_once(JPATH_COMPONENT."/helpers".DS.$dir[$i]);
		}
	}
}

global $_jversion,$configs,$configClass,$symbol,$mainframe,$lang_suffix,$languages,$jinput;
$languages = OSBHelper::getLanguages();
$mainframe = JFactory::getApplication();
$jinput = $mainframe->input;
$db = JFactory::getDBO();
$configClass = OSBHelper::loadConfig();
$configClass['root_link'] = JUri::root();
$user = JFactory::getUser();

if(($user->id > 0) and ($configClass['pass_captcha'] == 1)){
	$configClass['value_sch_include_captcha'] = 1;
}
if(($user->id > 0) and ($configClass['group_payment'] > 0)){
	$db->setQuery("Select count(user_id) from #__user_usergroup_map where user_id = '$user->id' and group_id = '".$configClass['group_payment']."'");
	$count = $db->loadResult();
	if($count > 0){
		$configClass['disable_payments'] = 0;
	}
}
$translatable = JLanguageMultilang::isEnabled() && count($languages);
if($translatable){
	//generate the suffix
	$lang_suffix = OSBHelper::getFieldSuffix();
}

//setup cookie
$session = JFactory::getSession();
if($unique_cookie == ""){
	$unique_cookie = $session->get( 'unique_cookie', '' );
	if($unique_cookie == ""){
		$unique_cookie = md5(rand(1000,9999));
		@setcookie('unique_cookie',$unique_cookie,time()+3600);
	}else{
		@setcookie('unique_cookie',$unique_cookie,time()+3600);
	}
}
$session->set( 'unique_cookie', $unique_cookie );

$date_from = $jinput->get('date_from','','string');
$date_to = $jinput->get('date_to','','string');
if($date_from != ""){
	$date_from = explode(" ",$date_from);
	$jinput->set('date_from',$date_from[0]);
}
if($date_to != ""){
	$date_to = explode(" ",$date_to);
	$jinput->set('date_to',$date_to[0]);
}

$select_date = $jinput->get('selected_date','','string');

if($select_date != ""){
	$date_from = $select_date;
	$date_to   = $select_date;
	$jinput->set('date_from',$select_date);
	$jinput->set('date_to',$select_date);
}


//@setcookie('unique_cookie',$unique_cookie,time()+3600);
$config = new JConfig();
$offset = $config->offset;
date_default_timezone_set($offset);
$task = $jinput->get('task','','string');
if($task == ""){
	$view = $jinput->get('view','','string');
	switch ($view){
		case "checkin":
			$task = "default_qrscan";
		break;
		case "listemployee":
			$task = "default_allemployees";
		break;
		case "venue":
			$task = "venue_listing";
		break;
		case "category":
			$task = "category_listing";
		break;
		case "employee":
			$task = "default_employeeworks";
		break;
		case "customer":
			$task = "default_customer";
		break;
        case "services":
            $task = "service_listing";
        break;
        case "allitems":
            $task = "service_listallitems";
        break;
		case "manageallorders":
			$task = "manage_orders";
		break;
		case "calendarview":
			$task = "service_calendarview";
		break;
		case "customerbalances":
			$task = "default_balances";
		break;
		case "employeesetting":
			$task = "default_employeesetting";
		break;
		case "monthlycalendar":
			$task = "calendar_monthly";
		break;
		default:
			$task = "default_layout";
		break;
	}
}

OSBHelper::loadTooltip();
if (version_compare(JVERSION, '3.0', 'le')){
	OSBHelper::loadBootstrap();
}else{
	if($configClass['load_bootstrap'] == 1){
		OSBHelper::loadBootstrap();
	}
}
global $mapClass;
OSBHelper::generateBoostrapVariables();
OSBHelper::generateMapClassNames();
//2.3.6
$document->addStyleSheet("//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css");
// If no data found from session, using mobile detect class to detect the device type
global $deviceType;
$mobileDetect = new OSB_Mobile_Detect();
$deviceType   = 'desktop';
if ($mobileDetect->isMobile()){
	$deviceType = 'mobile';
}
if ($mobileDetect->isTablet()){
	$deviceType = 'tablet';
}

$header_style = $configClass['header_style'];
$header_style = trim(str_replace("btn","",$header_style));
if(trim($header_style) != ""){
	$header_style = str_replace("btn","",$header_style);
	$header_style = str_replace("-","",$header_style);
	if(trim($header_style) != ""){
		$document->addStyleSheet(JUri::root()."media/com_osservicesbooking/assets/css/tabstyle/".$header_style.".css");
	}
}
$blacklisttasks = array('ajax_getprofiledata','ajax_getprofileemployee','defaul_paymentconfirm','default_qrcodecheckin');
if(!in_array($task,$blacklisttasks))
{
	?>
	<script type="text/javascript" src="<?php echo '//code.jquery.com/ui/1.11.4/jquery-ui.js'; ?>"></script>
	<div id="dialogstr4" title="<?php echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART_TITLE');?>">
	</div>
	<?php
}
if($task != ""){
	$taskArr = explode("_",$task);
	$maintask = $taskArr[0];
}else{
	//cpanel
	$maintask = "";
}
switch ($maintask){
	case "venue":
		OSappscheduleVenueFnt::display($option,$task);
	break;
	case "calendar":
		OsAppscheduleCalendar::display($option,$task);
	break;
	case "ajax":
		OsAppscheduleAjax::display($option,$task);
	break;
	case "form":
		OsAppscheduleForm::display($option,$task);
	break;
	case "category":
		OSappscheduleCategory::display($option,$task);
	break;
    case "service":
        OSappscheduleService::display($option,$task);
    break;
	case "manage":
		$user = JFactory::getUser();
		if ((int)$user->id == 0 || !JFactory::getUser()->authorise('osservicesbooking.orders', 'com_osservicesbooking')) 
		{
			throw new Exception (JText::_('OS_YOU_DONT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}
		else
		{
			OSappscheduleManage::display($option,$task);
		}
	break;
	default:
		OsAppscheduleDefault::display($option,$task);
	break;
}
?>