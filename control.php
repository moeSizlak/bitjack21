<?php
require_once 'common.php';
validate_session();

define('NUMDECKS', 8);
define('MAXSPLITS', 2);
define('MAXACESPLITS',1);
define('ALLOWTENSPLITS', 1);
define('ALLOWSPLITANYTENS', 1);
define('ONECARDAFTERACESPLIT', 1);
define('ALLOWDOUBLEAFTERSPLIT', 1);
define('ALLOWDOUBLEAFTERACESPLIT', 0);
define('BJAFTERACESPLIT', 0);
define('BJAFTERTENSPLIT', 0);
define('NATURALAFTERACESPLIT', 0);
define('NATURALAFTERTENSPLIT', 0);
define('ALLOWDOUBLE', 1);
define('ALLOWSURRENDER', 0);
define('H17', 0);
define('DEBUG', 0);

define('STAND', 0);
define('HIT', 1);
define('SPLIT', 2);
define('DOUBLE', 3);
define('SURRENDER', 4);

define('PERM_HIT', 1);
define('PERM_SPLIT', 2);
define('PERM_DOUBLE', 4);
define('PERM_SURRENDER', 8);

define('MAX_BET', 10);
define('MIN_BET', 1);

/*function shufflecards(&$cards, $n)
{
    for($i = 0; $i < $n; $i++)
    {
        $cards[$i] = $i % 52;
    }

    if ($n > 1)
    {
        for ($i = 0; $i < ($n - 1); $i++)
        {
            $j = $i + floor(rand() / (floor((getrandmax() / ($n - $i))) + 1));
            $t = $cards[$j];
            $cards[$j] = $cards[$i];
            $cards[$i] = $t;
        }
    }
}*/

function shufflecards(&$cards, $n)
{
    for($i = 0; $i < $n; $i++)
    {
        $cards[$i] = $i % 52;
    }

    if ($n > 1)
    {
        for ($i = ($n - 1); $i >= 1; $i--)
        {
            $j = rand(0,$i);
            $t = $cards[$j];
            $cards[$j] = $cards[$i];
            $cards[$i] = $t;
        }
    }
}

function hextobin(&$outstr, $instr, $append = true)
{
    if(!$append)
    {
        $outstr = '';
    }

    for($i = 0; $i < strlen($instr); $i++)
    {
        if($instr{$i} == '0') { $outstr .= '0000'; }
        else if($instr{$i} == '1') { $outstr .= '0001'; }
        else if($instr{$i} == '2') { $outstr .= '0010'; }
        else if($instr{$i} == '3') { $outstr .= '0011'; }
        else if($instr{$i} == '4') { $outstr .= '0100'; }
        else if($instr{$i} == '5') { $outstr .= '0101'; }
        else if($instr{$i} == '6') { $outstr .= '0110'; }
        else if($instr{$i} == '7') { $outstr .= '0111'; }
        else if($instr{$i} == '8') { $outstr .= '1000'; }
        else if($instr{$i} == '9') { $outstr .= '1001'; }
        else if($instr{$i} == 'a') { $outstr .= '1010'; }
        else if($instr{$i} == 'b') { $outstr .= '1011'; }
        else if($instr{$i} == 'c') { $outstr .= '1100'; }
        else if($instr{$i} == 'd') { $outstr .= '1101'; }
        else if($instr{$i} == 'e') { $outstr .= '1110'; }
        else if($instr{$i} == 'f') { $outstr .= '1111'; }
    }
}

/*
0->16557
1->16664
2->16725
3->16629
4->16723
5->16702
*/
function shufflecardsSHA256(&$cards, $n, $R1, $R2)
{
    if($n > 416)
    {
        throw new Exception('shufflecards256 can only be called with up to 416 cards!');
    }

    for($i = 0; $i < $n; $i++)
    {
        $cards[$i] = $i % 52;
    }

    if ($n > 1)
    {
        $randbits_hex = '';
        $randbits_bin = '';
        $r = 0;
        $randbits_hex = hash('sha256',''.$R1.$R2.$r);
        $r++;
        hextobin($randbits_bin, $randbits_hex);
        $bitsleft = 256;

        for ($i = ($n - 1); $i >= 1; $i--)
        {
            $bitsneeded = 0;
            $myrand = -1;
            if($i>=256)      { $bitsneeded = 9; }
            else if($i>=128) { $bitsneeded = 8; }
            else if($i>=64)  { $bitsneeded = 7; }
            else if($i>=32)  { $bitsneeded = 6; }
            else if($i>=16)  { $bitsneeded = 5; }
            else if($i>=8)   { $bitsneeded = 4; }
            else if($i>=4)   { $bitsneeded = 3; }
            else if($i>=2)   { $bitsneeded = 2; }
            else if($i>=1)   { $bitsneeded = 1; }
            do
            {
                if($bitsneeded > $bitsleft)
                {
                    $randbits_hex = hash('sha256',''.$R1.$R2.$r);
                    $r++;
                    hextobin($randbits_bin, $randbits_hex);
                    $bitsleft += 256;
                }
                $num = substr($randbits_bin, 0, $bitsneeded);
                $randbits_bin = substr($randbits_bin, $bitsneeded);
                $bitsleft -= $bitsneeded;
                $num = intval(base_convert($num, 2, 10));
                if($num <= $i)
                {
                    $myrand = $num;
                }
            }
            while($myrand == -1);

            $t = $cards[$myrand];
            $cards[$myrand] = $cards[$i];
            $cards[$i] = $t;
        }
    }
}

