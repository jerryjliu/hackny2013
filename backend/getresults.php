<?php
include("apicalls.php");
$query=$_GET['query'];
$nyTimes=$db->nytimes;
$cursor=$nyTimes->find(array("person"=>$query));
if($cursor->count()==0){
	fetchAndInsertData($query);
	$cursor=$nyTimes->find(array("person"=>$query));
}
foreach($cursor as $document){
	echo ($document["title"]);
}
?>
