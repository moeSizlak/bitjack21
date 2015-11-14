<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
<meta name="keywords" content="bitcoin blackjack, bitcoin, blackjack, bitcoin casino, bitcoin game, BTC blackjack, BTC, bitcoin game, bitcoin gambling" />
<meta name="description" content="BitJack21 - Bitcoin Blackjack" /> 
<link rel="Shortcut Icon" href="images/favicon.ico">
<title>BitJack21 - Bitcoin Blackjack</title> 
<base target="_self" />
	
<!--Stylesheets-->
<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" charset="utf-8"> 

<!--Javascript--> 

	<!--[if IE]>
		<script src="js/html5.js"></script>
	<![endif]-->

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.backgroundPosition.js_6.js"></script>
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript">
$(function() {
  $('#loginform').submit(dologin);
});

function dologin()
{
    $('#logmein').hide();
    $('#result').empty();
    $.post("login", { "username":$('#u').val(), "password":$('#p').val()}, updateState,"json");
    return false;
}

function updateState(data)
{
  if(data.error == 0)
  {
    $('#result').css('font-size','110%').css('color','rgb(255,0,0)').html("Logging in...");
    window.location="https://bitjack21.com/";
  }
  else
  {
    $('#result').css('font-size','110%').css('color','rgb(255,0,0)').html(data.error);
    $('#logmein').show();
    setTimeout(function(){
	$('#result').html("");
    }, 1200);
  }
}
</script>

</head>
<body>
<div id="wrapper"> 
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>

<div id="columns">
<div id="side1">


<?php
if(isset($_SESSION['username']))
{
  $fail = 0;
  $con = connectDB();
  if (!$con)
  {
    $fail = 1;
  }
  $result = mysql_query("SELECT * from users where username = '".$_SESSION['username']."'", $con);

  if(mysql_num_rows($result) ==  1)
  {
    $myuser = mysql_fetch_array($result);
  }  
  else
  {
    $fail = 1;
  }
  if($fail == 1)
  {
    echo "<span>Temporary Error: Database is down</span>";
  }
  else
  {
    $fail = 0;
    $btcbal = getBTCBalance();
    $chipbal = getBalance();
    if(is_null($btcbal) || is_null($chipbal))
    {
       echo "<span>Temporary Error: bitcoin daemon is down</span>";
    }
    else
    {
      echo '<table border="1" style="margin-left:auto;margin-right:auto;">';
      echo '<tr><td>Username</td><td>'.$myuser['username'].'</td></tr>';
      echo '<tr><td>Balance (BTC)</td><td>'.getBTCBalance().' BTC</td></tr>';
      echo '<tr><td>Balance (chips)</td><td>'.getBalance().' chips</td></tr>';
      echo '<tr><td>Deposit Address</td><td>'.$myuser['deposit'].'</td></tr></table>';
    }
  }
}
else
{
  $login_html = <<<TEST
Please login:<br>
<form id="loginform">
<table>
<tr><td>Username:</td><td><input id="u" type="text" size="20" name="username"></td></tr>
<tr><td>Password:</td><td><input id="p" type="password" size="20" name="password"></td></tr>
</table><input type="submit" value="Login"></form>
<br>
<span id="result" ></span>
TEST;
  echo $login_html;
}

