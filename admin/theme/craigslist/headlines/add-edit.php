<?php
/**
 * @page Craigslist Headlines Add/Edit
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$craigslist = new Craigslist;
$ca = new Categories();
$v = new Validator;

$craigslist_headline_id = ( isset( $_GET['chid'] ) ) ? $_GET['chid'] : false;

// Add Validation
$v->form_name = 'fAddEditHeadline';
$v->add_validation( 'tHeadline', 'req', _('The "Headline" field is required"') );
$v->add_validation( 'sCategoryID', 'req', _('The "Category" field is required"') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-headline' ) ) {
	// Server side validation
	$errs = $v->validate();

	if ( empty( $errs ) ) {
		if ( $craigslist_headline_id ) {
            $success = $craigslist->update_headline( $craigslist_headline_id, $_POST['sCategoryID'], $_POST['tHeadline'] );
        } else {
            $success = $craigslist->create_headline( $_POST['sCategoryID'], $_POST['tHeadline'] );
        }
	}
}

// Get everything
if ( $craigslist_headline_id || $success ) {
	$headline = ( !$craigslist_headline_id && $success ) ? $craigslist->get_headline( $success ) : $craigslist->get_headline( $craigslist_headline_id );
} else {
	$headline = array(
		'craigslist_headline_id' => ''
		, 'category_id' => ''
		, 'headline' => ''
	);
}

javascript( 'validator' );

$selected = 'craigslist';
$sub_title = ( $craigslist_headline_id ) ? _('Edit Headline') : _('Add Headline');
$title = "$sub_title | " . _('Markets') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'headlines' ); ?>
    
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $craigslist_headline_id ) ? _('Your headline has been updated successfully!') : _('Your headline has been added successfully!'); ?></p>
			<br />
            <p><?php echo _('Click here to'), ' <a href="/craigslist/headlines/" title="', _('Headlines'), '">', _('view your headlines'), '</a> or <a href="/craigslist/headlines/add-edit/" title="', _('Add Headline'), '">', _('add another headline'), '</a>.'; ?></p>
			<br />
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$craigslist_headline_id )
			$craigslist_headline_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p><br />";
		?>
		<form name="fAddEditHeadline" id="fAddEditHeadline" action="/craigslist/headlines/add-edit/<?php if ( $craigslist_headline_id ) echo "?chid=$craigslist_headline_id"; ?>" method="post">
		    <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label for="tHeadline"><?php echo _('Headline'); ?>:</label></td>
                    <td>
                        <input type="text" class="tb" name="tHeadline" id="tHeadline" value="<?php echo ( !$success && isset( $_POST['tHeadline'] ) ) ? $_POST['tHeadline'] : $headline['headline']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo _('Syntax Tags'); ?>:</label></td>
                    <td>
                        <p style="line-height:18px;">
                            [<?php echo _('Product Name'); ?>]<br />
                            [<?php echo _('Store Name'); ?>]<br />
                            [<?php echo _('Store Logo'); ?>]<br />
                            [<?php echo _('Category'); ?>]<br />
                            [<?php echo _('Brand'); ?>]<br />
                            [<?php echo _('Product Description'); ?>]<br />
                            [<?php echo _('SKU'); ?>]<br />
                            [<?php echo _('Photo'); ?>]
                        </p>
                    </td>
                </tr>
                <tr>
                    <td><label for="sCategoryID"><?php echo _('Category'); ?>:</label></td>
                    <td>
                        <select name="sCategoryID" id="sCategoryID">
    						<option value="">-- <?php echo _('Select a Category'); ?> --</option>
                            <?php
                            $category_id = ( !$success && isset( $_POST['sCategoryID'] ) ) ? $_POST['sCategoryID'] : $headline['category_id'];
                            $categories = $ca->get_list( $category_id );
                            echo $categories;
                            ?>
					    </select>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo ( $craigslist_headline_id ) ? _('Update Headline') : _('Add Headline'); ?>" /></td>
                </tr>
		    </table>
            <?php nonce::field('add-edit-headline'); ?>
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>