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
    private $activeAlarm;

    function __construct(){

        $fitbit = new FitBitPHP( self::FITBIT_KEY, self::FITBIT_SECRET, 0, null, 'json', 0);
        $this->fitbitHandler = $fitbit;

        if( isset( $_SESSION['fitbit_Session'] ) )
            $this->ContinueSession();

    }

    function StartSession(){
        $fitbit = $this->fitbitHandler;
        $fitbit->initSession('http://' . $_SERVER['HTTP_HOST'] ."/authorize");
        
        if( isset($_GET['oauth_token']) || isset($_GET['oauth_verifier'])){
            if( isset($_GET['oauth_token'])){
                $this->Set_oAuth_Token($_GET['oauth_verifier']);
            }

            if( isset($_GET['oauth_verifier'])){
                $this->Set_oAuth_Verifier($_GET['oauth_verifier']);
            }
        }
    }

    function ContinueSession(){
        $fitbit = $this->fitbitHandler;
        $fitbit->initSession('http://' . $_SERVER['HTTP_HOST'] ."/authorize");
        if( !isset($this->activeDevice) && !isset($_SESSION['deviceID'])){
            $devices = $fitbit->getDevices();
            $this->PromptDevices($devices);
        }
        
        if( isset($_GET['oauth_token']) || isset($_GET['oauth_verifier'])){
            if( isset($_GET['oauth_token'])){
                $this->Set_oAuth_Token($_GET['oauth_verifier']);
            }

            if( isset($_GET['oauth_verifier'])){
                $this->Set_oAuth_Verifier($_GET['oauth_verifier']);
            }
        }
    }

    function EndSession(){
        if($this->GetActiveDeviceID!=null){
            $this->DeleteAlarm($this->GetActiveDeviceID);
            $this->UnsetActiveDeviceID();
        }
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
        if(isset($_SESSION['fitbit_Session']) && $_SESSION['fitbit_Session']==2 && $this->Get_oAuth_Token() != "" && $this->Get_oAuth_Verifier() !=""){
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
        //setcookie("oauth_token", $value, time()+3600*24); 
    }

    function Set_oAuth_Verifier($value){
        $this->oauth_verifier = $value;
        $_SESSION['oauth_verifier'] = $value;
        //setcookie("oauth_verifier", $value, time()+3600*24); 
    }

    function GetAuthURL(){
        return "";
    }

    function PromptDevices($devices){
        $devices = $this->FindQualifiedDevices($devices);

        if(count($devices) == 0){
            //print "No qualified devices found.  Must have a Fitbit Flex, Fitbit Force or Fitbit One to use this application.";
            $this->EndSession();
        }else if(count($devices) == 1){
            //print "One device found, set as active device: " . $devices[0]->id;
            $this->SetActiveDeviceID($devices[0]->id);
        }else{
            //print "Too many qualified devices found.";
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
    
    function VerifyAlarm($alarmID){
        $alarms = $this->GetAlarms();
        foreach($alarms->trackerAlarms as $alarm){
            if($alarm->alarmId == $alarmID && $alarm->enabled){
                return true;
            }
        }
        return false;
    }

    function SetActiveDeviceID($deviceID){
        $this->activeDevice = $deviceID;
        $_SESSION['deviceID']=$deviceID;
        setcookie("device_id", $deviceID, time()+3600*24);
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
    
    function UnsetActiveDeviceID(){
        $this->activeDevice = null;
        unset($_SESSION['deviceID']);
        setcookie("device_id");
    }
    
    function SetActiveAlarmID($alarmID){
        $this->activeDevice = $alarmID;
        $_SESSION['alarmID']=$alarmID;
        setcookie("alarm_id", $alarmID, time()+3600*24);
    }

    function GetActiveAlarmID(){
        if($this->activeAlarm != null){
            if($this->VerifyAlarm($this->activeAlarm)){
                return $this->activeAlarm;
            }else{
                return null;
            }
        }else if(isset($_SESSION['alarmID'])){
            if($this->VerifyAlarm($_SESSION['alarmID'])){
                $this->activeAlarm = $_SESSION['alarmID'];
                return $this->activeAlarm;
            }else{
                return null;
            }
        }
        return null;
    }
    
    function UnsetActiveAlarmID(){
        $this->activeAlarm = null;
        unset($_SESSION['alarmID']);
        setcookie("alarm_id");
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
    
    function StartAlarm($seconds){
        $alarmID = $this->GetActiveAlarmID();
        $success = false;
        $message = "";
        if($alarmID){
            $success = $this->UpdateAlarm($alarmID,$seconds);
            if(!$success) $message="Unable to update active alarm";
        }else{
           $success = $this->AddAlarm($seconds);
           if(!$success) $message="Unable to add new alarm";
        }
        if($success) $message="Alarm Started";
        
        return json_encode(['success'=>$success, 'message'=>$message,'alarmID'=>$this->GetActiveAlarmID()]);
    }
    
    function StopAlarm(){
        $alarmID = $this->GetActiveAlarmID();
        $success = false;
        $message = "";
        if($alarmID){
            $success = $this->DeleteAlarm($alarmID);
            if($success){
                $message="Alarm stopped";
            }else{
                $message="Unable to stop alarm";
            }
        }else{
            $success=false;
            $message="No alarm to stop";
        }
        return json_encode(['success'=>$success, 'message'=>$message]);
    }
    
    function CheckSync(){
        if($this->GetActiveAlarmID()){
            $alarms = $this->GetAlarms();
            foreach($alarms->trackerAlarms as $alarm){
                if($alarm->alarmId == $this->GetActiveAlarmID() && $alarm->enabled && $alarm->syncedToDevice){
                    return json_encode([
                            'success'=>true,
                            'message'=>'The alarm is currently synced to the device',
                            'isSynced'=>$alarm->syncedToDevice,
                            'alarmID'=>$alarm->alarmId
                    ]);
                }
            }
        }
        return json_encode(['success'=>false, 'message'=>'There is currently not an active alarm']);
    }
    
    function CheckAlarm(){
        if($this->GetActiveAlarmID()){
            $alarms = $this->GetAlarms();
            foreach($alarms->trackerAlarms as $alarm){
                if($alarm->alarmId == $this->GetActiveAlarmID() && $alarm->enabled){
                    return json_encode([
                        'success'=>true, 
                        'message'=>'There is currently an active alarm', 
                        'time'=>$alarm->time, 
                        'alarmID'=>$alarm->alarmId, 
                        'isSynced'=>$alarm->syncedToDevice
                    ]);
                }
            }
        }
        return json_encode(['success'=>false, 'message'=>'There is currently not an active alarm']);
    }
    
    function AddAlarm($seconds){
        $deviceID = $this->GetActiveDeviceID();
        if($deviceID){
            $timestamp = strtotime("+$seconds seconds");
            $time = new DateTime(date("m/d/Y h:i:s A T",$timestamp));

            $fitbit = $this->fitbitHandler;
            $response = $fitbit->addAlarm($deviceID, $time, true, false);
            $this->SetActiveAlarmID($response->trackerAlarm->alarmId);
            return true;
        }
        return false;
    }

    function UpdateAlarm($alarmID, $seconds){
        $deviceID = $this->GetActiveDeviceID();

        if($deviceID){
            $timestamp = strtotime("+$seconds seconds");
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
            $this->UnsetActiveAlarmID();
            return true;
        }
        return false;
    }
}
?>