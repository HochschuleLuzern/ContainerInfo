<?php
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/
include_once("./Services/Repository/PluginSlot/class.ilObjectPluginAccess.php");
include_once("./Services/AccessControl/classes/class.ilRbacReview.php");

/**
 * Access/Condition checking for Container Info Plugin
 *
 * Please do not create instances of large application classes (like ilObjExample)
 * Write small methods within this class to determin the status.
 *
 *
 * @version $Id$
 */
class ilContainerInfoAccess extends ilObjectPluginAccess
{
    private static $admin_role_id = 0;
    /**
     * Checks if the user has Access to this plugin
     * @param integer $a_usr_id
     * @return boolean
     */
    public static function checkAccess($a_usr_id)
    {
        return self::isAdmin($a_usr_id);
    }

    /**
     * Gets the ID of the local Admin Role
     * @
     * @return integer
     */
    private static function getAdminRoleID()
    {
        global $ilDB;

        if (self::$admin_role_id == 0) {
            if (preg_match("/Administrator.*/", ilObject::_lookupTitle(2))
            && ilObject::_lookupType(2) == "role") {
                self::$admin_role_id = 2;
                return self::$admin_role_id;
            }
        }
        /*
        $query = sprintf("SELECT obj_id FROM object_data " .
                         "WHERE title='Administrator' " .
                         "AND type='role'");
        $res = $ilDB->query($query);
        $row = $ilDB->fetchAssoc($res);
        if($row['obj_id'])
        {
            return $row['obj_id'];
        }
        */
        return 0;
    }
    
    public static function isAdmin($a_user_id = 0)
    {
        global $ilUser, $rbacreview;

        if ($a_user_id == 0) {
            $a_user_id = $ilUser->getId();
        }
        
        if (self::$admin_role_id == 0) {
            self::getAdminRoleID();
        }
        return $rbacreview->isAssigned($a_user_id, self::$admin_role_id);
    }
}
