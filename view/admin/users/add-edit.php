<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a user
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var int $user_id
 */

echo $template->start( ( isset( $_GET['uid'] ) ? _('Edit User') : _('Add User') ) );
echo $form;

if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) {
?>
<br><br>
<hr>
<br>
<h2><?php echo _('Knowledge Base Articles'); ?></h2>
<br>
<table ajax="<?php echo url::add_query_arg( 'uid', $user_id, '/users/list-articles/' ); ?>" perPage="30,50,100">
    <thead>
        <tr>
            <th width="25%" sort="1"><?php echo _('Article'); ?></th>
            <th width="15%"><?php echo _('Section'); ?></th>
            <th width="25%"><?php echo _('Category'); ?></th>
            <th width="15%"><?php echo _('Page'); ?></th>
            <th width="10%"><?php echo _('Views'); ?></th>
            <th width="10%"><?php echo _('Helpful'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<?php
}

echo $template->end();
?>