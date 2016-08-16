<?php
class ilContainerInfoGUI
{
    private $containers;
    static $block_type = 'containerinfo';

    /**
     * Constructor
     * @param	ilContainerInfoObject	containers
     */
    public function __construct($containers)
    {
        global $tpl;
        $this->plugin = new ilContainerInfoPlugin();
        $this->containers = $containers;
        $this->global_tpl = $tpl;
    }

    /**
     * Convert a Size from Byte to a readable string
     * @param	integer 	$size
     * @return	string
     */
    private function sizeToReadableString($size)
    {
        if($size < 1024)
        {
            return $size . ' Byte';
        }
        else if($size < 1048576)
        {
            return round(($size/1024), 2) . ' kB';
        }
        else if($size < 1073741824)
        {
            return round(($size/1048576), 2) . ' MB';
        }
        else if($size < 1099511627776)
        {
            return round(($size/1073741824), 2) . ' GB';
        }
        else if($size < 1125899906842624)
        {
            return round(($size/1099511627776), 2) .  ' TB';
        }
    }

    /**
     * Is used to fill all informations from $this->containers into a
     * block from Block GUI.
     *
     * @return	Information of Containers as html
     */
    public function getHtml()
    {
        usort($this->containers, "ilContainerObject::compareContainerForSort");

        $this->global_tpl->addCss('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/templates/default/infobox.css');
        $html_output = '';

        foreach($this->containers as $container)
        {
            if(ilContainerObject::isContainer($container->type))
            {
                $timestamp = $container->newest_read_event['timestamp'];
                $timestamp_as_date = date('Y-m-d H:i:s', $timestamp);

                $tpl = new ilTemplate("tpl.infobox.html", true, true, "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo");
                $tpl->setCurrentBlock("infobox");

                $tpl->setVariable("CONTAINER_TYPE_LABEL", $this->plugin->txt('container_type'));
                $tpl->setVariable("CONTAINER_SIZE_LABEL", $this->plugin->txt('size'));
                $tpl->setVariable("SUBOBJ_COUNTER_LABEL", $this->plugin->txt('subobj_counter'));
                $tpl->setVariable("TEXT_COLOR_CLASS", $this->coloringNewestRead($timestamp));
                $tpl->setVariable("LAST_READ_EVENT_LABEL", $this->plugin->txt('newest_read'));
                $tpl->setVariable("LAST_READ_OBJ_NAME_LABEL", $this->plugin->txt('name_of_object'));
                $tpl->setVariable("CLICKCOUNTER_LABEL", $this->plugin->txt('clicks_in_last_six_months'));

                $tpl->setVariable("CONTAINER_TITLE_VAL", $container->title);
                $tpl->setVariable("CONTAINER_TYPE_VAL", $this->plugin->txt($container->type . '_obj'));
                $tpl->setVariable("CONTAINER_SIZE_VAL", $this->sizeToReadableString($container->size));
                $tpl->setVariable("SUBOBJ_COUNTER_VAL", $container->subobj_counter);
                $tpl->setVariable("LAST_READ_EVENT_VAL", $timestamp_as_date);
                $tpl->setVariable("LAST_READ_OBJ_NAME_VAL", ilObject::_lookupTitle($container->newest_read_event['obj_id']));
                $tpl->setVariable("CLICKCOUNTER_VAL", $container->clicks_in_last_six_months);
                $tpl->parseCurrentBlock();
                $html_output .= $tpl->get();
            }
        }

        if(count($this->containers) == 0)
        {
            $html_output = $this->plugin->txt('no_containers_available');
        }
        return $html_output;
    }

    /**
     * Returns the colorclassname for the last read event
     * @param  string 	Unix Timestamp
     * @return string   Color class
     */
    private function coloringNewestRead($timestamp)
    {
        if(strtotime('-1 year') < $timestamp)
        {
            return "last_use_this_year";
        }
        else if(strtotime('-2 year') < $timestamp)
        {
            return "last_use_over_1_year";
        }
        else if(strtotime('-4 year') < $timestamp)
        {
            return "last_use_over_2_years";
        }
        else if(strtotime('-6 year') < $timestamp)
        {
            return "last_use_over_4_years";
        }
        else
        {
            return "last_use_over_6_years";
        }
    }
}
?>