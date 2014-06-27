<?php
/**
 * @package Grey Suit Retail
 * @page List Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $categories
 */

require VIEW_PATH . $this->variables['view_base'] . 'sidebar.php';
?>
<div id="content">
    <div id="narrow-your-search-wrapper">
        <div id="narrow-your-search">
            <?php
            nonce::field( 'autocomplete', '_autocomplete' );
            nonce::field( 'store_session', '_store_session' );
            ?>

            <h2><?php echo _('Narrow Your Search'); ?></h2>

            <form action="" id="fSearch">
            <table class="formatted">
                <tr>
                    <td width="300">
                        <select id="user-option">
                            <option value="all">-- <?php echo _('Select Option'); ?> --</option>
                            <option value="created"><?php echo _('Created By'); ?></option>
                            <option value="modified"><?php echo _('Modified By'); ?></option>
                        </select>
                    </td>
                    <td width="300">
                        <select id="user">
                            <option value="all"><?php echo _('All Users'); ?></option>
                            <?php
                            if ( is_array( $product_users ) )
                            foreach ( $product_users as $product_user ) {
                            ?>
                            <option value="<?php echo $product_user->id; ?>"><?php echo $product_user->contact_name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select id="visibility">
                            <option value=""><?php echo _('All Products'); ?></option>
                            <option value="public"><?php echo _('Public Products'); ?></option>
                            <option value="private"><?php echo _('Private Products'); ?></option>
                            <option value="deleted"><?php echo _('Deleted Products'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select id="sAutoComplete">
                            <option value="sku"><?php echo _('SKU'); ?></option>
                            <option value="products"><?php echo _('Product Name'); ?></option>
                            <option value="brands"><?php echo _('Brands'); ?></option>
                        </select>
                    </td>
                    <td colspan="2">
                        <div style="margin-right: 18px">
                            <input type="text" class="tb" id="tAutoComplete" placeholder="<?php echo _('Enter Search...'); ?>" style="width: 100%" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select id="cid">
                            <option value="0">-- <?php echo _('Select a Category'); ?> --</option>
                            <?php
                            $depth = 0;

                            foreach ( $categories as $category ) {
                                ?>
                                <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ), $category->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td align="right" colspan="2"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
                </tr>
            </table>
            </form>
        </div>
    </div>

    <div id="subcontent-wrapper" class="narrow-your-search">
    <div id="subcontent">
        <table ajax="/products/list-all/" perPage="30,50,100">
            <thead>
                <tr>
                    <th width="40%" sort="1"><?php echo _('Name'); ?></th>
                    <th width="10%"><?php echo _('Created'); ?></th>
                    <th width="10%"><?php echo _('Updated'); ?></th>
                    <th width="10%"><?php echo _('Brand'); ?></th>
                    <th width="10%"><?php echo _('SKU'); ?></th>
                    <th><?php echo _('Category'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

<?php echo $template->end(); ?>