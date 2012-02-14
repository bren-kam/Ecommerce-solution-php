<div id="sidebar">
	<h2>Actions</h2>
	<a href="/craigslist/" title="<?php echo _('Craigslist Templates'); ?>" class="top"><?php echo _('Templates'); ?></a>
	<?php if ( isset( $templates ) ) { ?>
        <a href="/craigslist/" title="<?php echo _('View Craigslist Templates'); ?>" class="sub view first"><?php echo _('View'); ?></a>
        <a href="/craigslist/add-edit/" title="<?php echo _('Add Craigslist Template'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
	<?php } ?>

    <a href="/craigslist/headlines/" title="<?php echo _('Craigslist Headlines'); ?>" class="top"><?php echo _('Headlines'); ?></a>
	<?php if ( isset( $headlines ) ) { ?>
    <a href="/craigslist/headlines/add-edit/" title="<?php echo _('Add Craigslist Headline'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
    <?php } ?>

    <a href="/craigslist/accounts/" title="<?php echo _('Craigslist Accounts'); ?>" class="top"><?php echo _('Accounts'); ?></a>
	<?php if ( isset( $accounts ) ) { ?>
    <a href="/craigslist/accounts/add-edit/" title="<?php echo _('Add Craigslist Account'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
    <?php } ?>

    <a href="/craigslist/markets/" title="<?php echo _('Craigslist Markets'); ?>" class="top"><?php echo _('Markets'); ?></a>
	<?php if ( isset( $markets ) ) { ?>
	<a href="/craigslist/markets/add-edit/" title="<?php echo _('Add Craigslist Market'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
    <?php } ?>
</div>