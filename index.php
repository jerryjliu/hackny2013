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
    <script type="text/javascript">
		function focusin()
		{
			
		}
		
		function unfocus()
		{
			
		}
	</script>
    <div class="container-fluid">
    	<div class="home_container">
            
           	<div class="home_textContainerWrapper">
                <div class="home_logo">
                    Bickr
                </div>
                <div class="home_textContainer">
                    <form method="post" action="search.php">
                        <input type="text" class="span6" name="query" onMouseOver="focusin();" onMouseOut="unfocus();">
                    </form> 
                </div>
                <div class="home_footer">
        			By Jerry Liu and Luke Li
       		 	</div>
            </div>
            <!--<div class="home_extradata">
            	Questions? Read our quick how to guide <a href="http://google.com">here</a>.
            </div>-->
            
        </div>
        
    </div>
  </body>
</html>