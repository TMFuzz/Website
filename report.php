<?php 
$your_email ='support@fuzzyfrontier.com';// <<=== update to your email address

session_start();
$errors = '';
$name = '';
$visitor_email = '';
$user_message = '';

if(isset($_POST['submit']))
{
	
	$name = $_POST['name'];
	$visitor_email = $_POST['email'];
	$user_message = $_POST['message'];
	///------------Do Validations-------------
	if(empty($name)||empty($visitor_email))
	{
		$errors .= "\n Name and Email are required fields. ";	
	}
	if(IsInjected($visitor_email))
	{
		$errors .= "\n Bad email value!";
	}
	if(empty($_SESSION['6_letters_code'] ) ||
	  strcasecmp($_SESSION['6_letters_code'], $_POST['6_letters_code']) != 0)
	{
	//Note: the captcha code is compared case insensitively.
	//if you want case sensitive match, update the check above to
	// strcmp()
		$errors .= "\n The captcha code does not match!";
	}
	
	if(empty($errors))
	{
		//send the email
		$to = $your_email;
		$subject="Report";
		$from = $visitor_email;
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		
		$body = "A user  $name submitted the contact form:\n".
		"Name: $name\n".
		"Email: $visitor_email \n".
		"Message: \n ".
		"$user_message\n".
		"IP: $ip\n";	
		
		$headers = "From: $from \r\n";
		$headers .= "Reply-To: $visitor_email \r\n";
		
		mail($to, $subject, $body,$headers);
		
		header('Location: thank-you');
	}
}

