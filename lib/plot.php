<?php

//**************************************
include "./plot/pData.class.php";
include "./plot/pDraw.class.php";
include "./plot/pImage.class.php";
include "./plot/pPie.class.php";
 
//*************************************************************************
function PlotLine($rowX,$rowY,$name, $ch){

  
//**************************************
$myData = new pData();
$myData->addPoints($rowX,"Serie1");
$myData->setSerieDescription("Serie1","test");
//$myData->setSerieOnAxis("Serie1",0);

$myData->addPoints($rowY,"Absissa");
$myData->setAbscissa("Absissa");

//$myData->addPoints(array("January","February","March","April","May","June","July","August"),"Absissa");
//$myData->setAbscissa("Absissa");

$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
//$myData->setAxisName(0,"1st axis");
$myData->setAxisUnit(0,"");

$myPicture = new pImage(700,230,$myData);
$Settings = array("R"=>240, "G"=>242, "B"=>241, "Dash"=>1, "DashR"=>260, "DashG"=>262, "DashB"=>261);
$myPicture->drawFilledRectangle(0,0,700,230,$Settings);

$Settings = array("StartR"=>252, "StartG"=>255, "StartB"=>254, "EndR"=>252, "EndG"=>255, "EndB"=>254, "Alpha"=>50);
$myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);

$myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

$myPicture->setFontProperties(array("FontName"=>"plot/fonts/Forgotte.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
, "R"=>252, "G"=>252, "B"=>252, "DrawBox"=>1, "BoxAlpha"=>30);
//$myPicture->drawText(350,25,$name,$TextSettings);

$myPicture->setShadow(FALSE);
$myPicture->setGraphArea(50,50,675,190);
//$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/pf_arma_five.ttf","FontSize"=>6));

$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_FLOATING
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
$myPicture->drawScale($Settings);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

$Config = "";
if ($ch==1)
    $myPicture->drawSplineChart($Config);
if ($ch==2)
    $myPicture->drawBarChart($Config);
if ($ch==3)
    $myPicture->drawLineChart($Config);
if ($ch==4)
    $myPicture->drawPlotChart($Config);
if ($ch==5)
    $myPicture->drawStepChart($Config);
if ($ch==6)
    $myPicture->drawAreaChart($Config);
if ($ch==7)
    $myPicture->drawFilledSplineChart($Config);
if ($ch==8)
    $myPicture->drawFilledStepChart($Config);
if ($ch==9)
    $myPicture->drawStackedAreaChart($Config);

$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"plot/fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
, "Mode"=>LEGEND_HORIZONTAL
);
$myPicture->drawLegend(563,16,$Config);

$myPicture->stroke();
}

//**************************************************************************
function PlotPie($rowA,$rowB,$name, $cch){
     
 $MyData = new pData();    
 $MyData->addPoints($rowA,"ScoreA");   
 $MyData->setSerieDescription("ScoreA","Application A"); 

 /* Define the absissa serie */ 
 $MyData->addPoints($rowB,"Labels"); 
 $MyData->setAbscissa("Labels"); 

 /* Create the pChart object */ 
 $myPicture = new pImage(700,630,$MyData);

$myPicture->setFontProperties(array("FontName"=>"plot/fonts/Forgotte.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
, "R"=>252, "G"=>252, "B"=>252, "DrawBox"=>1, "BoxAlpha"=>30);
//$myPicture->drawText(350,25,$name,$TextSettings);

$myPicture->setShadow(FALSE);
$myPicture->setGraphArea(50,50,675,690);
//$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/pf_arma_five.ttf","FontSize"=>6));

$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_FLOATING
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
//$myPicture->drawScale($Settings);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

 /* Create the pPie object */  
 $PieChart = new pPie($myPicture,$MyData); 

 /* Draw an AA pie chart */  
if ($cch==11)
     $PieChart->draw2DPie(160,140,array("DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE)); 
 if ($cch==12)
     $PieChart->draw2DRing(160,140,array("WriteValues"=>TRUE,"ValueR"=>255,"ValueG"=>255,"ValueB"=>255,"Border"=>TRUE)); 
 if ($cch==13)
     $PieChart->draw3DPie(160,140,array("Radius"=>70,"DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE)); 


 /* Write the legend box */  
 $myPicture->setShadow(FALSE); 
 $PieChart->drawPieLegend(15,40,array("Alpha"=>20)); 

 /* Render the picture (choose the best way) */ 
 $myPicture->autoOutput("pictures/example.draw2DPie.labels.png"); 
}

//************************************************************************************************
//************************************************************************************************
session_start();

$ch=$_REQUEST['ch'];

  $variables=$_SESSION['result'];
  $Tch = explode("\n",$variables);
   $i=0;
   foreach ($Tch as $row){
        $X =   explode("=",$row);
        if ($X[0]!='') {    
           $rowX[$i]=$X[1]*1;
           $rowY[$i]=$X[0].'='.$X[1];
           }
    $i++; 
}

$name="indicator preview";

if ($ch>=10){
PlotPie($rowX,$rowY,$name, $ch);
}else{
PlotLine($rowX,$rowY,$name, $ch);
} 

?>