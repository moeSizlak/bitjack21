<?php
require_once 'common.php';
validate_session();


function failboat($ecode = -9999)
{
  if($ecode != 0 && $ecode != 1 && $ecode != 2)
  {
    error_log("FAIL: wd.php: $ecode.");
    mail_error("FAIL: wd.php: $ecode\nUser=".$_SESSION['username']."\n\n");
  }
  echo json_encode(array("error" => "$ecode"));
  exit(0);
}

/*************************************************************/

header("Cache-Control: no-cache");

$con = connectDB();
if (!$con)
{
  failboat('Database connection error -325.');/* . mysql_error());*/
}

if(!isset($_SESSION['username']))
{
  failboat("You must be logged in to withdraw bitcoins.");
}

$user = $_SESSION['username'];
$wallet = $_POST['wallet'];
$amount = $_POST['amount'];
$password = $_POST['password'];
$time = date("YmdHis");
$ipaddr = $_SERVER['REMOTE_ADDR'];
$ubalance = getBTCBalance();
$sbalance = getBTCBalance('');
if(is_null($ubalance) || is_null($sbalance))
{
  failboat("Temporary error: bitcoind is down.");
}

$dosend = -1;

$result = mysql_query("SELECT pwsalt,pwhash,email FROM users WHERE username = '$user'", $con);

if(mysql_num_rows($result) ==  1)
{
  $row = mysql_fetch_array($result);
  if(hash_password($password,$row['pwsalt'])  != $row['pwhash'])
  {
    failboat(2);
  }
}
else
{
  failboat("Error -802");
}


if(strlen($amount) <= 0 || $amount == 0 || !filter_var($amount, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{0,10}(\.[0-9]{1,8}){0,1}$/'))))
{
  failboat("Invalid bitcoin amount.  Amount must be greater than 0, and can only contain digits, and optionally a decimal point and 1-8 decimal places.");
}

$bitcoin = connectBitcoin();
try {
  $duh = $bitcoin->validateaddress($wallet);
} catch (Exception $e) {
  failboat("Temporary error: bitcoind is down.");
}
if($duh['isvalid'] != true)
{
  failboat("Invalid bitcoin address: $wallet");
}

if(BTCRound($amount + 0.01) > $ubalance)
{
  failboat("You do not have enough bitcoins to withdraw $amount BTC (plus 0.01 BTC fee).  You currently have $ubalance BTC.");
}
else
{
  $dosend = 0;
}

if($amount <= (0.85 * ($sbalance + $amount - 0.5)))
{
  $dosend = 1;
}

if($dosend == 0 || $dosend == 1)
{
  try {
    $bitcoin->move($user, "", BTCRound($amount + 0.01));
  } catch (Exception $e) {
    failboat("Temporary error: bitcoind is down.");
  }
}

if($dosend == 0)
{
  $result = mysql_query("INSERT into withdrawals (username, reqdate, amount, destination, ipaddr) values ('$user','$time','$amount','$wallet','$ipaddr')", $con);
  if(!$result)
  {
    error_log("ZOMFGFAIL ($user) Funds (BTC $amount plus fee) moved from user account but unable to log the withdraw to DB or send the BTC.");
    failboat("Database error -3547");
  }
  mail_manual_payout($row['email'], $user, $amount, $wallet);
  failboat(1);
}

try
{
  $txid = $bitcoin->sendfrom("",$wallet,(float)$amount);
}
catch (Exception $e)
{
  error_log("ZOMFGFAIL ($user) (Destination: $wallet) ($amount) ($time) Unable to send funds (sendfrom).");

  $result = mysql_query("INSERT into withdrawals (username, reqdate, amount, destination, ipaddr) values ('$user','$time','$amount','$wallet','$ipaddr')", $con);
  if(!$result)
  {
    error_log("ZOMFGFAIL: *ALSO* unable to log prev transaction to DB.");
    failboat("Database error -35472");
  }
  mail_manual_payout($row['email'], $user, $amount, $wallet);
  failboat(1);
}
$sendtime = date("YmdHis");

$result = mysql_query("INSERT into withdrawals (username, reqdate, amount, senddate, txid, destination, ipaddr) values ('$user','$time','$amount','$sendtime','$txid','$wallet','$ipaddr')", $con);
if(!$result)
{
  error_log("ZOMFGFAIL: Unable to log completed withdraw to DB ($user) ($amount) ($wallet) ($time) ($sendtime) ($txid).");
  failboat("Database error -3547");
}

echo json_encode(array("error"=>0,"txid"=>$txid));

mail_payout($row['email'], $user, $amount, $wallet, $txid);
?>
