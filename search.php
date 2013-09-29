<!DOCTYPE html>
<html>
  <head>
    <title>HackNY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/site.css" rel="stylesheet" media="screen">
  </head>	
  <body>
	<script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/search.js"></script>
    <script type="text/javascript">
	
		function expand(id)
		{
			//alert(id);
			$("#search_eventshort" + id).hide();
			$("#search_eventcontainerdetail" + id).show();
		}
		
		function contract(id)
		{
			//alert(id);
			$("#search_eventshort" + id).show();
			$("#search_eventcontainerdetail" + id).hide();	
		}
		
		$(document).ready(function(){
			//alert("hi2"); 
			var wolframinfo = new Object(); 
			var array = new Array(); 
			
			$(".search_wolfram").hide();
			
			var loader = $("<img src='img/ajax-loader.gif' style='display:block; margin-left:auto; margin-right:auto'/>");
			$("#search_timeline").append(loader);
			
			<?php echo "var url = 'backend/wolfram.php?query=".$_POST['query']."'";?>; 
			$.getJSON(url, function(data)
			{
				loader.remove();
				wolframinfo = data.wolfram; 
				if(wolframinfo.isResultPerson == "false")
				{
					$(".search_wolfram").html("<div class='search_wolfram_error'>Query failed</div>");
					$(".search_wolfram").show();
					alert('wolfram query failed');
					return;
				}
				else
				{
					$(".search_wolfram").show();
					$("#wolfram_name").html(wolframinfo.name);
					$("#wolfram_dob").html(wolframinfo.dob);
					$("#wolfram_birth").html(wolframinfo.birthplace);
					$("#wolfram_fact").html(wolframinfo.fact);
					$("#wolfram_image").attr("src", wolframinfo.img); 	
				}
								
				var url2 = "backend/getresults.php"; 
				$.getJSON(url2, function(data2)
				{
					var response2 = data2; 
					var numrecords = response2.totalrecords;
					for(i = 0; i < numrecords; i++)
					{
						var c_event = response2.items[i]; 
						var nyt = c_event.nyt;
						var bitly = c_event.bitly;
						var yt = c_event.youtube;
						var tw = c_event.twitter;
						
						var nyt_popularity = 0;
						var nyt_popindex;
						//var bitly_popularity = 0;
						//var bitly_popindex; 
						var yt_popularity = 0;
						var yt_popindex;
						
						//find most popular nyt article
						for(var j = 0; j < nyt.numarticles; j++)
						{
							var nytarticle = nyt.articles[j]; 
							var popularity = nytarticle.popularity;	
							if(popularity >= nyt_popularity) { nyt_popularity = popularity; nyt_popindex = j;}
						}
						
						//find most popular bitly article
						/*for(var k = 0; k < bitly.numarticles; k++)
						{
							var bitlyarticle = bitly.articles[k];
							var popularity = bitlyarticle.popularity;
							//alert(bitlyarticle.title); 
							if(popularity >= bitly_popularity) { bitly_popularity = popularity; bitly_popindex = k;}	
						} */
						
						//find most popular youtube video
						for(var l = 0; l < yt.numvids; l++)
						{
							var video = yt.vids[l];
							var popularity = video.views;
							if(popularity >= yt_popularity) {yt_popularity = popularity; yt_popindex = l;}	
						}
						
						//find most popular article (out of nyt)
						var title = nyt.articles[nyt_popindex].title;
						var excerpt = nyt.articles[nyt_popindex].excerpt;

						/*var title, excerpt;
						if(nyt_popularity >= bitly_popularity)
						{
							title = nyt.articles[nyt_popindex].title;
							excerpt = nyt.articles[nyt_popindex].excerpt;
						}
						else
						{
							title = bitly.articles[bitly_popindex].title;
							excerpt = bitly.articles[bitly_popindex].excerpt; 	
						} */
						
						
						//get related articles from bitly
						var bitly_related_html = "";
						for(var j = 0; j < Math.min(bitly.numarticles, 5); j++)
						{
							var bitly_title = bitly.articles[j].title;
							var bitly_url = bitly.articles[j].url;
							bitly_related_html = bitly_related_html.concat(
							"<div class='search_bitlyarticle'>"+
								"<a href='"+bitly_url+"'>"+bitly_title+"</a>"+
							"</div>");
						}
												
						//get related comments from nyt article
						var comments = nyt.articles[nyt_popindex].comments;
						var comments_html = "";
						for(var j = 0; j < comments.length; j++)
						{
							var comment = comments[j].comment;
							var user = comments[j].user; 
							comments_html = comments_html.concat(
							"<div class='search_featcomment'>"+
								"<div class='search_featcommentt'>"+
									comment+
								"</div>"+
								"<div class='search_featcommenta'>"+
									"- " + user+
								"</div>"+
							"</div>");
						}
						
						//get related tweets from twitter
						var twitter_html = "";
						var tweets = tw.tweets;
						for(var j = 0; j < tw.numtweets; j++)
						{
							var tweet = tweets[j];
							var tweett = tweet.tweet;
							var user = tweet.user;
							twitter_html = twitter_html.concat(
							"<div class='search_tweet'>"+
								"<div class='search_tweett'>"+
									tweett+
								"</div>"+
								"<div class='search_tweeta'>"+
									"@"+user+
								"</div>"+
							"</div>");
								
						}
						
						//get related info from youtube
						var yt_popvid = yt.vids[yt_popindex];
						var yt_dislikep = Math.round(yt_popvid.numdislikes * 100 / (yt_popvid.numdislikes + yt_popvid.numlikes)); 
						var yt_views = yt_popvid.views;
						var yt_numc = yt_popvid.numcomments;
						
						//get comments from youtube
						var yt_comments_html = "";
						for(var j = 0; j < yt_popvid.numcomments; j++)
						{
							var comment = yt_popvid.comments[j];
							var commentt = comment.comment;
							var user = comment.user;
							yt_comments_html = yt_comments_html.concat(
							"<div class='search_vidcomment'>"+
								"<div class='search_vidcommenta'>"+
									user+
								"</div>"+
								"<div class='search_vidcommentt'>"+
									commentt+
								"</div>"+
							"</div>");	
						}
						
						var eventcontainer = "<div class='search_eventcontainer'>" +
								"<div class='search_eventshort' id='search_eventshort" + i + "'>" +
									"<div class='search_eventleft'>" + 
										"<div class='search_eventtitle' id='title" + i + "'>" +
											"<a href='javascript:void(0)' onclick='expand(" + i + ")' id='link" + i + "'>" + title + "</a>" +
										"</div>" + 
										"<div class='search_eventdesc' id='desc" + i + "'>" + 
											excerpt +
										"</div>" +
									"</div>" +
									"<div class='search_eventright'>" + 
										"<div class='search_video' id='video" + i + "'>" + 
											"<iframe width='240' height='160' src='" + yt.vids[yt_popindex].url + "' frameborder='0' allowfullscreen></iframe>" +
										"</div>" + 
									"</div>" + 
									"<div class='clear'></div>" +
								"</div>" +
								"<div class='search_eventcontainerdetail' id='search_eventcontainerdetail"+ i + "'>" +
									"<div class='search_tsection'>" +
										"<div class='search_tsectiont'>"+
											title+
										"</div>"+
									"</div>"+
									"<div class='search_section2'>"+
										"<div class='search_eventleft2'>"+
											"<div class='search_eventdescfull'>"+
												excerpt+
											"</div>"+
										"</div>"+
										"<div class='search_eventright2'>"+
											"<div class='search_bitlyarticles'>"+
												"<div class='search_bitlyarticlest'>"+
													"Related Articles from Bitly"+
												"</div>"+
												"<div class='search_bitlyc'>"+
													bitly_related_html+
												"</div>"+
											"</div>"+
										"</div>"+
										"<div class='clear'></div>"+
									"</div>"+
									"<div class='search_section3'>"+
										"<div class='search_eventleft3'>"+
											"<div class='search_nytcomments'>"+
												"<div class='search_sectiont' id='search_commentst'>"+
													"Featured Comments:"+
												"</div>"+
												"<div class='search_sectionc'>"+
													comments_html+
												"</div>"+
											"</div>"+
										"</div>"+
										"<div class='search_eventright3'>"+
											"<div class='search_tweets'>"+
												"<div class='search_sectiont' id='search_tweetst'>"+
													"Featured Tweets:"+
												"</div>"+
												"<div class='search_sectionc'>"+
													twitter_html+
												"</div>"+
											"</div>"+
										"</div>"+
										"<div class='clear'></div>"+
									"</div>"+
									"<div class='clear'></div>"+
									"<div class='search_ytsection'>"+
										"<div class='search_ytsectiont'>"+
											"Featured Video"+
										"</div>"+
										"<div class='search_vidleft'>"+
											"<div class='search_ytsectionvid'>"+
												"<iframe width='100%' height='300' src='" + yt.vids[yt_popindex].url + "' frameborder='0' allowfullscreen></iframe>"+
											"</div>"+
										"</div>"+
										"<div class='search_vidright'>"+
											"<div class='search_vidstat' id='search_vid_numdislikes'>"+
												"<span style='color:red'>"+yt_dislikep + "%</span> of users dislike this video"+
											"</div>"+
											"<div class='search_vidstat' id='search_vid_numviews'>"+
												yt_views+" users have viewed this video"+
											"</div>"+
											"<div class='search_vidstat' id='search_vid_numcomments'>"+
												"There are "+yt_numc+" comments on this video"+
											"</div>"+
										"</div>"+
										"<div class='clear'></div>"+
										"<div class='search_vidcomments'>"+
											yt_comments_html+
										"</div>"+	
									"</div>"+						
								
							"</div>";
													
						$('#search_timeline').append(eventcontainer);
						contract(i);
					}
				});	
			});
		
		});
		
		
	</script>
    <div class="navbar">
      <div class="navbar-inner">
        	<div class="search_container">
                    <a class="brand" href="index.php">Name</a>
                <form class="navbar-form pull-left">
                    <input type="text" class="span6">
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
      </div>
    </div>
    <div class="container-fluid">
    	<div class="search_wolfram">
        	<div class="search_wolframt">
            	Basic Stats 
                <img src="img/mathematica.png" width="20px" height="20px"/>
            </div>
            <div style="float:left; width:40%;">
                <div class="search_wolfram_stat">
                    <span class="search_wolfram_statt">Name:</span>
                    <span class="search_wolfram_statd" id="wolfram_name"></span>
                </div>
                <div class="search_wolfram_stat">
                     <span class="search_wolfram_statt">Date of Birth:</span>
                    <span class="search_wolfram_statd" id="wolfram_dob"></span>
                </div>
                <div class="search_wolfram_stat">
                     <span class="search_wolfram_statt">Birthplace:</span>
                     <span class="search_wolfram_statd" id="wolfram_birth"></span>
                </div>
                <div class="search_wolfram_stat">
                    <span class="search_wolfram_statt">Fact:</span>
                    <span class="search_wolfram_statd" id="wolfram_fact"></span>
                </div>
            </div>
            <div style="float:left; padding-left:10px">
            	<img src="#" id="wolfram_image"/>
            </div>
            <div class="clear"></div>
        </div>
    	<div class="search_container">
            <div class="search_timeline" id="search_timeline">
          		
           	
            </div>
        </div>
    </div>
  </body>
</html>