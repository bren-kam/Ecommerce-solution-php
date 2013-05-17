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
 */

echo $template->start( ucwords( $_GET['s'] ) . ' ' . _('Articles') . ' ' . $link, '../sidebar' );
?>

<table ajax="<?php echo url::add_query_arg( 'section', KnowledgeBaseCategory::SECTION_ADMIN, '/knowledge-base/articles/list-all/' ); ?>" perPage="30,50,100">
    <thead>
        <tr>
            <th width="40%"><?php echo _('Title'); ?></th>
            <th width="30%" sort="1"><?php echo _('Category'); ?></th>
            <th width="30%"><?php echo _('Page'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>