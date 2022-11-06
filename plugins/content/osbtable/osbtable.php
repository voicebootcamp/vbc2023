<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
class plgContentOSBTable extends JPlugin
{

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param object $params  The object that holds the plugin parameters
     * @since 1.5
     */
    //function plgContentOSBTable(&$subject, $params)
    //{
        //parent::__construct($subject, $params);
    //}

    /**
     * Method is called by the view
     *
     * @param    object        The article object.  Note $article->text is also available
     * @param    object        The article params
     * @param    int           The 'page' number
     */
    function onContentPrepare($context, &$article, &$params, $limitstart)
    {
		global $mainframe;
        error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR);
        $mainframe = JFactory::getApplication();
        if ($mainframe->getName() != 'site') {
            return true;
        }
        if (strpos($article->text, 'osbtable') === false) {
            return true;
        }
        $regex = "#{osbtable (.*)}#s";
        preg_match($regex,$article->text,$matches);
       // print_r($matches);
       // die();
        $article->text = preg_replace_callback($regex, array(&$this, '_replaceOSBTable'), $article->text);
        return true;
    }

    /**
     * Replace the text with the event detail
     *
     * @param array $matches
     */
    function _replaceOSBTable(&$matches)
    {
    	global $configClass,$jinput,$mainframe;
    	$text		= "";
        $mainframe	= JFactory::getApplication();
		$jinput		= $mainframe->input;
        $Itemid		= $jinput->getInt('Itemid');        
        require_once(JPATH_ROOT . '/administrator/components/com_osservicesbooking/helpers/helper.php');
       
		jimport('joomla.html.parameter');
		jimport('joomla.filesystem.folder');
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
		$document = JFactory::getDocument();
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/ajax.js");
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/javascript.js");
		$document->addScript(JURI::root()."media/com_osservicesbooking/assets/js/paymentmethods.js");
	    $dir = JFolder::files(JPATH_ROOT."/components/com_osservicesbooking/classes");
	    $document->addStyleSheet(JURI::root()."media/com_osservicesbooking/assets/css/style.css");
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/omnipay.php';
		require_once JPATH_ROOT.'/components/com_osservicesbooking/helpers/payment/payment.php';
		require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payment.php';
		require_once JPATH_ROOT.'/components/com_osservicesbooking/plugins/os_payments.php';
		if(count($dir) > 0){
			for($i=0;$i<count($dir);$i++){
				require_once(JPATH_ROOT."/components/com_osservicesbooking/classes/".$dir[$i]);
			}
		}
		
		$dir = JFolder::files(JPATH_ROOT."/components/com_osservicesbooking/helpers");
		if(count($dir) > 0){
			for($i=0;$i<count($dir);$i++){
				if($dir[$i]!= "ipn_log.txt"){
					require_once(JPATH_ROOT."/components/com_osservicesbooking/helpers/".$dir[$i]);
				}
			}
		}
		
        OSBHelper::loadLanguage();
        $configClass = OSBHelper::loadConfig();
		if (version_compare(JVERSION, '3.0', 'le')){
			OSBHelper::loadBootstrap();
		}else{
			if($configClass['load_bootstrap'] == 1){
				OSBHelper::loadBootstrap();
			}else{
				OSBHelper::loadBootstrapStylesheet();
			}
		}
		global $mapClass;
		OSBHelper::loadMedia();
		OSBHelper::generateBoostrapVariables();
		OSBHelper::generateMapClassNames();
        
        $db = JFactory::getDBO();
        $parameters = $matches[1];
       // echo $parameters;die();
        if($parameters == ""){
        	return "";
        }else{
        	$parameterArr = explode("|",$parameters);
        	if(count($parameterArr) > 0){
        		$sid = '';
        		$vid = '';
        		$eid = '';
        		$cid = '';
        		
        		//sid
        		foreach ($parameterArr as $param){
        			$paramArr = explode(":",$param);
        			if($paramArr[0] == "sid"){
        				$sid = $paramArr[1];
        			}
        		}
        		
        		//eid
        		foreach ($parameterArr as $param){
        			$paramArr = explode(":",$param);
        			if($paramArr[0] == "eid"){
        				$eid = $paramArr[1];
        			}
        		}
        		//vid
        		foreach ($parameterArr as $param){
        			$paramArr = explode(":",$param);
        			if($paramArr[0] == "vid"){
        				$vid = $paramArr[1];
        			}
        		}
        		
        		//cid
        		foreach ($parameterArr as $param){
        			$paramArr = explode(":",$param);
        			if($paramArr[0] == "cid"){
        				$cid = $paramArr[1];
        			}
        		}
        		
        		$jinput->set('category_id',$cid);
        		$jinput->set('vid',$vid);
        		$jinput->set('employee_id',$eid);
        		$jinput->set('sid',$sid);
        		$document = JFactory::getDocument();			
        		ob_start();
				$document->addStyleSheet("//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css");
				?>
				<script type="text/javascript" src="<?php echo '//code.jquery.com/ui/1.11.4/jquery-ui.js'; ?>"></script>
				<div id="dialogstr4" title="<?php echo JText::_('OS_ITEM_HAS_BEEN_ADD_TO_CART_TITLE');?>">
				</div>
				<?php
        		OsAppscheduleDefault::defaultLayout('com_osservicesbooking');
        		$text = ob_get_contents();
        		ob_end_clean();
        	}
        }
        return $text;
    }
}
