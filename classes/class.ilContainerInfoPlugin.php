<?php

include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

/**
* ContainerInfo Plugin
*
* @author Raphael Heer <raphael.heer@hslu.ch>
* @version $Id$
*/
class ilContainerInfoPlugin extends ilUserInterfaceHookPlugin
{
    public function __construct()
    {
        global $DIC;
        $this->db = $DIC->database();
        parent::__construct($this->db, $DIC["component.repository"], "containerinfo");
    }
    public function getPluginName(): string
    {
        return "ContainerInfo";
    }
}
