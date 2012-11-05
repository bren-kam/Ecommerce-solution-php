<ol><?php 
global $user;
$nonce = nonce::create('change-website');

foreach ( $user['websites'] as $website_id => $w ) {
?>
<li><a href="/ajax/change-website/?wid=<?php echo $website_id; ?>&amp;_nonce=<?php echo $nonce; ?>" title="<?php echo _('Select'), ' ', $w['title']; ?>" ajax="1"><strong><?php echo $w['title']; ?></strong> - <?php echo $w['domain']; ?></a></li>
<?php
}
?>
</ol>