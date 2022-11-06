<?php

/**
 * @author Marosdee Uma, website:: www.danya-reload.com @ danya0365[at]live.com, danya0365[at]gmail.com, danya-planemo[at]live.com
 * @copyright 2009 
 * @version 1.5
 */

class calendar
{
	/**
	* 0-6 date('w')
	*
	* @var integer
	*/		
	public $todayDayOfWeek;
	
	/**
	* 1-31 date('j')
	*
	* @var integer
	*/	
	public $todayDay;
	
	/**
	* 1-6 getWeek()
	*
	* @var integer
	*/	
	public $todayWeek;
	
	/**
	* 1-12 date('n')
	*
	* @var integer
	*/	
	public $todayMonth;
	
	/**
	* 1970 or 2009 date('Y')
	*
	* @var integer
	*/	
	public $todayYear;
	
	/**
	* 0-6 date('w')
	*
	* @var integer
	*/	
	public $defaultDayofWeek;

	/**
	* 1-31 date('j')
	*
	* @var integer
	*/	
	public $defaultDay;

	/**
	* 1-6 getWeek()
	*
	* @var integer
	*/	
	public $defaultWeek;

	/**
	* 1-12 date('n')
	*
	* @var integer
	*/	
	public $defaultMonth;

	/**
	* 1970 or 2009 date('Y')
	*
	* @var integer
	*/	
	public $defaultYear;
	
	/**
	* 1-31
	*
	* @var integer
	*/	
	public $totalDayInMonth;

	/**
	* 0-6
	*
	* @var integer
	*/	
	public $firstDayOfWeek;

	/**
	* 0-6
	*
	* @var integer
	*/	
	public $lastDayOfWeek;

	/**
	* array of MonthTitle
	*
	* @var array
	*/	
	private $monthTitle = array('january','February','March','April','May','June','July','August','September','October','November','December');

	/**
	* Show color in table row
	*
	* @var boolean
	*/	
	private $showColor = true;

	/**
	* array of table color
	*
	* @var array
	*/	
	private $colorArray = array();

	/*
	* width of table
	*/
	private $defaultTableWidth = '100%';

	/**
	* column per row in year calendar
	*
	* @var integer
	*/	
	private $defaultPerRow = 2;

	/**
	* class in table row
	*
	* @var array
	*/	
	private $defaultClassCol = array('colDay1', 'colDay2', 'colDay3', 'colDay4', 'colDay5', 'colDay6', 'colDay7');

	/**
	* today class in calendar
	*
	* @var string
	*/	
	private $defaultTodayClass = 'today';
	
	/**
	* blank class in calendar
	*
	* @var string
	*/	
	private $defaultBlankClass = 'colBlank';

	/**
	* event class in calendar
	*
	* @var string
	*/	
	private $defaultEventDayClass = 'eventDay';

	/**
	* array of Day of Week Title
	*
	* @var array
	*/	
	private $dayOfWeekTitle = array('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY');

	/**
	* array of event Day
	*
	* @var array
	*/	
	private $eventDay = array();

	/**
	* title of week
	*
	* @var array
	*/	
	private $defaultWeekTitleFormat = " {week} - {month} - {year}";

	/**
	* title of month
	*
	* @var string
	*/	
	private $defaultMonthTitleFormat = "{month} - {year}";

	/**
	* title of year
	*
	* @var string
	*/	
	private $defaultYearTitleFormat = "{year}";
	
	/**
	* Thai year will +543
	*
	* @var boolean
	*/	
	private $thaiYear = false;

	/**
	* Show or hide Day of Back Month Or Week
	*
	* @var boolean
	*/	
	private $showBackDay = true;

