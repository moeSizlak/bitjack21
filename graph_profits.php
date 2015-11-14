<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_scatter.php');
require_once 'common.php';


$starttime = '1312070400'; // 2011-07-31 00:00:00
$endtime = time();
$interval = 30*60;

function  TimeCallback( $aVal) { 
    return date ('Y-m-d H:i:s',$aVal); 
}

function  TimeCallback2( $aVal) {
    return date ('Y-m-d',$aVal);
}

 
// Make a circle with a scatterplot
/*
$steps=16;
for($i=0; $i<$steps; ++$i) {
    $a=2*M_PI/$steps*$i;
    $datax[$i]=cos($a);
    $datay[$i]=sin($a);
}*/

$con = connectDB();
$datax = array();
$datay = array();
$datay2 = array();

$graph = new Graph(900,700);
$graph->SetShadow();
//$graph->SetAxisStyle(AXSTYLE_BOXIN);

$max = 0;
$min = 0;
$max2 = 0;
for($i = $starttime; $i <= $endtime; $i += $interval)
{
  $now = TimeCallback($i);
  $res = mysql_query("select sum(netGain)/-100 as profit, count(*) as gp from games where endTime < '$now' and player != 'moeSizlak'", $con);
  $d = mysql_fetch_assoc($res);

  if($d['profit'] < 0){ $d['profit']=0;}

  $datax[] = (int)$i;
  $datay[] = (float)$d['profit'];
  $datay2[] = (int)$d['gp'];
  if($d['profit']>$max){$max = $d['profit'];}
  if($d['profit']<$min){$min = $d['profit'];}
  if($d['gp']>$max2){$max2 = $d['gp'];}
} 

$graph->img->SetMargin(50,70,60,140);
 
$graph->title->Set('Step1: ?,   Step2: ?,   Step3: Profit.');
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->subtitle->Set('()');
$graph->subtitle->SetFont(FF_FONT1,FS_NORMAL);
 
// 10% top and bottom grace
//$graph->yscale->SetGrace(5,5);
//$graph->xscale->SetGrace(1,1);
//$graph->yaxis->scale->SetAutoMin(0);

$sp1 = new ScatterPlot($datay,$datax);
//$sp1->mark->SetType(MARK_FILLEDCIRCLE);
//$sp1->mark->SetFillColor('red');
$sp1->SetColor('blue');

$graph->SetScale("linlin", 0, $max);
$graph->SetY2Scale("lin",0,$max2);
$graph->yaxis->scale->ticks->Set(10,5); 
$graph->y2axis->scale->ticks->Set(2000,1000);  
$graph->yaxis->SetColor('blue');
$graph->y2axis->SetColor('red'); 
$graph->y2axis->title->Set('Hands Played');
$graph->y2axis->title->SetMargin(30);
$graph->yaxis->title->SetFont(FF_FONT2,FS_NORMAL,26);
$graph->y2axis->title->SetFont(FF_FONT2,FS_NORMAL,26);

$graph->xaxis->SetLabelFormatCallback('TimeCallback2');
$graph->xaxis->SetLabelAngle(45);
$graph->xaxis->SetPos("min");
$graph->yaxis->title->Set('Profit (BTC)');


$sp1->mark->SetWidth(0);
$sp1->link->Show();
$sp1->link->SetWeight(2);
$sp1->link->SetColor('blue');

$sp2 = new ScatterPlot($datay2,$datax);
//$sp1->mark->SetType(MARK_FILLEDCIRCLE);
//$sp1->mark->SetFillColor('red');
$sp2->SetColor('red');
$sp2->mark->SetWidth(0);
$sp2->link->Show();
$sp2->link->SetWeight(2);
$sp2->link->SetColor('red');

 
 
$graph->Add($sp1);
$graph->AddY2($sp2);
$graph->Stroke();
 
?>
