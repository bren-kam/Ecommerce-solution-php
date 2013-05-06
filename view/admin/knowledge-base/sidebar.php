<?php
/**
 * @var User $user
 * @var $template Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/knowledge-base/articles/" title="<?php echo _('Articles'); ?>" class="top first<?php $template->select('articles'); ?>"><?php echo _('Articles'); ?></a>
        <?php if ( $template->v('articles') ) { ?>
            <a href="/knowledge-base/articles/" title="<?php echo _('View'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="/knowledge-base/articles/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php } ?>

        <a href="<?php echo url::add_query_arg( 's', KnowledgeBaseCategory::SECTION_ADMIN, '/knowledge-base/pages/' ); ?>" title="<?php echo _('Pages'); ?>" class="top<?php $template->select('pages'); ?>"><?php echo _('Pages'); ?></a>
        <?php if ( $template->v('pages') ) { ?>
            <a href="<?php echo url::add_query_arg( 's', KnowledgeBaseCategory::SECTION_ADMIN, '/knowledge-base/pages/' ); ?>" title="<?php echo _('View'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <a href="<?php echo url::add_query_arg( 's', KnowledgeBaseCategory::SECTION_ADMIN, '/knowledge-base/pages/add-edit/' ); ?>" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php }


        if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ) {
        ?>
            <a href="<?php echo url::add_query_arg( 's', KnowledgeBaseCategory::SECTION_ADMIN, '/knowledge-base/categories/' ); ?>" title="<?php echo _('Categories'); ?>" class="top<?php $template->select('categories'); ?>"><?php echo _('Categories'); ?></a>
        <?php } ?>
    </div>
</div>