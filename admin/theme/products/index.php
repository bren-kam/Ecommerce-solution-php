<?php
/**
 * @page Products
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$c = new Categories();

// Unset the session
unset( $_SESSION['products'] );

// Set variables
$users = $u->get_product_users();
$categories = $c->get_list();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'jquery.ui', 'products/list' );
javascript( 'jquery', 'jquery.ui', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'jquery.tmp-val', 'products/list' );

$selected = 'products';
$title = _('Products') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Products'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	<div id="subcontent">
		<?php
		if ( isset( $_GET['m'] ) )
		switch ( $_GET['m'] ) {
			case '1':
				echo '<p class="message">', _('Your product has been successfully added.'), '</p>';
			break;
			
			case '2':
				echo '<p class="message">', _('Your product has been successfully updated.'), '</p>';
			break;
		}
		?>
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<?php 
				nonce::field( 'autocomplete', '_ajax_autocomplete' );
				nonce::field( 'search', '_ajax_search' );
				nonce::field( 'set-session', '_ajax_set_session' );
				nonce::field( 'delete-product', '_ajax_delete_product' );
				?>
				<h2><?php echo _('Narrow Your Search'); ?></h2>
				<form id="fSubmitSearch">
					<div style="float:right">
						<label for="sVisibility"><?php echo _('View:'); ?></label>
						<select id="sVisibility">
							<option value="all"><?php echo _('All Products'); ?></option>
							<option value="public"><?php echo _('Public Products'); ?></option>
							<option value="private"><?php echo _('Private Products'); ?></option>
						</select>
					</div>
					<br clear="left" />
					
					<table cellpadding="0" cellspacing="0" width="100%" id="tNarrowSearch">
						<tr>
							<td width="120">
								<select id="sProductStatus">
									<option value="all">-- <?php echo _('Select'); ?> --</option>
									<option value="created"><?php echo _('Created By'); ?></option>
									<option value="modified"><?php echo _('Modified By'); ?></option>
								</select>
							</td>
							<td>
								<select id="sUsers">
									<option value="all"><?php echo _('All Users'); ?></option>
									<?php
									if ( is_array( $users ) )
									foreach ( $users as $u ) {
									?>
									<option value="<?php echo $u['user_id']; ?>"><?php echo $u['contact_name']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
                        <tr>
							<td><label for="sCategoryID"><?php echo _('Category'); ?>:</label></td>
							<td>
								<select id="sCategoryID">
									<option value="0"><?php echo _('-- Select a Category --'); ?></option>
									<?php echo $categories; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<select id="sAutoComplete">
									<option value="sku"><?php echo _('SKU'); ?></option>
									<option value="products"><?php echo _('Product Name'); ?></option>
									<option value="brands"><?php echo _('Brands'); ?></option>
								</select>
							</td>
							<td valign="top"><input type="text" name="tAutoComplete" id="tAutoComplete" tmpval="<?php echo _('Enter Search...'); ?>" style="height:17px; padding: 1px 0 0 2px; width: 100%;" /></td>
						</tr>
						<tr><td colspan="2" style="padding-top:7px; text-align:right"><a href="javascript:;" id="aResetSearch" title="<?php echo _('Reset Search'); ?>" style="margin-right:14px"><?php echo _('Reset Search'); ?></a> <a href="javascript:;" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td></tr>
					</table>
					<br clear="left" />
				</form>
			</div>
		</div>
		<br clear="left" /><br />
		<br />
		<table cellpadding="0" cellspacing="0" id="tListProducts" width="100%">
			<thead>
				<tr>
					<th width="40%"><?php echo _('Name'); ?></th>
					<th><?php echo _('Brand'); ?></th>
					<th width="10%"><?php echo _('SKU'); ?></th>
					<th width="8%"><?php echo _('Status'); ?></th>
					<th width="12%"><?php echo _('Published'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<br clear="left" /><br />
	</div>
</div>

<?php get_footer(); ?>