	/**
	* Show or hide Day of Next Month Or Week
	*
	* @var boolean
	*/	
	private $showNextDay = true;
	
	
	/**
	* class constructor 
	* 
	* @return void
	*/
	public function __construct()
	{
		$this->todayDayOfWeek = $this->defaultDayofWeek = date('w');
		$this->todayDay = $this->defaultDay = date('j');
		$this->todayWeek = $this->defaultWeek = $this->getWeek( date('j'), date('n'), date('Y'));
		$this->todayMonth = $this->defaultMonth = date('n');
		$this->todayYear = $this->defaultYear = date('Y');
	}
	
	/**
	* set column class
	*  
 	* @param	array $class The name of column class 1-7
 	* 				[0] class in SUNDAY
 	* 				[1] class in MONDAY
 	*  				[2] class in TUESDAY    
 	*  				[3] class in WEDNESDAY
 	*  				[4] class in THURSDAY
 	*  				[5] class in FRIDAY
 	*  				[6] class in SATURDAY
	* 
	* @return 	void
	*/
	public function setColumnClass($class)
	{
		if ( count($class) >= 7 )
		$this->defaultClassCol = $class;
	}

	/**
 	* set column today class
 	* 
 	* @param	string $today The name of today class
 	* 
	* @return 	void
	*/
	public function setTodayClass($today)
	{
		$this->defaultTodayClass = $today;
	}
	
	/**
 	* set column blank class
 	* 
 	* @param	string $blank The name of blank class
 	* 
	* @return 	void
	*/
	public function setBlankClass($blank)
	{
		$this->defaultBlankClass = $blank;
	}

	/**
 	* set column event class
 	* 
 	* @param	string $event The name of event class
 	* 
	* @return 	void
	*/
	public function setEventClass($event)
	{
		$this->defaultEventDayClass = $event;
	}

	/**
    * set column per row in year calendar
    * 
    * @param	integer $perRow (1-99)
    * 
	* @return 	void
	*/
	public function setYearColumnPerRow($perRow)
	{
		if ( is_numeric($perRow) && $perRow > 0 ) $this->defaultPerRow = $perRow;
	}

	/**
 	* set day of week title format
 	* 
 	* @param 	string $string
	* 				{week} will replace with number of current week
	* 				{month} will replace with the name of current month
	* 				{year} will replace with number of current year
 	* 
	* @return 	void
	*/
	public function setWeekTitleFormat($string)
	{
		$this->defaultWeekTitleFormat = $string;
	}

	/**
 	* set month title format
 	* 
 	* @param	string $string
	* 				{month} will replace with the name of current month
	* 				{year} will replace with number of current year
	* 
	* @return 	void
	*/
	public function setMonthTitleFormat($string)
	{
		$this->defaultMonthTitleFormat = $string;
	}

	/**
 	* set month title format
 	* 
 	* @param	string $string
	* 				{year} will replace with number of current year
	* 
	* @return 	void
	*/
	public function setYearTitleFormat($string)
	{
		$this->defaultYearTitleFormat = $string;
	}

	/**
 	* set event day
 	* 
 	* @param	array $eventDay
	* 				[0] will check in caledar format '31-1-2009' 
	* 				[1] the name of event
	* 
	* @return 	void
	*/
	public function setEventDay($eventDay)
	{
		if ( count($eventDay) > 0 )
		$this->eventDay = $eventDay;
	}

	/**
 	* set color in day 
 	* 
 	* @param	array $color
 	* 				[0] color in SUNDAY
 	* 				[1] color in MONDAY
 	*  				[2] color in TUESDAY    
 	*  				[3] color in WEDNESDAY
 	*  				[4] color in THURSDAY
 	*  				[5] color in FRIDAY
 	*  				[6] color in SATURDAY
	* 
	* @return 	void
	*/
	public function setColor($color)
	{
		if ( count($color) >= 7 )
		$this->colorArray = $color;
	}


	/**
 	* set day of week title
 	* 
 	* @param	array $day
 	* 				[0] SUNDAY
 	* 				[1] MONDAY
 	*  				[2] TUESDAY    
 	*  				[3] WEDNESDAY
 	*  				[4] THURSDAY
 	*  				[5] FRIDAY
 	*  				[6] SATURDAY
	* 
	* @return 	void
	*/
	public function setDayOfWeekTitle($day)
	{
		if ( count($day) == 7 )
		$this->dayOfWeekTitle = $day;
	}
	
