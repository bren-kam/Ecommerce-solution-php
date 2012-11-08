<?php
$w = new Websites();
$settings = $w->get_settings( 'trumpia-username', 'trumpia-password' );
?>

<form action="http://greysuitmobile.com/action/index.act?mode=signin" method="post">
    <input type="hidden" name="id" value="<?php echo $settings['trumpia-username']; ?>" />
    <input type="hidden" name="password" value="<?php echo $settings['trumpia-password']; ?>" />
	<input type="hidden" name="version" value="2" />
</form>
<script type="text/javascript">
    document.forms[0].submit();
</script>