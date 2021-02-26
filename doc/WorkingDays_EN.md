# WorkingDays

The class born on the following use-cases
 * What is the due date while receiving those samples today
 * We just release the analysis results, did we repect the due date ?
Those 2 use-cases meant to be able to compute a date, forward or backward, using a delay expressed in working days. 

This abstracted PHP class provides a way to work on dates to get the following properties :
 * Check if week-end or public holidays
 * Get the next working day date
 * Get a due date according to a delay expressed in working days
 * Saturdays can be set as working days.
 
The class work on an input array providing the `date` et `dlt`. All entries other a sent back to the user without modification. The classes methods append data to the array. This is usefull to work on assynchronous processes. This class ask the **child class** to define the public holidays for a given country.

## Inside the class


### Public methods
 * *bool* `setDates()` set the dates array. Each enty is checked as valid date.
 * *array* `checkWorkingDay()` Check if the day is a working day (week end and public holidays)
 * *array* `getNextWDay()` Compute the previous/next working days. If the day is a working day, it returns itself
 * *array* `getDuedate()` Compute start/due dates
 * *array* `getAll()` Do all 3 methods above
 * *bool* `isWorkable()` send the working day status for a single day.
 
### Abstracted methods
 * *bool* `HolidaysOfYearToArray()` method that fills the public holidays array for a given year. This defined in child class. This method is __protected__
 
### Adaptation to other countries
Define a child class `WorkingDays_XX.php` where `XX` is a country code. This child class adapts the class behavior to the countries specificities.  
The child class sets (mandatory)
 * `protected CONST __NBHOLPERYEAR = 11;` number of public holidays in the country
 * `protected function HolidaysOfYearToArray($year = null)` stes the array `$this->publicHolidays` with the national public holidays for the given year.

This child class allows you to modify the array key labels as well as the error messages. This is an option.  
Examples provided for France, Belgium, Swiss. 
 
## Limits and performances
This class uses the PHP function `easter_date()`. If running a 32bits PHP version, the limit is year 2037.  
When `dlt` is null, the due date calculation returns the previous working day. This is not a bug.

Computing performance :
> 1200 entries in array  
> Delay set as random integer between 20 and 4000 working days  
> Execution time : **0.109 second**  

##Todo list
 * Move to PHPUnit
 * Move to Composer

## Example

## Input
````php
 array[]['date'] = 'YYYY-MM-DD';
 array[]['dlt'] = 'delivery lead time in working days. Can be negative';
 array[]['label'] = 'any data usefull for further processes, returned as provided';
````


### Output
This is the sample code provided in `./test/`
````txt
string 'Saturnay is a working day: no' (length=29)
array (size=6)
  0 => 
    array (size=3)
      'date' => string '2020-02-31' (length=10)
      'sample_no' => string 'Extra entry at users will' (length=25)
      'error' => string 'not a valid date' (length=16)
  1 => 
    array (size=7)
      'date' => string '2020-04-13' (length=10)
      'dlt' => int -2
      'timestamp' => int 1586728800
      'iswe' => boolean false
      'ishol' => boolean true
      'prevday' => string '2020-04-10' (length=10)
      'startdate' => string '2020-04-10' (length=10)
  2 => 
    array (size=7)
      'date' => string '2019-05-01' (length=10)
      'dlt' => int 20
      'timestamp' => int 1556661600
      'iswe' => boolean false
      'ishol' => boolean true
      'nextday' => string '2019-05-02' (length=10)
      'duedate' => string '2019-05-31' (length=10)
  3 => 
    array (size=7)
      'date' => string '2019-05-19' (length=10)
      'dlt' => int -12
      'timestamp' => int 1558216800
      'iswe' => boolean true
      'ishol' => boolean false
      'prevday' => string '2019-05-17' (length=10)
      'startdate' => string '2019-05-02' (length=10)
  4 => 
    array (size=6)
      'date' => string '2019-07-14' (length=10)
      'timestamp' => int 1563055200
      'iswe' => boolean true
      'ishol' => boolean true
      'nextday' => string '2019-07-15' (length=10)
      'error' => string 'no DLT provided' (length=15)
  5 => 
    array (size=7)
      'date' => string '2019-05-18' (length=10)
      'dlt' => int 0
      'timestamp' => int 1558130400
      'iswe' => boolean true
      'ishol' => boolean false
      'nextday' => string '2019-05-20' (length=10)
      'duedate' => string '2019-05-17' (length=10)

string 'Temps exec: 0' (length=13)

string '2020-01-10 is a working day: yes' (length=33)


````

