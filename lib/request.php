<?php
//****************************************************************************************

session_start();

//****************************************************************************************

$Choice=$_REQUEST['Action'];
require_once("../config/config.php");
require_once("../lib/service.php");

//****************************************************************************************
// the system create new coordinator agent according to requests sent by users (GUI agent) and communicate with related agent. this make the system more faster. Each coordinator agent instance can asks: Collector agent and/or transformation agent, and/or computation agent to start working in the same time according to users requests

//****************************************************************************************

if ($Choice=="StartCollect"){
 
   $StartCoordination= new AgentCoordinator;
 
   $StartCoordination->tracebase=$tracesbase;
   $StartCoordination->MoodleBase=$moodlebase;
   $StartCoordination->TimeFrom=$_POST["from"];
   $StartCoordination->TimeTo=$_POST["to"];
   $StartCoordination->Course=$_POST["Course"];
   $StartCoordination->StartCollectingData();
  }



if (isset($_REQUEST['ExecuteTransformation'])){

   $StartCoordination= new AgentCoordinator;
   $StartCoordination->StartNewTransformation();
   echo("<script>location ='../Index.php';</script>") ;

}

//****************************************************************************************
        
if($Choice=="ViewIndicator"){
 $ch=$_REQUEST['ch'];          
 echo("<script>location ='../lib/plot.php?ch=".$ch."' ;</script>") ;
}


//****************************************************************************************

if ($Choice=="SaveIndicators"){
   $SaveIndicator= new AgentInterface;
   $SaveIndicator->IndicatorBase=$indicatorsbase;
   $SaveIndicator->IndicatorName=$_REQUEST['IndicatorName']; 
   $SaveIndicator->IndicatorSubject=$_REQUEST['IndicatorSubject'];
   $SaveIndicator->IndicatorEquation=$_SESSION['equation'];
   $SaveIndicator->IndicatorValue=$_SESSION['result'];
   $SaveIndicator->IndicatorTimeValue="Local Time Not Yet";
   $SaveIndicator->IndicatorTransformation=$_SESSION['diagram'];
   $SaveIndicator->SaveIndicator();
   //echo("<script>location ='../Index.php';</script>") ;

}

//****************************************************************************************

if (isset($_REQUEST['init'])){
   
   $_SESSION['transformation']="";
   $_SESSION['result']="";
   $_SESSION['variables']="";
   $_SESSION['equation']="";
   $_SESSION['indicatorview']="";
   //echo("<script>location ='../Index.php';</script>") ;
}
?>