<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 24/04/15
 * Time: 15:10
 */

class RemarketingController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->title = 'Remarketing';
        $this->view_base = 'shopping-cart/remarketing/';
    }

    /**
     * Settings
     * @return RedirectResponse|TemplateResponse
     */
    public function settings() {

        $form = new BootstrapForm('remarketing-settings');

        $settings = $this->user->account->get_settings('remarketing-title', 'remarketing-intro-text', 'remarketing-idle-seconds');

        $form->add_field('text', 'Show popup after seconds', 'idle-seconds', $settings['remarketing-idle-seconds'] ? $settings['remarketing-idle-seconds'] : 60)
            ->add_validation('req', 'Required');

        $form->add_field('text', 'Title', 'title', $settings['remarketing-title']);
        $form->add_field('textarea', 'Intro Text', 'intro-text', $settings['remarketing-intro-text'])
            ->attribute('rte', 1);

        $upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
        $search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
        $delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
        $form->add_field('anchor', 'Add Image', 'Add Image')
            ->attribute('href', 'javascript:;')
            ->attribute('class', 'btn btn-default btn-xs')
            ->attribute('title', 'Open Media Manager')
            ->attribute('data-media-manager', '1')
            ->attribute('data-upload-url', $upload_url)
            ->attribute('data-search-url', $search_url)
            ->attribute('data-delete-url', $delete_url);

        if ( $form->posted() ) {
            $this->user->account->set_settings([
                'remarketing-title' => $_POST['title']
                , 'remarketing-intro-text' => $_POST['intro-text']
                , 'remarketing-idle-seconds' => $_POST['idle-seconds']
            ]);

            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $form_html = $form->generate_form();

        $this->resources->javascript('media-manager')->css('media-manager');

        return $this->get_template_response('settings')
            ->menu_item('shopping-cart/remarketing/settings')
            ->add_title('Settings')
            ->set(compact('form_html'));
    }

}