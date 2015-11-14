<?php
require_once 'common.php';
reset_session();

/*************************************************************/

header("Cache-Control: no-cache");


//if(isset($_POST['username']))
//{
  if(!filter_var($_POST['username'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-zA-Z0-9]{4,20}$/'))))
  {
    failboat();
  }

  $con = connectDB();
  if (!$con)
  {
    failboat('Temporary Error: Database is down (Error -901)');
  }

  $result = mysql_query("SELECT username,pwsalt,pwhash FROM users WHERE username = '".$_POST['username']."'", $con);

  if(mysql_num_rows($result) ==  1)
  {
    $row = mysql_fetch_array($result);
    if(hash_password($_POST['password'],$row['pwsalt'])  == $row['pwhash'])
    {
      $_SESSION['username'] = $row['username'];
    }
    else
    {
      failboat();
    }
  }  
  else
  {
    failboat();
  }

  allow_session();
  $logintime = date("YmdHis");
  $loginip = $_SERVER['REMOTE_ADDR'];
  $user = $_POST['username'];
  mysql_query("UPDATE users set lastlogin='$logintime',loginip='$loginip' where username='$user'", $con);

  echo json_encode(array("error"=>0));
//}


function failboat($ecode = "Login Failed.")
{
  echo json_encode(array("error" => "$ecode"));
  exit(0);
}
?>
