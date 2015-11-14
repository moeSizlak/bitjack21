<?php
require_once 'common.php';
validate_session();
unset($_SESSION['username']);
reset_session();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>BitJack21 - Bitcoin Blackjack</title>
<meta http-equiv="REFRESH" content="0;url=http://bitjack21.com"></HEAD>
<BODY>
Logging out...
</BODY>
</HTML>

