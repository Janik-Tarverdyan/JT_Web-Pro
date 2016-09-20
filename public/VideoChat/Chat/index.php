<?php if(!isset($_SESSION)) session_start(); ?>
<!DOCTYPE HTML>
<html>
 <head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title> Chat Room </title>
	<meta name="description" content="Script for chat simple, online, made with php and javascript, ajax" />
	<meta name="keywords" content="chat script, chat simple, chat online" />
	<link rel="stylesheet" type="text/css" href="chatfiles/chatstyle.css" />
	<link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
 </head>

 <body>
 	<div>
 	<canvas id="canvas"></canvas>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src="js/index.js"></script>
    <div>
	<?php include('chat.php'); ?>
	</div>
	</div>
 </body>
</html>
