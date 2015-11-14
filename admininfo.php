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
	<meta name="description" content="BitJack21 - Bitcoin Blackjack - Admin Info" /> 
	<link rel="Shortcut Icon" href="images/favicon.ico">
	<title>BitJack21 - Bitcoin Blackjack - Admin Info</title> 
	<base target="_self" />
	
<!--Stylesheets--> 
	<link href="css/layout.css" rel="stylesheet" type="text/css" media="screen" /> 	
	
<!--Javascript--> 

	<!--[if IE]>
		<script src="js/html5.js"></script>
	<![endif]-->

<?php
if(!isset($_SESSION['username']) || ($_SESSION['username'] != 'moeSizlak' && $_SESSION['username'] != 'Caesium'))
{
  header("Location: index");
  exit();
}
?>

<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.backgroundPosition.js_6.js"></script>
<script type="text/javascript" src="js/menu.js"></script>

</head> 
<body> 
<div id="wrapper">
<div id="header"></div>
<div id="menubar"><?php drawmenu(); ?></div>
<div id="columns">
<div id="side1">
<?php

  $fail = 0;
  $con = connectDB();
  if (!$con)
  {
    $fail = 1;
  }

  $date = time();
  $q1 = mysql_query("select sum(netGain)/-100 as gain from games where player != 'moeSizlak'", $con);
  $q2 = mysql_query("select player, sum(netGain)/100 as playergain from games group by player", $con);
  $q3 = mysql_query("select * from withdrawals order by reqdate asc", $con);
  $q4 = mysql_query("select count(*) as num from withdrawals where txid is null", $con);
  $q5 = mysql_query("select count(*) as users from users", $con);
  $q6 = mysql_query("select * from games order by gameid desc limit 50", $con);
  $q7 = mysql_query("select username,loginip,lastactive from users order by lastactive desc", $con);
  $q8 = mysql_query("select count(*) as num from users where lastactive >= FROM_UNIXTIME(".(int)$date-(60*10).")", $con);


  if(!$q1 || !$q2 || !$q3 || !$q4 || !$q5 || !$q6 || !$q7)
  {
    $fail = 1;
  }
  else
  {
    $a1 = mysql_fetch_array($q1);
    $a4 = mysql_fetch_array($q4);
    $a5 = mysql_fetch_array($q5);
    $a8 = mysql_fetch_array($q8);
  }

  if($fail == 1)
  {
    echo <<<DONE
Database error -38299
</div>
</div>
</div>
</BODY>
</HTML>
DONE;
    exit();
  }

echo '<table border="1" style="text-align:left;margin-left:auto;margin-right:auto;">';
echo '<tr><td>Total Server Profit (BTC)</td><td>'.$a1['gain'].'</td><tr>';
echo '<tr><td>Total Unique Users</td><td>'.$a5['users'].'</td><tr>';
echo '<tr><td>Withdraws pending manual AUTH</td><td>'.$a4['num'].'</td><tr>';
echo '<tr><td>Active users in last 10 minutes</td><td>'.$a8['num'].'</td><tr>';
echo '</table>';
?>
</div>

</div>

</body>

</html>
