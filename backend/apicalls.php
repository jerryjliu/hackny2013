<?php
//generic fetch url request
function curl_get_contents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

//xml to json
function xmlToJSON($xmlText){
	$xmlText = str_replace(array("\n", "\r", "\t"), '', $xmlText);
	$xmlText = trim(str_replace('"', "'", $xmlText));
	$simpleXml = simplexml_load_string($xmlText);
	$json = json_encode($simpleXml);
	return $json;
}

//wolfram alpha api
$wolfram_base_url="http://api.wolframalpha.com/v2/query?";
$wolfram_appid="Q44GJ5-K5AL7UJYGU";
function search_wolfram($keyword){
	global $wolfram_base_url, $wolfram_appid;
	$request_data=array('input'=>$keyword, 'appid'=>$wolfram_appid, 'format'=>'image,plaintext');
	$wolfram_request=$wolfram_base_url.http_build_query($request_data);
	$xmltext = (curl_get_contents($wolfram_request));
	return(xmlToJSON($xmltext));
}

//bitly api
$bitly_base_url="https://api-ssl.bitly.com";
$bitly_access_token="370bb3d02a0a1621777530595bc9ea9186395ec6";

function search_bitly($keyword){
	global $bitly_base_url, $bitly_access_token;
	$endpoint="/v3/search?";
	$request_data=array('query'=>$keyword, 'access_token'=>$bitly_access_token, 'format'=>'json');
	$bitly_request=$bitly_base_url.$endpoint.http_build_query($request_data);
	return(curl_get_contents($bitly_request));
}

//nytimes api
$nytimes_base_url="http://api.nytimes.com";
$nytimes_articlesearch_key="fe1e430f631e70dc2873b84ead2b39b9:6:68190009";
$nytimes_community_key="bb4631c307c8b6f59f953b83ce3bf6ca:12:68190009";
$nytimes_semantics_key="c1fccb0185ef120d818a4b056357fc11:19:68190009";

function search_nytimes_articles($keyword, $before = null){
	global $nytimes_base_url, $nytimes_articlesearch_key;
	$endpoint="/svc/search/v1/article?";
	$request_data=array('query'=>$keyword,'format'=>'json','api-key'=>$nytimes_articlesearch_key);
	$nytimes_request=$nytimes_base_url.$endpoint.http_build_query($request_data);
	$nytimes_request=str_replace("%3A",":",$nytimes_request);
	return(curl_get_contents($nytimes_request));
}

?>
