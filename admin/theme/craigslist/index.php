<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

unset( $_SESSION['craigslist'] );

css( 'data-tables/TableTools', 'data-tables/ui', 'jquery.ui', 'craigslist/list' );
javascript( 'jquery', 'jquery.ui', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard', 'data-tables/jquery.tableTools', 'jquery.tmp-val', 'craigslist/list' );

$selected = 'craigslist';
$title = _('Craigslist Templates') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Craigslist Templates'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/' ); ?>
	<div id="subcontent">
		<?php
		if( isset( $_GET['m'] ) )
		switch( $_GET['m'] ) {
			case '1':
				echo '<p class="message">', _('Your ad template has been successfully added.'), '</p>';
			break;
			
			case '2':
				echo '<p class="message">', _('Your ad template has been successfully updated.'), '</p>';
			break;
		}
		?>
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<?php 
				nonce::field( 'autocomplete', '_ajax_autocomplete' );
				nonce::field( 'change-state', '_ajax_change_state' );
				nonce::field( 'delete-craigslist', '_ajax_delete_craigslist' );
				nonce::field( 'search', '_ajax_search' );
				?>
				
				<h2><?php echo _('Narrow Your Search'); ?></h2>
				
				<form id="fSubmitSearch">
					
					<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%">
						<tr>
							<td width="50" class="col-1">
								<select id="sAutoComplete">
									<option value="title"><?php echo _('Title'); ?></option>
									<option value="content"><?php echo _('Content'); ?></option>
									<option value="category"><?php echo _('Category'); ?></option>
								</select>
							</td>
							<td width="245" valign="top"><input type="text" name="tAutoComplete" id="tAutoComplete" value="<?php echo _('Type a title...'); ?>" style="height:17px; padding: 1px 0 0 2px; width: 100%;" /></td>
							<td colspan="2">&nbsp;</td>
							<td id="tdSearch"><a href="javascript:;" id="aResetSearch" title="<?php echo _('Reset Search'); ?>" style="margin-right:14px"><?php echo _('Reset Search'); ?></a> <a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
						</tr>
					</table>
					<br clear="left" />
				</form>
				<img id="iNYSArrow" src="/images/craigslist/narrow-your-search.png" alt="" width="76" height="27" />
			</div>
		</div>
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" width="100%" id="tListCraigslist">
			<thead>
				<tr>
					<th width="20%"><?php echo _('Title'); ?></th>
					<th width="60%"><?php echo _('Content'); ?></th>
					<th width="10%"><?php echo _('Category' ); ?></th>
					<th width="10%"><?php echo _('Created'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>