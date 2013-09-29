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
		$(document).ready(function(){
			alert("hi"); 
			//alert("hi2"); 
			
			var url = "backend/wolfram.php"; 
			$.getJSON(url, function(data)
			{
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
						
						var nyt_popularity = 0;
						var nyt_popindex;
						var bitly_popularity = 0;
						var bitly_popindex; 
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
						for(var k = 0; k < bitly.numarticles; k++)
						{
							var bitlyarticle = bitly.articles[k];
							var popularity = bitlyarticle.popularity;
							if(popularity >= bitly_popularity) { bitly_popularity = popularity; bitly_popindex = k;}	
						}
						
						//find most popular youtube video
						for(var l = 0; l < yt.numvids; l++)
						{
							var video = yt.vids[l];
							var popularity = video.popularity;
							if(popularity > yt_popularity) {yt_popularity = popularity; yt_popindex = l;}	
						}
						
						//find most popular article (out of nyt and bitly)
						var title, excerpt;
						if(nyt_popularity >= bitly_popularity)
						{
							title = nyt.articles[nyt_popindex].title;
							excerpt = nyt.articles[nyt_popindex].excerpt;
						}
						else
						{
							title = bitly.articles[bitly_popindex].title;
							excerpt = bitly.articles[bitly_popindex].excerpt; 	
						}
						
						var eventcontainer = "<div class='search_eventcontainer'>" +
								"<div class='search_eventleft'>" + 
									"<div class='search_eventtitle' id='title" + i + "'>" +
										"<a href='#'>" + title + "</a>" +
									"</div>" + 
									"<div class='search_eventdesc' id='desc" + i + "'>" + 
										excerpt +
									"</div>" +
								"</div>" +
								"<div class='search_eventright'>" + 
									"<div class='search_video' id='video" + i + "'>" + 
                            			"Controversial Video: <br/>" + 
                            			"<iframe width='240' height='160' src='//www.youtube.com/embed/9bZkp7q19f0' frameborder='0' allowfullscreen></iframe>" +
                        			"</div>" + 
								"</div>" + 
								"<div class='clear'></div>" +
							"</div>";
						
						$('#search_timeline').append(eventcontainer);/*
							<div class='search_eventcontainer'>
								<div class='search_eventleft'>
									<div class='search_eventtitle' id='title" + i + "'>
										<a href='#'>Hello</a>
									</div>
									<div class='search_eventdesc' id='desc" + i + "'>
										asdf asdf asd fasdf asdf asdf asdfa
										sadsf asd
										f asdf asdf asdf asdfasd fasd fasd fa
										dsaf asdfasd fasdf asdf asd 
									</div>
								</div>
								<div class='search_eventright'>
									
								</div>
								<div class='clear'></div>
							</div>
						
						");*/
						$('#search_timeline').append("asdfasdfdadsf
						asdfa''sdf
						asdfasdf" + 1 + "asdfadsf");
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
    	<div class="search_container">
            <div class="search_timeline" id="search_timeline">
          		
           		<div class="search_eventcontainer">
                	<div class="search_eventleft">
                    	<div class="search_eventtitle" id="title1">
                            <a href="#">Hello</a>
                        </div>
                        <div class="search_eventdesc" id="desc1">
                            Desc asdfasdf asdf asdf asdf asdfa
                            sfa sdf
                            asdf
                             sdsdfsdfasdfa sdfas dfasd fasfd afd asdf asdf asdf asdf afds dsafasdf as
                        </div>
                    </div>
                   <div class="search_eventright">
                        <div class="search_video" id="video1">
                            Controversial Video: <br/>
                            <iframe width="240" height="160" src="//www.youtube.com/embed/9bZkp7q19f0" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>