<?php
/**
 * @package HybridAuth
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class HybridAuthHelperRoute extends DSCHelperRoute 
{
    static $itemids = null;
    
    public static function getItems( $option='com_hybridauth' )
    {
        return parent::getItems($option);        
    }
    
    /**
     * Finds the itemid for the set of variables provided in $needles
     *
     * @param array $needles
     * @return unknown_type
     */
    public static function findItemid($needles=array('view'=>'products', 'task'=>'', 'filter_category'=>'', 'id'=>''))
    {
        // populate the array of menu items for the extension
        if (empty(self::$itemids))
        {
            self::$itemids = array();
    
            // method=upgrade KILLS all of the useful properties in the __menus table,
            // so we need to do this manually
            // $menus      = &JApplication::getMenu('site', array());
            // $component  = &JComponentHelper::getComponent('com_sample');
            // $items      = $menus->getItems('componentid', $component->id);
            $items = self::getItems();
    
            if (empty( $items ))
            {
                return null;
            }
    
            foreach ($items as $item)
            {
                if (!empty($item->query) && !empty($item->query['view']))
                {
                    // reconstruct each url query, in case admin has created custom URLs
                    $query = "";
    
                    $view = $item->query['view'];
                    $query .= "&view=$view";
    
                    if (!empty($item->query['task']))
                    {
                        $task = $item->query['task'];
                        $query .= "&task=$task";
                    }
    
                    if (!empty($item->query['id']))
                    {
                        $id = $item->query['id'];
                        $query .= "&id=$id";
                    }
    
                    // set the itemid in the cache array
                    if (empty(self::$itemids[$query]))
                    {
                        self::$itemids[$query] = $item->id;
                    }
                }
            }
        }
    
        // Make this search the array of self::$itemids, matching with the properties of the $needles array
        // return null if nothing found
    
        // reconstruct query based on needle
        $query = "";
    
        if (!empty($needles['view']))
        {
            $view = $needles['view'];
            $query .= "&view=$view";
        }
    
        if (!empty($needles['task']))
        {
            $task = $needles['task'];
            $query .= "&task=$task";
        }
    
        if (!empty($needles['id']))
        {
            $id = $needles['id'];
            $query .= "&id=$id";
        }
    
        // if the query exists in the itemid cache, return it
        if (!empty(self::$itemids[$query]))
        {
            return self::$itemids[$query];
        }
    
        return null;
    }
}