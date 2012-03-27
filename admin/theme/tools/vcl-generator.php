<?php
/**
 * @page Tools - Bad Excel
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Define variables
$success = false;
$output = "";

if ( ! empty( $_POST['iplist'] ) ) {
	
		
	$output .=  "#varnishd -f /etc/varnish/s98_alldomains_prod.vcl -s malloc,32M -T :8887 -a 0.0.0.0:80\n";

	$output .=  "#BEGIN BACKEND LOGIC\n\n\n";
			
			
	//process
	$ip_data = $_POST['iplist'];
	
	$ips = array();
	
	//isolate data
	$lines = explode( "\n", $ip_data );
	
	// Split ips and hosts
	foreach ( $lines as $l ) {
		$ips[] = explode( "\t", $l );
	}
	

	
	//generate backends
	foreach( $ips as &$ip ) { 
		$ip[0] = trim( $ip[0], ' ' );
		$ip[1] = trim( $ip[1], ' ' );
		
		// Omit blank IPs and Empty hostnames
		if( $ip[0] && $ip[1] ) {
	 		$name = "b" . preg_replace( "/\./", "_", $ip[0] );
			
			$output .=  "backend " . $name . '{' . "\n";
			$output .=  '  .host = "' . $ip[0] . '";' . "\n";
			$output .=  '  .port = "' . $_POST['port'] . '";' . "\n";
			$output .=  '  .connect_timeout = 30s; ' . "\n";
			$output .=  '  .first_byte_timeout = 30s; ' . "\n";
			$output .=  '  .between_bytes_timeout = 30s; ' . "\n";
			$output .=  '}' . "\n\n";
			
			/* backend b199_204_138_145 {
			  .host = "199.204.138.145";
			  .port = "8001";
			  .connect_timeout = 30s;
			  .first_byte_timeout = 30s;
			  .between_bytes_timeout = 30s;
			} */
		}
	}
	
	$output .=  'backend default {' . "\n" ;
	$output .=  '  .host = "199.204.138.145";' . "\n" ;
	$output .=  '  .port = "' . $_POST['port'] . '";' . "\n";
	$output .=  '  .connect_timeout = 30s;' . "\n" ;
	$output .=  ' .first_byte_timeout = 30s;' . "\n" ;
	$output .=  '  .between_bytes_timeout = 30s;' . "\n" ;
	$output .=  '}' . "\n\n\n#BEGIN RECV LOGIC\n\n\n";
			
	//generate recv logic
	
	/*
	 * if ( req.http.host ~ "(adcockfurniture.com)|(bosticsugg.rawebplus.com)|(choice-furniture.com)|(croskeyfurniture.com)|(furnitureoutletbend.com)|(furnitureworldnw.com)|(gewilliamsfurniture.com)|(hartingfurniture.com)|(knapkecabinets.com)|(mccrarysfurniture.com)|(nixhomecenter.com)|(parrotts-furniture.com)|(ptflathead.com)|(resultsphysicaltherapy.org)|(roomsmadeeasy.cc.cc)|(rwmcdonaldandsons.com)|(seatnsleep.com)|(stansellfurnitureandappliance.com)|(vm001.studio98.com)|(wescohomefurn.com)|(www.majordiscountfurniture.com)" ) {
	 set req.backend = b199_204_138_145;
	}*/
	
	$output .=  '#@Casey - redifined to add some host logic' . "\n";
	$output .=  'sub vcl_recv {' . "\n";
	$first = true;
	// Buffer, for ordering
	$buffer = false;
	foreach( $ips as &$ip ) {
		// Omit blank IPs and Empty hostnames
		if( $ip[0] && $ip[1] ) {		
			if( $ip[0] == '199.47.222.13' ) {
				$buffer = $ip; 
				continue;
			} else {
			 	
				$name = "b" . preg_replace( "/\./", "_", $ip[0] );
				$domains = preg_replace( "/ /", ")|(", $ip[1] );
				if ( $first ) {
					$output .=  '  if ( req.http.host ~ "(' . $domains . ')" ) {' . "\n";
					$first = false;
				} else {
					$output .=  ' elsif ( req.http.host ~ "(' . $domains . ')" ) {' . "\n";
				}
				$output .=  '    set req.backend = ' . $name . ';' . "\n";
				$output .=  '  }';
				
				if ( $buffer ) {
					$name = "b" . preg_replace( "/\./", "_", $buffer[0] );
					$domains = preg_replace( "/ /", ")|(", $buffer[1] );
					if ( $first ) {
						$output .=  '  if ( req.http.host ~ "(' . $domains . ')" ) {' . "\n";
						$first = false;
					} else {
						$output .=  ' elsif ( req.http.host ~ "(' . $domains . ')" ) {' . "\n";
					}
					$output .=  '    set req.backend = ' . $name . ';' . "\n";
					$output .=  '  }';
				
					$buffer = false;
				}
			}
		}
	}
	
	// default
	$output .=  ' else {' . "\n";
	$output .=  '    set req.backend = default;' . "\n";
	$output .=  '  }' . "\n";
	$output .=  $_POST['custom_recv'];
	$output .=  '}' . "\n";
	
	$output .= 'sub vcl_fetch {' . "\n";
	$output .= '    ' . $_POST['custom_fetch'] . "\n";
	$output .= '}';
}


