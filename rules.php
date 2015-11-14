<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
	<meta name="keywords" content="blackjack, bitcoin" />
	<meta name="description" content="BitJack21 - Bitcoin Blackjack - Rules" /> 
	<link rel="Shortcut Icon" href="images/favicon.ico">
	<title>BitJack21 - Bitcoin Blackjack - Rules</title> 
	<base target="_self" />
	
<!--Stylesheets--> 
	<link href="css/layout.css" rel="stylesheet" type="text/css" media="screen" /> 
	
<!--Javascript--> 

	<!--[if IE]>
		<script src="js/html5.js"></script>
	<![endif]-->

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.backgroundPosition.js_6.js"></script>
<script type="text/javascript" src="js/menu.js"></script>

</head>
 
<body> 
<div id="wrapper">
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>
<div id="singlecolumn">

<h1>Welcome to bitjack21.com !</h1><br>
<br>
The game uses common casino blackjack rules:<br><br>

* Dealer stands on all 17's<br>
* Blackjack pays 3:2<br>
* Double on any 2 cards<br>
* Split to up to 3 hands<br>
* Double after split (except aces)<br>
* If aces are split, you get 1 card per hand.<br>
* 1 "chip" is currently worth 0.01 BTC<br>
* Minimum bet is 0.01 BTC (1 chip)<br>
* <font style="font-size:120%;color:red">Maximum bet has been TEMPORARILY lowered in order to keep volume low in preparation for the change of management and servers on 2011-08-25 and 2011-08-26.</font><br>
* Maximum bet WILL be increased later and is currently <font style="font-size:120%;color:red">0.10 BTC</font> (10 chips).<br>
* 8 decks are shuffled before every hand.<br>
<br>
The "house edge" with this ruleset is razor thin, less than 0.5%, enjoy :)

</div>

</div>

</body>

</html>
