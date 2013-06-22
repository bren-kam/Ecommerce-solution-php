<?php
/**
 * @package Grey Suit Retail
 * @page List Articles
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $link
 * @var string $kb_section
 */

echo $template->start( ucwords( $_GET['s'] ) . ' ' . _('Articles') . ' ' . $link, '../sidebar' );
?>

<table ajax="<?php echo url::add_query_arg( 'section', $_GET['s'], '/knowledge-base/articles/list-all/' ); ?>" perPage="30,50,100">
    <thead>
        <tr>
            <th width="20%"><?php echo _('Title'); ?></th>
            <th width="20%" sort="1"><?php echo _('Category'); ?></th>
            <th width="20%"><?php echo _('Page'); ?></th>
            <th width="9%"><?php echo _('Helpful'); ?></th>
            <th width="9%"><?php echo _('Unhelpful'); ?></th>
            <th width="8.5%"><?php echo _('Ratings'); ?></th>
            <th width="8.5%"><?php echo _('Views'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>