	/**
 	* set month title name
 	* 
 	* @param	array $month
 	* 				[0] January 
 	* 				[1] February 
 	*  				[2] March     
 	*  				[3] April 
 	*  				[4] May 
 	*  				[5] June 
 	*  				[6] July 
 	* 				[7] August 
 	* 				[8] September 
 	* 				[9] October 
 	* 				[10] Noverber 
 	* 				[11] December
	* 
	* @return 	void
	*/
	public function setMonthTitle($month)
	{
		if ( count($month) == 12 )
		$this->monthTitle = $month;
	}

	/**
 	* set calendar table width
 	* 
 	* @param	integer $width (1-2000)
	* 
	* @return 	void
	*/
	public function setTableWidth($width)
	{
		$this->defaultTableWidth = $width;
	}

	/**
 	* set current calendar week
 	* 
 	* @param	integer $w (1-6)
	* 
	* @return 	void
	*/
	public function setWeek($w)
	{
		if ( is_numeric($w) && $w > 0 && $w < 7 ) $this->defaultWeek = $w * 1;
	}

	/**
 	* set current calendar month
 	* 
 	* @param	integer $m (1-12)
	* 
	* @return 	void
	*/
	public function setMonth($m)
	{
		if ( is_numeric($m) && $m > 0 && $m < 13) $this->defaultMonth = $m * 1;
	}

	/**
 	* set current calendar year
 	* 
 	* @param	integer $y (1970 or 2009)
	* 
	* @return 	void
	*/
	public function setYear($y)
	{
		if ( is_numeric($y) && $y > 0 ) $this->defaultYear = $y * 1;
	}

	/**
 	* set display color in day of week column
 	* 
 	* @param	boolean $boolean (TRUE or FALSE)
	* 
	* @return 	void
	*/
	public function setShowColor($boolean)
	{
		if ( is_bool($boolean) ) $this->showColor = $boolean;
	}
	
	/**
 	* set use Thai year
 	* 
 	* @param	boolean $boolean (TRUE or FALSE)
	* 
	* @return 	void
	*/
	public function setThaiYear($boolean)
	{
		if ( is_bool($boolean) ) $this->thaiYear = $boolean;
	}

	/**
 	* set show back day of back month
 	* 
 	* @param	boolean $boolean (TRUE or FALSE)
	* 
	* @return 	void
	*/
	public function setShowBackDay($boolean)
	{
		if ( is_bool($boolean) ) $this->showBackDay = $boolean;
	}

	/**
 	* set show next day of next month
 	* 
 	* @param	boolean $boolean (TRUE or FALSE)
	* 
	* @return 	void
	*/
	public function setShowNextDay($boolean)
	{
		if ( is_bool($boolean) ) $this->showNextDay = $boolean;
	}

	/**
 	* calculate number of week
 	* 
 	* @param	integer $d (1-31)
 	* @param	integer $m (1-12)
 	* @param	integer $y (1970 or 2009)
 	* 
	* @return integer
	*/
	public function getWeek($d = '', $m = '', $y = '')
	{
		$day 	= 	date('j');
		$month 	=	date('n');
		$year 	= 	date('Y');
		
		if ( is_numeric($d) && $d > 0 ) $day 	= $d * 1;
		if ( is_numeric($m) && $m > 0 ) $month 	= $m * 1;
		if ( is_numeric($y) && $y > 0 ) $year 	= $y * 1;
		
		$firstWeek 		= 	date('w', mktime( 0, 0, 0, $month, 1, $year));
		
		return ceil( ( $day + $firstWeek )  / 7 );
	}
	
