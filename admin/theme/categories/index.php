<?php
/**
 * @page Categories
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'categories/list', 'jquery.ui' );
javascript( 'jquery', 'jquery.ui', 'categories/list' );

$selected = 'products';
$title = _('Categories') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Categories'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'categories/' ); ?>
	<div id="subcontent">
		<?php
		nonce::field( 'get-categories', '_ajax_get_categories' );
		nonce::field( 'update-category-sequence', '_ajax_update_category_sequence' );
		nonce::field( 'delete-category', '_ajax_delete_category' );
		?>
		<div class="screen" id="dCategories">
			<div class="box page" id="dCategoriesPage">
				<div id="dCategoryBreadCrumb"><?php echo _('Main Categories'); ?></div>
				<h3 id="hCurrentCategory">
					<span><?php echo _('Main Categories'); ?></span>
					<small id="smEditDeleteCategory" class="hidden"> -
					<a href="javascript:;" id="aEditCategory" title="<?php echo _('Edit Category'); ?>"><?php echo _('Edit'); ?></a>
					<small>|</small>
					<a href="javascript:;" id="aDeleteCategory" title="<?php echo _('Delete Category'); ?>"><?php echo _('Delete'); ?></a>
					</small>
				</h3>
				<a href="javascript:;" title="Add Category" id="dAddCategory"><?php echo _('Add Category'); ?></a>
				<input type="hidden" name="hCurrentCategoryID" id="hCurrentCategoryID" value="0" />
				<input type="hidden" name="hRootURL" id="hRootURL" value="admin.imagineretailer.com" />
				
				<p id="pCurrentURL"><a href="javascript:;" title="" target="_blank"></a></p>
				
				<br clear="right" />
				<br />
				<hr />
				<br />
				
				<p align="center" class="hidden" id="pNoSubCategories">
					<?php echo _('No sub categories have been created for this category.'); ?>
					<a href="javascript:;" title="<?php echo _('Add Category'); ?>"><?php echo _('Add a category'); ?></a> <?php echo _('now'); ?>.
				</p>
				<div id="dCategoriesList">
				</div>
				<br clear="all" /><br />
			</div>
		</div>
	</div>
</div>

<div id="dEditCategory" title="Edit Category" class="hidden"></div>

<?php get_footer(); ?>