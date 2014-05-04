<?php
require_once( 'thirdParty/fitbit/FitBitPHP.php' );

class Fitbit{

	const FITBIT_KEY ="fea407e2274949ca8d203987d50d156c";
	const FITBIT_SECRET = "3bff1b5a660c46b5bf89e3a87530ece5";
	const FITBIT_REQUEST_TOKEN_URL = "https://api.fitbit.com/oauth/request_token";
	const FITBIT_REQUEST_ACCESS_URL = "https://api.fitbit.com/oauth/access_token";
	const FITBIT_AUTHORIZE = "https://www.fitbit.com/oauth/authorize";

	private $response;
	private $fitbitHandler;
	private $oauth_token;
	private $oauth_verifier;
	private $activeDevice;
	
	function __construct(){
		
		$fitbit = new FitBitPHP( self::FITBIT_KEY, self::FITBIT_SECRET, 0, null, 'json', 0);
		$fitbit->initSession('http://' . $_SERVER['HTTP_HOST'] ."/authorize");
		$this->fitbitHandler = $fitbit;
		
		if( isset( $_SESSION['fitbit_Session'] ) )
			$this->Auth();
			
		if( !isset($this->activeDevice) && !isset($_SESSION['deviceID'])){
			$devices = $fitbit->getDevices();
			$this->PromptDevices($devices);
		}else{
			//print_r($this->GetAlarms($_SESSION['deviceID']));	
			//print "<br/>";
			//print_r($this->SetAlarm(25));
			//print_r($this->UpdateAlarm(17032885, 5));
			//print_r($this->DeleteAlarm(17032885));
			//print "<br/>";
			//print_r($this->GetAlarms($_SESSION['deviceID']));	
		}
	}
	
	function Auth(){
		if( isset($_GET['oauth_token']) || isset($_GET['oauth_verifier'])){
			if( isset($_GET['oauth_token'])){
				$this->Set_oAuth_Token($_GET['oauth_verifier']);
			}
				
			if( isset($_GET['oauth_verifier'])){
				$this->Set_oAuth_Verifier($_GET['oauth_verifier']);
			}
		}
	}
	
	function DeAuth(){
		$fitbit = $this->fitbitHandler;
		$fitbit->resetSession();
		session_destroy();
		$this->Set_oAuth_Token("");
		$this->Set_oAuth_Verifier("");
	}
	
	function GetProfile(){
		return $this->response;
	}
	
	function IsAuthenticated(){
		if($_SESSION['fitbit_Session']==2 && $this->Get_oAuth_Token() != "" && $this->Get_oAuth_Verifier() !=""){
			return true;
		}else{
			return false;
		}
	}
	
	function Get_oAuth_Token(){
		if($this->oauth_token != ""){
			return $this->oauth_token;
		}else{
			if(isset($_SESSION['oauth_token'])){
				$this->oauth_token = $_SESSION['oauth_token'];
				return $_SESSION['oauth_token'];
			}
		}
		return "";
	}
	
	function Get_oAuth_Verifier(){
		if($this->oauth_verifier != ""){			
			return $this->oauth_token;
		}else{
			if(isset($_SESSION['oauth_verifier'])){
				$this->oauth_verifier = $_SESSION['oauth_verifier'];
				return $_SESSION['oauth_verifier'];
			}
		}
		return "";
	}
	
	function Set_oAuth_Token($value){
		$this->oauth_token = $value;
		$_SESSION['oauth_token'] = $value;
		setcookie("oauth_token", $value, time()+3600*24); 
	}
	
	function Set_oAuth_Verifier($value){
		$this->oauth_verifier = $value;
		$_SESSION['oauth_verifier'] = $value;
		setcookie("oauth_verifier", $value, time()+3600*24); 
	}
	
	function GetAuthURL(){
		return "";
	}
	
	function PromptDevices($devices){
		$devices = $this->FindQualifiedDevices($devices);
		
		if(count($devices) == 0){
			print "No qualified devices found.  Must have a Fitbit Flex, Fitbit Force or Fitbit One to use this application.";
			$this->DeAuth();
		}else if(count($devices) == 1){
			echo "One device found, set as active device: " . $devices[0]->id;
			$this->SetActiveDeviceID($devices[0]->id);
		}else{
			print "Too many qualified devices found.";
			print_r($devices);
		}
	}
	
	function FindQualifiedDevices($devices){
		$qualifiedDeviceVersions = ["Flex", "Force", "One"];
		$qualifiedDevices = [];
		
		foreach($devices as $device){
			
			if(in_array($device->deviceVersion, $qualifiedDeviceVersions)){
				$qualifiedDevices[] = $device;
			}
		}
		return $qualifiedDevices;
	}
	
	function SetActiveDeviceID($deviceID){
		$this->activeDevice = $deviceID;
		$_SESSION['deviceID']=$deviceID;
	}
	
	function GetActiveDeviceID(){
		if($this->activeDevice != null){
			return $this->activeDevice;
		}else if(isset($_SESSION['deviceID'])){
			 $this->activeDevice = $_SESSION['deviceID'];
			 return $this->activeDevice;
		}
		return null;
	}
	
	function GetAlarms(){
		$deviceID = $this->GetActiveDeviceID();
		if($deviceID){
			$fitbit = $this->fitbitHandler;
			$alarms  = $fitbit->getAlarms($deviceID);
			return $alarms;
		}
		return false;
	}
	
	function SetAlarm($minutes){
		$deviceID = $this->GetActiveDeviceID();
		if($deviceID){
			$timestamp = strtotime("+$minutes minutes");
			$time = new DateTime(date("m/d/Y h:i:s A T",$timestamp));
			
			$fitbit = $this->fitbitHandler;
			$response = $fitbit->addAlarm($deviceID, $time, true, false);
			$alertID = $response->trackerAlarm->alarmId;
			return $alertID;
		}
		return false;
	}
	
	function UpdateAlarm($alarmID, $minutes){
		$deviceID = $this->GetActiveDeviceID();
		if($deviceID){
			$timestamp = strtotime("+$minutes minutes");
			$time = new DateTime(date("m/d/Y h:i:s A T",$timestamp));
			
			$fitbit = $this->fitbitHandler;
			$response = $fitbit->updateAlarm($deviceID, $alarmID, $time, true, false, null, 9, 2);
			$alertID = $response->trackerAlarm->alarmId;
			return $alertID;
		}
		return false;
	}
	
	function DeleteAlarm($alarmID){
		$deviceID = $this->GetActiveDeviceID();
		if($deviceID){
			$fitbit = $this->fitbitHandler;
			$response = $fitbit->deleteAlarm($deviceID, $alarmID);
			return true;
		}
		return false;
	}
	
}
?>