<?php
/**
 * @page Craigslist Markets Add/Edit
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$craigslist = new Craigslist;
$v = new Validator;

$craigslist_market_id = ( isset( $_GET['cmid'] ) ) ? $_GET['cmid'] : false;

// Add Validation
$v->form_name = 'fAddEditMarket';
$v->add_validation( 'sState', 'req', _('The "State" field is required"') );
$v->add_validation( 'tCity', 'req', _('The "City" field is required"') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-market' ) ) {
	// Server side validation
	$errs = $v->validate();

	if ( empty( $errs ) ) {
		if ( $craigslist_market_id ) {
            $success = $craigslist->update_market( $craigslist_market_id, $_POST['sState'], $_POST['tCity'], $_POST['tArea'] );
        } else {
            $success = $craigslist->create_market( $_POST['sState'], $_POST['tCity'], $_POST['tArea'] );
        }
	}
}

// Get everything
if ( $craigslist_market_id || $success ) {
	$market = ( !$craigslist_market_id && $success ) ? $craigslist->get_market( $success ) : $craigslist->get_market( $craigslist_market_id );
} else {
	$market = array(
		'craigslist_market_id' => ''
		, 'state' => ''
		, 'city' => ''
		, 'area' => ''
	);
}

javascript( 'validator' );

$selected = 'craigslist';
$sub_title = ( $craigslist_market_id ) ? _('Edit Market') : _('Add Market');
$title = "$sub_title | " . _('Markets') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'markets' ); ?>
    
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $craigslist_market_id ) ? _('Your market has been updated successfully!') : _('Your market has been added successfully!'); ?></p>
			<br />
            <p><?php echo _('Click here to'), ' <a href="/craigslist/markets/" title="', _('Markets'), '">', _('view your markets'), '</a> or <a href="/craigslist/markets/add-edit/" title="', _('Add Market'), '">', _('add another market'), '</a>.'; ?></p>
			<br />
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$craigslist_market_id )
			$craigslist_market_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p><br />";
		?>
		<form name="fAddEditMarket" id="fAddEditMarket" action="/craigslist/markets/add-edit/<?php if ( $craigslist_market_id ) echo "?cmid=$craigslist_market_id"; ?>" method="post">
		    <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label for="sState"><?php echo _('State'); ?>:</label></td>
                    <td>
                        <select name="sState" id="sState">
                            <?php
                            $state = ( !$success && isset( $_POST['sState'] ) ) ? $_POST['sState'] : $market['state'];
                            data::states( true, $state );
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="tCity"><?php echo _('City'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tCity" id="tCity" value="<?php echo ( !$success && isset( $_POST['tCity'] ) ) ? $_POST['tCity'] : $market['city']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="tArea"><?php echo _('Area'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tArea" id="tArea" value="<?php echo ( !$success && isset( $_POST['tArea'] ) ) ? $_POST['tArea'] : $market['area']; ?>" /></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo ( $craigslist_market_id ) ? _('Update Market') : _('Add Market'); ?>" /></td>
                </tr>
		    </table>
            <?php nonce::field('add-edit-market'); ?>
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>