function bj(&$cards)
{
    $c1 = $cards[0] % 13;
    $c2 = $cards[1] % 13;

    if(($c1 == 12 && $c2 > 7 && $c2 < 12) || ($c2 == 12 && $c1 > 7 && $c1 < 12))
        return 1;
    else
        return 0;
}

function softcount(&$cards/*, $n*/)
{
    $score = 0;

    //for($i=0; $i<$n; $i++)
    foreach($cards as $i => $j)
    {
    //    $j = $cards[$i] % 13;
	$j = $j % 13;
        if($j <= 7)
            $score += ($j + 2);
        else if($j <= 11)
            $score += 10;
        else
            $score += 1;
    }

    return $score;
}

function hardcount(&$cards/*, $n*/)
{
    $score = 0;
    $ace = 0;

    //for($i=0; $i<$n; $i++)
    foreach($cards as $i => $j)
    {
        //$j = $cards[$i] % 13;
	$j = $j % 13;
        if($j <= 7)
        {
            $score += ($j + 2);
        }
        else if($j <= 11)
        {
            $score += 10;
        }
        else
        {
            if(!$ace)
            {
                $score += 11;
                $ace = 1;
            }
            else
            {
                $score += 1;
            }
        }
    }

    if($score > 21 && $ace)
    {
        $score -= 10;
    }

    return $score;
}

function dealerdecide(&$cards/*, $n*/)
{
    $hard = hardcount($cards/*, $n*/);
    $soft = softcount($cards/*, $n*/);

    if($hard <= 16)
        return HIT;

    if(H17 && $hard == 17 && $soft == 7)
        return HIT;

    return STAND;
}



function validateInt($inData) {
  $intRetVal = -1;

  $IntValue = intval($inData);
  $StrValue = strval($IntValue);
  if($StrValue == $inData)
  {
    $intRetVal = $IntValue;
  }

  return $intRetVal;
}

function failboat($ecode = -9999)
{
  error_log("FAIL: control.php: $ecode.");
  mail_error("FAIL: control.php: $ecode\nUser=".$_SESSION['username']."\n\n");
  echo json_encode(array("errorCode" => "$ecode"));
  exit(0);
}

function getCurrentGame()
{
  global $con;

  $result = mysql_query("SELECT * FROM games WHERE player = '".$_SESSION['username']."' AND endTime is null", $con);
  if(!$result) { return failboat(-101); }

  if(mysql_num_rows($result) >  1)
  {
    echo json_encode(array("errorCode"=>"-3"));
    exit(0);
  }
  else if(mysql_num_rows($result) == 0)
  {
    $game = NULL;
  }
  else
  {
    $game = mysql_fetch_array($result);
  }
  
  return $game;
}

function getRandBytes($num=16)
{
    $f = fopen("/dev/urandom", "rb");
    $b = fread($f, $num);
    fclose($f);
    if (strlen($b) != $num)
    {
        error_log("FAILED TO GET RANDOM BYTES: getRandBytes.");

        $characterList = "abcdef0123456789";
        $i = 0;
        $b = "";
        do {
                $b .= $characterList{mt_rand(0,strlen($characterList)-1)};
                $i++;
        } while ($i <= $num);
        return $b;
    }
    return (bin2hex($b));
}


