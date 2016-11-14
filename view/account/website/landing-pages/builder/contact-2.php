<!DOCTYPE html>
<html>
	<head>
	   	<title>Mega Blocks</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="author" content="" />
		
		<!-- Mobile Specific Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- bootstrap magic -->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		
        <!-- theme custom -->
		<link rel="stylesheet" href="css/style.css" />

		<!-- fonts -->
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
		
		<!-- Font Awesome -->
		<link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.min.css" />

		<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->

		
	</head>
	
	
	<body>
	
	<div id="page" class="page">
	
	<div class="contact-1 contact-2">
		<div class="container">
			<div class="col-md-6 map">
				<div id="test1" class="gmap3"></div>
			</div>
			
			<div class="col-md-6 right">
				<p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
			
				<address>
				  <strong>MegaBlocks, Inc.</strong><br>
				  1355 Market Street, Suite 900<br>
				  San Francisco, CA 94103<br>
				  <abbr title="email">Email:</abbr> email@yourdomain.com<br>
				  <abbr title="Phone">Phone:</abbr> (123) 456-7890<br>
				  <abbr title="Fax">Fax:</abbr> +99 (0) 800 0000 008
				</address>
				<ul class="f-socials">
					<li><a href="#"><i class="fa fa-facebook"></i></a></li>
					<li><a href="#"><i class="fa fa-twitter"></i></a></li>
					<li><a href="#"><i class="fa fa-skype"></i></a></li>
					<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
				</ul>
			</div>
		</div>

	</div>
		
	</div>
	
	
	
	<!-- Jquery Libs -->
	<!-- Latest Version Of Jquery -->
	<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
	<!-- Bootstrap Jquery -->
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/SmoothScroll.js"></script>
	<script type="text/javascript" src="js/jquery.sticky.js"></script>
	<!-- Google Maps -->
	<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<script type="text/javascript" src="js/gmap3.min.js"></script>
	<!-- Theme Custom -->
	<script type="text/javascript" src="js/custom.js"></script>
	
	<script>
		 /* ==============================================
		Google Maps
	=============================================== */
	$('#test1').gmap3({
          marker:{
            address: "Mahilpur, Punjab, India",
			options: {
			 icon: new google.maps.MarkerImage(
			   "http://gj-designs.in/effecty/marker.png",
			   new google.maps.Size(32, 54, "px", "px")
			 )
			}
          },
          map:{
            options:{
              zoom: 12
            }
          }
        });
	</script>
	
	
	</body>
	</html>