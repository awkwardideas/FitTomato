<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="/assets/ico/favicon.ico">

    <title><?php echo SITE_NAME; ?> - The Pomodoro Technique&reg; meets Fitbit</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Loading FlipClock --->
    <link href="/assets/css/flipclock.css" rel="stylesheet">
    <!-- Loading Flat UI -->
    <link href="/assets/css/flat-ui.css" rel="stylesheet">
    <!-- Loading Custom -->
    <link href="/assets/css/custom.css" rel="stylesheet">

    <script type="text/javascript" src="/assets/js/jquery.js"></script>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    
    <div class="navbar navbar-inverse navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand<?php if($page=="home"){?> active<?php } ?>" href="/"><?php echo SITE_NAME; ?></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li <?php if($page=="home"){?>class="active"<?php } ?>><a href="/">Home</a></li>
            <li <?php if($page=="about"){?>class="active"<?php } ?>><a href="/about">About</a></li>
          </ul>
          <div class="pull-right">
            <?php if(!(isset($fitbit) && $fitbit->IsAuthenticated())){?>
                <a class="btn btn-info palette-belize-hole auth-btn" href="/authorize">Authorize</a>
            <?php }else{ ?>
                <a class="btn btn-warning auth-btn" href="/deauthorize">Deauthorize</a>
            <?php } ?>            
          </div>
        </div><!--/.nav-collapse -->
      </div>
    </div>
        
    <div class="container">