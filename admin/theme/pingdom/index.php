<?php
$timer = new Timer();

header::type('xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<pingdom_http_custom_check>
    <status>OK</status>
    <response_time><?php echo $timer->stop(); ?></response_time>
</pingdom_http_custom_check>