function getR1RX()
{
    global $con;

    $result = mysql_query("SELECT nextR1,nextRX FROM users WHERE username =  '".$_SESSION['username']."'", $con);
    if(!$result) { return failboat(-777); }

    $u = mysql_fetch_array($result);
    $o = array();

    if(is_null($u['nextR1']) || strlen($u['nextR1']) != 32)
    {
        $o['R1'] = getRandBytes();
        $result = mysql_query("UPDATE users SET nextR1='".$o['R1']."' WHERE username = '".$_SESSION['username']."'", $con);
        if(!$result) { return failboat(-778); }
    }
    else
    {
        $o['R1'] = $u['nextR1'];
    }

    if(is_null($u['nextRX']) || strlen($u['nextRX']) != 32)
    {
        $o['RX'] = getRandBytes();
        $result = mysql_query("UPDATE users SET nextRX='".$o['RX']."' WHERE username = '".$_SESSION['username']."'", $con);
        if(!$result) { return failboat(-779); }
    }
    else
    {
        $o['RX'] = $u['nextRX'];
    }

    return $o;
}


function getState($bet)
{
  global $con;

  $balance=getFlooredBalance();
  if(is_null($balance)) { failboat(-6601); }
  $player = $_SESSION['username'];
  $p1score = null;
  $p2score = null;
  $p3score = null;
  $usrmsg = "";
  
  $game = getCurrentGame();
  if($game==NULL)
  {
    $showDeal = 1;
    $usrmsg = "";
    $r1rx = getR1RX();
    $hr1rx = hash('sha256',''.$r1rx['R1'].$r1rx['RX']);
    /*if($balance > MIN_BET && $bet <= $balance && $bet >= MIN_BET && $bet <= MAX_BET)
    {
      $showDeal   = 1;
      //$usrmsg = "Click DEAL to begin.";
      $usrmsg = "";
    }
    else
    {
      $showDeal   = 0;
      if($balance > MIN_BET)
      {
	//$usrmsg = "Place your bet.";
      }
      else
      {
	$usrmsg = "You don't have enough money. (The minimum bet is ".MIN_BET.".)";
      }
    }*/
    
    $response = array(
      "errorCode"=>0, "gameover"=>0, "player"=>$player, "balance"=>$balance, "bet"=>$bet, "showDeal"=>$showDeal, "showExit"=>1, "showHit"=>0,
      "showDouble"=>0, "showSplit"=>0, "showStay"=>0, "dcards"=>NULL, "p1cards"=>NULL, "p2cards"=>NULL, "p3cards"=>NULL, "dscore"=>0, "p1score"=>0, 
      "p2score"=>0, "p3score"=>0, "numSplits"=>0, "p1double"=>0, "p2double"=>0, "p3double"=>0, "gameID"=>NULL, "currentHand"=>0, "msg"=>$usrmsg,
      "p1msg"=>"", "p2msg"=>"", "p3msg"=>"", "maxbet"=>MAX_BET, "minbet"=>MIN_BET, "nextHR1RX"=>$hr1rx);
      
    return json_encode($response);
  }
  else
  {
    if($game['p1cards'] == NULL)
    {
      $p1cards = NULL;
    }
    else
    {
      $p1cards = explode(',',$game['p1cards']);
      $soft = softcount($p1cards);
      $hard = hardcount($p1cards);
      if($soft == $hard)
      {
	$p1score = $hard;
      }
      else
      {
	$p1score = "$soft,$hard";
      }
    }
    
    if($game['p2cards'] == NULL)
    {
      $p2cards = NULL;
    }
    else
    {
      $p2cards = explode(',',$game['p2cards']);
      $soft = softcount($p2cards);
      $hard = hardcount($p2cards);
      if($soft == $hard)
      {
	$p2score = $hard;
      }
      else
      {
	$p2score = "$soft,$hard";
      }
    }
    
    if($game['p3cards'] == NULL)
    {
      $p3cards = NULL;
    }
    else
    {
      $p3cards = explode(',',$game['p3cards']);
      $soft = softcount($p3cards);
      $hard = hardcount($p3cards);
      if($soft == $hard)
      {
	$p3score = $hard;
      }
      else
      {
	$p3score = "$soft,$hard";
      }
    }
    
    if($game['endTime'] == NULL)
    {
      $temp = explode(',',$game['dcards']);
      $dcards = array(52,$temp[0]);
      $temp2 = array($temp[0]);
      $soft = softcount($temp2);
      $hard = hardcount($temp2);
      if($soft == $hard)
      {
	$dscore = $hard;
      }
      else
      {
	$dscore = "$soft,$hard";
      }
    }
    else
    {
      $dcards = explode(',',$game['dcards']);
      $soft = softcount($dcards);
      $hard = hardcount($dcards);
      if($soft == $hard)
      {
	$dscore = $hard;
      }
      else
      {
	$dscore = "$soft,$hard";
      }
    }

    $hr1rx = hash('sha256',''.$game['R1'].$game['RX']);
    
    $response = array(
      "errorCode"=>0, "gameover"=>0, "player"=>$player, "balance"=>$balance, "bet"=>$game['bet'], "showDeal"=>0, "showExit"=>0,
      "showHit"=>$game['showHit'], "showDouble"=>$game['showDouble'], "showSplit"=>$game['showSplit'], "showStay"=>$game['showStay'], "dcards"=>$dcards,
      "p1cards"=>$p1cards, "p2cards"=>$p2cards, "p3cards"=>$p3cards, "dscore"=>$dscore, "p1score"=>$p1score, 
      "p2score"=>$p2score, "p3score"=>$p3score, "numSplits"=>$game['numSplits'], "currentHand"=>$game['currentHand'], "p1double"=>$game['p1double'],
      "p2double"=>$game['p2double'], "p3double"=>$game['p3double'], "gameID"=>$game['gameID'], "msg"=>$usrmsg, "p1msg"=>"", "p2msg"=>"", "p3msg"=>"", "maxbet"=>MAX_BET, "minbet"=>MIN_BET, "thisR2"=>$game['R2'], "thisHR1RX"=>$hr1rx);
      
    return json_encode($response);  
  }
}