?>
<br><br><h1>News:</h1><br>
<h1>August 25, 2011:</h1><br><br>
Some downtime expected today and possibly tomorrow.<br><br>
The server will be moving to its new home sometime later today, likely starting around 11pm GMT.<br><br>
Depending on how long the move takes the site may be down for several hours.<br><br>
<h1>August 24, 2011:</h1><br><br>
The site is now under new management.  You can expect a few changes (for the better!) over the coming weeks.  There may be some limited downtime, never more than an hour or 2.  Eventually the site will be moved to a new server.<br><br>
Here are the winners of the August 23 promo:<br><br>
<font style="font-size:120%;color:red">tywe</font>     - (2 BTC) - Won the most BTC (+7.805 BTC)<br>
<font style="font-size:120%;color:red">st4rdust</font> - (3 BTC) - Lost the most BTC (-3.195 BTC)<br>
<font style="font-size:120%;color:red">tywe</font>     - (1 BTC) - Played the most hands (675)<br>[greenmike was a close 2nd at 568 hands]<br><br>
Congrats!  And as always, thanks for playing<br><br>
<h1>August 22, 2011:</h1><br><br>
Promo running from 5AM GMT 2011-08-23 until 5AM GMT 2011-08-24<br><br>
The prizes are as follows:<br><br>
2 BTC - To the player who wins the most BTC.<br>
3 BTC - To the player who loses the most BTC.<br>
1 BTC - To the player who plays the most hands.<br><br>
I will announce the winners here after the promo is over.  If this is well received I will make it a regular occurrence.<br><br>
<h1>August 9, 2011:</h1><br><br>
<a href="cryptoproof" style="color:#ff0000">Crypto-Proof is now in effect!</a><br><br>
BitJack21.com is the *ONLY* bitcoin blackjack site on the internet that can mathematically PROVE, in a verifiable manner, that it is 100% honest!<br><br>
It only seems fitting that cryptography, which is the basis for our trust in the bitcoin system, will now be used as the basis for trust in my bitcoin blackjack game.  Crypto-proof mathematically proves, and allows you to verify that:<br>
* The order of the cards was 100% completely random.<br>
* The order of the cards was determined before any cards were dealt and did not change during the hand.<br><br>
This is made possible by allowing the client (you!, or your web browser) to supply a random number seed prior to each hand (call this number R2).  The server also genrates a random seed prior to each hand (using a hardware random number generator), call this number R1.  The server then generates another random number, called RX.  The server then computes an SHA256 cryptographic hash of R1 and RX and displays this to you prior to the hand starting.  The server then shuffles the cards into a random order by using both R1 **and** R2 (which is generated by YOU, or your web browser, whichever you choose).  After the hand is over, the server displays to you the actual values of R1 and RX, which allows you to verify that the order of the cards was 100% random, dependent on the random seed YOU provided, and did not change during the hand.  In the near future I will be writing a verifier in javascript that you can employ to more easily verify everything.  Please read this page for all the details!:<br> <h1><a href="cryptoproof" style="color:#ff0000">Crypto-Proof!</a></h1><br><br>
I wasn't sure it was possible to PROVE the honesty of the game mathmatically.  Thank you to users
bitplane and gmaxwell (and to the SHA256 hashing algorithm) for helping me devise a method to do exactly that!
</div>
<div id="side2">
<h1>Welcome to bitjack21.com !</h1><br>
The original (and still the best) bitcoin blackjack site on the internet.<br>
<br>
<img alt="Gameplay Picture" src="images/gameplay1.jpg">
<br>

The game uses common casino blackjack rules:<br><br>

* Dealer stands on all 17's<br>
* Blackjack pays 3:2<br>
* Double on any 2 cards<br>
* Split to up to 3 hands<br>
* Double after split (except aces)<br>
* If aces are split, you get 1 card per hand.<br>
* 1 "chip" is currently worth 0.01 BTC<br>
* Minimum bet is currently <font style="font-size:120%;color:red">0.01 BTC</font> (1 chip)<br>
* <font style="font-size:120%;color:red">Maximum bet has been TEMPORARILY lowered in order to keep volume low in preparation for the change of management and servers on 2011-08-25 and 2011-08-26.</font><br>
* Maximum bet WILL be increased later and is currently <font style="font-size:120%;color:red">0.10 BTC</font> (10 chips).<br>
* 8 decks are shuffled before every hand.<br>
<br>
The "house edge" with this ruleset is razor thin, less than 0.5%, enjoy :)
<br><br>

</div>
</div>

<div id="footer"><span>&copy; 2011 Mr. Sizlak</span></div>
</div>

</body>
</html>
