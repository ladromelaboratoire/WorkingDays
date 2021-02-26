<?php

/********************
 *
 *	file : WorkingDays_CH.php
 *	author : La Drome laboratoire
 *	version: 1.0.2
 *	Date: 27/01/2020
 *	
 *	Classe qui étends la classe WorkingDays.php pour définir les jours fériés suisses
 *
 *
 ********************/

 namespace ladromelaboratoire\workingdays;
 class WorkingDays_CH extends WorkingDays {
	
	/*************
	 *
	 * This constants defines the number of public holiday per year.
	 * Swiss public holidays
	 *
	 *************/	
	protected CONST __NBHOLPERYEAR = 7;
	
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
			$publicHolidays[] = mktime(0, 0, 0, 1, 1, $year);// 1er janvier
			$publicHolidays[] = mktime(0, 0, 0, 8, 1, $year);// Fete nationale
			$publicHolidays[] = mktime(0, 0, 0, 12, 25, $year);// Noel

			// Jour feries qui dependent de paques
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay - 2, $easterYear);// Vendredi Saint
			$publicHolidays[] = mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear);// Lundi de paques
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