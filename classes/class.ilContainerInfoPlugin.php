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
    public function getPluginName(): string
    {
        return "ContainerInfo";
    }
}
