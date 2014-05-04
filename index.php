<?php 
    include_once('includes/config.php');
    if($renderPage){
        include_once('includes/header.php');
	include_once("pages/$page.php");
	include_once('includes/footer.php');
    }
?>