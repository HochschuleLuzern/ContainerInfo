<?php
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoGUI.php');
/**
* Ajax-handler class
*
* @author Raphael Heer <raphael.heer@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*
* @ilCtrl_isCalledBy ilContainerInfoAjaxHandler: ilRouterGUI, ilUIPluginRouterGUI
*/

class ilContainerInfoAjaxHandler
{
	function __construct()
	{
	    $this->ref_id = $_GET['ref_id'];
	}
    
    public function executeCommand()
    {
        global $ilCtrl, $tpl;
        $cmd = $ilCtrl->getCmd();
        
        switch(strtolower($cmd))
        {
            case 'getcontainerinfos':
                echo $this->returnContainerInfos();
                exit;
            default:
                break;
        }
    }
	
    public function returnContainerInfos()
    {
        $containers = ilContainerObject::getChildContainerFromParentRefId($this->ref_id);
        
        $html = $this->getContainerInfosAsHtml($containers);
        
        return $html;
    }
    
    private function getContainerInfosAsHtml($containers)
    {
        $container_info = new ilContainerInfoGUI($containers);
        return $container_info->getHtml();
    }
}

?>