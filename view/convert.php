<?php 
$diagram = json_decode($_POST["diag"], true);


$nodes = array();
$relations= array();

$nodes = $diagram["nodeDataArray"];

$relations = $diagram["linkDataArray"];

foreach ($relations as $relation)
{
	$Source=$relation["from"];  

	foreach ($nodes as $node)
    {
	
	   if ($node["key"]==$Source) {  
	       $Tsource=$node["text"]; 
	       break;
       }
	}
    
    $Source=$relation["to"];  
	
	foreach ($nodes as $node)
    {
	
	   if ($node["key"]==$Source) {  
	       $Tdestination=$node["text"]; 
	       break;
       }
	}


	echo $Tdestination.'='.$Tsource.'['.$relation["text"].'];<br>';
	// filter (after=param) source to destination;
    // filter (before=param) source to destination;
    // filter (user=param) source to destination;
    // fusion source1 and source2 to destination;


}

?>