	/**
 	* get Calendar Week
 	* 
 	* @param	integer $w (1-6)
 	* @param	integer $m (1-12)
 	* @param	integer $y (1970 or 2009)
 	* 
	* @return 	output
	*/
	public function calendarWeek($w = '', $m = '', $y = '', $sid = array(), $eid = array())
	{
		$list = '';
		
		if ( is_numeric($w) && $w > 0 && $w < 7 ) $this->defaultWeek = $w * 1;
		if ( is_numeric($m) && $m > 0 && $m < 13) $this->defaultMonth = $m * 1;
		if ( is_numeric($y) && $y > 0 ) $this->defaultYear = $y * 1;
		
		$this->totalDayInMonth = date('t', mktime( 0, 0, 0, $this->defaultMonth, 1, $this->defaultYear) );
		$this->firstDayOfWeek = $this->_getFirstDayOfWeek();
		$this->lastDayOfWeek = $this->_getLastDayOfWeek();

		$backWeek	=	$this->defaultWeek - 1;
		$backMonth	=	$this->defaultMonth;
		$backYear	=	$this->defaultYear;	

		$nextWeek	=	$this->defaultWeek + 1;
		$nextMonth	=	$this->defaultMonth;
		$nextYear	=	$this->defaultYear;	
		
		if ( $this->defaultWeek == 1 && $this->firstDayOfWeek != 0 )
		{
			$backMonth	=	$this->defaultMonth -1;
			$backYear	=	$this->defaultYear;	
			
			if ( $backMonth < 1 )
			{
				$backMonth	=	12;
				$backYear	=	$this->defaultYear -1;
			}
			
			$backWeek = $this->getWeek( date('t', mktime( 0, 0, 0, $backMonth, 1, $backYear) ) , $backMonth, $backYear);
			
			$totalDay = 7 - $this->firstDayOfWeek;

			$totalDayInBackMonth = date('t', mktime( 0, 0, 0, $backMonth, 1, $backYear) );

			$startDayInBackMonth = ( $totalDayInBackMonth - $this->firstDayOfWeek ) + 1;
			
			$list .= "<tr>\n<td class='dateValue'></td>";
			
			for ( $i = $startDayInBackMonth; $i <= $totalDayInBackMonth; $i++ )
			{
				$y = ( $this->showBackDay ) ? $i : 0;
				$list .= $this->_blankFormat($y, $backMonth, $backYear);
			}
			for ( $i = 1; $i <= $totalDay; $i++ )
			{
				$list .= $this->_dayFormat($i);
			}
			$list .= "</tr>\n";

			for($h=0; $h<24;$h++)
			{
				$list .= "<tr>";
				$list .= "<td class='dateValue'>";
				if($h == 0)
				{
					$hvalue = "12 <sup>AM</sup>";
				}
				elseif($h < 12)
				{
					$hvalue = $h." <sup>AM</sup>";
				}
				elseif($h == 12)
				{
					$hvalue = $h." <sup>PM</sup>";
				}
				else
				{
					$value = $h - 12;
					$hvalue = $value." <sup>PM</sup>";
				}
				$list .= "<strong>".$hvalue."</strong>";
				$list .= "</td>";
				for ( $i = $startDayInBackMonth; $i <= $totalDayInBackMonth; $i++ )
				{
					$list .= "<td class='bookingValue'>";
					$list .= OsAppscheduleCalendar::listItemsByHour($h, $i, $backMonth, $backYear, $sid, $eid);
					$list .= "</td>";
					//$list .= $this->_blankFormat($y, $backMonth, $backYear);
				}
				for ( $i = 1; $i <= $totalDay; $i++ )
				{
					$list .= "<td class='bookingValue'>";
					$list .= OsAppscheduleCalendar::listItemsByHour($h, $i, $this->defaultMonth, $this->defaultYear, $sid, $eid);
					$list .= "</td>";
					//$list .= $this->_dayFormat($i);
				}
				
				$list .= "</tr>\n";
			}
		}
		else
		{
			$m = 1;

			$startDay = ( ( 7 * ( $this->defaultWeek - 1 ) ) - $this->firstDayOfWeek ) + 1;
			
			$i = $startDay;

			$list .= "<tr>\n<td class='dateValue'></td>";

			while ( $i < ( 7 + $startDay ) )
			{
				if ( $i <= $this->totalDayInMonth )
				{
					$list .= $this->_dayFormat($i);
					$m++;
				}
				$i++;
			}

			if ( $m < 8 )
			{
				$nextWeek	=	1;
				$nextMonth	=	$this->defaultMonth +1;
				$nextYear	=	$this->defaultYear;	

				if ( $nextMonth > 12 )
				{
					$nextMonth	=	1;
					$nextYear	=	$this->defaultYear +1;
				}

				$total = 8 - $m;
				
				for ( $x = 1; $x <= $total; $x++ )
				{
					$z = ( $this->showNextDay ) ? $x : 0;
					$list .= $this->_blankFormat($z, $nextMonth, $nextYear);
				}
				$list .= "</tr>\n";

			}

			for($h=0; $h<24;$h++)
			{
				$list .= "<tr>";
				$list .= "<td class='dateValue'>";
				if($h == 0)
				{
					$hvalue = "12 <sup>AM</sup>";
				}
				elseif($h < 12)
				{
					$hvalue = $h." <sup>AM</sup>";
				}
				elseif($h == 12)
				{
					$hvalue = $h." <sup>PM</sup>";
				}
				else
				{
					$value = $h - 12;
					$hvalue = $value." <sup>PM</sup>";
				}
				$list .= "<strong>".$hvalue."</strong>";
				$list .= "</td>";
				$m = 1;

				$startDay = ( ( 7 * ( $this->defaultWeek - 1 ) ) - $this->firstDayOfWeek ) + 1;
				
				$i = $startDay;

				//$list .= "<tr>\n<td class='dateValue'></td>";

				while ( $i < ( 7 + $startDay ) )
				{
					if ( $i <= $this->totalDayInMonth )
					{
						$list .= "<td class='bookingValue'>";
						$list .= OsAppscheduleCalendar::listItemsByHour($h, $i, $this->defaultMonth, $this->defaultYear, $sid, $eid);
						$list .= "</td>";
						$m++;
					}
					$i++;
				}
				if ( $m < 8 )
				{
					$nextWeek	=	1;
					$nextMonth	=	$this->defaultMonth +1;
					$nextYear	=	$this->defaultYear;	

					if ( $nextMonth > 12 )
					{
						$nextMonth	=	1;
						$nextYear	=	$this->defaultYear +1;
					}

					$total = 8 - $m;
					
					for ( $x = 1; $x <= $total; $x++ )
					{
						$z = ( $this->showNextDay ) ? $x : 0;
						$list .= "<td class='bookingValue'>";
						$list .= OsAppscheduleCalendar::listItemsByHour($h, $z, $nextMonth, $nextYear, $sid, $eid);
						$list .= "</td>";
					}
					$list .= "</tr>\n";
					//$list .= $this->_dayFormat($i);
				}
				
				$list .= "</tr>\n";
			}
			
			if ( $m == 8 )
			{
				$list .= "</tr>\n";
			}
		}
			
		$weekTitle = $this->weekTitleFormat();
		
		$javaScript = $this->_showJavaScript();

		return <<<HTML
			{$javaScript}
			<table width="{$this->defaultTableWidth}" class="calendartable">
				<tr class="calendar-header">
					<td class="calendar-header"><a href='?w={$backWeek}&m={$backMonth}&y={$backYear}' onclick='getCalendarWeek($backWeek,$backMonth,$backYear); return false;'> </a></td>
					<td colspan='6' class="calendar-header">
						{$weekTitle}
					</td>
					<td class="calendar-header"><a href='?w={$nextWeek}&m={$nextMonth}&y={$nextYear}' onclick='getCalendarWeek($nextWeek,$nextMonth,$nextYear); return false;'>  </a></td>
				</tr>
				<tr class="calendar-title">
					<th width='5.5%' class="calendar-title"></th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[0]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[1]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[2]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[3]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[4]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[5]}</th>
					<th width='13.5%' class="calendar-title">{$this->dayOfWeekTitle[6]}</th>
				</tr>
				{$list}
			</table>
HTML;
	}
	
	/**
 	* calculate First Day Of Week
 	* 
	* @return 	integer
	*/
	private function _getFirstDayOfWeek()
	{
		return date('w', mktime( 0, 0, 0, $this->defaultMonth, 1, $this->defaultYear) );
	}
	
	/**
 	* calculate Last Day Of Week
 	* 
	* @return 	integer
	*/
	private function _getLastDayOfWeek()
	{
		return date('w', mktime( 0, 0, 0, $this->defaultMonth, date('t', mktime( 0, 0, 0, $this->defaultMonth, 1, $this->defaultYear) ), $this->defaultYear) );
	}
	
	/**
 	* get Calendar Month
 	* 
 	* @param	integer $m (1-2)
 	* @param	integer $y (1970 or 2009)
 	* 
	* @return 	output
	*/
	public function calendarMonth( $m = '', $y = '', $inYearCalendar = false)
	{
		$list = '';
		if ( is_numeric($m) && $m > 0 && $m < 13) $this->defaultMonth = $m;
		if ( is_numeric($y) && $y > 0 ) $this->defaultYear = $y;
		
		$this->totalDayInMonth = date('t', mktime( 0, 0, 0, $this->defaultMonth, 1, $this->defaultYear) );
		$this->firstDayOfWeek = $this->_getFirstDayOfWeek();
		$this->lastDayOfWeek = $this->_getLastDayOfWeek();

		$backMonth	= $this->defaultMonth - 1;
		$backYear	= $this->defaultYear;

		if ( $backMonth < 1 )
		{
			$backMonth = 12;
			$backYear = $this->defaultYear - 1;
		}

		$nextMonth	= $this->defaultMonth + 1;
		$nextYear	= $this->defaultYear;

		if ( $nextMonth > 12 )
		{
			$nextMonth = 1;
			$nextYear = $this->defaultYear + 1;
		}

		if ( $this->firstDayOfWeek )
		{
			$totalDayInBackMonth = date('t', mktime( 0, 0, 0, $backMonth, 1, $backYear) );

			$startDayInBackMonth = ( $totalDayInBackMonth - $this->firstDayOfWeek ) + 1;

			$list .= "<tr>\n";

			for ( $i = $startDayInBackMonth; $i <= $totalDayInBackMonth; $i++ )
			{
				$y = ( $this->showBackDay ) ? $i : 0;
				$list .= $this->_blankFormat($y, $backMonth, $backYear);
			}
		}

		for ( $i = 1; $i <= $this->totalDayInMonth; $i++ )
		{
			$dayOfWeek = $this->_dayOfWeek($i);

			if ( $dayOfWeek == 0 ) $list .= "<tr>\n";

			$list .= $this->_dayFormat($i);

			if ( $dayOfWeek == 6 ) $list .= "</tr>\n";
		}

		if ( $this->lastDayOfWeek != 6 )
		{
			$total = 6 - $this->lastDayOfWeek;

			for ( $i = 1; $i <= $total; $i++ )
			{
				$z = ( $this->showNextDay ) ? $i : 0;
				$list .= $this->_blankFormat($z, $nextMonth, $nextYear);
			}
			$list .= "</tr>\n";
		}
		
		$monthTitleFormat = $this->monthTitleFormat();

		$colspan = ( ! $inYearCalendar ) ? 5 : 7;

		$backNavigation = ( ! $inYearCalendar ) ? "<td class='calendar-header'><a href='?m={$backMonth}&y={$backYear}' onclick='getCalendarMonth($backMonth,$backYear); return false;'></a></td>" : '&nbsp;'; 

		$nextNavigation = ( ! $inYearCalendar ) ? "<td class='calendar-header'><a href='?m={$nextMonth}&y={$nextYear}' onclick='getCalendarMonth($nextMonth,$nextYear); return false;'> </a></td>" : '&nbsp;';
		
		$javaScript = $this->_showJavaScript();

		return <<<HTML
			{$javaScript}
			<table width="{$this->defaultTableWidth}" class="calendartable">
				<tr class="calendar-header">
					{$backNavigation}
					<td colspan='{$colspan}' class="calendar-header">
						{$monthTitleFormat}	
					</td>
					{$nextNavigation}
				</tr>
				<tr class="calendar-title">
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[0]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[1]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[2]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[3]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[4]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[5]}</th>
					<th width='14%' class="calendar-title">{$this->dayOfWeekTitle[6]}</th>
				</tr>
				{$list}
				<tr>
					<td colspan='7' class="calendar-today">
						<a href='?m={$this->todayMonth}&y={$this->todayYear}'> Today </a>
					</td>
				</tr>
			</table>\n
