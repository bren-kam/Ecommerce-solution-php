<?php
/**
 * @page Fan Offer
 * @package Grey Suit Retail
 *
 * @var array $signed_request
 * @var FanOffer $fan_offer
 * @var string $form
 * @var bool $success
 */
?>

<div id="content">
	<?php
    if ( $success )
        echo '<p>Your have been successfully added to our email list!</p>';

    echo $fan_offer->content;

    if ( $signed_request['page']['liked'] && !empty( $fan_offer->share_text ) ) {
		$link = 'http://www.facebook.com/dialog/feed?';
		$link .= 'app_id=165348580198324&';
		$link .= 'link=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_165348580198324&';
		$link .= 'picture=' . $fan_offer->share_image_url . '&';
		$link .= 'name=' . urlencode( $fan_offer->share_title ) . '&';
		$link .= 'description=' . urlencode( $fan_offer->share_text ) . '&';
		$link .= 'message=' . urlencode( 'Checkout this Fan Offer!' ) . '&';
		$link .= 'redirect_uri=http://www.facebook.com/pages/Test/' . $signed_request['page']['id'] . '?sk=app_165348580198324';
	    ?>
	    <p style="float:right"><a href="#" onclick="top.location.href='<?php echo $link; ?>';" title="Share"><img src="http://apps.imagineretailer.com/images/buttons/share.png" width="72" height="32" alt="<?php echo _('Share'); ?>" /></a>
    	<?php
	}
    ?>
    <p style="float:left; margin-top: 10px"><a href="#" onclick="window.print();" title="Print">Print</a></p>

    <?php
    if ( $signed_request['page']['liked'] && !$success )
        echo $form;
    ?>
</div>