function deal($bet, $R2)
{
  global $con;
  global $bitcoin;

  $player = $_SESSION['username'];
  $priorBalance=getFlooredBalance();
  if(is_null($priorBalance)) { failboat(-6602); }
  try {
    $bitcoin->move($player, "", BTCRound($bet/CHIPSPERBTC));
  } catch(Exception $e) {
    return failboat(-6603);
  }
  $balance=getFlooredBalance();
  if(is_null($balance)) { error_log("FAIL.  took user (bet was $bet) money then bitcoin failed."); failboat(-6604); }

  $r1rx = getR1RX();
  $r1 = $r1rx['R1'];
  $rx = $r1rx['RX'];

  $cards = array();
  //if($player == 'moeSizlak')
  //{
  //error_log("Calling shufflecardsSHA256 ($player)with R1=$r1 and R2=$R2");
  shufflecardsSHA256($cards,416,$r1,$R2);
  //}
  //else
  //{  
  //shufflecards($cards, 416);
  //}
  $cards = array_slice($cards, 0 /*rand(0,NUMDECKS*52-85)*/ /*$cards[0]+$cards[1]+$cards[2]*/, 84);
  $_cards = implode(',', $cards);
//if($player == 'moeSizlak')
//{
//$_cards = '10,6,21,30,31,15,35,25,22,18,0,47,39,16,26,43,26,35,5,19,3,49,30,27,11,35,36,14,50,3,2,9,44,32,50,29,28,41,48,18,20,22,33,2,21,18,23,0,40,51,28,50,37,39,44,22,45,18,31,41,47,36,27,26,25,20,20,3,17,45,5,29,7,4,20,17,31,45,28,29,4,34,46,36';

//$_cards = '10,6,21,30,10,12,12,12,22,18,0,47,39,16,26,43,26,35,5,19,3,49,30,27,11,35,36,14,50,3,2,9,44,32,50,29,28,41,48,18,20,22,33,2,21,18,23,0,40,51,28,50,37,39,44,22,45,18,31,41,47,36,27,26,25,20,20,3,17,45,5,29,7,4,20,17,31,45,28,29,4,34,46,36';
//$cards = explode(',',$_cards);
//}
  $_p1cards = "$cards[0],$cards[2]";
  $temp = array($cards[0],$cards[2]);
  $p1score = hardcount($temp/*,2*/);
  $_dcards = "$cards[1],$cards[3]";
  $temp = array($cards[1],$cards[3]);
  $dscore = hardcount($temp/*,2*/);
  
  $showSplit = 0;
  $p1 = $cards[0]%13;
  $p2 = $cards[2]%13;
  if(MAXSPLITS > 0 && $balance >= $bet)
  {
    if($p1 == $p2)
    {
      if($p1 <= 7)
      {
	$showSplit = 1;
      }
      else if($p1 < 12 && ALLOWTENSPLITS > 0)
      {
	$showSplit = 1;  
      }
      else if(MAXACESPLITS > 0)
      {
	$showSplit = 1;
      }     
    }
    else if(ALLOWSPLITANYTENS && $p1 > 7 && $p1 < 12 && $p2 > 7 && $p2 < 12)
    {
      $showSplit = 1;
    }
  }
  
  if($balance >= $bet)
  {
    $showDouble = 1;
  }
  else
  {
    $showDouble = 0;
  }
  $showHit = 1;
  $showStay = 1;
  
  $time = date("YmdHis");
  $netGain  = 0 - $bet;
//  $con = mysql_connect("localhost","bj","12qwerty56qwaszx");if(!$result) { return failboat(-102); }
//  mysql_select_db("bj", $con);

  $result = mysql_query("INSERT INTO games(player,shoe,shoenext,dcards,dscore,dnext,p1cards,p2cards,p3cards,p1score,p2score,p3score,p1next,p2next,p3next,p1double,p2double,p3double,bet,priorBalance, startTime, showSplit, showDouble, showHit, showStay, currentHand, numSplits, netGain, R2, R1, RX) VALUES('$player','$_cards','4','$_dcards','$dscore','2','$_p1cards',null,null,'$p1score','0','0','2','0','0','0','0','0','$bet','$priorBalance','$time','$showSplit','$showDouble','$showHit','$showStay','0','0','$netGain','$R2','$r1','$rx')", $con);
  if(!$result) { return failboat(-103); }
  
  if($p1score == 21 || $dscore == 21)
  {
    return game_over();
  }

  return getState($bet);
}

