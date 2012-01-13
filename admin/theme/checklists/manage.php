<?php
/**
 * @page Manage Checklists
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure we have a checklist selected
if ( $user['role'] < 8 )
	url::redirect( '/checklists/' );

css( 'form', 'checklists/manage' );
javascript( 'jquery', 'checklists/manage' );

$c = new Checklists;
$sections = $c->get_sections();
$items = $c->get_items();

$selected = 'checklists';
$title = _('View Checklist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Manage Checklists'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'checklists/' ); ?> 
	
	<div id="subcontent">

		<br />
        <form action="" method="POST" name="fChecklist">
            <div id="checklist-sections">
                <?php
                    if ( is_array( $sections ) )
                    foreach ( $sections as $section ) {
                    ?>
                    <div id="dSection<?php echo $section['checklist_section_id']; ?>" class="section">

                        <input type="text" name="sections[<?php echo $section['checklist_section_id']; ?>]" class="section-title" value="<?php echo $section['name']; ?>" />
                        <br />
                        <div class="section-items" id="dSectionItems<?php echo $section['checklist_section_id']; ?>">
                            <?php
                            if ( is_array( $items[$section['checklist_section_id']] ) )
                            foreach ( $items[$section['checklist_section_id']] as $item ) { ?>
                                <div class="item">
                                    <input type="text" name="items[<?php echo $section['checklist_section_id']; ?>][<?php echo $item['checklist_item_id']; ?>][description]" class="tb item-description" value="<?php echo $item['name']; ?>" />
                                    <input type="text" name="items[<?php echo $section['checklist_section_id']; ?>][<?php echo $item['checklist_item_id']; ?>][assigned_to]" class="tb item-assigned-to" value="<?php echo $item['assigned_to']; ?>" />
                                </div>
                            <?php } ?>
                            <a href="javascript:;" class="add-section-item" id="aAddSectionItem<?php echo $section['checklist_section_id']; ?>" title="<?php echo _('Add Item'); ?>"><?php echo _('Add Item'); ?></a>
                            <br /><br />
                        </div>
                    </div>
                <?php } ?>
                <a href="javascript:;" id="aAddSection" title="<?php echo _('Add Section'); ?>"><?php echo _('Add Section'); ?></a>
            </div>
        </form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>