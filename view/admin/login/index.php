<div id="content">
	<h1><?php echo _('Login'); ?></h1>
	<br clear="all" />
	<br />
	<?php if ( !empty( $errs ) ) echo "<p class='red'>$errs</p><br />"; ?>
	<form action="" method="post" name="fLogin">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td><label for="tEmail"><?php echo _('Email:'); ?></label></td>
                <td><input type="text" class="tb" name="tEmail" id="tEmail" value="<?php if ( isset( $_POST['tEmail'] ) ) echo $_POST['tEmail']; ?>" maxlength="200" /></td>
            </tr>
            <tr>
                <td><label for="tPassword"><?php echo _('Password:'); ?></label></td>
                <td><input type="password" class="tb" name="tPassword" id="tPassword" maxlength="30" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="checkbox" class="cb" name="cbRememberMe" id="cbRememberMe" value="yes" /> <label for="cbRememberMe"><?php echo _('Remember me?'); ?></label></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" value="Login" class="button" />
                    <br /><br />
                    <a href="/forgot-your-password/" title="<?php echo _('Forgot Your Password?'); ?>"><?php echo _('Forgot Your Password?'); ?></a>
                </td>
            </tr>
        </table>
        <input type="hidden" name="referer" value="<?php if ( isset( $_GET['r'] ) ) echo urldecode( $_GET['r'] ); ?>" />
	    <?php nonce::field( 'login' ); ?>
	</form>
	<?php echo $v->js_validation(); ?>
</div>