function hit($doubled = 0, $split = 0)
{
  global $con;
  global $bitcoin;

  $player = $_SESSION['username'];
  $game = getCurrentGame();
  $gameid = $game['gameID'];
  $balance = getFlooredBalance();
  if(is_null($balance)) { failboat(-6605); }
  $bet = $game['bet'];
  
//  $con = mysql_connect("localhost","bj","12qwerty56qwaszx") ;if(!$result) { return failboat(-104); }
//  mysql_select_db("bj", $con);
  
  if($doubled == 1 || $split == 1)
  {
    try {
      $bitcoin->move($player, "", BTCRound($bet/CHIPSPERBTC));
    } catch(Exception $e) {
      failboat(-6606);
    }
    $balance=getFlooredBalance();  
    if(is_null($balance)) { failboat(-6607); }
    
    $result = mysql_query("UPDATE games SET netGain=netGain-$bet WHERE gameID = '$gameid'", $con);
    if(!$result) { return failboat(-105); }
  }
  
  if($game['currentHand']==0) { $p = 'p1'; $np = 'p'.($game['numSplits']+2); }
  else if($game['currentHand']==1) { $p = 'p2'; $np = 'p'.($game['numSplits']+2); }
  else if($game['currentHand']==2) { $p = 'p3'; }
  
  if($split == 1)
  {
    $numSplits = $game['numSplits'] + 1;

    $pcards = explode(',',$game[$p.'cards']);
    $npcards = array($pcards[1]);
    $pcards = array($pcards[0]);    
    $_pcards = implode(',', $pcards);    
    $_npcards = implode(',',$npcards);
    $pnext = 1;
    $npnext = 1;
    $pscore = hardcount($pcards/*, $pnext*/);
    $npscore = hardcount($npcards/*, $pnext*/);
    
    $result = mysql_query("UPDATE games SET " . $p . "cards='$_pcards', " . $p . "next='$pnext', " . $p . "score='$pscore', " . $np . "cards='$_npcards', " . $np . "next='$npnext', " . $np . "score='$npscore', numSplits='$numSplits' WHERE gameID='$gameid'", $con);
    if(!$result) { return failboat(-106); }
    $game = getCurrentGame();
  }  
  
  $shoe = explode (',',$game['shoe']);
    
  $pcards = explode(',',$game[$p.'cards']);
  $pcards[] = $shoe[$game['shoenext']];
  $_pcards = implode(',', $pcards);
  $pnext = $game[$p.'next'] + 1 ;
  $shoenext = $game['shoenext'] + 1;
  $pscore = hardcount($pcards/*, $pnext*/);  
  
  $showSplit = 0;
  $p1 = $pcards[0]%13;
  $p2 = $pcards[1]%13;
  if($pnext == 2 && MAXSPLITS > $game['numSplits'] && $balance >= $game['bet'])
  {
    if($p1 == $p2)
    {
      if($p1 <= 7)
      {
	$showSplit = 1;
      }
      else if($p1 < 12 && ALLOWTENSPLITS > 0)
      {
	$showSplit = 1;  
      }
      else if(MAXACESPLITS > $game['numSplits'])
      {
	$showSplit = 1;
      }     
    }
    else if(ALLOWSPLITANYTENS && $p1 > 7 && $p1 < 12 && $p2 > 7 && $p2 < 12)
    {
      $showSplit = 1;
    }
  }
  
  if(ALLOWDOUBLE && $pnext == 2 && $balance >= $game['bet'] && ($game['numSplits'] == 0 || ALLOWDOUBLEAFTERSPLIT) && ($pcards[0]%13 != 12 || ALLOWDOUBLEAFTERACESPLIT))
  {
    $showDouble = 1;
  }
  else
  {
    $showDouble = 0;
  }
  
  $result = mysql_query("UPDATE games SET $p"."cards='$_pcards', $p"."next='$pnext', shoenext='$shoenext', $p"."score='$pscore', showHit='1', showStay='1', showDouble='$showDouble',showSplit='$showSplit', $p"."double='$doubled' WHERE gameID='$gameid'", $con); 
  if(!$result) { return failboat(-107); }
  
  if(ONECARDAFTERACESPLIT && $game['numSplits'] == 1 && $pcards[0]%13 == 12)
  {
    if($game['currentHand'] == 0)
    {
      $result = mysql_query("UPDATE games SET currentHand='1' WHERE gameID='$gameid'", $con);
      if(!$result) { return failboat(-108); }
      return hit();
    }
    else //if($game['currentHand'] == 1)
    {
      return game_over();
    }
  }
  
  if($pscore >= 21 || $doubled == 1)
  {
    if($game['numSplits'] == 0)
    {
      return game_over();
    }
    else if($game['numSplits'] == 1)
    {
      if($game['currentHand'] == 1)
      {
	return game_over();
      }
      else// if($game['currentHand'] == 0)
      {
	$result = mysql_query("UPDATE games SET currentHand='1' WHERE gameID='$gameid'", $con);
	if(!$result) { return failboat(-109); }
	return hit();
      }
    }
    else// if($game['numSplits'] == 2)
    {
      if($game['currentHand'] == 2)
      {
	return game_over();
      }
      else if($game['currentHand'] == 1)
      {
	$result = mysql_query("UPDATE games SET currentHand='2' WHERE gameID='$gameid'", $con);
	if(!$result) { return failboat(-110); }
	return hit();      
      }
      else// if($game['currentHand'] == 0)
      {
	$result = mysql_query("UPDATE games SET currentHand='1' WHERE gameID='$gameid'", $con);
	if(!$result) { return failboat(-111); }
	return hit();
      }
    }
  }
  
  return getState($game['bet']);
}

