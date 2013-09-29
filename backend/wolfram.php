<?php
error_reporting(0);
$query = $_GET['query'];
$fixedQuery = str_replace(array('%20','+'),' ',$query);
include("apicalls.php");
$wolframalpha=$db->wolframalpha;
$cursor=$wolframalpha->find(array("wolfram.name"=>$fixedQuery));
if($cursor->count()==0){
$json=json_decode(search_wolfram($query), true);
//print_r($json);
$pods=($json['pod']);
$isResultPerson="false";
$interpretation=$img=$name=$dob=$birthplace=$fact="";
for($i=0;$i<count($pods);$i++){
	//print_r($pods[$i]);
	if($pods[$i]['@attributes']['title']=='Input interpretation'){
		$interpretation=$pods[$i]['subpod']['plaintext'];
		//$img=$pods[$i]['subpod']['img']['@attributes']['src'];
		$name=trim(preg_replace("/\([^)]+\)/","",$interpretation));
	}
	else if($pods[$i]['@attributes']['title']=='Basic information'&&$pods[$i]['@attributes']['id']=='BasicInformation:PeopleData'){
		$isResultPerson="true";
		$personinfo=$pods[$i]['subpod']['plaintext'];
		$personinfo= str_replace(" | ","",$personinfo);
		preg_match("/date of birth(.+)place of birth/", $personinfo, $matches);
		$dob=$matches[1];
		preg_match("/place of birth(.+)/", $personinfo, $matches);
		$birthplace=$matches[1];
	}
	else if($pods[$i]['@attributes']['title']=='Notable facts'&&$pods[$i]['@attributes']['id']=='NotableFacts:PeopleData'){
		$plainfacts=$pods[$i]['subpod']['plaintext'];
		preg_match_all("/(.+?)[a-z][A-Z]/", $plainfacts, $matches);
		//print_r($matches[0]);
		$indexes=array();
		array_push($indexes,0);
		for($j=0;$j<count($matches[0]);$j++){
			array_push($indexes,strlen($matches[0][$j])-1);
		}
		$fact = substr($plainfacts,0,$indexes[1]).".";
		//echo($fact);
		/*
		print_r($indexes);
		for($j=1;$j<count($indexes);$j++){
			echo(substr($plainfacts,$indexes[$j-1],$indexes[$j]).".<br />");
		}
		*/
	}
	else if($pods[$i]['@attributes']['title']=='Image'&&$pods[$i]['@attributes']['id']=='Image:PeopleData'){
		//http://www4a.wolframalpha.com/Calculate/MSP/MSP34601hi62c9c0bb9724d00000gie12bfc5if162f?MSPStoreType=image/gif&s=64
		//http:\/\/www4a.wolframalpha.com\/Calculate\/MSP\/MSP34601hi62c9c0bb9724d00000gie12bfc5if162f?MSPStoreType=image\/gif&s=64
		$img = $pods[$i]['subpod']['img']['@attributes']['src'];
	}

}
$result_array=array('isResultPerson'=>$isResultPerson,'interpretation'=>$interpretation,'img'=>$img,'name'=>$name,'dob'=>$dob,'birthplace'=>$birthplace,'fact'=>$fact);
$result = array('wolfram' => $result_array);
$wolframalpha->insert($result);
echo(json_encode($result, JSON_FORCE_OBJECT));
//$cursor=$wolframalpha->find(array("name"=>$query));
//$result = json_encode(array('wolfram' => $result_array), JSON_FORCE_OBJECT);
}
else{
foreach($cursor as $document){
	//print_r($document);
	echo(json_encode($document));
	break;
}
}
//echo($result);

?>
