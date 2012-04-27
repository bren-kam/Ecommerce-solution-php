<?php

library('whm-api');

$whm = new WHM_API();

fn::info( $whm->app_list() );