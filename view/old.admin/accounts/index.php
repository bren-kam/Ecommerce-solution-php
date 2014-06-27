<?php
/**
 * @package Grey Suit Retail
 * @page List Accounts
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
        nonce::field( 'store_session', '_store_session' );
        ?>

        <h2><?php echo _('Narrow Your Search'); ?></h2>

        <form action="" id="fSearch">
        <table class="formatted">
            <tr>
                <td width="300">
                    <select id="state">
                        <option value="all"><?php echo _('All Accounts'); ?></option>
                        <option value="live"><?php echo _('Live'); ?></option>
                        <option value="staging"><?php echo _('Staging'); ?></option>
                        <option value="inactive"><?php echo _('Inactive'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <select id="sAutoComplete">
                        <option value="title"><?php echo _('Account Name'); ?></option>
                        <option value="domain"><?php echo _('Account Domain'); ?></option>
                        <option value="store_name"><?php echo _('Store Name'); ?></option>
                    </select>
                </td>
                <td><input type="text" class="tb" id="tAutoComplete" placeholder="<?php echo _('Enter Name...'); ?>" style="position: relative; top: 1px;" /></td>
                <td align="right"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
            </tr>
        </table>
        </form>
    </div>
</div>

<div id="subcontent-wrapper" class="narrow-your-search">
<div id="subcontent">
    <table ajax="/accounts/list-all/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="4%">&nbsp;</th>
                <th width="55%" sort="1"><?php echo _('Account'); ?></th>
                <th width="16%"><?php echo _('User Name' ); ?></th>
                <th width="15%"><?php echo _('Online Specialist'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php echo $template->end(); ?>