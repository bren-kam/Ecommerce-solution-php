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
		<link rel="stylesheet" type="text/css" href="/resources/css_single/?f=PageBuilder/css/bootstrap/css/bootstrap.min" />
		
		<!-- bootstrap datepicker -->
		<link rel="stylesheet" type="text/css" href="/resources/css_single/?f=PageBuilder/css/bootstrap-datepicker" />
		
        <!-- theme custom -->
		<link rel="stylesheet" href="/resources/css_single/?f=PageBuilder/elements/css/style" />

		<!-- fonts -->
		<link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
		
		<!-- Font Awesome -->
		<link rel="stylesheet" type="text/css" href="/resources/css_single/?f=PageBuildercss/font-awesome.min" />

		<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!--[if lt IE 9]>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
        <![endif]-->

		
	</head>
	
	
	<body>
	
	<div id="page" class="page">	
		
	</div>
	
	
	
	<!-- Jquery Libs -->
	<!-- Latest Version Of Jquery -->
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/elements/js/jquery-2.1.3.min"></script>
	<!-- Bootstrap Jquery -->
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/js/bootstrap.min"></script>
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/elements/js/SmoothScroll"></script>
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/elements/js/jquery.sticky"></script>
	<!-- Bootstrap Datepicker -->
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/elements/js/bootstrap-datepicker.min"></script>
	<!-- Theme Custom -->
	<script type="text/javascript" src="/resources/js_single/?f=PageBuilder/elements/js/custom"></script>
	
	<!-- Google Maps -->
	
	<script type="text/javascript">
				function ajax()
				{
				var a=document.getElementById('contact_name').value;
				var b=document.getElementById('contact_email').value;
				var c=document.getElementById('contact_subject').value;
				var e=document.getElementById('contact_message').value;



				var x;
				if(window.XMLHttpRequest)
				{
				x=new XMLHttpRequest();
				}
				else
				{
				x=new ActiveXObject("Microsoft.XMLHTTP");
				}

				x.open("GET","contact.php?name="+a+"& email="+b+"& subject="+c+"& message="+e,true);

				x.send();

				x.onreadystatechange=function()
				{
				if(x.readyState==4 && x.status==200)
				{
				document.getElementById("p1").innerHTML=x.responseText;
				}
				}
				}

		</script>

	
	</body>
	</html>