function game_over()
{
  global $con;
  global $bitcoin;

  $player = $_SESSION['username'];
  $game = getCurrentGame();
  $gameid = $game['gameID'];
  $bet = $game['bet'];
  $dbj = 0;
  $pbj = 0;
  $won = 0;
  $p2cards = null;
  $p3cards = null;
  $p1outcome = "";
  $p2outcome = "";
  $p3outcome = "";
  $usrmsg = "";
  $shoenext = $game['shoenext'];
  $numsplits = $game['numSplits'];
  
//  $con = mysql_connect("localhost","bj","12qwerty56qwaszx") ;if(!$result) { return failboat(-112); }
//  mysql_select_db("bj", $con);
  
  $dcards = explode(',',$game['dcards']);
  $_dcards = implode(',',$dcards);
  $p1cards = explode(',',$game['p1cards']);
  if($game['p2cards'] != null)
  {
    $p2cards = explode(',',$game['p2cards']);
  }
  if($game['p3cards'] != null)
  {
    $p3cards = explode(',',$game['p3cards']);
  }
  $dscore = $game['dscore'];
  $p1score = $game['p1score'];
  $p2score = $game['p2score'];
  $p3score = $game['p3score'];
  
  if($numsplits == 0 && bj($dcards) == 1)
  {
    $dbj = 1;
    $dscore = 21;
  }
  if($numsplits == 0 && bj($p1cards) == 1)
  {
    $pbj = 1;
  }
  
  if($dbj == 1 || $pbj ==1)
  {
    if($dbj == 1)
    {
      if($pbj == 1)
      {
	$won = $bet;
	$p1outcome = "Push";
      }
      else
      {
	$usrmsg .= "Dealer Blackjack<br>";
	$p1outcome = "Lose";
      }
    }
    else
    {
      $won = 2.5*$bet;
      $p1outcome = "BLACKJACK!";
    }
  }
  else /* no blackjacks: */
  {    
    $shoe = explode (',',$game['shoe']);
      
    if($p1score <= 21 || ($p2cards != null && $p2score <= 21) || ($p2cards != null && $p2score <= 21))
    {
      while(dealerdecide($dcards) == HIT)
      {
	$dcards[] = $shoe[$shoenext++];    
      }
      $dscore = hardcount($dcards);
      $_dcards = implode(',',$dcards);
    }
    
    if($p1score > 21)
    {
      $p1outcome = "Bust";
    }
    else if($dscore > 21 || $p1score > $dscore)
    {
      if($dscore > 21)
      {
	$usrmsg .= "Dealer Bust!<br>";
      }
      $won = $won + 2*$bet;
      if($game['p1double'])
      {
	$won = $won + 2*$bet;
      }
      $p1outcome = "Win";
    }
    else if($p1score == $dscore)
    {
      $won = $won + $bet;
      if($game['p1double'])
      {
	$won = $won + $bet;
      }
      $p1outcome = "Push";
    }
    else
    {
      $p1outcome = "Lose";
    }
    
    if($p2cards != null)
    {
      if($p2score > 21)
      {
	$p2outcome = "Bust";
      }
      else if($dscore > 21 || $p2score > $dscore)
      {
	$won = $won + 2*$bet;
	if($game['p2double'])
	{
	  $won = $won + 2*$bet;
	}
	$p2outcome = "Win";
      }
      else if($p2score == $dscore)
      {
	$won = $won + $bet;
	if($game['p2double'])
	{
	  $won = $won + $bet;
	}
	$p2outcome = "Push";
      }
      else
      {
        $p2outcome = "Lose";
      }
    }
    
    if($p3cards != null)
    {
      if($p3score > 21)
      {
	$p3outcome = "Bust";
      }
      else if($dscore > 21 || $p3score > $dscore)
      {
	$won = $won + 2*$bet;
	if($game['p3double'])
	{
	  $won = $won + 2*$bet;
	}     
	$p3outcome = "Win";
      }
      else if($p3score == $dscore)
      {
	$won = $won + $bet;
	if($game['p3double'])
	{
	  $won = $won + $bet;
	}
	$p3outcome = "Push";
      }
      else
      {
        $p3outcome = "Lose";
      }
    }
      
  }
  
  $time = date("YmdHis");
  
  $result = mysql_query("UPDATE games SET netGain=netGain+$won, endTime='$time' WHERE gameID = '$gameid'", $con);
  if(!$result) { return failboat(-113); }
  $result = mysql_query("UPDATE games SET dcards='$_dcards', shoenext='$shoenext', dscore='$dscore' WHERE gameID = '$gameid'", $con);
  if(!$result) { return failboat(-114); }

  $nextr1 = getRandBytes();
  $nextrx = getRandBytes();
  $result = mysql_query("UPDATE users SET nextR1='$nextr1',nextRX='$nextrx' WHERE username = '".$_SESSION['username']."'", $con);
  if(!$result) { return failboat(-1776);}
  
  if($won > 0)
  {
    try {
      $bitcoin->move("", $player, BTCRound($won/CHIPSPERBTC));
    } catch(Exception $e) {
      failboat(-6608);
    }
  }
  $balance=getFlooredBalance();
  if(is_null($balance)) { failboat(-6609); }
  
  $usrmsg .= "Net Gain: ".($game['netGain']+$won);
  
  $hr1rx = hash('sha256',''.$game['R1'].$game['RX']);
  $response = array(
    "errorCode"=>0, "gameover"=>1, "player"=>$player, "balance"=>$balance, "bet"=>$game['bet'], "showDeal"=>0, "showExit"=>0,
    "showHit"=>0, "showDouble"=>0, "showSplit"=>0, "showStay"=>0, "dcards"=>$dcards,
    "p1cards"=>$p1cards, "p2cards"=>$p2cards, "p3cards"=>$p3cards, "dscore"=>$dscore, "p1score"=>$p1score, 
    "p2score"=>$p2score, "p3score"=>$p3score, "numSplits"=>$game['numSplits'], "p1double"=>$game['p1double'],
    "p2double"=>$game['p2double'], "p3double"=>$game['p3double'], "gameID"=>$game['gameID'], "currentHand"=>$game['currentHand'],
    "msg"=>$usrmsg, "p1msg"=>$p1outcome, "p2msg"=>$p2outcome, "p3msg"=>$p3outcome, "maxbet"=>MAX_BET, "minbet"=>MIN_BET, "thisR1"=>$game['R1'], "thisRX"=>$game['RX'], "thisR2"=>$game['R2'],"thisHR1RX"=>$hr1rx);
    
  return json_encode($response);
}

