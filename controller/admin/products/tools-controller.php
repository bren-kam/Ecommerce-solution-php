<?php
class ToolsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/tools/';
        $this->section = 'products';
        $this->title = _('Tools');
    }

    /**
     * Redirect
     *
     * @return RedirectResponse
     */
    protected function index() {
        // Make sure they have a right to be here
        if ( !$this->user->has_permission( User::ROLE_SUPER_ADMIN ) && User::JEFF != $this->user->id )
            return new RedirectResponse('/products/');

        return new RedirectResponse('/products/tools/discontinue-ashley-products-by-sku/');
    }

    /**
     * Discontinue ashley products by sku
     */
    protected function discontinue_ashley_products_by_sku() {
        // Let it run for some minutes
        set_time_limit(300);


        $form = new FormTable('fDiscontinueAshleyProducts');
        $form->add_field( 'textarea', 'SKUs', 'tSKUs' )
            ->add_validation( 'req', _('The "SKUs" field is required') );

        if ( $form->posted() ) {
            $skus = explode( "\n", str_replace( "\r", '', $_POST['tSKUs'] ) );

            $product = new Product();
            $account_product = new AccountProduct();

            $affected_skus = $product->discontinue_ashley_products_by_skus( $skus, $this->user->id );
            $account_product->remove_all_discontinued();

            $this->notify( $affected_skus . ' have been marked as discontinued and removed from all websites.' );
        }

        return $this->get_template_response( 'discontinue-ashley-products-by-skus' )
            ->kb( 0 )
            ->add_title( _('Discontinue Products by SKU') . ' | ' . _('Attributes') )
            ->select( 'tools', 'view' )
            ->set( array( 'form' => $form->generate_form() ) );
    }
}