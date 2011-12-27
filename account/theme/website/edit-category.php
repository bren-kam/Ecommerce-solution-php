<?php
/**
 * @page Edit Website Category
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$category_id = (int) $_GET['cid'];

// Send to website listing page
if ( empty( $category_id ) )
	url::redirect('/website/categories/');

// Instantiate classes
$c = new Categories;

// Get cateogry
$category = $c->get_website_category( $category_id );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'edit-category' ) ) {
    // We don't want to submit that as it will override the default category
    if ( _('Category Title...') == $_POST['tTitle'] )
        $_POST['tTitle'] = '';

    // Update the category
    $success = $c->update_website_category( $category_id, stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['taContent'] ), $_POST['rPosition'] );

    // Get new category
    $category = $c->get_website_category( $category_id );
}

$selected = "pages";
$title = _('Edit Category') . ' | ' . TITLE;
css('website/page');
javascript('mammoth');
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit category'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your category has been updated.'); ?> <a href="<?php echo $c->category_url( $category_id ); ?>" title="<?php echo _('View Category'); ?>" target="_blank"><?php echo _('View the category.'); ?></a></p>
			<p><a href="/website/categories/" title="<?php echo _('Edit Other Categories'); ?>"><?php echo _('Click here to edit other categories.'); ?></a></p>
		</div>
		<?php
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
		<form name="fEditPage" action="/website/edit-category/?cid=<?php echo $category_id; ?>" method="post">
            <div id="dTitleContainer">
                <input name="tTitle" id="tTitle" class="tb" value="<?php echo $category['title']; ?>" tmpval="<?php echo _('Category Title...'); ?>" />
            </div>
            <br />
            <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $category['content']; ?></textarea>
            <br />
            <table>
                <tr>
                    <td class="top"><label for="rPosition1"><?php echo _('Position'); ?>:</label></td>
                    <td>
                        <p><input type="radio" class="rb" name="rPosition" id="rPosition1" value="1"<?php if ( '0' != $category['top'] ) echo ' checked="checked"'; ?> /> <label for="rPosition1"><?php echo _('Top'); ?></label></p>
                        <p><input type="radio" class="rb" name="rPosition" id="rPosition2" value="0"<?php if ( '0' == $category['top'] ) echo ' checked="checked"'; ?> /> <label for="rPosition2"><?php echo _('Bottom'); ?></label></p>
                    </td>
                </tr>
            </table>
            <br /><br />
            <br /><br />
            <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
            <?php nonce::field( 'edit-category' ); ?>
		</form>
		<br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>