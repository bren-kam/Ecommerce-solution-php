<?php
/**
 * @page Tools - Bad Excel
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$title = _('Tools') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Tools'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'tools/' ); ?>
	<div id="subcontent">
        <p><?php echo _('Please use the tools below to speed up website building. Go go, team!'); ?></p>
        <ul>
            <li><a href="/tools/bad-excel/" title="<?php echo _('Bad Excel'); ?>"><?php echo _('Bad Excel'); ?></a></li>
            <li><a href="/tools/extract-zip-codes/" title="<?php echo _('Extract Zip Codes'); ?>"><?php echo _('Extract Zip Codes'); ?></a></li>
        </ul>
        <br /><br />
        <br /><br />
        <br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>