<?php
/**
 * @page Share and Save
 * @package Grey Suit Retail
 *
 * @var array $signed_request
 * @var ShareAndSave $share_and_save
 * @var string $form
 * @var bool $success
 * @var int $app_id
 */
?>

<div id="content">
	<?php
    if ( $success )
        echo '<p>Your have been successfully added to our email list!</p>';

    $remaining = $share_and_save->minimum - $share_and_save->total;

	// How many are left
	if ( !empty( $share_and_save->content ) && !empty( $share_and_save->minimum ) )
		echo ( $remaining > 0 ) ? '<h2 class="share-save">Only ' . $remaining . ' more until this deal is active!</h2>' : '<h2 class="share-save">This deal is active!</h2>';
        
	if( $share_and_save->total > $share_and_save->maximum )
		echo '<p class="error">The maximum number of deals has been attained. Stay tuned for another offer...</p>';


    echo $share_and_save->content;

    if ( $signed_request['page']['liked'] && !$success )
        echo $form;
    ?>
    <p style="float:left; margin-top: 10px"><a href="#" onclick="window.print();" title="Print">Print</a></p>
    <?php
    if ( $signed_request['page']['liked'] && !empty( $share_and_save->share_text ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=165348580198324&';
		$link .= 'link=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_' . $app_id . '&';
		$link .= 'picture=' . $share_and_save->share_image_url . '&';
		$link .= 'name=' . urlencode( $share_and_save->share_title ) . '&';
		$link .= 'description=' . urlencode( $share_and_save->share_text ) . '&';
		$link .= 'message=' . urlencode( 'Checkout this Fan Offer!' ) . '&';
		$link .= 'redirect_uri=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_' . $app_id;
	    ?>
	    <p style="float:right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="Share"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
    	<?php
	}
    ?>
</div>