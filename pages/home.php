<?php /* Home Page */ 
    if(isset($fitbit) && $fitbit->IsAuthenticated()){
            include_once('includes/authorized.php');
    }else{
            include_once('includes/unauthorized.php');
    }
?>