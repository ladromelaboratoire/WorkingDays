# WorkingDays - Jours Ouvrés

Cette classe est née des deux cas d'usage suivants :
 * Quelle est la date de rendu des échantillons reçus aujourd'hui avec ce délai ?
 * Nous venons de rendre les échantillons suivants. Avons nous respecté le délai ?
Cela implique de pouvoir calculer une date future ou passée en tenant compte des jours ouvrés et fériés.

Cette classe PHP abstraite propose de la manipulation de dates avec les capacités suivantes :
 * Vérification du statut de la date (week-end et/ou jour férié)
 * Calcul du précédent/prochain jour ouvré
 * Calcul d'un délai en fonction d'un nombre de jour en tenant compte des jours ouvrés. Cela fonctionne avec un délai négatif
 * Le samedi peut être déclaré en jour ouvré
	
La classe propose de traiter un tableau de dates `date` et de délai `dlt`. Toute autre entrée est restituée en sortie sans modification. Cela permet à l'utilisateur d'utiliser la classe pour un traitement assychrone et retourver une référence comme un numéro d'échantillon.

Cette classe laisse à la **classe fille** le soin de définir la liste des jours fériés pour un pays donné. Elle permet également de personnaliser le nom des étiquettes du tableau et les messages.

## Detail de la classe

### Méthodes publiques
 * *bool* `setDates()` intègre un tableau de dates sur lesquelles travailler. Vérifie la validité de toutes les dates fournies
 * *array* `checkWorkingDay()` vérifie pour chaque jour s'il est ouvrable
 * *array* `getNextWDay()` calcule le précédent/prochain jour ouvré pour chaque date
 * *array* `getDueDate()` calcule la date de début/rendu en fonction du délai indiqué en jour ouvrables
 * *array* `getAll()` combine les 3 calculs ci-dessus
 * *bool* `isWorkable()` renvoi le statut ouvrable d'un jour donné.

### Méthodes abstraites
 * *bool* `HolidaysOfYearToArray()` Méthode de remplissage du tableau des jours fériés pour un pays et pour une année donnée en paramètre. Méthode __protected__
 
### Adaptation à d'autres pays
Définissez un classe fille `WorkingDays_XX.php` où `XX` est un code pays. Cette classe fille adapte le comportement de la classe aux spécificités du pays.  
Elements à définir obligatoirement :
 * `protected CONST __NBHOLPERYEAR = 11;` qui définit le nombre de jours fériés en France
 * `protected function HolidaysOfYearToArray($year = null)` remplit le tableau `$this->publicHolidays` avec les jours français.

En option vous pouvez personnaliser le nom des étiquettes du tableau et les messages. La France, la Belgique et la Suisse sont inclus à tritre d'exemple.
 
## Limitations et performances
Cette classe utilise la fonction PHP `easter_date()` qui est valide jusque l'an 2037 si la version de PHP est 32bits.  
Lorsque le délai est à 0, le calcul du délai renvoie le précédent jour ouvré. Ce n'est pas un bug.

Performance calculée :
> 1200 dates à calculer  
> Délai est un tirage aléatoire entre 20 et 4000 jours  
> Temps exécution : **0.109 secondes**  

## Todo list
 * utiliser PHPUnit
 * utiliser Composer

## Exemple

## Entrée
````php
 array[]['date'] = 'YYYY-MM-DD';
 array[]['dlt'] = 'délai en jour ouvré, peut être négatif';
 array[]['label'] = 'Toute donnée utile dans un autre process. Renvoyé tel que reçu';
````

### Resultat
Fournit par l'exemple de code dans `./tests/`
````txt
string 'Le samedi est travaillé: non' (length=29)

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
string 'Le 2020-01-10 est travaillé: oui' (length=33)


````

