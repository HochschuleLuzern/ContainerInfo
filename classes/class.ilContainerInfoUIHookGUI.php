<?php

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoBlockGUI.php');
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerObject.php');
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoAccess.php');

/**
* ContainerInfo class
*
* @author Raphael Heer <raphael.heer@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*/
class ilContainerInfoUIHookGUI extends ilUIHookPluginGUI
{
    
    /**
     * {@inheritDoc}
     * @see ilUIHookPluginGUI::getHTML()
     */
    public function getHTML($a_comp, $a_part, $a_par = array())
    {
        global $ilUser;
        if ($a_comp == 'Services/Container' && $a_part == 'right_column') {
            $ref_id = (int) $_GET['ref_id'];
            $user_id = $ilUser->getId();
            
            if (ilContainerInfoAccess::checkAccess($user_id) && $ref_id > 0) {
                return array('mode' => ilUIHookPluginGUI::PREPEND,
                             'html' => $this->getBlockHTML($ref_id));
            }
        }
        
        return array('mode' => ilUIHookPluginGUI::KEEP, 'html' => '');
    }
    
    /**
     * Returns the HTML of a Block Item
     * @param integer $ref_id
     * @return string
     */
    public function getBlockHTML($ref_id)
    {
        //$containers = ilContainerObject::getChildContainerFromParentRefId($ref_id);
        $block = new ilContainerInfoBlockGUI();
        
        return $block->getHTML();
    }
}
