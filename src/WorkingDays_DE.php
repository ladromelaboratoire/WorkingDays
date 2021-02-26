<?php

/********************
 *
 *	file : WorkingDays_DE.php
 *	author : La Drome laboratoire
 *	version: 1.0.2
 *	Date: 27/01/2020
 *	
 *	This class extends WorkingDays.php to define German public holidays
 *
 *
 ********************/

 namespace ladromelaboratoire\workingdays;
 class WorkingDays_DE extends WorkingDays {
	
	/*************
	 *
	 * This constants defines the number of public holiday per year.
	 * Swiss public holidays
	 *
	 *************/	
	protected CONST __NBHOLPERYEAR = 9;
	
	/*************
	 *
	 * You can modify array keys labels & error messages here below
	 *
	 *************/	
	// protected CONST __IN_DATE = 'date';
	// protected CONST __IN_DLT = 'dlt';
	// protected CONST __OUT_NEXTDAY = 'nextday';
	// protected CONST __OUT_PREVDAY = 'prevday';
	// protected CONST __OUT_ISWE = 'iswe';
	// protected CONST __OUT_ISHOL = 'ishol';
	// protected CONST __OUT_DUEDATE = 'duedate';
	// protected CONST __OUT_STARTDATE = 'startdate';
	// protected CONST __OUT_ERROR = 'error';
	// protected CONST __OUT_TIMESTP = 'timestamp';
	// protected CONST __MSG_INVALIDDATE = 'Date provided invalid';
	// protected CONST __MSG_NODLT = 'No DLT provided';
	
	/*************
	 *
	 * This function adds all the holidays of a given year to the holidays array.
	 * Swiss public holidays
	 *
	 *************/
	protected function HolidaysOfYearToArray($year = null) {

		if (is_int($year)) {
			$easterDate = easter_date($year);
			$easterDay = date('j', $easterDate);
			$easterMonth = date('n', $easterDate);
			$easterYear = date('Y', $easterDate);

			// Jours feries fixes
			$publicHolidays[] = mktime(0, 0, 0, 1, 1, $year);// January 1st
			$publicHolidays[] = mktime(0, 0, 0, 5, 1, $year);// Workers day
			$publicHolidays[] = mktime(0, 0, 0, 10, 3, $year);// Germany's unity day
			$publicHolidays[] = mktime(0, 0, 0, 12, 25, $year);// Christmas
			$publicHolidays[] = mktime(0, 0, 0, 12, 26, $year);// Christmas' next day

			// Jour feries qui dependent de paques
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay - 2, $easterYear);// Holy Friday
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear);// Easter Monday
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear);// Ascension
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear); // Pentecote

			//insert in array only public holidays that are not normal weekend to avoid computing them twice
			foreach($publicHolidays as $publicHoliday) {
				if(!$this->isWeekEnd($publicHoliday)) $this->publicHolidays[] = $publicHoliday;
			}

			sort($this->publicHolidays);
			return true;
		}
		return false;
		
	}
	/*********************************************
	 *
	 * END OF CLASS - END OF FILE
	 *
	 *********************************************/	
 }
 
 ?>