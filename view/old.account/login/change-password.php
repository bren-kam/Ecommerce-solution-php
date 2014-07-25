<?php

/**
 * @package Grey Suit Retail
 * @page Authorized Users | Settings | Email Marketing
 *
 * Declare the variables we have available from other sources

 * @var Template $template
 * @var User $user
 * @var string $form
 * @var AuthUserWebsite $auth_user_website
 */
echo $template->start(_('Set Your Password'), false);
//if (count($errors)) {
//    foreach ($errors as $err) {
//        echo '<div class="error">' . $err . "</div>";
//    }
//}

echo $form;

echo $template->end();
?>