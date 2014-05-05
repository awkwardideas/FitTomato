<?php
    define('SITE_NAME', 'FitTomato');
    define('SITE_URL', 'http:\\fittomato.awkwardideas.com');

    $page="home";
    $renderPage=true;            

    require_once('thirdParty/fitbit/FitBit.init.php');
    $fitbit = new Fitbit();
    
    if(isset($_GET['page'])){
        $url = $_GET['page'];
        $url = explode("/", $url);
        switch($url[0]){
            case 'about':
                $page="about";
                break;
            case 'authorize':
                $fitbit->StartSession();				
                break;
            case 'deauthorize':
                $fitbit->EndSession();
                break;
            case 'start':
                $renderPage=false;
                header('Content-type: application/json');
                if(isset($_POST['seconds']) && is_numeric($_POST['seconds'])){
                    echo $fitbit->StartAlarm($_POST['seconds']);
                }else{
                    echo json_encode(['success'=>false, 'message'=>'Invalid seconds parameter']);
                }
                break;
            case 'stop':            
                $renderPage=false;
                header('Content-type: application/json');                
                echo $fitbit->StopAlarm();              
                
                break;
            case 'check':
                $renderPage=false;
                header('Content-type: application/json');
                echo $fitbit->CheckAlarm();              
                break;
            case 'sync':
                $renderPage=false;
                header('Content-type: application/json');
                echo $fitbit->CheckSync();              
                break;
        }
    }
?>