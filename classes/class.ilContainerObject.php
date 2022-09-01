<?php
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ContainerInfo/classes/class.ilAccessEvent.php');
include_once('./Services/MediaObjects/classes/class.ilObjMediaObject.php');
include_once("./Modules/File/classes/class.ilObjFileAccess.php");

class ilContainerObject
{
    /**
     * object ID of container
     * @var integer
     */
    public $obj_id;
    
    /**
     * reference ID of container
     * @var integer
     */
    public $ref_id;
    
    /**
     * title or name of the container
     * @var string
     */
    public $title;
    
    /**
     * size of the container
     * @var integer
     */
    public $size;
    
    /**
     * type of the container
     * @var string
     */
    public $type;
    
    /**
     * Number of subobjects
     * @var integer
     */
    public $subobj_counter;
    
    /**
     * count of clicks in the last 6 months
     * @var integer
     */
    public $clicks_in_last_six_months;
    
    /**
     * the newest read event of this container
     * @var ilContainerInfoAccess
     */
    public $newest_read_event = array();
    
    /**
     * If true, admin clicks will be ignored
     * @var boolean
     */
    public $ignore_admin_clicks = true;
    
    /**
     * Constructor
     */
    public function __construct($ref_id)
    {
        $this->obj_id = ilObject::_lookupObjId($ref_id);
        $this->title = ilObject::_lookupTitle($this->obj_id);
        $this->ref_id = $ref_id;
        $this->type = ilObject::_lookupType($this->obj_id);
        
        if (self::isContainer($this->type)) {
            $this->clicks_in_last_six_months = 0;
            $this->size = 0;
            $this->subobj_counter = 0;
            $this->checkForReadEvents($this->obj_id);
            $this->readChildrensRecursive($ref_id);
        }
    }
    
    /**
     * Reads recursive through the "filesystem" respectively the ilias tree
     * and gets the needed information like size of all files, last access and
     * count of clicks.
     *
     * @param	integer		ref id of the object
     */
    private function readChildrensRecursive($ref_id)
    {
        foreach (self::getChildRefIdsFromParentRefId($ref_id) as $child_ref_id) {
            $child_obj_id = ilObject::_lookupObjectId($child_ref_id);
            $child_type = ilObject::_lookupType($child_obj_id);

            if (self::isContainer($child_type)) {
                // if is container -> go deeper in tree
                $this->readChildrensRecursive($child_ref_id);
            } else {
                switch ($child_type) {
                    case 'file':
                        $this->size += $this->getFileSize($child_obj_id);
                        break;
                    case 'mcst':
                        $this->size += $this->getSizeOfMcst($child_obj_id);
                        break;
                    case 'mep':
                        $this->size += $this->getSizeOfMep($child_obj_id);
                        break;
                    default:
                        break;
                }
            }
            
            if ($child_type != 'role') {
                $this->subobj_counter++;
            }
            
            $this->checkForReadEvents($child_obj_id);
        }
    }
        
    /**
     * Returns
     * @param unknown $obj_id
     * @return unknown|number
     */
    private function getFileSize($obj_id)
    {
        return ilObjFileAccess::_lookupFileSize($obj_id);
        /*global $ilDB;
        $query = sprintf("SELECT file_size FROM file_data ".
                         "WHERE file_data.file_id = %s",
                         $ilDB->quote($obj_id, 'integer'));
        $res = $ilDB->query($query);
        if($obj = $ilDB->fetchAssoc($res))
        {
            $size = $obj['file_size'];
            if($size != null)
            {
                return $size;
            }
        }*/
        return 0;
    }
    
    /**
     * Calculates the size of a Mediacast Object
     * and adds it to the size of the container
     *
     * @param integer $obj_id Object ID of the Mediacast
     */
    private function getSizeOfMcst($obj_id)
    {
        global $ilDB;
        
        // I didn found any lookup-functions for this...
        $query = sprintf(
            "SELECT mob_id FROM il_news_item " .
                         "WHERE context_obj_id = %s",
            $ilDB->quote($obj_id, 'integer')
        );
        $res = $ilDB->query($query);
        
        if ($row = $ilDB->fetchAssoc($res)) {
            $mob_id = $row['mob_id'];
            if ($mob_id != null) {
                $file = ilObjMediaObject::_lookupItemPath($mob_id, false, false);
                if (is_file($file)) {
                    return filesize($file);
                }
            }
        }
        
        return 0;
    }
    
