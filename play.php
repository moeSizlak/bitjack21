<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<?php
if(!isset($_SESSION['username']))
{
  echo <<<DONE
<title>Logging Out</title>
<meta http-equiv="REFRESH" content="0;url=index"></HEAD>
<BODY>
Your session has ended, logging out...
</BODY>
</HTML>
DONE;
exit();
}
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
<meta name="keywords" content="bitcoin blackjack, bitcoin casino, bitcoin games, bitcoin, blackjack, casino, games, bitcoin gambling, gambling" />
<meta name="description" content="BitJack21 - Bitcoin Blackjack - Play" /> 
<link rel="Shortcut Icon" href="images/favicon.ico">
<title>BitJack21 - Bitcoin Blackjack - Play</title> 
<base target="_self" />
	
<!--Stylesheets--> 
<link href="css/style.css" rel="stylesheet" type="text/css" media="screen" /> 

<!--[if lte IE 7]>
  <link href="css/ie.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]-->	

<!--[if IE 6]>
  <link href="css/ie6.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]-->
	
	
<!--Javascript--> 

<!--[if IE]>
  <script src="js/html5.js"></script>
<![endif]-->

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/bitjack21.js"></script>

</head>
 
<body> 
	<div id="wrapper">
		<div id="table">
			<div id="gameid"></div>
			<div id="cryptoproof">
				<div id="cpcurrent">
					<p style="text-align:center" id="cpcurrenttext"></p>
					<table id="cptable">
                                                <tr class="even"><td>Client Random Seed (R2)</td><td id="cpctr2"><input id="csinput" type="text" name="cseed" size="37" maxlength="32"><p id="cstext"></p></td></tr>
                                                <tr class="odd"><td>Server Random Seed Hash (SHA256(R1+RX))</td><td id="cpcthr1rx"></td></tr>
                                        </table>
				</div>
				<div id="cplast">
					<p style="text-align:center">Most Recently Finished Hand:</p>
					<table id="cptable">
						<tr class="even"><td>Game ID</td><td id="cpltgid"></td></tr>
						<tr class="odd"><td>Server Random Seed (R1)</td><td id="cpltr1"></td></tr>
						<tr class="even"><td>Server RX value</td><td id="cpltrx"></td></tr>
						<tr class="odd"><td>SHA256(R1+RX)</td><td id="cplthr1rx"></td></tr>
						<tr class="even"><td>Client Random Seed (R2)</td><td id="cpltr2"></td></tr>
					</table>
				</div>
			</div>
			<div id="score">
				<ul>
					<li id="money">CHIPS: <span></span> </li>
					<li id="bet">BET: <span></span> </li>
				</ul>
			</div>
			<div id="shoe"></div>
			<div id="msg"></div>
			<div id="p1msg"></div>
			<div id="p2msg"></div>
			<div id="p3msg"></div>
			<div class="curValue player1"></div>
			<div class="curValue player2"></div>
			<div class="curValue player3"></div>
			<div class="curValue dealer"></div>

			<div id="rules"></div>
			<div id="players"></div>
			<div id="chips">
				<ul>
					<li class="chip c1"><img src="images/chips/c1.png" alt="1chip"/></li>
					<li class="chip c5"><img src="images/chips/c5.png" alt="5chip"/></li>
					<li class="chip c10"><img src="images/chips/c10.png" alt="10chip"/></li>
					<li class="chip c0"><img src="images/chips/c0.png" alt="0chip"/></li>
				</ul>
			</div>
			<div id="gameField"></div>
			<div id="control">
				<div id="double">DBL</div>
				<div id="hit">HIT</div>
				<div id="deal">DEAL</div>
				<div id="stay">STAY</div>
				<div id="split">SPLIT</div>
			</div>
		</div><!--end of table-->	

	</div><!--end of wrapper-->
		<footer>
			<div id="copyright"><span>&copy; 2011 Mr. Sizlak</span></div>
		</footer>
</body>

</html>
