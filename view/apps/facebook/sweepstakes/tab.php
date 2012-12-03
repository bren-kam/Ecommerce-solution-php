<?php
/**
 * @page Sweepstakes
 * @package Grey Suit Retail
 *
 * @var array $signed_request
 * @var Sweepstakes $sweepstakes
 * @var string $form
 * @var bool $success
 * @var int $app_id
 * @var string $url
 */
?>

<div id="content">
	<?php
    if ( $success )
        echo '<p>Your have been successfully added to our email list!</p>';
    
    echo ( $sweepstakes->valid ) ? $sweepstakes->content : '<p>No active Sweepstakes...</p>';
    
    if ( $signed_request['page']['liked'] && !$success && $sweepstakes->valid )
        echo $form;
    ?>
    <p style="float:left; margin-top: 10px"><a href="#" onclick="window.print();" title="Print">Print</a></p>
    <?php
    if ( $signed_request['page']['liked'] && !empty( $sweepstakes->share_text ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=' . $app_id . '&';
		$link .= 'link=' . urlencode( $url ) . '&';
		$link .= 'picture=' . $sweepstakes->share_image_url . '&';
		$link .= 'name=' . urlencode( $sweepstakes->share_title ) . '&';
		$link .= 'description=' . urlencode( $sweepstakes->share_text ) . '&';
		$link .= 'message=' . urlencode( 'Checkout these Sweepstakes!' ) . '&';
		$link .= 'redirect_uri=' . urlencode( $url );
	    ?>
	    <p style="float:right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="Share"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
    	<?php
	}
    ?>
</div>