<?php
/**
 * @package Grey Suit Retail
 * @page Select Account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Select Account'), false );

$return_url = $_SERVER['REQUEST_URI'];

if ( !empty( $_SERVER['QUERY_STRING'] ) )
    $return_url .= '?' . $_SERVER['QUERY_STRING']
?>
<ul>
    <?php
    /**
     * @var Account $account
     */
    foreach ( $user->accounts as $account ) {
    ?>
        <li><a href="/home/change-account/?aid=<?php echo $account->id; ?>&amp;r=<?php echo urlencode( $_SERVER['REQUEST_URI'] ); ?>" title="<?php echo _('Change Account'); ?>"><strong><?php echo $account->title; ?></strong> - <?php echo $account->domain; ?></a></li>
    <?php } ?>
</ul>
<?php echo $template->end(); ?>