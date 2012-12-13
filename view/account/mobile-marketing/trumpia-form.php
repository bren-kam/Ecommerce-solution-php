<?php
/**
 * @package Grey Suit Retail
 * @page Mobile Marketing - Trumpia form
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var bool $logged_in
 * @var string $trumpia_username
 * @var string $trumpia_password
 */
?>
<form action="http://greysuitmobile.com/action/index.act?mode=signin" method="post">
    <input type="hidden" name="id" value="<?php echo $trumpia_username; ?>" />
    <input type="hidden" name="password" value="<?php echo $trumpia_password; ?>" />
	<input type="hidden" name="version" value="2" />
</form>
<script type="text/javascript">
    document.forms[0].submit();
</script>