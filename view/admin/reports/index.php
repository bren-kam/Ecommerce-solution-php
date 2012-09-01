<?php
/**
 * @package Grey Suit Retail
 * @page Search Reports
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

require VIEW_PATH . $this->variables['view_base'] . 'sidebar.php';
?>

<div id="content">
    <div id="narrow-your-search-wrapper">
    <div id="narrow-your-search">
        <?php
        nonce::field( 'autocomplete', '_autocomplete' );
        nonce::field( 'search', '_search' );
        ?>

        <h2><?php echo _('Narrow Your Search'); ?></h2>

        <form action="" id="fSearch">
        <table class="formatted">
            <tr>
                <td width="300">
                    <select id="type">
                        <option value="brand"><?php echo _('Brand'); ?></option>
                        <option value="online_specialist"><?php echo _('Online Specialist'); ?></option>
                        <option value="marketing_specialist"><?php echo _('Marketing Specialist'); ?></option>
                        <?php if ( $user->has_permission(7) ) { ?>
                            <option value="company"><?php echo _('Company'); ?></option>
                        <?php } ?>
                        <option value="billing_state"><?php echo _('State'); ?></option>
                        <option value="package"><?php echo _('Package'); ?></option>
                    </select>
                </td>
                <td>
                    <div style="margin-right:18px">
                        <input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter Search...'); ?>" style="position: relative; top: 1px; width: 100%" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <select id="services" rel="<?php echo _('Service'); ?>">
                        <option value="">-- <?php echo _('Add Service'); ?> --</option>
                        <?php foreach ( $services as $k => $v ) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td align="right"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
            </tr>
            <tr>
                <td>
                    <div id="criteria"></div>
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>

<div id="subcontent-wrapper" class="narrow-your-search">
<div id="subcontent">
    <table id="table" class="formatted">
        <thead>
            <tr>
                <th><?php echo _('Title'); ?></th>
                <th><?php echo _('Company'); ?></th>
                <th><?php echo _('Products'); ?></th>
                <th><?php echo _('Signed Up'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>

<div class="hidden">
    <div class="criterion" id="criterion-template">
        <span class="type" rel="[type-value]">[type-text]</span> - <span class="search" rel="[object-id]">[object-value]</span>
        <a href="#" class="remove-criterion" title="<?php echo _('Remove'); ?>"><img src="/images/icons/x.png" width="15" height="17" alt="<?php echo _('Remove'); ?>"></a>
    </div>
</div>