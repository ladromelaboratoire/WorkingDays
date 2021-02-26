<?php

/********************
 *
 *	file : WorkingDays.php
 *	author : La Drome laboratoire
 *	version: 2.1.3
 *	Date: 27/01/2020
 *	
 *	This class works on dates to determine 
 *		- is Weekend
 *		- is public holiday
 *		- get the previous / next working day date
 *		- get a start / due date calculated on the provided date plus a delay expressed in working days
 *
 *	Input
 *			array[]['date'] = 'YYYY-MM-DD'
 *			array[]['dlt'] = delivery lead time in working days. Can be negative
 *			array[]['label'] = any data usefull for further processes, returned as provided
 *			
 *	Output
 *			array[]['date'] = 'YYYY-MM-DD'
 *			array[]['dlt'] = delivery lead time in working days. Can be negative
 *			array[]['nextday'] or array[]['prevday'] = previous/next working days YYYY-MM-DD
 *			array[]['iswe'] = boolean is weekend
 *			array[]['ishol'] = boolean is public holiday
 *			array[]['duedate'] or array[]['startdate'] = Start / Due date YYYY-MM-DD
 *			array[]['error'] = Error message while dealing with input
 *
 *
 ********************/
 
 namespace ladromelaboratoire\workingdays;
 
 abstract class WorkingDays {

	protected CONST __NBHOLPERYEAR = 1;
	
	/* Protected CONST to give ability to modify array keys labels in child class */
	protected CONST __IN_DATE = 'date';
	protected CONST __IN_DLT = 'dlt';
	protected CONST __OUT_NEXTDAY = 'nextday';
	protected CONST __OUT_PREVDAY = 'prevday';
	protected CONST __OUT_ISWE = 'iswe';
	protected CONST __OUT_ISHOL = 'ishol';
	protected CONST __OUT_DUEDATE = 'duedate';
	protected CONST __OUT_STARTDATE = 'startdate';
	protected CONST __OUT_ERROR = 'error';
	protected CONST __OUT_TIMESTP = 'timestamp';
	protected CONST __MSG_INVALIDDATE = 'Date provided invalid';
	protected CONST __MSG_NODLT = 'No DLT provided';
	
	private CONST __ISWD = 0;
	private CONST __NEXTDAY = 1;
	private CONST __DUEDAY = 3;
	private CONST __ALL = -1;
	private CONST __NBWEEKPERYEAR = 52;
	private CONST __NBDAYPERYEAR = 365;
	private CONST __SECONDSPERDAY = 86400;
	private CONST __SATURDAY = 6;
	private CONST __SUNDAY = 0;
	private CONST __DAYSPERWEEK = 7;
	private CONST __WORKINGDAYPERWEEK = 5;
	private CONST __WORKINGDAYPERWEEKWITHSATURDAY = 6;
	private CONST __DATEPATTERN = '/[1|2][0-9]{3}-([0][1-9]|[1][0-2])-([0][1-9]|[1-2][0-9]|[3][0-1])/';
	private CONST __INPUTDATEPATTERN = 'Y-m-d';
	
	protected $publicHolidays = array();
	
	private $Years = array();
	private $Dates = array();
	private $SaturdayIsOn = false;
	 
	/**********
	 *
	 * Class constructor
	 * @param : bool saturdayIsOn
	 *		true : saturday is a working day
	 *		false : saturday is not a working day (default)
	 *
	 **********/
	
	public function  __construct ($saturdayIsOn = false) {
		if (is_bool($saturdayIsOn)) $this->SaturdayIsOn = $saturdayIsOn;
	}
	
	/**********
	 *
	 * Public function to set the dates array to treat.
	 * @param : array dates
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 *
	 * @output boolean
	 *
	 **********/
	public function setDates($dates) {
		if(is_array($dates)) {
			$this->Dates = $dates;
			return $this->checkDates();
		}
		else {
			return false;
		}
	}

	/**********
	 *
	 * Public function to check for each date of the input array is working day.
	 * @param : array dates optionnal
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 *
	 *	If param not provided, the class checks if $this->Dates is set
	 *
	 * @output : array dates
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 * 		dates[]['timestamp'] int date timestamp
	 * 		dates[]['iswe'] bool is week end
	 * 		dates[]['ishol'] bool is holiday
	 * 		dates[]['error'] error message if needed
	 *
	 **********/
	public function checkWorkingDay($dates = null) {
		if($this->setDates($dates) || count($this->Dates) > 0) {
			$this->doJob(self::__ISWD);
			return $this->Dates;
		}
		else {
			return false;
		}
		
	}
	
	/**********
	 *
	 * Public function to compute the next working day date.
	 * @param : array dates optionnal
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 *
	 *	If param not provided, the class checks if $this->Dates is set
	 *
	 * @output : array dates
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 * 		dates[]['timestamp'] int date timestamp
	 * 		dates[]['nextday'] string YYYY-MM-DD
	 * 		dates[]['error'] error message if needed
	 *
	 **********/
	public function getNextWDay($dates = null) {
		if($this->setDates($dates) || count($this->Dates) > 0) {
			$this->doJob(self::__NEXTDAY);
			return $this->Dates;
		}
		else {
			return false;
		}
	}
	
	/**********
	 *
	 * Public function to compute the delivery lead time date in "working days".
	 * @param : array dates optionnal
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 *
	 *	If param not provided, the class checks if $this->Dates is set
	 *
	 * @output : array dates
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 * 		dates[]['timestamp'] int date timestamp
	 * 		dates[]['duedate'] string YYYY-MM-DD
	 * 		dates[]['error'] error message if needed
	 *
	 **********/
	public function getDueDate($dates = null) {
		if($this->setDates($dates) || count($this->Dates) > 0) {
			$this->doJob(self::__DUEDAY);
			return $this->Dates;
		}
		else {
			return false;
		}		
	}
	
	/**********
	 *
	 * Public function to compute the delivery lead time date in "working days".
	 * @param : array dates optionnal
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 *
	 *	If param not provided, the class checks if $this->Dates is set
	 *
	 * @output : array dates
	 *		dates[]['date'] string YYYY-MM-DD
	 * 		dates[]['dlt'] int delivery lead time
	 * 		dates[]['timestamp'] int date timestamp
	 * 		dates[]['iswe'] bool is week end
	 * 		dates[]['ishol'] bool is holiday
	 * 		dates[]['nextday'] string YYYY-MM-DD
	 * 		dates[]['duedate'] string YYYY-MM-DD
	 * 		dates[]['error'] error message if needed
	 *
	 **********/
	public function getAll($dates = null) {
		if($this->setDates($dates) || count($this->Dates) > 0) {
			$this->doJob(self::__ALL);
			return $this->Dates;
		}
		else {
			return false;
		}		
	}
	
	/**********
	 *
	 * Public function to check if a specific day is workable
	 * @param : string YYYY-MM-DD
	 *
	 * @output : boolean
	 *		true : is workable
	 *		false : is not workable
	 *
	 **********/
	public function isWorkable($date = null) {
		$isdate = (bool)preg_match(self::__DATEPATTERN, $date);
		if($date !== null && $isdate) {
			$thatDay[0][self::__IN_DATE] = $date;
			if($this->setDates($thatDay)) {
				$this->doJob(self::__ISWD);
				if (!$this->Dates[0][self::__OUT_ISWE] && !$this->Dates[0][self::__OUT_ISHOL]) {
					return true;
				}
			}
		}
		return false;
	}
	
	/******************************************************************/
	/******************************************************************/
	/******************************************************************/
	

	/*************
	 *
	 * This function drives the calculation job acording to users request
	 *
	 *************/	
	
	private function doJob($job) {
		$this->buildHolidaysArray();
		switch ($job) {
			case self::__ISWD:
				return $this->isWorkingDay();
				break;
			case self::__NEXTDAY:
				return $this->nextWorkingDay();
				break;
			case self::__DUEDAY:
				return $this->DUEDate();
				break;
			default:
				$this->isWorkingDay();
				$this->nextWorkingDay();
				return $this->DUEDate();
				break;
		}
	}

	/*************
	 *
	 * This function computes the due day for each entry of the array. The calculation workh forward and backward
	 *
	 *************/		
	private function DUEDate() {
		$nbJouvre = ($this->SaturdayIsOn) ? self::__WORKINGDAYPERWEEKWITHSATURDAY : self::__WORKINGDAYPERWEEK;
				
		foreach($this->Dates as &$date) {
			if(!array_key_exists(self::__OUT_ERROR, $date)) {
				if(array_key_exists(self::__IN_DLT, $date) && is_int($date[self::__IN_DLT])) {
					$publicHolidays = $this->publicHolidays;
					$isBackward = $this->isBackward($date);
					//calcul de la date de rendu brute
					
					$date_brute = $date[self::__OUT_TIMESTP] + 
						(
							(
								intdiv($date[self::__IN_DLT], $nbJouvre) 
								* self::__DAYSPERWEEK) 
							+ ($date[self::__IN_DLT] % $nbJouvre) 
							+ (($isBackward) ? -2 : 0) //added in version 2.1.1
						) * self::__SECONDSPERDAY ;
						
					//if $date_brute is public holiday, get the next working day
					if ($this->isHolidays($date_brute)) $date_brute = $this->getNextWorkingDay($date_brute, $isBackward);
					
					//détermination du nombre de jours féries entre la date de départ et la date brute.
					if (!in_array($date[self::__OUT_TIMESTP], $publicHolidays)) $publicHolidays[] = $date[self::__OUT_TIMESTP];
					if (!in_array($date_brute, $publicHolidays)) $publicHolidays[] = $date_brute;
					sort($publicHolidays);
					
					$nbjourFerie = array_keys($publicHolidays, $date_brute)[0] - array_keys($publicHolidays, $date[self::__OUT_TIMESTP])[0] + (($isBackward) ? 1 : -1) ;
					$date_finale = $this->getNextWorkingDay($date_brute + $nbjourFerie * self::__SECONDSPERDAY, $isBackward);
		
					$date[(($isBackward) ? self::__OUT_STARTDATE : self::__OUT_DUEDATE)] = date(self::__INPUTDATEPATTERN, $date_finale);
				}
				else {
					$date[self::__OUT_ERROR] = self::__MSG_NODLT;
				}
			}
		}
		return true;
	}
	/*************
	 *
	 * This function calculates the next working day based on timestanps
	 *
	 *************/	 	
	private function getNextWorkingDay($date, $backward = false) {
		
		while($this->isHolidays($date) || $this->isWeekEnd($date)) {
			$date += (($backward) ? -1 : 1) * self::__SECONDSPERDAY;
		}
		
		return $date;
	}
	

	/*************
	 *
	 * This function calculates the next/previous working day for each date of the input array. Previous day if 'dlt' is negative
	 *
	 *************/	 
	private function nextWorkingDay() {
		
		
		foreach($this->Dates as &$date) {
			if(array_key_exists(self::__OUT_TIMESTP, $date)) {
				$isBackward = $this->isBackward($date);
				$date[(($isBackward) ? self::__OUT_PREVDAY : self::__OUT_NEXTDAY)] = date(self::__INPUTDATEPATTERN, $this->getNextWorkingDay($date[self::__OUT_TIMESTP], $isBackward));
			}
		}
		return true;
	}
	
	
	/*************
	 *
	 * This function calculates the not working status of the provided dates.
	 *
	 *************/	
		
	private function isWorkingDay() {
		foreach($this->Dates as &$date) {
			if(array_key_exists(self::__OUT_TIMESTP, $date)) {
				$date[self::__OUT_ISWE] = $this->isWeekEnd($date[self::__OUT_TIMESTP]);
				$date[self::__OUT_ISHOL] = $this->isHolidays($date[self::__OUT_TIMESTP]);
			}
		}
		return true;
	}

	/*************
	 *
	 * This function builds the hollydays dates array to check the working days and compute delivery lead time
	 * Works while counting forward and backwards
	 *
	 *************/	
	
	private function buildHolidaysArray() {
		
		$years_involved = array();
		
		foreach($this->Dates as $date) {
			if(!array_key_exists(self::__OUT_ERROR, $date)) {
				
				$year = intval(date("Y",strtotime($date[self::__IN_DATE])));
				$years_involved[] = $year;
				
				if(array_key_exists(self::__IN_DLT, $date) && is_int($date[self::__IN_DLT])) {
					
					$years_involved[] = $year + intdiv($date[self::__IN_DLT], (self::__NBDAYPERYEAR - (self::__NBWEEKPERYEAR + self::__NBHOLPERYEAR))); //rounded number of year for the requested delay. This is needed to get the correct holidays array and findout all the holidays between dates	
				}
			}
		}
		$years_involved = array_unique($years_involved, SORT_NUMERIC);

		$this->addYearToHolidayArray(min($years_involved)-1); //minimum year -1 to compute public holidays
		$this->addYearToHolidayArray(max($years_involved)+1); //maximum year +1 to compute public holidays
		
		$this->YearFillGaps();
	}
	
	
	/*************
	 *
	 * This function, for each given year, checks if already done. If not, adds one it and calls the HolidaysOfYearToArray() method
	 *
	 *************/	
	
	private function addYearToHolidayArray($year = null) {
		
		if (is_int($year)) {
			//add this year if needed
			if (!in_array($year, $this->Years)) {
				//add public holidays
				if($this->HolidaysOfYearToArray($year)) {
					$this->Years[] = $year;
				}
				sort($this->Years);
				return true;
			}
		}
		return false;
	}
	/*************
	 *
	 * This function fills gaps in Annee & public holiday array to avoid bad calculation
	 *
	 *************/		
	private function YearFillGaps() {
		//Fill gaps in public holidays array
		$size = count($this->Years);
		while (($this->Years[$size - 1] - $this->Years[0]) >= $size) {
			$i = 0;
			while (($this->Years[$i+1] - $this->Years[$i]) == 1) {
				$i++;
			}
			$this->addYearToHolidayArray($this->Years[$i] + 1);
			$size = count($this->Years);
		}
		return true;
	}
	
	/*************
	 *
	 * This function adds all the holidays of a given year to the holidays array.
	 * This abstracted function need to be defined in class extension per country.
	 *
	 *************/
	abstract protected function HolidaysOfYearToArray($year);
	
	/*************
	 *
	 * This function checks if date array has the following minimal format and each provided date is valid:
	 *			$array[0]['date'] = "YYYY-MM-DD"		
	 *			$array[1]['date'] = "YYYY-MM-DD"		
	 *			$array[2]['date'] = "YYYY-MM-DD"
	 *
	 *************/	
	private function checkDates() {
		
		$result = true;
		if (array_key_exists(self::__IN_DATE, $this->Dates[0])) {
			foreach ($this->Dates as &$date) {
				$jour = explode("-",$date[self::__IN_DATE]);
				
				$valabledate = checkdate($jour[1], $jour[2], $jour[0]);
				if ($valabledate) {
					$date[self::__OUT_TIMESTP] = strtotime($date[self::__IN_DATE]);
				}
				else {
					$date[self::__OUT_ERROR] = self::__MSG_INVALIDDATE ;
				}
				$result = $result && $valabledate; 

			}
			return $result;
		}
		else {
			return false;
		}
	}
	
	/*************
	 *
	 * This check if the proposed day is a weekdend day according to $this->SaturdayIsOn
	 *
	 *************/	
	protected function isWeekEnd($date = null) {
		// Saturday as working day => $saturdayIsOn = true
		// Working on timestamps
		
		$jour = date("w", $date);
		
		if ($jour == self::__SUNDAY || ($jour == self::__SATURDAY && !$this->SaturdayIsOn) ) { 
			return true; 
		}
		else { 
			return false;
		}
	}
	/*************
	 *
	 * This check if the proposed day is holiday
	 *
	 *************/	
	private function isHolidays($date = null) {
		return in_array($date, $this->publicHolidays);
	}
	
	/*************
	 *
	 * This check if we count backward or forward
	 *
	 *************/	
	private function isBackward($date = null) {
		if(array_key_exists(self::__IN_DLT, $date) && $date[self::__IN_DLT] < 0) return true;
		return false;
	}		
	
	/*************
	 *
	 * Simple debug function
	 *
	 *************/	
	private function Debug($arr) {
		$debug = array();
		foreach($arr as $item) {
			$debug[] = array('timestamp' => $item, 'date' => date(self::__INPUTDATEPATTERN,$item));
		}
		var_dump($debug);
	}
	
	
	/*********************************************
	 *
	 * END OF CLASS - END OF FILE
	 *
	 *********************************************/	
 }
 
 ?>