$title = _('VCL Generator') . ' | ' . _('Tools') . ' | ' . TITLE;
css( 'forms' );
get_header();
?>
<div id="content">
	<h1><?php echo _('VCL Generator'); ?></h1>
	<br clear="all" /><br />

	<div style="float: left; padding-right: 2em;">
		<form action="" method="post">
			<p>
				<strong>IPs</strong><br/>
				<textarea class="ta" cols="36" name="iplist"></textarea>
			</p>
			<p>
				<strong>Backend Port</strong><br/>
				<input class="tb" type="text" name="port" value="8001" />
			</p>
			<p>
				<strong>Custom recv Logic</strong><br/>
				<textarea name="custom_recv">#Handle Captcha requests
  	if (req.url ~ "captcha/" || req.request == "POST" ) {
  		/*Pipe captcha and POST requests entirely*/
  		return (pipe);
  	}
  
    if (req.request != "GET" && req.request != "HEAD") {
        /* We only deal with GET and HEAD by default */
        return (pass);
    }	
    
    #Go ahead and get cache up on static files
  	if ( req.url ~ "\.(js|css|jpg|jpeg|png|gif)$" || req.url ~ "/js/" || req.url ~ "/css/" ) {
  		unset req.http.Cookie;
  		return(lookup);
  	}
  
	if ( req.http.Cookie ~ "gsr_" ) {
		return(pass);
	} elsif ( req.url ~ "wp-admin" ) {
		return(pass);
	}

	unset req.http.Cookie;
</textarea></p>
				<p><strong>Custom fetch Logic</strong><br/>
				<textarea name="custom_fetch">#Go ahead and get cache up on static files
  	if ( req.url ~ "\.(js|css|jpg|jpeg|png|gif)$" || req.url ~ "/js/" || req.url ~ "/css/" ) {
  		unset beresp.http.set-cookie;
  	}</textarea>
			</p>
			
			<input class="button" type="submit" />
		</form>
	</div>
	<p>
		<strong>VCL</strong><br/>
		<textarea cols="80" rows="20"><?php echo $output; ?></textarea>
	</p>
	<br style="clear: both;" />
	
	<h1>Reloading VCL</h1>
	<br style="clear: both;" />
	<?php $rando = substr( md5( date('Y-m-d H:i:s', time() ) ), -8, 8); ?>
	
	<p>
		Load new VCL (rl01 can be any unique name)<br/><br/>
		<pre>varnishadm -T localhost:6082 vcl.load rl<?php echo $rando; ?> /etc/varnish/s98_alldomains_prod.vcl
varnishadm -T localhost:6082 vcl.use rl<?php echo $rando; ?></pre>
	</p>
</div>



<?php get_footer(); ?>
