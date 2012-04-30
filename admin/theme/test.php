<?php

/*
library('whm-api');
$whm = new WHM_API();
fn::info( $whm->app_list() );
*/

//library('pm-api');
//$pm = new PM_API( config::key('s98-pm-key') );
//fn::info( $pm->get_groups() );

for ( $i = 0; $i < 10; $i++ ) {
    echo security::generate_password() . "<br />\n";
}