function stay()
{
  global $con;

  $player = $_SESSION['username'];
  $game = getCurrentGame();
  $gameid = $game['gameID'];
  
  if($game['numSplits'] == 0)
  {
    return game_over();
  }
  else if($game['numSplits'] == 1)
  {
    if($game['currentHand'] == 1)
    {
      return game_over();
    }
    else// if($game['currentHand'] == 0)
    {
      $result = mysql_query("UPDATE games SET currentHand='1' WHERE gameID='$gameid'", $con);
      if(!$result) { return failboat(-115); }
      return hit();
    }
  }
  else// if($game['numSplits'] == 2)
  {
    if($game['currentHand'] == 2)
    {
      return game_over();
    }
    else if($game['currentHand'] == 1)
    {
      $result = mysql_query("UPDATE games SET currentHand='2' WHERE gameID='$gameid'", $con);
      if(!$result) { return failboat(-116); }
      return hit();      
    }
    else// if($game['currentHand'] == 0)
    {
      $result = mysql_query("UPDATE games SET currentHand='1' WHERE gameID='$gameid'", $con);
      if(!$result) { return failboat(-117); }
      return hit();
    }
  }
  
  return getState($game['bet']);
}



/*******************************************************************************************/
header("Cache-Control: no-cache"); 

$con = connectDB();
if(!$con) { return failboat(-100); }

