<?php
/**
 * @package Grey Suit Retail
 * @page Posting - Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 */

echo $template->start( _('Posts') . ' - ' . $page->name, '../sidebar' );
?>

<table perPage="25,50,100" ajax="/social-media/facebook/posting/list-posts/">
    <thead>
        <tr>
            <th width="50%"><?php echo _('Summary'); ?></th>
            <th width="20%"><?php echo _('Status'); ?></th>
            <th width="30%" sort="1 desc"><?php echo _('Date Posted'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>