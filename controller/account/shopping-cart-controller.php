<?php
class ShoppingCartController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'shopping-cart/';
        $this->section = 'shopping-cart';
        $this->title = _('Shopping Cart');
    }

    /**
     * List Shopping Cart Users
     *
     * @return RedirectResponse
     */
    protected function index() {
        return new RedirectResponse('/shopping-cart/users/');
    }
}