// Function to validate against any email injection attempts
function IsInjected($str)
{
  $injections = array('(\n+)',
              '(\r+)',
              '(\t+)',
              '(%0A+)',
              '(%0D+)',
              '(%08+)',
              '(%09+)'
              );
  $inject = join('|', $injections);
  $inject = "/$inject/i";
  if(preg_match($inject,$str))
    {
    return true;
  }
  else
    {
    return false;
  }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>Player Reporting</title>

<link href="http://fonts.googleapis.com/css?family=Oswald:400,700,300" rel="stylesheet" type="text/css" />
<!-- Included CSS Files -->
<link rel="stylesheet" href="stylesheets/main.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link rel="stylesheet" href="stylesheets/devices.css" type="text/css" media="screen" title="no title" charset="utf-8"  />
<link rel="stylesheet" href="stylesheets/post.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link rel="stylesheet" href="stylesheets/validationEngine.jquery.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link rel="stylesheet" href="stylesheets/jquery.fancybox.css?v=2.1.2" type="text/css"  media="screen" />
<!--[if IE ]>
<link rel="stylesheet" href="stylesheets/ie.css"> 
<![endif]-->
</head>

<body>

<!--********************************************* Main wrapper Start *********************************************--> 
<div id="footer_image">
<div id="main_wrapper">

    <!--********************************************* Logo Start *********************************************-->
    <div id="logo"> <a href="./index"><img alt="alt_example" src="./images/logo.png"  /></a>
      <?php include 'ssi/social.txt' ?>
    
    </div>
    <!--********************************************* Logo end *********************************************--> 
    
    <!--********************************************* Main_in Start *********************************************-->
    <div id="main_in">  
      
    <!--********************************************* Mainmenu Start *********************************************-->
    <?php include 'ssi/menu.txt' ?>
    
    <!--********************************************* Mainmenu end *********************************************--> 
        
        

     <!--********************************************* Main start *********************************************-->
     <div id="main_news_wrapper">
 
       <div id="row"> 
       <!-- Left wrapper Start -->
        <div id="left_wrapper">
                <div class="header">
                	<h2><span>Fuzzy frontier //</span> <a href="./report">Player Reporting</a></h2>
                </div>
                 
                <div id="post_wrapper">
                
                    <!-- Leave a response Start -->
                    <div id="response" class="contact_form">
                    	<h1> Drop us a message! </h1>
                        <p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque sit amet lacus justo. Mauris suscipit dolor id lacus commodo ultricies elementum ante tempor. Sed lacus arcu, iaculis id auctor commodo, facilisis eget felis. </p>

                        <?php
if(!empty($errors)){
echo "<p class='err'>".nl2br($errors)."</p>";
}
?>
                  
                        	<div class="form_left">
                           
   <div id='contact_form_errorloc' class='err'></div>
<form method="POST" name="contact_form" 
action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>"> 
<p>
<label for='name'>Name: </label><br>
<input type="text" name="name" value='<?php echo htmlentities($name) ?>'>
</p>
<p>
<label for='email'>Email: </label><br>
<input type="text" name="email" value='<?php echo htmlentities($visitor_email) ?>'>
</p>
<p>
<label for='message'>Message:</label> <br>
<textarea name="message" rows=8 cols=30><?php echo htmlentities($user_message) ?></textarea>
</p>
<p>
<img src="captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' ><br>
<label for='message'>Enter the code above here :</label><br>
<input id="6_letters_code" name="6_letters_code" type="text"><br>
<small>Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh</small>

<br>
<br>
<br>
<input type="submit" value="Submit" name='submit'>
</p></form>
      
    						</div>
                            
                          
                      </form>

                    </div>
                    <!-- Leave a response end -->  
                    
                    
                    <div class="clear"></div>
               </div> 
            </div>
            <!-- Left wrapper end -->
            
 
      	<div class="clear"></div>
        
        </div>
      </div>
    
    <div class="bottom_shadow"></div>
  
    <!--********************************************* Main end *********************************************--> 
    
    <!--********************************************* Main advert start *********************************************-->
    <div class="main_advert">
      <p>Ad space
      
    </div>
    <!--********************************************* Main advert end *********************************************--> 

    <!--********************************************* Footer start *********************************************-->
    <div id="footer">
    <div class="row">
      <div class="footer_widget">
        <div class="header"><a href="#">About ORIZON</a></div>
        <div class="body">
          <p><img alt="alt_example" src="./images/about_img.png" align="left" style="margin:0px 15px 5px 0px;"  />Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
          <p>Praesent aliquet justo quis lacus mollis molestie pellentesque habitant morbi tristique senectus et e. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque pulvinar urna eget ante pharetra vitae fermentum dui sagittis. Vivamus non ipsum elit, et tincidunt quam.</p>
          <img alt="alt_example" src="./images/orizon_about.png" style="margin:11px 0px 0px 55px;"/></div>
      </div>
      <div class="divider_footer"></div>
      <div id="latest_media">
        <div class="header"><a href="#">latest media</a></div>
        <div class="body">
          <ul id="l_media_list">
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
            <li><a class="shadowbox" href="./images/media/full/1.jpg" rel="gallery" ><img alt="alt_example" src="./images/media/thumb/1.jpg" /></a></li>
          </ul>
        </div>
      </div>
      <div class="clear"></div>
    </div>
    </div>
    <!--********************************************* Footer end *********************************************--> 
    <div class="clear"></div>
    <!--********************************************* Twitter feed start *********************************************-->
    <div id="twitter_last"> <a id="tr_left" href="https://twitter.com/FuzzyFrontier"><img alt="alt_example" src="./images/blank.gif" width="100%" height="30px" border="0" /></a>
      <div id="tr_right">
        <ul id="tw">
        </ul>
      </div>
    </div>
    <!--********************************************* Twitter feed end *********************************************--> 

     </div>
<!--********************************************* Main_in Start *********************************************--> 
     
     <?php include 'ssi/footer.txt' ?>
     
	</div>
</div>
<!--********************************************* Main wrapper end *********************************************--> 

<script src="http://code.jquery.com/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="./javascript/getTweet.js" type="text/javascript" ></script>
<script src="./javascript/jquery.validationEngine-en.js" type="text/javascript"></script>
<script src="./javascript/jquery.validationEngine.js" type="text/javascript"></script>
<script src="./javascript/jquery.fancybox.js?v=2.1.3" type="text/javascript" ></script>

<!--******* Javascript Code for the comment form *******-->
<script type="text/javascript">
$(document).ready(function() {

	// SUCCESS AJAX CALL, replace "success: false," by:     success : function() { callSuccessFunction() }, 
	$("#form").validationEngine({
		ajaxSubmit: true,
			ajaxSubmitFile: "ajaxSubmit.php",
			ajaxSubmitMessage: "Thank you, your post has been submitted!",
		success :  false, ajaxSubmitMessage: "Thank you, your post has been submitted!",
		failure : function() {}
	})


});
</script>


<!--******* Javascript Code for the image shadowbox *******-->
<script type="text/javascript">
$(document).ready(function() {
	/*
	*  Simple image gallery. Uses default settings
	*/
	
	$('.shadowbox').fancybox();
});
</script>

<!--******* Javascript Code for the menu *******-->

<script type="text/javascript">
    $(document).ready(function() {
        $('#menu li').bind('mouseover', openSubMenu);
        $('#menu > li').bind('mouseout', closeSubMenu);

        function openSubMenu() {
            $(this).find('ul').css('visibility', 'visible');
        };

        function closeSubMenu() {
            $(this).find('ul').css('visibility', 'hidden');
        };
    });
</script>

<script type="text/javascript">
    $(function() {
        var pull    = $('#pull');
        menu 		= $('ul#menu');

        $(pull).on('click', function(e) {
            e.preventDefault();
            menu.slideToggle();
        });

        $(window).resize(function(){
            var w = $(window).width();
            if(w > 767 && $('ul#menu').css('visibility', 'hidden')) {
                $('ul#menu').removeAttr('style');
            };
            var menu = $('#menu_wrapper').width();
            $('#pull').width(menu - 20);
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        var menu = $('#menu_wrapper').width();
        $('#pull').width(menu - 20);
    });
</script>
<?php include 'ssi/tracker.txt' ?>
</body>
</html>

