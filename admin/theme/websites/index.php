<?php
/**
 * @page Websites
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

unset( $_SESSION['websites'] );

css( 'data-tables/TableTools', 'data-tables/ui', 'websites/list' );
javascript( 'jquery', 'jquery.ui', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'jquery.tmp-val', 'websites/list' );

$selected = 'websites';
$title = _('Websites') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Websites'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'websites/' ); ?>
	<div id="subcontent">
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<?php 
				nonce::field( 'autocomplete', '_ajax_autocomplete' );
				nonce::field( 'change-state', '_ajax_change_state' );
				nonce::field( 'search', '_ajax_search' );
				?>
				
				<h2><?php echo _('Narrow Your Search'); ?></h2>
				
				<form id="fSubmitSearch">
					<div style="float:right">
					<label for="sState"><?php echo _('View:'); ?></label>
					<select id="sState">
						<option value="all"><?php echo _('All Websites'); ?></option>
						<option value="live"><?php echo _('Live'); ?></option>
						<option value="staging"><?php echo _('Staging'); ?></option>
					</select>
					</div>
					<br clear="left" />
					
					<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%">
						<tr>
							<td width="50" class="col-1">
								<select id="sAutoComplete">
									<option value="title"><?php echo _('Website Name'); ?></option>
									<option value="domain"><?php echo _('Website Domain'); ?></option>
									<option value="store_name"><?php echo _('Store Name'); ?></option>
								</select>
							</td>
							<td width="245" valign="top"><input type="text" name="tAutoComplete" id="tAutoComplete" value="<?php echo _('Enter Website Name...'); ?>" style="height:17px; padding: 1px 0 0 2px; width: 100%;" /></td>
							<td colspan="2">&nbsp;</td>
							<td id="tdSearch"><a href="javascript:;" id="aResetSearch" title="<?php echo _('Reset Search'); ?>" style="margin-right:14px"><?php echo _('Reset Search'); ?></a> <a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
						</tr>
					</table>
					<br clear="left" />
				</form>
				<img id="iNYSArrow" src="/images/websites/narrow-your-search.png" alt="" width="76" height="27" />
			</div>
		</div>
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" width="100%" id="tListWebsites">
			<thead>
				<tr>
					<th width="4%">&nbsp;</th>
					<th width="35%"><?php echo _('Website'); ?></th>
					<th width="20%"><?php echo _('Store Name'); ?></th>
					<th width="18%"><?php echo _('User Name' ); ?></th>
					<th width="13%"><?php echo _('Products'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>