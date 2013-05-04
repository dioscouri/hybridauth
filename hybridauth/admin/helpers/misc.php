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

if ( !class_exists('HybridAuth') ) 
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );

HybridAuth::load( "HybridAuthHelperBase", 'helpers._base' );

class HybridAuthHelperMisc extends HybridAuthHelperBase 
{
	/**
	 *
	 * @return unknown_type
	 */
	function setDateVariables( $curdate, $enddate, $period )
	{
		$database = JFactory::getDBO();

		$return = new stdClass();
		$return->thisdate = '';
		$return->nextdate = '';

		switch ($period)
		{
			case "daily":
					$thisdate = $curdate;
					$query = " SELECT DATE_ADD('".$curdate."', INTERVAL 1 DAY) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();
				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
			  break;
			case "weekly":
				$start 	= getdate( strtotime($curdate) );

				// First period should be days between x day and the immediate Sunday
					if ($start['wday'] < '1') {
						$thisdate = $curdate;
						$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 1 DAY ) ";
						$database->setQuery( $query );
						$nextdate = $database->loadResult();
					} elseif ($start['wday'] > '1') {
						$interval = 8 - $start['wday'];
						$thisdate = $curdate;
						$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL {$interval} DAY ) ";
						$database->setQuery( $query );
						$nextdate = $database->loadResult();
					} else {
						// then every period following should be Mon-Sun
						$thisdate = $curdate;
						$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 7 DAY ) ";
						$database->setQuery( $query );
						$nextdate = $database->loadResult();
					}

					if ( $nextdate > $enddate ) {
						$query = " SELECT DATE_ADD( '".$nextdate."', INTERVAL 1 DAY ) ";
						$database->setQuery( $query );
						$nextdate = $database->loadResult();
					}
				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
			  break;
			case "monthly":
				$start 	= getdate( strtotime($curdate) );
				$start_datetime = date("Y-m-d", strtotime($start['year']."-".$start['mon']."-01"));
					$thisdate = $start_datetime;
					$query = " SELECT DATE_ADD( '".$thisdate."', INTERVAL 1 MONTH ) ";
					$database->setQuery( $query );
					$nextdate = $database->loadResult();

				$return->thisdate = $thisdate;
				$return->nextdate = $nextdate;
			  break;
			default:
			  break;
		}

		return $return;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function getToday()
	{
		static $today;

		if (empty($today))
		{
			$config = JFactory::getConfig();
			$offset = $config->getValue('config.offset');
			$date = JFactory::getDate();
			$today = $date->toFormat( "%Y-%m-%d 00:00:00" );

			if ($offset > 0) {
				$command = 'DATE_ADD';
			} elseif ($offset < 0) {
				$command = 'DATE_SUB';
			} else {
				return $today;
			}

			$database = JFactory::getDBO();
			$query = "
				SELECT
					{$command}( '{$today}', INTERVAL {$offset} HOUR )
				";

			$database->setQuery( $query );
			$today = $database->loadResult();
		}
		return $today;
	}

	/**
	 *
	 * @param $date
	 * @return unknown_type
	 */
	function getOffsetDate( $date )
	{
		$config = JFactory::getConfig();
		$offset = $config->getValue('config.offset');
		if ($offset > 0) {
			$command = 'DATE_ADD';
		} elseif ($offset < 0) {
			$command = 'DATE_SUB';
		} else {
			$command = '';
		}
		if ($command)
		{
			$database = JFactory::getDBO();
			$query = "
				SELECT
					{$command}( '{$date}', INTERVAL {$offset} HOUR )
				";
			$database->setQuery( $query );
			$date = $database->loadResult();
		}
		return $date;
	}

	function getPeriodData( $start_datetime, $end_datetime, $period='daily', $select="tbl.*", $type='list' )
	{
		static $items;

		if (empty($items[$start_datetime][$end_datetime][$period][$select]))
		{
			$runningtotal = 0;
			$return = new stdClass();
			$database = JFactory::getDBO();

			// the following would be used if there were an additional filter in the Inputs
			$filter_where 	= "";
			$filter_select 	= "";
			$filter_join 	= "";
			$filter_typeid 	= "";
			if ($filter_typeid) {
				$filter_where 	= "";
				$filter_select 	= "";
				$filter_join 	= "";
			}

			$start_datetime = strval( htmlspecialchars( $start_datetime ) );
			$end_datetime = strval( htmlspecialchars( $end_datetime ) );

			$start 	= getdate( strtotime($start_datetime) );

			// start with first day of the period, corrected for offset
			$mainframe = JFactory::getApplication();
			$offset = $mainframe->getCfg( 'offset' );
			if ($offset > 0) {
				$command = 'DATE_ADD';
			} elseif ($offset < 0) {
				$command = 'DATE_SUB';
			} else {
				$command = '';
			}
			if ($command)
			{
				$database = JFactory::getDBO();
				$query = "
					SELECT
						{$command}( '{$start_datetime}', INTERVAL {$offset} HOUR )
					";

				$database->setQuery( $query );
				$curdate = $database->loadResult();

				$query = "
					SELECT
						{$command}( '{$end_datetime}', INTERVAL {$offset} HOUR )
					";

				$database->setQuery( $query );
				$enddate = $database->loadResult();
			}
				else
			{
				$curdate = $start_datetime;
				$enddate = $end_datetime;
			}

			// while the current date <= end_date
			// grab data for the period
			$num = 0;
			$result = array();
			while ($curdate <= $enddate)
			{
				// set working variables
					$variables = HybridAuthHelperBase::setDateVariables( $curdate, $enddate, $period );
					$thisdate = $variables->thisdate;
					$nextdate = $variables->nextdate;

				// grab all records
				// TODO Set the query here
					$query = new DSCQuery();
					$query->select( $select );
					$rows = $this->selectPeriodData( $thisdate, $nextdate, $select, $type );
					$total = $this->selectPeriodData( $thisdate, $nextdate, "COUNT(*)", "result" );

				//store the value in an array
				$result[$num]['rows']		= $rows;
				$result[$num]['datedata'] 	= getdate( strtotime($thisdate) );
				$result[$num]['countdata']	= $total;
				$runningtotal 				= $runningtotal + $total;

				// increase curdate to the next value
				$curdate = $nextdate;
				$num++;

			} // end of the while loop

			$return->rows 		= $result;
			$return->total 		= $runningtotal;
			$items[$start_datetime][$end_datetime][$period][$select] = $return;
		}

		return $items[$start_datetime][$end_datetime][$period][$select];
	}
	
	/**
	 * includeJQueryUI function.
	 * 
	 * @access public
	 * @return void
	 */
	function includeJQueryUI()
	{
        self::includeJQuery();
	    JHTML::_('script', 'jquery-ui-1.7.2.min.js', 'media/com_hybridauth/js/');
        JHTML::_('stylesheet', 'jquery-ui.css', 'media/com_hybridauth/css/');
	}

	/**
	 * includeJQuery function.
	 * 
	 * @access public
	 * @return void
	 */
	function includeJQuery()
	{
	    JHTML::_('script', 'jquery-1.3.2.min.js', 'media/com_hybridauth/js/');
	}
	
	/**
	 * Include JQueryMultiFile script
	 */
	function includeMultiFile()
	{
		JHTML::_('script', 'Stickman.MultiUpload.js', 'media/com_hybridauth/js/');
		JHTML::_('stylesheet', 'Stickman.MultiUpload.css', 'media/com_hybridauth/css/');
	}
	

    /**
     * Formats and converts a number according to currency rules
     * As of v0.5.0 is a wrapper
     * 
     * @param unknown_type $amount
     * @param unknown_type $currency
     * @return unknown_type
     */
    function currency($amount, $currency='', $options='')
    {
        HybridAuth::load('HybridAuthHelperCurrency', 'helpers.currency');
        $amount = HybridAuthHelperCurrency::_($amount, $currency, $options);
        return $amount;
    }
	
	/**
	 * Return a mesure with its unit
	 * @param float $amount
	 * @param string $type could be dimension or weight
	 */
	function measure($amount, $type='dimension')
	{
        // default to whatever is in config
            
        $config = HybridAuth::getInstance();
        $dim_unit = $config->get('dimensions_unit', 'cm');
        $weight_unit = $config->get('weight_unit', 'kg');
            
        if(strtolower($type) == 'dimension'){
        	return $amount.$dim_unit;
        } else{
        	return $amount.$weight_unit;
        }
		
	}

	/**
	 * Nicely format a number
	 * 
	 * @param $number
	 * @return unknown_type
	 */
    function number($number, $options='' )
	{
		$config = HybridAuth::getInstance();
        $options = (array) $options;
        
        $thousands = isset($options['thousands']) ? $options['thousands'] : $config->get('currency_thousands', ',');
        $decimal = isset($options['decimal']) ? $options['decimal'] : $config->get('currency_decimal', '.');
        $num_decimals = isset($options['num_decimals']) ? $options['num_decimals'] : $config->get('currency_num_decimals', '2');
		
		$return = number_format($number, $num_decimals, $decimal, $thousands);
		return $return;
	}
}