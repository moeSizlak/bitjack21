<?php
require_once 'common.php';
validate_session();

function failboat($ecode = -9999)
{
  echo json_encode(array("error" => "$ecode"));
  exit(0);
}

/*************************************************************/

header("Cache-Control: no-cache");
//failboat('Site is in closed beta currently');
$con = connectDB();
if (!$con)
{
  failboat('Temporary error: Database connection error. (-352)');/* . mysql_error());*/
}

if(isset($_SESSION['username']))
{
  failboat("You are already logged in.  Log out before registering a new account.");
}
reset_session();


//if(isset($_POST['username']))
//{
   if(!filter_var($_POST['username'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-zA-Z0-9]{4,20}$/'))))
   {
      failboat("Username must be between 4 and 20 characters long and contain only a-z, A-Z, and 0-9.");
   }

   if(!filter_var($_POST['password'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/.{5,250}/'))))
   {
      failboat("Password must be between 5 and 250 characters long.");
   }

   if($_POST['password'] != $_POST['password2'])
   {
      failboat("Passwords do not match.");
   }

   if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
   {
      failboat("Invalid e-mail address.");
   }

   $bitcoin = connectBitcoin();

   if($_POST['wallet'])
   {
      try {
        $duh = $bitcoin->validateaddress($_POST['wallet']);
      } catch (Exception $e) {
         failboat("Temporary error: bitcoin daemon is down");
      }
      if($duh['isvalid'] != true)
      {
         failboat("Invalid bitcoin wallet address.");
      }
   }


   $user = $_POST['username'];
   $email = $_POST['email'];
   $wallet = $_POST['wallet'];
   $pw = $_POST['password'];
   $salt = generateSalt();
   $hash = hash_password($pw,$salt); 
   
   $result = mysql_query("SELECT * FROM users WHERE username = '$user'", $con);
   if(!$result) { failboat("Temporary error: Database error -101 ". mysql_error()); }

   if(mysql_num_rows($result) >=  1)
   {
      failboat("A user with username $user already exists, choose a different username."); 
   }

   try {
      $deposit = $bitcoin->getnewaddress($user);
   } catch(Exception $e) {
      failboat("Temporary error: bitcoin daemon is down (-363)");
   }
   if(!filter_var($deposit, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-zA-Z0-9]{25,34}$/'))))
   {
      failboat("bitcoind error -103");
   }
   
   $jointime = date("YmdHis");
   $joinip = $_SERVER['REMOTE_ADDR']; 
   $result = mysql_query("INSERT into users (username, email, wallet, pwhash, pwsalt, deposit, joindate, lastactive, lastlogin, joinip, loginip) values ('$user','$email','$wallet','$hash','$salt','$deposit','$jointime','$jointime','$jointime','$joinip','$joinip')", $con);
   if(!$result)
   {
      failboat("Database error -102");
   }

   $_SESSION['username'] = $user;
   allow_session();
   echo json_encode(array("error"=>0));
?>
