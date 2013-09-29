<!DOCTYPE html>
<html>
  <head>
    <title>HackNY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/site.css" rel="stylesheet" media="screen">
  </head>	
  <body style="background-image:url(img/wallpaper.jpg)">
	<script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/search.js"></script>
    <script type="text/javascript">
	function expand(id, el)
		{
			//alert(el.class);
			/*var index;
			var els = document.getElementsByClassName('arrows');
			for(var i=0;i<els.length;i++){
				if(els[i]==el){
					index = i;
					break;
				}
			}
			alert(index); */
			$("#search_eventshort" + id).hide();
			$("#search_eventcontainerdetail" + id).show(400, function(){});
			//$("#togglearrow").click(contract(id));
			var arr = document.getElementById("togglearrow" + id);
			arr.onclick = function()
			{
				contract(id);
			};
		}
		
		function contract(id, el)
		{
			//alert(id);
			$("#search_eventshort" + id).show(400, function(){});
			$("#search_eventcontainerdetail" + id).hide();	
			var arr = document.getElementById("togglearrow" + id);
			arr.onclick = function()
			{
				expand(id);
			};
		}
		
		$(document).ready(function(){
			$.ajaxSetup({timeout:30000});
			//alert("hi2"); 
			var wolframinfo = new Object(); 
			var array = new Array(); 
			
			$(".search_wolfram").hide();
			
			var loader = $("<img src='img/ajax-loader.gif' style='display:block; margin-left:auto; margin-right:auto'/>");
			$("#search_timeline").append(loader);
			
			<?php echo "var query = '".$_POST['query']."';"?>
			var textfield = document.getElementById("queryTextField");
			textfield.value = query;
			<?php echo "var url = 'backend/wolfram.php?query=".$_POST['query']."'";?>;
			$.get(url, function(data)
			{
				//alert(data);
				var datajson = $.parseJSON(data);
				wolframinfo = datajson.wolfram; 
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
								
				var url2 = "backend/getresults.php?query=" + wolframinfo.name; 
				$.get(url2, function(data2)
				{
					//alert(data2);
					loader.remove();
					var response2 = $.parseJSON(data2); 
					//alert(response2);
					var numrecords = response2.length;
					//alert(numrecords);
					for(var i = 0; i < numrecords; i++)
					{
						var c_event = response2[i]; 
						var nyt = c_event;
						
						for (property in nyt) {
							//output += property + ': ' + object[property]+'; ';
						}
						//var bitly = c_event.bitly;
						var tw = c_event;
						//find most popular bitly article
						/*for(var k = 0; k < bitly.numarticles; k++)
						{
							var bitlyarticle = bitly.articles[k];
							var popularity = bitlyarticle.popularity;
							//alert(bitlyarticle.title); 
							if(popularity >= bitly_popularity) { bitly_popularity = popularity; bitly_popindex = k;}	
						} */
						
						//find most popular youtube video
						/*for(var l = 0; l < yt.numvids; l++)
						{
							var video = yt.vids[l];
							var popularity = video.views;
							if(popularity >= yt_popularity) {yt_popularity = popularity; yt_popindex = l;}	
						}*/
						if(nyt.has_results=='false'){
							continue;
						}
						//find most popular article (out of nyt)
						var title = nyt.best_article.headline;
						//alert("hola");

						var excerpt = nyt.best_article.snippet;
						//alert(title);
						//alert(title);

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
						var ny_related_html = "";
						var counter = 0;
						for(var j in nyt.similar_articles)
						{
							if(counter > 4)
							{
								break;
							}
							counter++;
							var ny_related_url = nyt.similar_articles[j];
							ny_related_html = ny_related_html.concat(
							"<div class='search_bitlyarticle'>"+
								"<a href='"+ny_related_url+"'>"+ny_related_url+"</a>"+
							"</div>");
						}
						if(ny_related_html == "") ny_related_html = "<div class='search_bitlyarticle'>no related articles</div>";
																		
						//get related comments from nyt article
						var comments = nyt.best_article.comments;
						var comments_html = "";
						for(var j = 0; j < comments.length; j++)
						{
							var comment = comments[j];
							comments_html = comments_html.concat(
							"<div class='search_featcomment'>"+
								"<div class='search_featcommentt'>"+
									"\""+comment+"\""+
								"</div>"+
							"</div>");
						}
						
						if(comments_html == "") comments_html="<div class='search_featcomment'>No comments available.</div>";
						
						//get related tweets from twitter
						var twitter_html = "";
						var tweets = tw.tweets;
						for(var j = 0; j < tweets.length; j++)
						{
							var tweet = tweets[j];
							twitter_html = twitter_html.concat(
							"<div class='search_tweet'>"+
								"<div class='search_tweett'>"+
									"\""+tweet+"\""+
								"</div>"+
							"</div>");
								
						}
						if(twitter_html=="") twitter_html="<div class='search_tweet'>No tweets available.</div>";
						
						//get related info from youtube
						/*var yt_popvid = yt.vids[yt_popindex];
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
						} */
						
						// get date of article
						var d = new Date(0);
						d.setUTCSeconds(nyt.best_article.datePublished);
						
						
						var eventcontainer = "<div class='search_eventcontainer'>" +
								"<div class='search_eventshort' id='search_eventshort" + i + "'>" +
									"<div class='search_eventleft'>" + 
										"<div class='search_eventtitle' id='title" + i + "'>" +
											"<a href='javascript:void(0)' onclick='expand(" + i + ", this)' id='link" + i + "'>" + title + "</a>" +
										"</div>" + 
										"<div class='search_eventdesc' id='desc" + i + "'>" +
											"<div>"+
												d.toLocaleString()+
											"</div>"+
											excerpt +
										"</div>" +
									"</div>" +
									"<div class='search_eventright'>" + 
										"<div class='search_bitlyarticles'>"+
											"<div class='search_bitlyarticlest'>"+
												"Related Articles"+
											"</div>"+
											"<div class='search_bitlyc'>"+
												ny_related_html+
											"</div>"+
										"</div>"+
									"</div>" + 
									"<div class='clear'></div>" +
								"</div>" +
								"<div class='search_eventcontainerdetail' id='search_eventcontainerdetail"+ i + "'>" +
									"<div class='search_tsection'>" +
										"<div class='search_tsectiont'>"+
											title+
										"</div>"+
										"<div>"+
											d.toLocaleString()+
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
													"Related Articles"+
												"</div>"+
												"<div class='search_bitlyc'>"+
													ny_related_html+
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
									"</div>"+
									"<div class='search_arrow'><a onclick='expand("+i+", this)' id='togglearrow"+i+"'><img src='img/arrow.png'/></a></div>"+
							"</div>";
									/*"<div class='search_ytsection'>"+
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
									"</div>"+*/						
									
						$('#search_timeline').append(eventcontainer);
						contract(i);
					}
				});
			});
		
		});
	</script>
    <div class="navbar" id="search_navbar">
      <div class="navbar-inner">
        	<div class="search_container">
                    <a class="brand" href="index.php" style="position:relative;top:3px;">Bickr</a>
                <form class="navbar-form pull-left" method="post" action="search.php">
                    <input type="text" class="span6" name="query" id="queryTextField">
                    <button type="submit" class="btn"><img src="img/searchButton.png"/></button>
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
            <div style="float:left; width:40%; padding-bottom:10px;">
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
            <div style="float:right; padding-right:10px; padding-bottom:10px;position:relative;bottom:25px;">
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