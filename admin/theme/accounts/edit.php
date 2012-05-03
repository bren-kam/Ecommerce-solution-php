<?php
/**
 * @page Edit Account
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;
$i = new Industries;
$v = new Validator;

$industries = $i->get_all();

$v->form_name = 'fEditAccount';

$v->add_validation( 'tTitle', 'req', _('The "Account Title" field is required') );
$v->add_validation( 'tDomain', 'req', _('The "Account Domain" field is required') );
$v->add_validation( 'tTheme', 'req', _('The "Account Theme" field is required') );

$v->add_validation( 'tProducts', 'req', _('The "Products" field is required') );
$v->add_validation( 'tProducts', 'num', _('The "Products" field must contain a number') );

$v->add_validation( 'tType', 'req', _('The "Account Type" is required') );

$v->add_validation( 'tGAProfileID', 'num', _('The "Google Analytics Profile ID" field must contain a number') );

// Initialize variable
$success = false;
$errs = '';

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-account' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		// Start fields array
		$fields = array( 
			// Information
			'user_id' => $_POST['sUserID'],
			'os_user_id' => $_POST['sOSUserID'],
			'domain' => $_POST['tDomain'],
			'subdomain' => $_POST['tSubDomain'],
			'title' => stripslashes( $_POST['tTitle'] ),
            'plan_name' => stripslashes( $_POST['tPlanName'] ),
            'plan_description' => stripslashes( $_POST['tPlanDescription'] ),
			'theme' => $_POST['tTheme'],
			'phone' => $_POST['tPhone'],
			'products' => $_POST['tProducts'],
			'ga_profile_id' => $_POST['tGAProfileID'],
			'ga_tracking_key' => $_POST['tGATrackingKey'],
			// Features
			'pages' => $_POST['cbWebsite'], // Website/Page (same thing)
 			'product_catalog' => $_POST['cbProductCatalog'],
			'blog' => $_POST['cbBlog'],
			'email_marketing' => $_POST['cbEmailMarketing'],
            'mobile_marketing' => $_POST['cbMobileMarketing'],
			'shopping_cart' => $_POST['cbShoppingCart'],
			'seo' => $_POST['cbSEO'],
			'room_planner' => $_POST['cbRoomPlanner'],
			'craigslist' => $_POST['cbCraigslist'],
			'social_media' => $_POST['cbSocialMedia'],
			'domain_registration' => $_POST['cbDomainRegistration'],
			'additional_email_addresses' => $_POST['cbAdditionalEmail'],
			// Extras
			'mc_list_id' => $_POST['tMCListID'],
			'type' => $_POST['tType'],
			'live' => (isset( $_POST['cbLive'] ) ) ? 1 : 0
		);
		
		// Start DB safety preparation
		$fields_safety = 'iisssssssiisiiiiiiiiiiiissi';
		
		// FTP data
		if ( !empty( $_POST['tFTPHost'] ) ) {
			$fields['ftp_host'] = security::encrypt( $_POST['tFTPHost'], ENCRYPTION_KEY, true );
			$fields_safety .= 's';
		}
		
		if ( !empty( $_POST['tFTPUser'] ) ) {
			$fields['ftp_username'] = security::encrypt( $_POST['tFTPUser'], ENCRYPTION_KEY, true );
			$fields_safety .= 's';
		}
		
		if ( !empty( $_POST['tFTPPassword'] ) ) {
			$fields['ftp_password'] = security::encrypt( stripslashes( $_POST['tFTPPassword'] ), ENCRYPTION_KEY, true );
			$fields_safety .= 's';
		}
		
		// Industries
		$w->remove_industries( $_GET['wid'] );
		
		foreach ( $industries as $industry ) {
			if ( isset( $_POST['cbIndustry' . $industry['industry_id']] ) )
				$industry_ids[] = $industry['industry_id'];
		}
		
		$w->add_industries( $industry_ids, $_GET['wid'] );
			
		// Extras
		if ( !empty( $_POST['tWordPressUsername'] ) ) {
			$fields['wordpress_username'] = security::encrypt( $_POST['tWordPressUsername'], ENCRYPTION_KEY, true );
			$fields_safety .= 's';
		}

		if ( !empty( $_POST['tWordPressPassword'] ) ) {
			$fields['wordpress_password'] = security::encrypt( $_POST['tWordPressPassword'], ENCRYPTION_KEY, true );
			$fields_safety .= 's';
		}
		
		if ( isset( $_POST['cbCustomImageSize'] ) ) {
			$size = ( 500 > (int) $_POST['tCustomImageSize'] ) ? 500 : (int) $_POST['tCustomImageSize'];
			$w->update_settings( $_GET['wid'], array( 'custom-image-size' => $size ) );
		} else {
			$w->delete_settings( $_GET['wid'], array( 'custom-image-size' ) );
		}
		
		$success = $w->update( $_GET['wid'], $fields, $fields_safety );

        $sm_add_ons = @unserialize( $w->get_setting( $_GET['wid'], 'social-media-add-ons' ) );

        if ( is_array( $sm_add_ons ) ) {
            $sm = new Social_Media();

            foreach ( $sm_add_ons as $smao ) {
                if ( !in_array( $smao, $_POST['sSocialMedia'] ) )
                    $sm->reset( $_GET['wid'], $smao );
            }
        }

		// Update Facebook settings
		$w->update_settings( $_GET['wid'], array( 
			'facebook-url' => $_POST['tFacebookURL']
			, 'limited-products' => ( isset( $_POST['cbLimitedProducts'] ) ) ? 1 : 0
            , 'advertising-url' => $_POST['tAdvertisingURL']
            , 'ga-username' => ( empty( $_POST['tGAUsername'] ) ) ? '' : security::encrypt( $_POST['tGAUsername'], ENCRYPTION_KEY, true )
            , 'ga-password' => ( empty( $_POST['tGAPassword'] ) ) ? '' : security::encrypt( $_POST['tGAPassword'], ENCRYPTION_KEY, true )
            , 'ashley-ftp-username' => ( empty( $_POST['tAshleyFTPUsername'] ) ) ? '' : security::encrypt( $_POST['tAshleyFTPUsername'], ENCRYPTION_KEY, true )
            , 'ashley-ftp-password' => ( empty( $_POST['tAshleyFTPPassword'] ) ) ? '' : security::encrypt( stripslashes( $_POST['tAshleyFTPPassword'] ), ENCRYPTION_KEY, true )
            , 'ashley-alternate-folder' => $_POST['cbAshleyAlternateFolder']
            , 'social-media-add-ons' => serialize( $_POST['sSocialMedia'] )
            , 'trumpia-api-key' => $_POST['tTrumpiaAPIKey']
		) );
	}
}

$web = $w->get_website( $_GET['wid'] );
$ftp = $w->get_ftp_data( $_GET['wid'] );
$website_industries = $w->get_industries( $_GET['wid'] );
$users = $u->get_users();

$settings = $w->get_settings( $_GET['wid'], array(
    'limited-products'
    , 'custom-image-size'
    , 'facebook-url'
    , 'advertising-url'
    , 'ga-username'
    , 'ga-password'
    , 'ashley-ftp-username'
    , 'ashley-ftp-password'
    , 'ashley-alternate-folder'
    , 'social-media-add-ons'
    , 'trumpia-api-key'
));

$web['custom_image_size'] = $settings['custom-image-size'];

// We must strip slashes, since $_POST automatically inserts them!
foreach ( $web as &$slot ) {
	$slot = stripslashes( $slot );
}

// Do some error checking
if ( '1' != $web['user_status'] )
    $errs .= "The owner's account has been deactivated. This account will not work until it has an active owner.";

css( 'form', 'accounts/edit' );
javascript( 'validator', 'jquery', 'accounts/edit' );

$selected = 'accounts';
$title = _('Edit Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
    <h1><?php echo $web['title']; ?></h1>
	<br clear="all" /><br />
	<?php $sidebar_emails = true; get_sidebar( 'accounts/', 'accounts' ); ?>
	<div id="subcontent">
		<?php 
		if ( !isset( $success ) || !$success ) {
			$success_class = ' class="hidden"';
			$main_form_class = '';
        } else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php
			if ( !empty( $errs ) )
				echo "<p class='red'>$errs</p>";

            if ( $user['role'] >= 7 ) {
            ?>
                <p align="right">
                    <?php if ( 10 == $user['role'] ) { ?>
					<a href="/accounts/dns/?wid=<?php echo $_GET['wid']; ?>" title="<?php echo _('Edit DNS'); ?>"><?php echo _('Edit DNS'); ?></a> |
					<?php } ?>
                    <a href="javascript:;" id="aDeleteProducts" rel="<?php echo $_GET['wid']; ?>" title="<?php echo _('Delete Categories and Products'); ?>"><?php echo _('Delete Categories and Products'); ?></a> |
                    <?php if ( 10 == $user['role'] ) { ?>
                        <a href="/accounts/delete/?wid=<?php echo $_GET['wid']; ?>" id="aCancelAccount" title="<?php echo _('Cancel'), ' ', $web['title']; ?>"><?php echo _('Cancel Account'); ?></a>
                    <?php } ?>
                </p>
            <?php } ?>
			<form action="/accounts/edit/?wid=<?php echo $_GET['wid']; ?>" method="post" name="fEditAccount">
			<?php 
			if ( '0' == $web['version'] )
				echo '<p>', _('Website has not been installed. Please verify domain and FTP data below and'), ' <a href="/accounts/install/?wid=', $web['website_id'], '" title="', _('Install Account'), '">', _('click here to install the account'), '</a>.</p>';
			
			if ( isset( $_GET['i'] ) )
				echo ( '1' == $_GET['i'] ) ? '<p>' . _('The website was successfully installed!') . '</p>' : '<p>' . _('An error occurred while trying to install the account. Please check the error log for details.') . '</p>';
			?>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" class="block-labels">
						<h2><?php echo _('Information'); ?></h2>
						<p>
							<label for="tTitle"><?php echo _('Title'); ?>:</label>
							<input type="text" name="tTitle" id="tTitle" value="<?php echo $web['title']; ?>" class="tb" />
						</p>
						<p>
							<label for="tDomain"><?php echo _('Domain'); ?>:</label>
							<input type="text" name="tDomain" id="tDomain" value="<?php echo $web['domain']; ?>" class="tb" />
						</p>
						<p>
							<label for="tSubDomain"><?php echo _('Sub Domain'); ?>:</label>
							<input type="text" name="tSubDomain" id="tSubDomain" value="<?php echo $web['subdomain']; ?>" class="tb" />
						</p>
						<p>
						<p>
							<label for="tTheme"><?php echo _('Theme'); ?>:</label>
							<input type="text" name="tTheme" id="tTheme" value="<?php echo $web['theme']; ?>" class="tb" />
						</p>
						<p>
							<label for="tPhone"><?php echo _('Phone'); ?>:</label>
							<input type="text" name="tPhone" id="tPhone" value="<?php echo $web['phone']; ?>" class="tb" />
						</p>
						<p>
							<label for="tProducts"><?php echo _('Products'); ?>:</label>
							<input type="text" name="tProducts" id="tProducts" value="<?php echo $web['products']; ?>" class="tb" />
						</p>
						<p>
							<label for="sUserID"><?php echo _('Owner'); ?>:</label>
							<select name="sUserID" id="sUserID">
								<option value="">-- <?php echo _('Select a User'); ?> --</option>
								<?php 
								$user_email = '';
								foreach ( $users as $u ) { 
									// We don't want any empty users
									if ( '' == $u['contact_name'] )
										continue;
									
									$selected = ( $web['user_id'] == $u['user_id'] ) ? ' selected="selected"' : '';
									$email = ( $u['email'] ) ? 'email="' . $u['email'] . '"' : '';
									if ( $web['user_id'] == $u['user_id'] ) $user_email = $u['email'];
								?>
								<option value="<?php echo $u['user_id']; ?>"<?php echo $selected . $email; ?>><?php echo $u['contact_name']; ?></option>
								<?php } ?>
							</select>
						</p>

						<p>
                        	<label for="tUserEmail"><?php echo _('Email'); ?>:</label>
                            <input type="text" class="tb read-only" name="tUserEmail" id="tUserEmail" readonly="readonly" value="<?php echo $user_email; ?>" />
                        <p>
							<label for="sOSUserID"><?php echo _('Online Specialist'); ?>:</label>
							<select name="sOSUserID" id="sOSUserID" class="tb">
								<option value="">-- <?php echo _('Select a User'); ?> --</option>
								<?php 
								foreach ( $users as $u ) { 
									// We don't want any empty users
									if ( '' == $u['contact_name'] || $u['role'] < 7 )
										continue;
									
									$selected = ( $web['os_user_id'] == $u['user_id'] ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $u['user_id']; ?>"<?php echo $selected; ?>><?php echo $u['contact_name']; ?></option>
								<?php } ?>
							</select>
						</p>
					</td>
					<td valign="top">
						<h2><?php echo _('Features'); ?></h2>
						<p><input type="checkbox" name="cbWebsite" id="cbWebsite" value="1" class="cb"<?php if ( $web['pages'] ) echo ' checked="checked"'; ?> /> <label for="cbWebsite"><?php echo _('Website'); ?></label></p>
						<p><input type="checkbox" name="cbProductCatalog" id="cbProductCatalog" value="1" class="cb"<?php if ( $web['product_catalog'] ) echo ' checked="checked"'; ?> /> <label for="cbProductCatalog"><?php echo _('Product Catalog'); ?></label></p>
						<p><input type="checkbox" name="cbLimitedProducts" id="cbLimitedProducts" value="1" class="cb"<?php if ( '1' == $settings['limited-products'] ) echo ' checked="checked"'; ?> /> <label for="cbLimitedProducts"><?php echo _('Limited Products'); ?></label></p>
						<p><input type="checkbox" name="cbBlog" id="cbBlog" value="1" class="cb"<?php if ( $web['blog'] ) echo ' checked="checked"'; ?> /> <label for="cbBlog"><?php echo _('Blog'); ?></label></p>
						<p><input type="checkbox" name="cbEmailMarketing" id="cbEmailMarketing" value="1" class="cb"<?php if ( $web['email_marketing'] ) echo ' checked="checked"'; ?> /> <label for="cbEmailMarketing"><?php echo _('Email Marketing'); ?></label></p>
                        <p><input type="checkbox" name="cbMobileMarketing" id="cbMobileMarketing" value="1" class="cb"<?php if ( $web['mobile_marketing'] ) echo ' checked="checked"'; ?> /> <label for="cbMobileMarketing"><?php echo _('Mobile Marketing'); ?></label></p>
						<p><input type="checkbox" name="cbShoppingCart" id="cbShoppingCart" value="1" class="cb"<?php if ( $web['shopping_cart'] ) echo ' checked="checked"'; ?> /> <label for="cbShoppingCart"><?php echo _('Shopping Cart'); ?></label></p>
						<p><input type="checkbox" name="cbSEO" id="cbSEO" value="1" class="cb"<?php if ( $web['seo'] ) echo 'checked="checked"'; ?> /> <label for="cbSEO"><?php echo _('SEO'); ?></label></p>
						<p><input type="checkbox" name="cbRoomPlanner" id="cbRoomPlanner" value="1" class="cb"<?php if ( $web['room_planner'] ) echo ' checked="checked"'; ?> /> <label for="cbRoomPlanner"><?php echo _('Room Planner'); ?></label></p>
						<p><input type="checkbox" name="cbCraigslist" id="cbCraigslist" value="1" class="cb"<?php if ( $web['craigslist'] ) echo ' checked="checked"'; ?> /> <label for="cbCraigslist"><?php echo _('Craigslist'); ?></label></p>
						<p>
                            <input type="checkbox" name="cbSocialMedia" id="cbSocialMedia" value="1" class="cb"<?php if ( $web['social_media'] ) echo ' checked="checked"'; ?> /> <label for="cbSocialMedia"><?php echo _('Social Media'); ?></label>
                            <div id="dSocialMedia"<?php if ( !$web['social_media'] ) echo ' class="hidden"'; ?>>
                                <select name="sSocialMedia[]" id="sSocialMedia" multiple="multiple" class="multiple">
                                    <?php 
                                    $social_media_add_ons = array(
                                        'email-sign-up' => _('Email Sign Up')
                                        , 'fan-offer' => _('Fan Offer')
                                        , 'sweepstakes' => _('Sweepstakes')
                                        , 'share-and-save' => _('Share and Save')
                                        , 'facebook-site' => _('Facebook Site')
                                        , 'contact-us' => _('Contact Us')
                                        , 'about-us' => _('About Us')
                                        , 'products' => _('Products')
                                        , 'current-ad' => _('Current Ad')
                                        , 'posting' => _('Posting')
                                    );

                                    $website_social_media_add_ons = @unserialize( $settings['social-media-add-ons'] );
                                    
                                    foreach ( $social_media_add_ons as $value => $name ) {
                                        $selected = ( is_array( $website_social_media_add_ons ) && in_array( $value, $website_social_media_add_ons ) ) ? ' selected="selected"' : '';
                                    ?>
                                        <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </p>
						<p><input type="checkbox" name="cbDomainRegistration" id="cbDomainRegistration" value="1" class="cb"<?php if ( $web['domain_registration'] ) echo ' checked="checked"'; ?> /> <label for="cbDomainRegistration"><?php echo _('Domain Registration'); ?></label></p>
						<p><input type="checkbox" name="cbAdditionalEmail" id="cbAdditionalEmail" value="1" class="cb"<?php if ( $web['additional_email_addresses'] ) echo ' checked="checked"'; ?> /> <label for="cbAdditionalEmail"><?php echo _('Additional Email Addresses'); ?></label></p>
					</td>
					<td valign="top">
						<h2><?php echo _('FTP'); ?></h2>
						<div class="block-labels">
							<p>
								<label for="tFTPHost"><?php echo _('Host'); ?>:</label>
								<input type="text" name="tFTPHost" id="tFTPHost" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_host'] ), ENCRYPTION_KEY ); ?>" class="tb" />
							</p>
							<p>
								<label for="tFTPUser"><?php echo _('User Name'); ?>:</label>
								<input type="text" name="tFTPUser" id="tFTPUser" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_username'] ), ENCRYPTION_KEY ); ?>" class="tb" autocomplete="off" />
							</p>
							<p>
								<label for="tFTPPassword"><?php echo _('Password'); ?>:</label>
								<input type="password" name="tFTPPassword" id="tFTPPassword" value="<?php echo security::decrypt( base64_decode( $ftp['ftp_password'] ), ENCRYPTION_KEY ); ?>" class="tb" autocomplete="off" />
							</p>
						</div>
						<br />
						
						<h2><?php echo _('Industries'); ?></h2>
						<?php 
						foreach ( $industries as $i ) { 
							$checked = ( in_array( $i['industry_id'], $website_industries ) ) ? ' checked="checked"' : '';
						?>
						<p><input type="checkbox" name="cbIndustry<?php echo $i['industry_id']; ?>" id="cbIndustry<?php echo $i['industry_id']; ?>" value="" class="cb"<?php echo $checked; ?>/> <label for="cbIndustry<?php echo $i['industry_id']; ?>"><?php echo ucwords( $i['name'] ); ?></label></p>
						<?php } ?>
					</td>
					<td valign="top" class="block-labels">
						<h2><?php echo _('Extras'); ?></h2>
						<p>
							<label for="tType"><?php echo _('Type'); ?>:</label>
							<input type="text" name="tType" id="tType" value="<?php echo $web['type']; ?>" class="tb" />
						</p>
						<p>
							<?php echo _('Logo'); ?>:
							<strong><?php echo $web['logo']; ?></strong>
						</p>
                        <p>
                            <label for="tGAUsername"><?php echo _('Google Analytics Username'); ?>:</label>
							<input type="text" name="tGAUsername" id="tGAUsername" value="<?php if ( !empty( $settings['ga-username'] ) ) echo security::decrypt( base64_decode( $settings['ga-username'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
						<p>
							<label for="tGAPassword"><?php echo _('Google Analytics Password'); ?>:</label>
							<input type="text" name="tGAPassword" id="tGAPassword" value="<?php if ( !empty( $settings['ga-password'] ) ) echo security::decrypt( base64_decode( $settings['ga-password'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
						<p>
							<label for="tGAProfileID"><?php echo _('Google Analytics Profile ID'); ?>:</label>
							<input type="text" name="tGAProfileID" id="tGAProfileID" value="<?php echo $web['ga_profile_id']; ?>" class="tb" />
						</p>
						<p>
							<label for="tGATrackingKey"><?php echo _('Google Analytics Tracking Key'); ?>:</label>
							<input type="text" name="tGATrackingKey" id="tGATrackingKey" value="<?php echo $web['ga_tracking_key']; ?>" class="tb" />
						</p>
						<p>
                            <label for="tWordPressUsername"><?php echo _('WordPress Username'); ?>:</label>
							<input type="text" name="tWordPressUsername" id="tWordPressUsername" value="<?php if ( !empty( $web['wordpress_username'] ) ) echo security::decrypt( base64_decode( $web['wordpress_username'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
						<p>
							<label for="tWordPressPassword"><?php echo _('WordPress Password'); ?>:</label>
							<input type="text" name="tWordPressPassword" id="tWordPressPassword" value="<?php if ( !empty( $web['wordpress_password'] ) ) echo security::decrypt( base64_decode( $web['wordpress_password'] ), ENCRYPTION_KEY ); ?>" class="tb" />
						</p>
                        <p>
                            <label for="tAshleyFTPUsername"><?php echo _('Ashley FTP Username'); ?>:</label>
                            <input type="text" name="tAshleyFTPUsername" id="tAshleyFTPUsername" value="<?php if ( !empty( $settings['ashley-ftp-username'] ) ) echo security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY ); ?>" class="tb" />
                        </p>
                        <p>
                            <label for="tAshleyFTPPassword"><?php echo _('Ashley FTP Password'); ?>:</label>
                            <input type="text" name="tAshleyFTPPassword" id="tAshleyFTPPassword" value="<?php if ( !empty( $settings['ashley-ftp-password'] ) ) echo htmlspecialchars( security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY ) ); ?>" class="tb" />
                        </p>
                        <p>
                            <input type="checkbox" class="cb" name="cbAshleyAlternateFolder" id="cbAshleyAlternateFolder" value="1"<?php if ( !empty( $settings['ashley-alternate-folder'] ) ) echo ' checked="checked"'; ?> />
                            <label for="cbAshleyAlternateFolder" class="inline"><?php echo _('Ashley - Alternate Folder'); ?>:</label>
                        </p>

                        <?php if ( !empty( $settings['ashley-ftp-password'] ) ) { ?>
                        <p><a href="/bots/ashley-feed/?wid=<?php echo $_GET['wid']; ?>" title="<?php echo _('Run Ashley Feed'); ?>"><?php echo _('Run Ashley Feed'); ?></a></p>
                        <br />
                        <?php } ?>
						<p>
							<label for="tFacebookURL"><?php echo _('Facebook Page Insights URL'); ?>:</label>
							<input type="text" name="tFacebookURL" id="tFacebookURL" value="<?php if ( !is_array( $settings['facebook-url'] ) ) echo $settings['facebook-url']; ?>" class="tb" />
						</p>
						<p>
							<label for="tAdvertisingURL"><?php echo _('Advertising URL'); ?>:</label>
							<input type="text" name="tAdvertisingURL" id="tAdvertisingURL" value="<?php if ( !is_array( $settings['advertising-url'] ) ) echo $settings['advertising-url']; ?>" class="tb" />
						</p>
						<p>
							<label for="tMCListID"><?php echo _('MailChimp List ID'); ?>:</label>
							<input type="text" name="tMCListID" id="tMCListID" value="<?php echo $web['mc_list_id']; ?>" class="tb" />
						</p>
                        <p>
							<label for="tTrumpiaAPIKey"><?php echo _('Trumpia API Key'); ?>:</label>
                            <?php if ( empty( $settings['trumpia-api-key'] ) ) { ?>
                                <a href="/accounts/create-mobile-account/?wid=<?php echo $_GET['wid']; ?>"><?php echo _('Create Trumpia Account'); ?></a>
                            <?php } else { ?>
							<input type="text" name="tTrumpiaAPIKey" id="tTrumpiaAPIKey" value="<?php if ( isset( $settings['trumpia-api-key'] ) ) echo $settings['trumpia-api-key']; ?>" class="tb" />
                            <?php } ?>
						</p>
						<p>
                        	<input type="checkbox" name="cbCustomImageSize" id="cbCustomImageSize" value="" class="cb"<?php if ( isset( $web['custom_image_size'] ) && $web['custom_image_size'] != 0 ) echo ' checked="checked"'; ?>/> 
                            <label for="cbLive" class="inline"><?php echo _('Max image size for custom products:'); ?></label>&nbsp;
                            <input type="text" name="tCustomImageSize" id="tCustomImageSize" style="width:50px !important;" value="<?php echo ( isset( $web['custom_image_size'] ) ) ? $web['custom_image_size'] : ''; ?>" class="tb" />
						</p>
                        <p><input type="checkbox" name="cbLive" id="cbLive" value="" class="cb"<?php if ( $web['live'] ) echo ' checked="checked"'; ?>/> <label for="cbLive" class="inline"><?php echo _('Live'); ?></label></p>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
            <br /><br />
            <h2><?php echo $web['company'], ' ', _('Plan'); ?></h2>
			<p><label for="tPlanName"><?php echo _('Plan Name'); ?></label></p>
            <p><input type="text" class="tb" name="tPlanName" id="tPlanName" tmpval="<?php echo _('Plan Name...'); ?>" value="<?php echo $web['plan_name']; ?>" /></p>
            <p><label for="tPlanDescription"><?php echo _('Plan Description'); ?></label></p>
            <p><textarea name="tPlanDescription" id="tPlanDescription" rows="5" cols="50" tmpval="<?php echo _('Plan Description...'); ?>"><?php echo $web['plan_description']; ?></textarea></p>
            <br /><br />
			<br />
			<input type="submit" id="bSubmit" name="bSubmit" value="<?php echo _('Save'); ?>" class="button" />
			<?php nonce::field( 'update-account' ); ?>
		</form>
		<?php
            add_footer( $v->js_validation() );
            nonce::field( 'delete-products', '_ajax_delete_products' );
        ?>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('Account has been successfully updated!'); ?></p>
			<p><?php echo _('Click here to <a href="/accounts/" title="View Accounts">view all accounts</a> or <a href="javascript:;" id="aContinueEditingAccount" title="Continue Editing Account">continue editing account</a>.'); ?></p>
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
