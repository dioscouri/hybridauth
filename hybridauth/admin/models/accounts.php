<?php
/**
 * @version 1.5
 * @package HybridAuth
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

HybridAuth::load( 'HybridAuthModelBase', 'models._base' );

class HybridAuthModelAccounts extends HybridAuthModelBase 
{   
    protected function _buildQueryWhere(&$query)
    {
        $filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
        $filter_name    = $this->getState('filter_name');
        $filter_account    = $this->getState('filter_account');
        
        $filter_date_from = $this->getState('filter_date_from');
        $filter_date_to   = $this->getState('filter_date_to');
        $filter_datetype	= $this->getState('filter_datetype');
                        
        if ($filter) 
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

            $where = array();
            $where[] = 'LOWER(tbl.account_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.user_id) LIKE '.$key;
            
            $query->where('('.implode(' OR ', $where).')');
        }
        
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.account_id >= '.(int) $filter_id_from);
            }
                else
            {
                $query->where('tbl.account_id = '.(int) $filter_id_from);
            }
        }
        
        if (strlen($filter_id_to))
        {
            $query->where('tbl.account_id <= '.(int) $filter_id_to);
        }
        
        if ($filter_name) 
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.account_download_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }

        if (strlen($filter_account))
        {
            $query->where("tbl.account_id = '" . $filter_account . "'");
        }

        if (strlen($filter_date_from))
        {
        	switch ($filter_datetype)
        	{
        		case "created":
        		default:
        			$query->where("tbl.account_created >= '".$filter_date_from."'");		
        		  break;
        	}
       	}
       	
		if (strlen($filter_date_to))
        {
			switch ($filter_datetype)
        	{
        		case "created":
        		default:
        			$query->where("tbl.account_created <= '".$filter_date_to."'");		
        		  break;
        	}
       	}
    }

    /**
     * Builds JOINS clauses for the query
     */
    protected function _buildQueryJoins(&$query)
    {
        // $query->join( 'LEFT', '#__users AS u ON tbl.user_id = u.id' );
    }
    
    /**
     * Builds SELECT fields list for the query
     */
    protected function _buildQueryFields(&$query)
    {
		$query->select( $this->getState( 'select', 'tbl.*' ) );
		//$fields = array();
    }
    
    public function getList($refresh = false)
    {
        if ($list = parent::getList($refresh)) 
        { 
            foreach($list as $item)
            {
                $item->link = 'index.php?option=com_hybridauth&view=accounts&task=edit&id='.$item->account_id;
            }
        }
        return $list;
    }
}
