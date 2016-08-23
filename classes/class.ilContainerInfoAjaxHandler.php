<?php
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoGUI.php');
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoAccess.php');
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
        global $ilCtrl, $tpl, $ilUser;
        $cmd = $ilCtrl->getCmd();
        
        if(ilContainerInfoAccess::checkAccess($ilUser->getId()))
        {
            switch(strtolower($cmd))
            {
                case 'getcontainerinfos':
                    echo $this->returnContainerInfos();
                    exit;
                default:
                    echo 'cmd unknown...';
                    exit;
                    break;
            }
        }
        else 
        {
            echo 'Permission denied.';exit;
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