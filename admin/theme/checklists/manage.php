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

// Instantiate class
$c = new Checklists;

// Get these variables so we can tell what's new
$sections = $c->get_sections();
$items = $c->get_items();

$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-checklist' ) ) {
    $remove_sections = $remove_items = array();

    /**** Handle Sections *****/
    if ( is_array( $sections ) )
    foreach ( $sections as $section ) {
        if ( !array_key_exists( $section['checklist_section_id'], $_POST['sections'] ) )
            $remove_sections[] = $section['checklist_section_id'];
    }

    // Remove the sections
    $c->remove_sections( $remove_sections );

    // Update the other sections
    $c->update_sections( $_POST['sections'] );

    /**** Handle Items *****/
    if ( is_array( $items ) )
    foreach ( $items as $checklist_section_id => $item_array ) {
        if ( is_array( $item_array[$checklist_section_id] ) )
        foreach ( $item_array as $item ) {
            if ( !array_key_exists( $item['checklist_section_id'], $_POST['items'] ) || !array_key_exists( $item['checklist_item_id'], $_POST['items'][$item['checklist_section_id']] ) )
                $remove_items[] = $item['checklist_item_id'];
        }
    }
    
    // Remove the items
    $c->remove_items( $remove_items );

    // Update the other sections
    $c->update_items( $_POST['items'] );

    // Refetch variables
    $sections = $c->get_sections();
    $items = $c->get_items();

    $success = true;
}

css( 'form', 'checklists/manage' );
javascript( 'jquery', 'checklists/manage' );

$selected = 'checklists';
$title = _('View Checklist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Manage Checklists'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'checklists/' ); ?> 
	
	<div id="subcontent">
        <?php if ( $success ) { ?>
            <p class="success" id="pSuccess"><?php echo _('The Master Checklist has been successfully updated!'); ?></p>
        <?php } ?>
		<br />
        <form action="" method="post" name="fChecklist">
            <div id="checklist-sections">
                <?php
                    if ( is_array( $sections ) )
                    foreach ( $sections as $section ) {
                    ?>
                    <div id="dSection<?php echo $section['checklist_section_id']; ?>" class="section">
                        <input type="text" name="sections[<?php echo $section['checklist_section_id']; ?>]" class="section-title" value="<?php echo $section['name']; ?>" />
                        <a href="javascript:;" class="remove-section hidden" title="<?php echo _('Remove Section'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Section'); ?>" /></a>
                        <br />
                        <div class="section-items" id="dSectionItems<?php echo $section['checklist_section_id']; ?>">
                            <?php
                            if ( is_array( $items[$section['checklist_section_id']] ) )
                            foreach ( $items[$section['checklist_section_id']] as $item ) { ?>
                                <div class="item">
                                    <input type="text" name="items[<?php echo $section['checklist_section_id']; ?>][<?php echo $item['checklist_item_id']; ?>][name]" class="tb item-name" value="<?php echo $item['name']; ?>" />
                                    <input type="text" name="items[<?php echo $section['checklist_section_id']; ?>][<?php echo $item['checklist_item_id']; ?>][assigned_to]" class="tb item-assigned-to" value="<?php echo $item['assigned_to']; ?>" />
                                    <a href="javascript:;" class="remove-item hidden" title="<?php echo _('Remove Item'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove Item'); ?>" /></a>
                                </div>
                            <?php } ?>
                            <a href="javascript:;" class="add-section-item" id="aAddSectionItem<?php echo $section['checklist_section_id']; ?>" title="<?php echo _('Add Item'); ?>"><?php echo _('Add Item'); ?></a>
                            <br /><br />
                        </div>
                    </div>
                <?php } ?>
                <a href="javascript:;" id="aAddSection" title="<?php echo _('Add Section'); ?>"><?php echo _('Add Section'); ?></a>
            </div>
            <br /><br />
            <input type="submit" class="button" value="<?php echo _('Save Master Checklist'); ?>" />
            <?php nonce::field('update-checklist'); ?>
        </form>
        <?php
            nonce::field( 'create-section', '_ajax_create_section' );
            nonce::field( 'create-item', '_ajax_create_item' );
        ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>