$bitcoin = connectBitcoin();

if(!isset($_SESSION['username']))
{
  reset_session();
  echo json_encode(array("errorCode"=>"-1: Your session has ended, please re-login."));
  exit(0);
}

$balance = getFlooredBalance();
if(is_null($balance)) { failboat(-6610); }


$thecall = null;
if(isset($_POST['func']))
{
  $thecall = $_POST['func'];
}

//if($thecall == NULL)
//{ echo deal(1);
  //echo json_encode(array("errorCode"=>"-2"));
//  exit(0);
//}

$bet = null;
if(isset($_POST['bet']))
{
  $bet = $_POST['bet'];
  $bet = validateInt($bet);
  if($bet < 0)
  {
    $bet = NULL;
  }
}

$cseed = 'deadbeef';
if(isset($_POST['cseed']))
{
  $cseed = $_POST['cseed'];
  if($cseed === '')
  {
    $cseed = 'deadbeef';
  }

  if(!filter_var($cseed, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^([a-f0-9])*$/'))))
  {
    echo json_encode(array("errorCode"=>"-15"));
    exit(0);

  }
}


if($thecall == 'getState')
{
  if(is_null($bet))
  {
    echo json_encode(array("errorCode"=>"-4"));
    exit(0);
  }
  echo getState($bet);
  exit(0);
}
else if($thecall == 'deal')
{
  $game = getCurrentGame();
  if(is_null($bet) || $bet > MAX_BET || $bet > $balance || $bet < MIN_BET || $game != NULL)
  {
    echo json_encode(array("errorCode"=>"-5"));
    exit(0);  
  }
//  else if($bet < MINBET || $bet > MAX_BET || $bet > $balance)
//  {
//    echo getState($bet);
//    exit(0);
//  }
  echo deal($bet,$cseed);
  exit(0);
}
else if($thecall == 'hit')
{
  $game = getCurrentGame();
  if($game == NULL)
  {
    echo json_encode(array("errorCode"=>"-6"));
    exit(0);  
  }
  if($game['showHit'] != 1)
  {
    echo json_encode(array("errorCode"=>"-7"));
    exit(0);  
  }
  echo hit();
  exit(0);
}
else if($thecall == 'double')
{
  $game = getCurrentGame();
  if($game == NULL)
  {
    echo json_encode(array("errorCode"=>"-8"));
    exit(0);  
  }
  if($game['showDouble'] != 1||$game['bet']>$balance)
  {
    echo json_encode(array("errorCode"=>"-9"));
    exit(0);  
  }
  echo hit(1,0);
  exit(0);
}
else if($thecall == 'split')
{
  $game = getCurrentGame();
  if($game == NULL)
  {
    echo json_encode(array("errorCode"=>"-10"));
    exit(0);  
  }
  if($game['showSplit'] != 1||$game['bet']>$balance)
  {
    echo json_encode(array("errorCode"=>"-11"));
    exit(0);  
  }
  echo hit(0,1);
  exit(0);
}
else if($thecall == 'stay')
{
  $game = getCurrentGame();
  if($game == NULL)
  {
    echo json_encode(array("errorCode"=>"-12"));
    exit(0);  
  }
  if($game['showStay'] != 1)
  {
    echo json_encode(array("errorCode"=>"-13"));
    exit(0);  
  }
  echo stay();
  exit(0);
}
else
{
  echo json_encode(array("errorCode"=>"-14"));
  exit(0); 

}

?>
