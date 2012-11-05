<?php
/**
 * @page Dashboard
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$nonce = nonce::create('change-website');

$title = _('Select Website') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Select Website'); ?></h1>
	<br clear="all" />
	<br /><br />
    <ul>
    <?php
    if ( is_array( $user['websites'] ) )
    foreach ( $user['websites'] as $website_id => $w ) {
    ?>
        <li><a href="/change-website/?wid=<?php echo $website_id; ?>&amp;_nonce=<?php echo $nonce; ?>" title="<?php echo _('Change Website'); ?>"><strong><?php echo $w['title']; ?></strong> - <?php echo $w['domain']; ?></a></li>
    <?php } ?>
    </ul>
	<br clear="all" />
	
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>