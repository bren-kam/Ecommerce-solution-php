<?php
/**
 * @page List Website Categories
 * @package Imagine Retailer
 */

// Get current user
global $user;

// Instantiate Categories class
$c = new Categories;
$categories_list = $c->get_list( 0, 0, 0, false );

// If user is not logged in
if ( !$user )
	login();

$selected = "pages";
$title = _('Website Categories') . ' | ' . TITLE;
css('website/categories');
javascript('website/categories');
get_header();
?>

<div id="content" class="relative">
	<h1><?php echo _('Website Categories'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/' ); ?>
	<div id="subcontent">
        <div id="dParentCategory">
            <label for="sParentCategoryID"><?php echo _('Parent Category'); ?>:</label> 
            <select id="sParentCategoryID">
                <option value="0">-- <?php echo _('Top'); ?> --</option>
                <?php echo $categories_list; ?>
            </select>
        </div>
		<table id="tWebsiteCategories" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="65%"><?php echo _('Title'); ?></th>
					<th width="35%"><?php echo _('Last Updated'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>