HTML;
	}

	/**
 	* get Calendar Year
 	* 
 	* @param	integer $y (1970 or 2009)
 	* 
	* @return 	output
	*/
	public function calendarYear($y = '')
	{
		$list = '';
		$i = 0;

		if ( is_numeric($y) && $y > 0 ) $this->defaultYear = $y * 1;
		
		$colWidth = ceil( 100 / $this->defaultPerRow );

		for ( $m = 1; $m <= 12; $m++ )
		{
			if ( $i == 0 ) $list .= "<tr>\n";
			$list .= "<td valign='top' align='center' width='{$colWidth}%'  class='calendar-year'>" . $this->calendarMonth( $m, $y, true ) . "</td>\n";
			$i++;
			if ( $i == $this->defaultPerRow ){ $list .= "</tr>\n"; $i = 0; }
		}

		if ( $i != 0 )
		{
			while ( $i != $this->defaultPerRow )
			{
				$list .= "<td width='{$colWidth}%'>&nbsp;</td>\n";
				$i++;
			}
			$list .= "</tr>\n";
		}

		$backYear = $this->defaultYear - 1;
		$nextYear = $this->defaultYear + 1;
		$yearTitle = $this->yearTitleFormat();
		$javaScript = $this->_showJavaScript();

		return <<<HTML
			{$javaScript}
			<table width='100%' cellpadding='5' cellspacing='2'>
				<tr class="calendar-header">
					<td colspan='{$this->defaultPerRow}' class="calendar-header">
						<a href='?y={$backYear}' onclick='getCalendarYear($backYear); return false;'>  </a>
						{$yearTitle}	
						<a href='?y={$nextYear}' onclick='getCalendarYear($nextYear); return false;'>  </a>
					</td>
				</tr>
				{$list}
			</table>
HTML;
	}
	
	/**
 	* JavaScript to Show Event
 	* 
 	* @param 	integer $day (1-31)
 	* 
	* @return 	string
	*/
	protected function _showJavaScript()
	{
		return <<<HTML
<script type="text/javascript">
	function showEvent(id)
	{
		document.getElementById(id).style.display = "block";
	}
	function hideEvent(id)
	{
		document.getElementById(id).style.display = "none";
	}
</script>
HTML;
	}

	/**
 	* day Format
 	* 
 	* @param 	integer $day (1-31)
 	* 
	* @return 	string
	*/
	protected function _dayFormat($day)
	{
		$bg = '';
		
		$class = '';
		
		$dayFormat = '';
		
		$dayOfWeek	=	$this->_dayOfWeek($day);
		
		$tDMY		=	date('j') . '-' .  date('n') . '-' . date('Y');

		$DMY		=	$day . '-' .  $this->defaultMonth . '-' . $this->defaultYear;

		if ( isset($this->eventDay[$DMY]) )
		{
			$dayFormat = $this->eventDayFormat($day, $DMY);
		}
		else
		{
			$dayFormat = $this->normalDayFormat($day);
		}

		if ( $tDMY == $DMY ) 
		{
			$class = "class='{$this->defaultTodayClass} dateValue'";
		}
		else
		{
			$class = "class='dateValue'";
		}
		
		if ( $this->showColor && $tDMY != $DMY )
		{
			$bg = "style='background-color: {$this->colorArray[$dayOfWeek]};'";
		}
		
		return "<td $class $bg>
					$dayFormat
				</td>\n";
	}

	/**
 	* normal day format
 	* 
 	* @param	integer $day (1-31)
 	* 
	* @return 	integer
	*/
	protected function normalDayFormat($day)
	{
		return "<div>".$day."/".$this->defaultMonth."/".$this->defaultYear."</div>";
	}

	/**
 	* normal day format
 	* 
 	* @param	integer $day (1-31)
 	* @param	string $DMY ( date('j-n-Y') )
 	* 
	* @return 	integer
	*/
	protected function eventDayFormat($day,$DMY)
	{
		return "<div class='{$this->defaultEventDayClass}' onmouseover=\"showEvent('$DMY')\" onmouseout=\"hideEvent('$DMY')\" >
					<a class='link1' href=\"javascript:openPopUp('700','500','activity/popup.php?date={$DMY}','calendarPopup')\">$day</a>
				</div>
				<div id='{$DMY}' class='popup'>{$this->eventDay[$DMY]}</div>";
	}

	/**
 	* calculate day of week
 	* 
 	* @param	integer $day (1-31)
 	* 
	* @return 	integer
	*/
	private function _dayOfWeek($day)
	{
		return $weekDay = date('w', mktime( 0, 0, 0, $this->defaultMonth, $day, $this->defaultYear) );
	}

	/**
 	* blank column
 	* 
	* @return 	string
	*/
	private function _blankFormat($day, $month, $year)
	{
		$day = ( $day == 0 ) ? '&nbsp;' : $day;
		return "<td class='{$this->defaultBlankClass} dateValue'><div>".$day."/".$month."/".$year."</div></td>\n";
	}

	/**
 	* display page title
 	* 
 	* @param	string	$mode
 	* 						w :: display week title
 	* 						m :: display month title
 	* 						y :: display year title
 	* 
	* @return 	output
	*/
	public function getPageTitle( $mode = '')
	{
		switch ( $mode )
		{
			case 'w':
				return $this->weekTitleFormat();
			break;

			case 'm':
				return $this->monthTitleFormat();
			break;

			case 'y':
				return $this->yearTitleFormat();
			break;

			default:
				return $this->yearTitleFormat();
			break;
		}
	}

	/**
 	* title of format in calendar week
 	* 
	* @return 	string
	*/
	public function weekTitleFormat()
	{
		$title = '';
		$monthTitle = $this->monthTitle[$this->defaultMonth-1];
		$year = ( $this->thaiYear == true ) ? ($this->defaultYear) : $this->defaultYear;

		$title = str_replace('{week}', $this->defaultWeek, $this->defaultWeekTitleFormat);
		$title = str_replace('{month}', $monthTitle, $title);
		$title = str_replace('{year}', $year, $title);
		return $title;
	}

	/**
 	* title of format in calendar month
 	* 
	* @return 	string
	*/
	public function monthTitleFormat()
	{
		$title = '';
		$monthTitle = $this->monthTitle[$this->defaultMonth-1];
		$year = ( $this->thaiYear == true ) ? ($this->defaultYear + 543) : $this->defaultYear;

		$title = str_replace('{month}', $monthTitle, $this->defaultMonthTitleFormat);
		$title = str_replace('{year}', $year, $title);
		return $title;
	}

	/**
 	* title of format in calendar year
 	* 
	* @return 	string
	*/
	public function yearTitleFormat()
	{
		$title = '';
		$year = ( $this->thaiYear == true ) ? ($this->defaultYear + 543) : $this->defaultYear;
		$title = str_replace('{year}', $year, $this->defaultYearTitleFormat);
		return $title;
	}
}

?>