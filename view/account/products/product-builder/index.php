<?php
/**
 * @package Grey Suit Retail
 * @page Product Builder | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

require VIEW_PATH . $this->variables['view_base'] . '../sidebar.php';
?>

<div id="content">
    <div id="narrow-your-search-wrapper">
    <div id="narrow-your-search">
        <?php
        nonce::field( 'autocomplete', '_autocomplete' );
        ?>
        <h2><?php echo _('Narrow Your Search'); ?></h2>
        <br />
        <form action="" id="fSearch">
        <table class="formatted">
            <tr>
                <td>
                    <select id="sAutoComplete">
                        <option value="sku"><?php echo _('SKU'); ?></option>
                        <option value="product"><?php echo _('Product Name'); ?></option>
                        <option value="brand"><?php echo _('Brand'); ?></option>
                    </select>
                </td>
                <td><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="position: relative; top: 1px;" /></td>
                <td align="right"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
            </tr>
        </table>
        </form>
    </div>
</div>

<div id="subcontent-wrapper" class="narrow-your-search">
    <div id="subcontent">
        <table perPage="100,250,500" id="tViewProducts">
            <thead>
                <tr>
                    <th width="45%" sort="1"><?php echo _('Name'); ?></th>
                    <th width="20%"><?php echo _('Brand'); ?></th>
                    <th width="10%"><?php echo _('SKU'); ?></th>
                    <th width="25%"><?php echo _('Category'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<?php echo $template->end(); ?>