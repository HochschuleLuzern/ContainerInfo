<?php

include_once './Services/Block/classes/class.ilBlockGUI.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilContainerInfoPlugin.php';
include_once './Services/UICore/classes/class.ilTemplate.php';

/**
 * BlockGUI class for Container Info Block
 * @author Raphael Heer <raphael.heer@hslu.ch>
 * @version $Id$
 * @ilCtrl_IsCalledBy ilContaienrInfoBlockGUI: ilColumnGUI
 */
class ilContainerInfoBlockGUI extends ilBlockGUI
{
	private $containers;
	static $block_type = 'containerinfo';
	
	/**
	 * Constructor
	 * @param	ilContainerInfoObject	containers
	 */
	public function __construct()
	{
	    global $tpl;
		parent::__construct();
		$this->plugin = new ilContainerInfoPlugin();
		$this->setTitle($this->plugin->txt('title'));
		$this->global_tpl = $tpl;
	}
	
	/**
	 * Get block type
	 * @return    string    Block type.
	 */
	function getBlockType() : string {
		return self::$block_type;
	}
	
	/**
	 * Get block type
	 * @return    string    Block type.
	 */
	function isRepositoryObject() : bool {
		return false;
	}
	
	/**
	 * Is used to fill all informations from $this->containers into a
	 * block from Block GUI. 
	 *  
	 * @return	Information of Containers as html
	 */
	public function fillDataSection() 
	{
	    global $ilCtrl;
	    
		$this->global_tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/templates/default/infobox.css');
		$this->global_tpl->addJavaScript('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/templates/default/infobox_functions.js');
		
		
		$tpl = new ilTemplate("tpl.content_for_ajax.html", true, true, "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo");
		
		$ref_id = $_GET['ref_id'];
		$ilCtrl->setParameterByClass('ilContainerInfoAjaxHandler', 'ref_id', $ref_id);
		
		$ajax_link = $ilCtrl->getLinkTargetByClass(array(
        					'ilUIPluginRouterGUI',
        					'ilContainerInfoAjaxHandler',
        			),'getContainerInfos');
		
		$tpl->setCurrentBlock("container_info_ajax_target");
		$tpl->setVariable("CONTAINER_INFO_AJAX_LINK", $ajax_link);
		
		$tpl->setVariable("CONTAINER_INFO_LOAD_INFO", $this->plugin->txt("ajax_text_load_info"));
		$tpl->setVariable("CONTAINER_INFO_LOAD_BTN", $this->plugin->txt("ajax_btn_load_infos"));
		$tpl->setVariable("CONTAINER_INFO_PAST_TIME", $this->plugin->txt("ajax_past_time"));
		
		$tpl->parseCurrentBlock();
		
		$this->setDataSection($tpl->get());		
	}
	
}
?>
