<?php
$w = new Websites();
$settings = $w->get_settings( 'trumpia-username', 'trumpia-password' );
?>

<form action="http://greysuitmobile.com/" method="post">
    <input type="hidden" name="id" value="<?php echo $settings['trumpia-username']; ?>" />
    <input type="hidden" name="password" value="<?php echo $settings['trumpia-password']; ?>" />
</form>
<script type="text/javascript">
    document.forms[0].submit();
</script>