<?php
/**
 * @package Grey Suit Retail
 * @page CSS | Customize
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $css
 * @var Account $account
 */

echo $template->start( _('CSS for ') . $account->title );
nonce::field('save_css');
?>

<div id="editor-container">
    <div id="editor"><?php echo $css; ?></div>
</div>
<br>
<p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/customize/save-css/' ); ?>" class="button" id="save-css" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>

<script src="http://ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/chrome");
    editor.getSession().setMode("ace/mode/css");
</script>

<?php echo $template->end(); ?>