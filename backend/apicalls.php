<?php
//include("dbconnect.php");

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

//generic helper function if word in array is contained in string
function contains($str, array $arr)
{
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) return true;
    }
    return false;
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

function get_nytimes_comments($url){
	//http://api.nytimes.com/svc/community/{version}/comments/url/{match-type}[.response-format]?{url=url-to-match}&[offset=int]&api-key={your-API-key}
	global $nytimes_base_url, $nytimes_community_key;
	$endpoint="/svc/community/v2/comments/url/exact-match.json?";
	$request_data=array('url'=>$url,'sort'=>'recommended','api-key'=>$nytimes_community_key);
	$nytimes_request=$nytimes_base_url.$endpoint.http_build_query($request_data);
	//echo(curl_get_contents($nytimes_request));
	$commentsJson=json_decode(curl_get_contents($nytimes_request),true);
	$articleComments=array();
	$buzzwords=array('bad','horrible','awful','stupid','dumb','atrocious','dreadful','lousy','raunchy','crap','shit','fuck');
	for($i=0;$i<count($commentsJson['results']['comments']);$i++){
		$commentBody = $commentsJson['results']['comments'][$i]['commentBody'];
		$commentBody = strip_tags($commentBody);
		if(contains($commentBody,$buzzwords)){
			array_push($articleComments,$commentBody);
		}
	}
	/*
	for($i=0;$i<count($commentsJson['results']['comments']);$i++){
		$comment = $commentsJson['results']['comments'][$i];
		$commentBody = $comment['commentBody'];
		if($comment['replies']['comments']!==null){
			if(count($commentsJson['results']['comments'][$i]['replies']['comments'])>0){
				if(!in_array($commentBody,$articleComments)){
					array_push($articleComments,$commentBody);
				}
			}
		}
	}*/
	for($i=0;$i<count($commentsJson['results']['comments']);$i++){
		$commentBody = $commentsJson['results']['comments'][$i]['commentBody'];
		$commentBody = strip_tags($commentBody);
		if(!in_array($commentBody,$articleComments)){
			array_push($articleComments,$commentBody);
		}
	}
	if(count($articleComments)>5){
		$articleComments = array_slice($articleComments,0,5);
	}
	return($articleComments);
}

function search_nytimes_articles($keyword, $start = null, $end = null){
	global $nytimes_base_url, $nytimes_articlesearch_key;
	//http://api.nytimes.com/svc/search/v2/articlesearch.json?api-key=fe1e430f631e70dc2873b84ead2b39b9:6:68190009&q=mitt+romney&sort=newest
	$endpoint="/svc/search/v2/articlesearch.json?";
	$request_data=array('q'=>$keyword,'begin_date'=>$start,'end_date'=>$end,'api-key'=>$nytimes_articlesearch_key);
	$nytimes_request=$nytimes_base_url.$endpoint.http_build_query($request_data);
	$nytimes_request=str_replace("%3A",":",$nytimes_request);
	//echo($nytimes_request);
	//echo("<br />");
	return(curl_get_contents($nytimes_request));
}

function fetchAndInsertData($query){
	$endTime = time();
	for($i=0;$i<10;$i++){
		$beginTime = $endTime - 60*60*24*7*2;
		$endDate = date("Ymd",$endTime);
		$beginDate = date("Ymd",$beginTime);
		parseNYContent($query, search_nytimes_articles($query,$beginDate,$endDate), $beginTime, $endTime);
		$endTime = $beginTime - 60*60*24;
	}
}

function parseNYContent($keyword, $nyResults, $startTime, $endTime){
	//echo($nyResults);
	$mongoJson = array("person"=>$keyword);
	$mongoJson['start_time']=$startTime;
	$mongoJson['end_time']=$endTime;
	
	$person = $keyword;
	$nyjson = json_decode($nyResults, true);
	$articles = $nyjson['response']['docs'];
	$personPlainText = str_replace(array("%20","+"," ")," ",$person);
	$person_keywords = explode(" ",$personPlainText);
	$best = -1;
	$bestValue = 0;
	$similarArticles = array();
	for($i=0;$i<count($articles);$i++){
		$article = $articles[$i];
		$articleValue = 0;
		$keywordsConcat = "";
		for($j=0;$j<count($article['keywords']);$j++){
			$keywordsConcat=$keywordsConcat.$article['keywords'][$j]['value'];
		}
		//echo($keywordsConcat."<br />");
		if(contains($article['headline']['main'],$person_keywords)){
			$articleValue = 3;
		}
		else if(contains($keywordsConcat,$person_keywords)){
			$articleValue = 2;
		}
		else if(contains($article['snippet'],$person_keywords)){
			$articleValue = 1;
		}
		if($articleValue > $bestValue){
			$bestValue = $articleValue;
			$best = $i;
		}
		if($articleValue > 0){
			array_push($similarArticles, $article['web_url']);
		}
	}
	if($best<0){
		$mongoJson['has_results']='false';
	}
	else{
		$mongoJson['has_results']='true';
		$bestArticle = $articles[$best];
		$mainArticle = array("url"=>$bestArticle['web_url']);
		$mainArticle['headline']=$bestArticle['headline']['main'];
		$mainArticle['snippet']=$bestArticle['snippet'];
		$mainArticle['datePublished']=strtotime($bestArticle['pub_date']);
		$mainArticle['author']=$bestArticle['byline']['original'];
		$article_comments=get_nytimes_comments($bestArticle['web_url']);
		$mainArticle['comments']=$article_comments;
		//get_nytimes_comments($bestArticle['web_url']);
		
		$mongoJson['best_article']=$mainArticle;
		if(($key = array_search($bestArticle['web_url'], $similarArticles)) !== false) {
			unset($similarArticles[$key]);
		}
		$mongoJson['similar_articles']=$similarArticles;
	}
	echo(json_encode($mongoJson, JSON_FORCE_OBJECT));
	echo("<br /><br />");
}
//fetchAndInsertData("Mitt Romney");

?>
