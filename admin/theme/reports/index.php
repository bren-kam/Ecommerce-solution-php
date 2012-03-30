<?php
/**
 * @page Reports
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

if ( $user['role'] < 7 )
	url::redirect( '/' );

$w = new Websites;
$website_count = $w->count_websites('');

unset( $_SESSION['reports'] );

css( 'form', 'reports/list' );
javascript( 'jquery', 'jquery.tmp-val', 'reports/list' );

$selected = 'reports';
$title = _('Reports') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Reports'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'reports/' ); ?>
	<div id="subcontent">
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<?php
				nonce::field( 'autocomplete', '_ajax_autocomplete' );
				nonce::field( 'search', '_ajax_search' );
				?>
				
				<h2><?php echo _('Search'); ?></h2>
				<div style="float:right"><?php echo _('Total:'); ?><span id="sTotal">0</span></div>
				<br clear="left" /><br />
				
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="280">
							<select id="sType">
								<option value="brand"><?php echo _('Brand'); ?></option>
								<option value="online_specialist"><?php echo _('Online Specialist'); ?></option>
                                <option value="marketing_specialist"><?php echo _('Marketing Specialist'); ?></option>
								<option value="company"><?php echo _('Company'); ?></option>
                                <option value="billing_state"><?php echo _('State'); ?></option>
							</select>
						</td>
						<td>
							<input type="text" id="tSearch" class="tb" tmpval="<?php echo _('Enter search here...'); ?>" style="width:100%" />
							<br />
							<div id="dCriteria"></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="checkbox" class="cb" id="cbProductCatalog" value="product-catalog" /> <label for="cbProductCatalog"><?php echo _('Product Catalog'); ?></label>
							&nbsp;
							<input type="checkbox" class="cb" id="cbBlog" value="blog" /> <label for="cbBlog"><?php echo _('Blog'); ?></label>
							&nbsp;
							<input type="checkbox" class="cb" id="cbEmailMarketing" value="email-marketing" /> <label for="cbEmailMarketing"><?php echo _('Email Marketing'); ?></label>
							&nbsp;
                            <input type="checkbox" class="cb" id="cbMobileMarketing" value="mobile-marketing" /> <label for="cbMobileMarketing"><?php echo _('Mobile Marketing'); ?></label>
                            &nbsp;
							<input type="checkbox" class="cb" id="cbShoppingCart" value="shopping-cart" /> <label for="cbShoppingCart"><?php echo _('Shopping Cart'); ?></label>
							&nbsp;
							<input type="checkbox" class="cb" id="cbSEO" value="seo" /> <label for="cbSEO"><?php echo _('SEO'); ?></label>
							&nbsp;
							<input type="checkbox" class="cb" id="cbRoomPlanner" value="room-planner" /> <label for="cbRoomPlanner"><?php echo _('Room Planner'); ?></label>
							&nbsp;
                            <input type="checkbox" class="cb" id="cbCraigslist" value="craigslist" /> <label for="cbCraigslist"><?php echo _('Craigslist'); ?></label>
                            &nbsp;
							<input type="checkbox" class="cb" id="cbDomainRegistration" value="domain-registration" /> <label for="cbDomainRegistration"><?php echo _('Domain Registration'); ?></label>
							&nbsp;
							<input type="checkbox" class="cb" id="cbAdditionalEmailAddresses" value="additional-email-addresses" /> <label for="cbAdditionalEmailAddresses"><?php echo _('Additional Email Addresses'); ?></label>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2" style="text-align:right"><a href="javascript:;" class="button" id="aSearch" title="<?php echo _('Search'); ?>"><?php echo _('Search'); ?></a></td></tr>
				</table>
			</div>
		</div>
		<br /><br />
		<div id="dTable">
			<div align="right"><a href="/reports/download-excel/" id="aDownloadExcel" class="button hidden" title="<?php echo _('Download Excel'); ?>"><?php echo _('Download Excel'); ?></a></div>
			<br clear="right" />
			<table id="table" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><?php echo _('Title'); ?></th>
						<th><?php echo _('Company'); ?></th>
						<th><?php echo _('Products'); ?></th>
						<th><?php echo _('Signed Up'); ?></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>	
	<br /><br />
</div>

<?php get_footer(); ?>