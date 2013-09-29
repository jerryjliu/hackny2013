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
            
           	<div class="home_textContainerWrapper" style="background-color:transparent;">
                <div class="home_logo" style="position:relative;left:32px;color:white;font-size:60px;">
                    Bickr
                </div>
                <div class="home_textContainer">
                    <form method="post" action="search.php">
                        <input type="text" style="position:relative;left:130px;font-size:20px;height:25px" class="span6" name="query" onMouseOver="focusin();" onMouseOut="unfocus();">
                    </form> 
                </div>
                <div class="home_footer" style="position:relative;left:32px;font-size:22px;color:white;">
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