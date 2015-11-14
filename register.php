<?php
require_once 'common.php';
validate_session();
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<!--<meta http-equiv="refresh" content="3" /> uncoment this for auto refresh-->
	<meta name="keywords" content="blackjack, bitcoin, register" />
	<meta name="description" content="BitJack21 - Bitcoin Blackjack - Register" /> 
	<link rel="Shortcut Icon" href="images/favicon.ico">
	<title>BitJack21 - Bitcoin Blackjack - Register</title> 
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
<script type="text/javascript">

$(function() {
  $('#reg').click(function() {
    $('#reg').hide();
    $('#result').empty();
    $.post("reg", { "username":$('#u').val(), "password":$('#p').val(), "password2":$('#p2').val(), "email":$('#e').val(), "wallet":$('#w').val()}, updateState,"json");
  });
});

function updateState(data)
{
  if(data.error == 0)
  {
    $('#result').css('font-size','140%').css('color','rgb(255,0,0)').html("SUCCESS.  Returning to login page in 5 seconds...");
    setTimeout(function(){
      window.location="https://bitjack21.com";
    }, 5000);
  }
  else
  {
    $('#result').css('font-size','140%').css('color','rgb(255,0,0)').html(data.error);
    $('#reg').show();
  }
}
</script>

</head>
 
<body> 
<div id="wrapper">
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>
<div id="singlecolumn">



<br><br><p style="text-align:center;font-size:150%">Welcome to bitjack21.com !</p><br><br><br>
<table border="1" style="text-align:left;margin-left:auto;margin-right:auto;">
<tr><td>Username:</td><td><input id="u" type="text" size="40" name="username"></td></tr>
<tr><td>Password:</td><td><input id="p" type="password" size="40" name="password"></td></tr>
<tr><td>Re-enter password:</td><td><input id="p2" type="password" size="40" name="password2"></td></tr>
<tr><td>Email Address:</td><td><input id="e" type="text" size="40" name="email"></td></tr>
<tr><td>Emergency Bitcoin Wallet Address (optional):</td><td><input id="w" type="text" size="40" name="wallet"></td></tr>
</table>
<div style="text-align: center;"><br><button id="reg" type="button">Register</button><br><br>
<span id="result" ></span></div>
</div>

</div>

</body>

</html>
