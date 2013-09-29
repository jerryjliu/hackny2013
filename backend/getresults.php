<?php
include("apicalls.php");
$query=$_GET['query'];
$nyTimes=$db->nytimes;
$cursor=$nyTimes->find(array("person"=>$query));
if($cursor->count()==0){
	fetchAndInsertData($query);
	$cursor=$nyTimes->find(array("person"=>$query));
}
$cursor->sort(array('end_time' => -1));
$resJson = array();
foreach($cursor as $document){
	array_push($resJson,$document);
}
echo(json_encode($resJson));
?>
