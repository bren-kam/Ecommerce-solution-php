<?php
/**
 * @page Hide Categories
 * @package Grey Suit Retail
 *
 * @since 1.0.3
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Add validation
$c = new Categories( false );
$wc = new Website_Categories();

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) ) {
    $p = new Products();

    if ( nonce::verify( $_POST['_nonce'], 'hide-categories' ) ) {
        // Server side validation
        $success = $wc->block_categories( $_POST['sCategories'] );
    } elseif( nonce::verify( $_POST['_nonce'], 'unblock-categories' ) && is_array( $_POST['unblock-categories']) ) {
        $success = $wc->unblock_categories( $_POST['unblock-categories'] );
    }

    $p->reorganize_categories();
}

$hidden_categories = $wc->get_blocked_categories();
$categories = $wc->generate_list( array_diff( $wc->get_ids(), $hidden_categories ) );

$title = _('Hide Categories') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Hide Categories'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
			<p class="success"><?php echo _('Hidden Categories have been successfully updated!'); ?></p>
		<?php
		}
		?>
		<form action="/products/hide-categories/" method="post" name="fHideCategories">
            <select name="sCategories[]" id="sCategories[]" multiple="multiple" style="height: 200px;">
                <?php echo $categories; ?>
            </select>
            <br /><br />
			<p><input type="submit" class="button" value="<?php echo _('Hide Categories'); ?>" /></p>
			<?php nonce::field('hide-categories'); ?>
		</form>
        <br /><br />
        <?php if ( !empty( $hidden_categories ) ) { ?>
            <h2><?php echo _('Hidden Categories'); ?></h2>
                <br />
            <form action="/products/hide-categories/" method="post" name="fUnblockCategories">
                <table>
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><strong><?php echo _('Name'); ?></strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( $hidden_categories as $hidden_category_id ) {
                            $parent_categories = $c->get_parent_categories( $hidden_category_id );
                            $category = $c->get( $hidden_category_id );
                            $name = $category['name'];

                            foreach ( $parent_categories as $pc ) {
                                $name = $pc['name'] . ' &gt; ' . $name;
                            }
                        ?>
                        <tr>
                            <td><input type="checkbox" class="cb" name="unblock-categories[]" value="<?php echo $hidden_category_id; ?>" /></td>
                            <td><?php echo $name; ?></td>
                        </tr>
                        <?php } ?>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" class="button" value="<?php echo _('Unblock Categories'); ?>" /></td>
                        </tr>
                    </tbody>
                </table>
                <?php nonce::field('unblock-categories'); ?>
            </form>
        <?php } ?>
    </div>
	<br /><br />
</div>

<?php get_footer(); ?>