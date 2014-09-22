<?php
/**
 * @package Grey Suit Retail
 * @page HTML Header
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $html_header
 */

echo $template->start( _('HTML &lt;head&gt;') );
?>

<hr />
<br />

<form action="" name="fHTMLHeader" method="post">
    <p>
        <label for="taContent">HTML Code:</label><br />
        <textarea id="taContent" name="html-header" rows="10" placeholder="Place your HTML code, will be placed at the bottom of every <head>"><?php echo $html_header ?></textarea>
    </p>
    <input type="submit" class="button" value="<?php echo _('Save'); ?>">
    <?php nonce::field('html_header'); ?>
</form>
<br clear="all"><br>

<?php echo $template->end(); ?>