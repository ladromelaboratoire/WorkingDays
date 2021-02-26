<?php
	require '../vendor/autoload.php';
	use ladromelaboratoire\workingdays\WorkingDays_FR;	


	$samedi = true;

	$jour[0]['date'] = '2020-05-01';
	$jour[0]['dlt'] = 20;
	$jour[2]['date'] = '2020-05-19';
	$jour[2]['dlt'] = 723;
	$jour[3]['date'] = '2020-05-18';
	$jour[3]['dlt'] = 3321;
	
	// $date = strtotime("now");
	// $i = 1194;
	// while($i > 0) {
		// $jour[] = array('date' => date("Y-m-d", $date + ($i * 86400)), 'dlt' => random_int(-1200, 1200));
		// $i--;
	// }
	
	$nb_calculs = count($jour);


	$message = 'Le samedi est travaillé: ';
	$message .= ($samedi) ? 'oui' : 'non';
	var_dump($message);

	$temps = microtime(true);
	$date = new WorkingDays_FR($samedi);
	var_dump($date->getAll($jour));
	$temps = "Temps exec: " . ((microtime(true) - $temps)/$nb_calculs);
	var_dump($temps);

	$autredate = '2020-01-10';
	$isworkable = ($date->isWorkable($autredate)) ? 'oui' : 'non';
	$message =  'Le '. $autredate . ' est travaillé: ' . $isworkable . ' // methode $date->isWorkable()';
	var_dump($message);

?>