    /**
     * Calculates the size of a Media Pool Object
     * and adds it to the size of the container
     *
     * @param integer $obj_id Object ID of the Media Pool
     */
    private function getSizeOfMep($obj_id)
    {
        global $ilDB;
        
        // I didn found any lookup-functions for this...
        $query = sprintf(
            "SELECT foreign_id FROM mep_tree " .
                         "JOIN mep_item ON mep_tree.child = mep_item.obj_id " .
                         "AND mep_item.type = %s " .
                         "WHERE mep_tree.mep_id = %s",
            $ilDB->quote("mob", "text"),
            $ilDB->quote($obj_id, 'integer')
        );
        $res = $ilDB->query($query);
        
        if ($row = $ilDB->fetchAssoc($res)) {
            $mob_id = $row['foreign_id'];
            if ($mob_id != null) {
                $file = ilObjMediaObject::_lookupItemPath($mob_id, false, false);
                if (is_file($file)) {
                    return filesize($file);
                }
            }
        }
        
        return 0;
    }
    
    /**
     * This method checks for the readevents of the given object.
     * It sets the newest_read_event timestamp if needed and counts
     * the clicks for the last 6 Months.
     *
     * @param integer $obj_id ID of the Object
     */
    private function checkForReadEvents($obj_id)
    {
        /* This dbquery could be replaced with  ilChangeEvent::_lookupReadEvents($obj_id)
         * But since the lookup-method returns every dataset, this query is faster.
         */
        global $ilDB;
        $query = sprintf(
            "SELECT obj_id, usr_id, last_access, read_count FROM read_event " .
                         "WHERE read_event.obj_id = %s " .
                         "ORDER BY read_event.last_access DESC",
            $ilDB->quote($obj_id, 'integer')
        );
        $res = $ilDB->query($query);

        $is_over_six_months = false;
        while (($obj = $ilDB->fetchAssoc($res)) !== null && !$is_over_six_months) {
            $timestamp = $obj['last_access'];
            $usr_id = $obj['usr_id'];
            
            if (!($this->ignore_admin_clicks && ilContainerInfoAccess::isAdmin($usr_id))) {
                
                // Check if this is the newest readevent
                // if($this->newest_read_event === null || $this->newest_read_event->timestamp < $timestamp)
                if ($this->newest_read_event === null || $this->newest_read_event['timestamp'] < $timestamp) {
                    $this->newest_read_event['timestamp'] = $timestamp;
                    $this->newest_read_event['obj_id'] = $obj_id;
                    //$this->newest_read_event = new ilAccessEvent($obj_id, $timestamp, $usr_id);
                }
                
                // count clicks for the last six months
                if (strtotime('-6 month') > $timestamp) {
                    $is_over_six_months = true;
                } else {
                    $this->clicks_in_last_six_months += $obj['read_count'];
                }
            } elseif (strtotime('-6 month') > $timestamp) {
                $is_over_six_months = true;
            }
        }
    }
    
    /**
     * check if object type is a container
     *
     * @param string $type
     * @return boolean
     */
    public static function isContainer($type)
    {
        return in_array($type, array('cat', 'fold', 'crs', 'grp'));
    }
    
    /**
     * Returns an array with all the Ref_IDs of the child of a given parent
     * in the Tree
     *
     * @param integer $parent_ref RefID of the parent object in the tree
     */
    public static function getChildRefIdsFromParentRefId($parent_ref)
    {
        global $tree;

        return $tree->getChildIds($parent_ref);
    }
    
    /**
     *
     *
     * @param integer $parent_ref
     */
    public static function getChildContainerFromParentRefId($parent_ref)
    {
        $children = array();
        $i = 0;

        foreach (self::getChildRefIdsFromParentRefId($parent_ref) as $childNodeId) {
            $type = ilObject::_lookupType($childNodeId, true);
            if (self::isContainer($type)) {
                $children[$i] = new ilContainerObject($childNodeId);
                $i++;
            }
        }
        
        return $children;
    }
    
    /**
     * Compares two Containers for sort function.
     * First sortlayer is the type, second the title.
     *
     * @param ilContainerObject $a
     * @param ilContainerObject $b
     */
    public static function compareContainerForSort($a, $b)
    {
        $res = strcmp($a->type, $b->type);
        if (!$res) {
            return strcasecmp($a->title, $b->title);
        }
        return $res;
    }
}
