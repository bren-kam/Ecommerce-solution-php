<?php
/**
 * @package Grey Suit Retail
 * @page CSS | Customize
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $less
 * @var Account $account
 * @var string|bool $unlocked_less
 */

echo $template->start( _('LESS CSS for ') . $account->title );
nonce::field('save_less');

if ( $unlocked_less ) {
?>
<h2><?php echo _('Core LESS'); ?></h2>
<div id="core-container">
    <div id="core"><?php echo $unlocked_less; ?></div>
</div>
<br><br>
<h2><?php echo _('LESS'); ?></h2>
<?php } ?>
<div id="editor-container">
    <div id="editor"><?php echo $less; ?></div>
</div>
<br>
<p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/customize/save-less/' ); ?>" class="button" id="save-less" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>

<script src="http://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    <?php if ( $unlocked_less ) {?>
    var core = ace.edit("core");
    core.setReadOnly(true);
    core.setTheme("ace/theme/chrome");
    core.getSession().setMode("ace/mode/less");
    <?php } ?>

    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/chrome");
    editor.getSession().setMode("ace/mode/less");
</script>

<?php echo $template->end(); ?>