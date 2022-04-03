
<?php
//*************************************************************************************
//                      IC-MAS: basic Agents. V.1 Created By Tarek DJOUAD. 15-12-2015
//                      Email: tarek.djouad@gmail.com
//*************************************************************************************
// we create here 5 agents: AgentCollector, AgentTransformation, AgentComputation,  AgentCoordinator and AgentInterface

// 
//*************************************************************************************************
// Collect Agent : this agent connects to Moodle course and bring useful data used to compute indicator
// according to what user selects from collect interface.
//*************************************************************************************************

Class AgentCollector
{
 
public $time1;
public $time2;
public $course;
public $tracebase;
public $Moodlebase;
public $IC_MASbase;
public function CollectAgent() {


if ( empty ($this->time1) or empty ($this->time2)){
      echo"<br>You must specify collcting time interval before starting collecting data!!<br>";
      exit;
}

if ( empty ($this->course)){
      echo"<br> You must choose Your Course Before starting collecting data<br>";
      exit;
}


$CourseName=$this->course;
$temps1= strtotime("$this->time1");
$temps2= strtotime("$this->time2");
$tracesbase=$this->tracebase;
$moodlebase=$this->Moodlebase;
$icmasbase=$this->IC_MASbase;

//*************************************************************************************************
// Create primary m-trace model: this m-trace is empty.  
//*************************************************************************************************


mysql_query("CREATE DATABASE IF NOT EXISTS $tracesbase ");
mysql_select_db("$tracesbase")or die (mysql_error());

mysql_query("CREATE TABLE IF NOT EXISTS `primarytrace` (
  `ObservedID` varchar(60) NOT NULL,
  `UserID` varchar(60) NOT NULL,
  `ObservedType` varchar(60) NOT NULL,
  `ToolID` varchar(60) NOT NULL,
  `TimeVal` bigint(20) NOT NULL,
  `ObservedVal` text NOT NULL,
 
  `Comment` varchar(60) NOT NULL) ") or die (mysql_error()); 

//*************************************************************************************************
// Generate instances related to primary m-trace.  
//*************************************************************************************************


//initialization step
//*************************************************************************************************

mysql_select_db("$moodlebase") or die (mysql_error()); 
$sql=("SELECT  `id` FROM  `mdl_course` WHERE  `fullname`= '$CourseName'");
$idcourse = mysql_query($sql);
$TempCourse= mysql_fetch_array($idcourse);
$idc=$TempCourse['id'] ;

//**************************************************************************************************

mysql_select_db($tracesbase);
$sql = "SHOW TABLES FROM $tracesbase";
$result = mysql_query($sql);
while ($row = mysql_fetch_row($result)) {
      if ($row[0]!='primarytrace') {
          $sql= 'drop table '.$row[0];
          mysql_query($sql);    
      } 

}  
mysql_query("TRUNCATE TABLE primarytrace ");

//**************************************************************************************************
// Start Collect
//**************************************************************************************************
// CHAT ENTER
//**************************************************************************************************

if (isset($_POST['ChatEnter'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `message`,`chatid`,`timestamp` FROM `mdl_chat_messages` WHERE (`timestamp` >= '$temps1') and (`timestamp`<= '$temps2') and (`message`='enter') and `chatid` in (select `id`from `mdl_chat` where `course`='$idc')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
        $A='ChatEnter'.$data[0];
        $B=$data[1];
        $C='ChatEnter';
        $D='ToolChat'.$data[3];
        $E=$data[4];
        $F=$data[2];
        $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
          values ('$A','$B','$C','$D','$E','$F')";
        mysql_query($sql) or die (mysql_error());    
    }
}
//**************************************************************************************************
// CHAT EXIT
//**************************************************************************************************

if (isset($_POST['ChatExit'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `message`,`chatid`,`timestamp` FROM `mdl_chat_messages` WHERE (`timestamp` >= '$temps1') and (`timestamp`<= '$temps2') and (`message`='exit') and `chatid` in (select `id` from `mdl_chat` where `course`='$idc')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
        $A='ChatExit'.$data[0];
        $B=$data[1];
        $C='ChatExit';
        $D='ToolChat'.$data[3];
        $E=$data[4];
        $F=$data[2];
        $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                  values ('$A','$B','$C','$D','$E','$F')";
        mysql_query($sql) or die (mysql_error()); 
    }
}

//**************************************************************************************************
// CHAT MESSAGE
//**************************************************************************************************

if (isset($_POST['login'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `message`,`chatid`,`timestamp` FROM `mdl_chat_messages` WHERE (`timestamp` >= '$temps1') and (`timestamp`<= '$temps2') and (`message`<>'exit') and (`message`<>'enter') and `chatid` in (select `id` from `mdl_chat` where `course`='$idc')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
        $A='ChatMessage'.$data[0];
        $B=$data[1];
        $C='ChatMessage';
        $D='ToolChat'.$data[3];
        $E=$data[4];
        $F=addslashes($data[2]);
        $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                  values ('$A','$B','$C','$D','$E','$F')";
        mysql_query($sql) or die (mysql_error()); 

    }
}
//**************************************************************************************************
// Private Message
//**************************************************************************************************

if (isset($_POST['PrivateMessage'])) {
    mysql_select_db("$moodlebase") or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `useridfrom`, `useridto`, `timecreated`, `smallmessage` FROM `mdl_message` WHERE (`TimeCreated` >= '$temps1') and (`TimeCreated`<= '$temps2') ");
    mysql_select_db("$tracesbase") or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='PrivateMessage'.$data[0];
            $B=$data[1];
            $C='PrivateMessage';
            $D=$data[2];
            $E=$data[3];
            $F=addslashes($data[4]);
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`,`comment`)
                      values ('$A','$B','$C','','$E','$F','$D')";
            mysql_query($sql) or die (mysql_error()); 

    }
}

//**************************************************************************************************
// Resource View
//**************************************************************************************************

if (isset($_POST['ResourceView'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query(
    "SELECT  `id`, `userid`, `time`,`info` 
    FROM `mdl_log` WHERE 
    (`time` >= '$temps1') and (`time`<= '$temps2') and (`module`='page') and (`action`='view') and (`course`='$idc')")
    or die(mysql_error());
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='ResourceView'.$data[0];
            $B=$data[1];
            $C='ResourceView';
            $D='ToolResource'.$data[3];
            $E=$data[2];
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                      values ('$A','$B','$C','$D','$E','')";
            mysql_query($sql)or die (mysql_error()); 

    }
}

//**************************************************************************************************
// Login
//**************************************************************************************************

if (isset($_POST['ch21'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `time` FROM `mdl_log` WHERE (`time` >= '$temps1') and (`time`<= '$temps2') and (`action`='login')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='Login'.$data[0];
            $B=$data[1];
            $C='login';
            $E=$data[2];
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                      values ('$A','$B','$C','','$E','')";
            mysql_query($sql) or die (mysql_error()); 
    }
}
//**************************************************************************************************
// Logout
//**************************************************************************************************

if (isset($_POST['Logout'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `time` FROM `mdl_log` WHERE (`time` >= '$temps1') and (`time`<= '$temps2') and (`action`='logout')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='Logout'.$data[0];
            $B=$data[1];
            $C='logout';
            $E=$data[2];
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                      values ('$A','$B','$C','','$E','')";
            mysql_query($sql) or die (mysql_error()); 
    }
}

//**************************************************************************************************
// Upload resource
//**************************************************************************************************

if (isset($_POST['Upload'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `time`,`info`  FROM `mdl_log` WHERE (`time` >= '$temps1') and (`time`<= '$temps2') and (`action`='upload')");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='Upload'.$data[0];
            $B=$data[1];
            $C='Upload';
            $E=$data[2];
            $F=$data[3];
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`)
                      values ('$A','$B','$C','','$E','$F')";
            mysql_query($sql) or die (mysql_error()); 

    }
}

//**************************************************************************************************
// Forum Post Message
//**************************************************************************************************

if (isset($_POST['ForumPost'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query( "
    SELECT
    `mdl_forum_posts`.`id` ,
    `mdl_forum_discussions`.`forum` ,
    `mdl_forum_posts`.`userid` ,
    `mdl_forum_posts`.`discussion` ,
    `mdl_forum_posts`.`message` ,
    `mdl_forum_posts`.`created`
    FROM  `mdl_forum_posts` ,`mdl_forum_discussions`
    WHERE `mdl_forum_posts`.`discussion` =  `mdl_forum_discussions`.`id`
    AND   `mdl_forum_discussions`.`course`='$idc'
    AND   `mdl_forum_posts`.`created` >= '$temps1'
    AND   `mdl_forum_posts`.`created` <=  '$temps2'");
    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
        $A='ForumPostMessage'.$data[0];
        $B=$data[2];
        $C='ForumPostMessage';
        $D='ToolForum'.$data[1];
        $E=$data[5];
        $F=addslashes($data[4]);
        $G='Disucussion'.$data[3];
        $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`,`comment` )
                  values ('$A','$B','$C','$D','$E','$F', '$G')";
        mysql_query($sql) or die (mysql_error()); 
    }
}
//**************************************************************************************************
// Forum View
//**************************************************************************************************
if (isset($_POST['ChatMessage'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("SELECT  `id`, `userid`, `time`,`info`  FROM `mdl_log` WHERE (`time` >= '$temps1') and (`time`<= '$temps2') and (`module`='forum') and (`action`='view forum')");

    mysql_select_db("$tracesbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($req)){
            $A='ForumView'.$data[0];
            $B=$data[1];
            $C='ForumView';
            $D='ToolForum'.$data[3];
            $E=$data[2];
            $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`)
                      values ('$A','$B','$C','$D','$E')";
            mysql_query($sql) or die (mysql_error()); 
    }
}

//**************************************************************************************************
// Edit Wiki
//**************************************************************************************************

if (isset($_POST['WikiEdit'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query
    ("
    SELECT  `id`, `userid`, `pageid`, `Content` , `timecreated`
    from `mdl_wiki_versions` where 
    `timecreated`>='$temps1'  and 
    `timecreated`<='$temps2' and 
    `pageid` in (select `id` from `mdl_wiki_pages` where `subwikiid` 
                 in ( select `id` from `mdl_wiki` where `course`='$idc')
                 )"
    );


while ($data = mysql_fetch_row($req)){
      $A='EditWiki'.$data[0];
      $B=$data[1];
      $C='EditWiki';
      $E=$data[4]; 
      $F=addslashes($data[3]);        
      $u=$data[2];        
      mysql_select_db("$moodlebase")or die (mysql_error()); 
      $r=mysql_query("SELECT * FROM mdl_wiki where course='$idc' and id in 
      (select subwikiid from mdl_wiki_pages WHERE id ='$u') ") or  die(mysql_error());        
      $d = mysql_fetch_row($r);         
      $D='ToolWiki'.$d[0];
      $G='';      
      $r=mysql_query("SELECT title FROM mdl_wiki_pages WHERE id ='$u' ") or  die(mysql_error());        
      $d = mysql_fetch_row($r);         
      $G=$d[0];
      $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`ObservedVal`,`comment` )
                values ('$A','$B','$C','$D','$E','$F', '$G')";
      mysql_select_db("$tracesbase")or die (mysql_error()); 
      mysql_query($sql) or die (mysql_error()); 

  }
}
//**************************************************************************************************
// View Wiki
//**************************************************************************************************

if (isset($_POST['WikiView'])) {
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $req=mysql_query("
    SELECT  `id`, `userid`, `time`, `url`
    FROM `mdl_log`
    WHERE
    (`time` >= '$temps1') and
    (`time`<= '$temps2') and
    (`action`='view') and
    (`module`='wiki')and
    (`course`='$idc')");
    while ($data = mysql_fetch_row($req)){
        $A='ViewWiki'.$data[0];
        $B=$data[1];
        $C='ViewWiki';
        $E=$data[2];
        $Y=$data[3];
        $X1=explode('?', $Y);
        $X=explode('=', $X1[1]);
        mysql_select_db("$moodlebase")or die (mysql_error()); 
        if ($X[0]=='id'){
            $D='ToolWiki'.$X[1];
            $F='';
        }
        if ($X[0]=='pageid'){
            $req1=mysql_query("
            SELECT subwikiid, title
            FROM  `mdl_wiki_pages`
            WHERE id =  '$X[1]'");            
            $data1 = mysql_fetch_row($req1);
            $D='ToolWiki'.$data1[0];
            $F=$data1[1];
        }
        mysql_select_db("$tracesbase")or die (mysql_error()); 
        $sql="Insert into `primarytrace` (`ObservedID`,`UserID`,`ObservedType`,`ToolId`,`TimeVal`,`comment`)
                  values ('$A','$B','$C','$D','$E','$F')";
        mysql_query($sql) or die ("error in EditWiki Collecting");

    }
}

//**************************************************************************************************
// Get users list
//**************************************************************************************************

  mysql_select_db("$icmasbase")or die (mysql_error()); 
  mysql_query("CREATE TABLE IF NOT EXISTS `mdl_user` (
  `id` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `firstname` varchar(60) NOT NULL)") or die (mysql_error()); 
  mysql_select_db("$moodlebase")or die (mysql_error()); 
  $result=mysql_query("SELECT id, username, firstname FROM  `mdl_user`");
  mysql_select_db("$icmasbase")or die("cannot connect to IC_MAS base");
  while ($data = mysql_fetch_row($result)){
      mysql_query("TRUNCATE TABLE mdl_user");
      $sql="insert into `mdl_user` (`id`,`username`,`firstname`)
      values ('$data[0]','$data[1]','$data[2]')";
      mysql_query($sql)or die (mysql_error()); 

  }

//**************************************************************************************************
// Get Tools used in course
//**************************************************************************************************

    mysql_select_db("$icmasbase")or die (mysql_error()); 
    mysql_query("CREATE TABLE IF NOT EXISTS `mdl_tools` (
    `id` varchar(60) NOT NULL,
    `toolname` varchar(360) NOT NULL)") or die (mysql_error()); 
    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $result=mysql_query("SELECT id, name FROM  `mdl_chat` where `course`='$idc'");
    mysql_select_db("$icmasbase")or die (mysql_error()); 
    mysql_query("TRUNCATE TABLE mdl_tools");
    while ($data = mysql_fetch_row($result)){   
           $sql="insert into `mdl_tools` (`id`,`toolname`)
                  values ('ToolChat$data[0]','$data[1]')";         
           mysql_query($sql) or die (mysql_error());

    }

//**************************************************************************************************
//**************************************************************************************************
    mysql_select_db("$moodlebase")or die (mysql_error());  
    $result=mysql_query("SELECT id, name FROM  `mdl_forum` where `course`='$idc'");
    mysql_select_db("$icmasbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($result)){
        $sql="insert into `mdl_tools` (`id`,`toolname`)
        values ('ToolForum$data[0]','$data[1]')";
        mysql_query($sql) or  die (mysql_error());

    }
         
//**************************************************************************************************
//**************************************************************************************************

    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $result=mysql_query("SELECT id, name FROM  `mdl_wiki` where `course`='$idc'");
    mysql_select_db("$icmasbase")or die (mysql_error()); 
    while ($data = mysql_fetch_row($result)){
        $sql="insert into `mdl_tools` (`id`,`toolname`)
        values ('ToolWiki$data1[0]','$data1[1]')";
         mysql_query($sql) or  die (mysql_error());

    }

//**************************************************************************************************
//**************************************************************************************************

    mysql_select_db("$moodlebase")or die (mysql_error()); 
    $result=mysql_query("SELECT id, name FROM  `mdl_resource` where `course`='$idc'");
    mysql_select_db("$icmasbase")or die (mysql_error()); 

    while ($data = mysql_fetch_row($result)){
            $x=str_replace("'"," ", $data[1]);
            $sql="insert into `mdl_tools` (`id`,`toolname`)
                      values ('ToolResource$data[0]','$x')";
            mysql_query($sql) or die (mysql_error());

    }
//**************************************************************************************************
//**************************************************************************************************


//**************************************************************************************************
// sort obsels in time
//**************************************************************************************************

    mysql_select_db("$tracesbase")or die (mysql_error()); 
    mysql_query("Alter Table primarytrace order by `TimeVal` ASC");
    $result= mysql_query("select count(*) from primarytrace");
    $row = mysql_fetch_row($result);
    
    echo "

    <b>Collet is done with sucess.</b><br><br>
    You have choosed:<br><br>
    <UL>
     <li><b>Course Name : </b> $CourseName"."</li><br>
     <li><b>Time Interval From : </b> ".date('d/m/Y', $this->time1)."<b> To:</b> ".date('d/m/Y', $this->time2)."</li><br>    
     <li><b>The instances number in Primarry Trace is : </b>$row[0]"."</li>
    </UL>";

     
    mysql_close();
}


}
//**************************************************************************************************
// ENd Agent Collect
//**************************************************************************************************



class AgentTransformation
{

public $tracebase;
//public $IC_MASbase;
public $transformationGraph;


//**************************************************************************************************


function ExecuteTrasformation(){

  $tracesbase=$this->tracebase;
  $TextTranformation=$this->transformationGraph;
  $nodes = array();
  $relations= array();
  $CountTrace=0;
  $variablesList="";

  $sequence = json_decode( $TextTranformation, true);
  
  $nodes = $sequence["nodeDataArray"];
  $relations = $sequence["linkDataArray"];
  


  foreach ($relations as $relation) {

        $Source=$relation["from"];  
        foreach ($nodes as $node) {

                if ($node["key"]==$Source) {  
                    $Tsource=$node["text"]; 
                    break;
                }
        }

        $Source=$relation["to"];  

        foreach ($nodes as $node) {

                if ($node["key"]==$Source) {  
                  $Tdestination=$node["text"]; 
                  break;
                }
        }



  //**************************************************************************************************
  // GET TRANSFORMATION PARAMETERS
  //**************************************************************************************************

        $operatorAll= explode(":", $relation["text"]);
        $operator=$operatorAll[0];

  //**************************************************************************************************


        if ($operator=='filter'){
              $items=explode("=", $operatorAll[1]);
              $Kind=$items[0];
              $Value=$items[1];

              $source=$Tsource;
              $destination=$Tdestination;
               
        //**************************************************************************************************
        // create destination trace model
        //**************************************************************************************************
              mysql_select_db("$tracesbase")or die (mysql_error()); 

              mysql_query("CREATE TABLE IF NOT EXISTS ".$destination." (
              `ObservedID` varchar(60) NOT NULL,
              `UserID` varchar(60) NOT NULL,
              `ObservedType` varchar(60) NOT NULL,
              `ToolID` varchar(60) NOT NULL,
              `TimeVal` bigint(20) NOT NULL,
              `ObservedVal` text NOT NULL,
              `Comment` varchar(60) NOT NULL)");

        //**************************************************************************************************

              if ($Kind=='after')
                  $sql = "insert into ".$destination." select * from ".$source." where TimeVal<='".$Value."'"; 
              if ($Kind=='before')
                  $sql = "insert into ".$destination." select * from ".$source." where TimeVal>='".$Value."'"; 
              if ($Kind=='user') 
                  $sql = "insert into ".$destination." select * from ".$source." where UserID='".$Value."'"; 
              if ($Kind=='tool') 
                  $sql = "insert into ".$destination." select * from ".$source." where ToolID='".$Value."'"; 
              if ($Kind=='activity') 
                  $sql = "insert into ".$destination." select * from ".$source." where ObservedType='".$Value."'"; 
              
              if ($sql!="")        
                  mysql_query($sql) or die (mysql_error());
        }

  //***************************************************************

        if ($operator=='fusion'){ 
              $source1=$Tsource;
              $source2=$operatorAll[1];
              $destination=$Tdestination;
              $sql = "insert into ".$destination." select * from ". $source1." union SELECT * FROM ".$source2." ORDER BY TimeVal";       
              if ($sql!="")        
                  mysql_query($sql) or die (mysql_error()) ;
        
        }

        if ($operator!='equation'){ 
            $result= mysql_query("select count(*) as nb from ". $destination) or die (mysql_error());
            $data = mysql_fetch_assoc($result);
            $CountTrace=$data['nb'];
            $variablesList= $variablesList.$destination."=".$CountTrace."\n";
        }

        if ($operator=='equation'){ 
              $_SESSION['equation']=''; 
              $_SESSION['CurrentIndicatorName']='';
              $_SESSION['equation']=$operatorAll[1];
              $_SESSION['CurrentIndicatorName']=$Tdestination;
        }


  }
return $variablesList;
}

}
//**************************************************************************************************
// ENd Agent Transformation
//**************************************************************************************************

//*************************************************************************************************
// Agent Computation
//*************************************************************************************************
class computationAgent {

   public $variables;
   public $equation;
   
   function computeEquation (){   
     
     require_once('../lib/evalmath.class.php');
     
     $variable=$this->variables;
     $equation=$this->equation;

     $equation=$variable."\n".$equation;
     
     $equation = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $equation);
     $equation=str_replace(' ','',$equation);
     $m = new EvalMath;
     $Val='';
     $m->suppress_errors = true;
     $Tch    =   explode("\n",$equation);
     foreach ($Tch as $value){
        $X =   explode("=",$value);
        $res=$m->evaluate($value);
        if ($X[0]!='')
           $Val=$Val.$X[0]."=".$res."\n";
     }
     
     $Val = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $Val);
     $Val=str_replace(' ','',$Val);
     $_SESSION['result']=ltrim($Val);
     $_SESSION['variables']=ltrim($Val);
     $res=$_SESSION['result'];
    return $res;
}

}

//*************************************************************************************************
// Agent Coordinator
//*************************************************************************************************
class AgentCoordinator{

   public $tracebase;
   public $MoodleBase;
   
   public $TimeFrom;
   public $TimeTo;
   public $Course;
 


    function StartCollectingData(){

        $StartCollect=  new AgentCollector;
        $StartCollect->tracebase=$this->tracebase;
        $StartCollect->Moodlebase=$this->MoodleBase;
        $StartCollect->time1=$this->TimeFrom;
        $StartCollect->time2=$this->TimeTo;
        $StartCollect->course=$this->Course;
        $StartCollect->CollectAgent();

    }

    function StartNewTransformation(){  
        
        include "../config/config.php";

        $StartTransformation=  new AgentTransformation;
        $StartTransformation->tracebase=$tracesbase;
        $StartTransformation->transformationGraph=$_REQUEST['DiagramText'];
        $result=$StartTransformation->ExecuteTrasformation();
        $_SESSION['variables']=$result;

        $Startcomputation=new computationAgent;
        $Startcomputation->variables=$result;
        $Startcomputation->equation=$_SESSION['equation'];

        $result=$Startcomputation->computeEquation(); 
        $_SESSION['result']=$result;
        $_SESSION['diagram']= $_REQUEST['DiagramText'];
    }
}

//*************************************************************************************************
// Agent Interface
//*************************************************************************************************
Class AgentInterface{

  public $IndicatorBase; 
  public $IndicatorName; 
  public $IndicatorSubject;
  public $IndicatorEquation;
  public $IndicatorValue;
  public $IndicatorTimeValue;
  public $IndicatorTransformation;

  function SaveIndicator(){
    
    mysql_select_db("$this->IndicatorBase") or die (mysql_error()); 

    $sql="INSERT INTO `indicator`

      (  `name` , `subject` , `equation` ,   `ivalues`,  `timevalue` , `transformation` ) 
     VALUES (
     '$this->IndicatorName', 
     '$this->IndicatorSubject',
     '$this->IndicatorEquation', 
     '$this->IndicatorValue', 
     '$this->IndicatorTimeValue',
     '$this->IndicatorTransformation'
     )";   
     //echo $sql;
      mysql_query($sql) or die (mysql_error());    
     //mysql_close();    
 
  }

}

?>

