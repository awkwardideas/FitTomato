<?php
	define('SITE_NAME', 'FitTomato');
	define('SITE_URL', 'http:\\fittomato.awkwardideas.com');
	
	$page="home";
	
	if(isset($_GET['page'])){
		$url = $_GET['page'];
		$url = explode("/", $url);
		switch($url[0]){
			case 'about':
				$page="about";
				break;
			case 'authorize':
				require_once('thirdParty/fitbit/FitBit.init.php');
				$fitbit = new Fitbit();
				$fitbit->Auth();				
				break;
			case 'deauthorize':
				require_once('thirdParty/fitbit/FitBit.init.php');
				$fitbit = new Fitbit();
				$fitbit->DeAuth();
				break;
		}
	}
?>