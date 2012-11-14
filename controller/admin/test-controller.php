<?php
class TestController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'test/';
    }

    /**
     * List Accounts
     *
     * @return TemplateResponse
     */
    protected function index() {
        /** Update Craigslist Stats */
        $craigslist = new Craigslist;
        $craigslist->update_stats();
        $craigslist->update_tags();

        /*
        $ashley_package_feed = new AshleyPackageProductFeedGateway();
        $ashley_package_feed->run();

        /*$mobile_marketing = new MobileMarketing();
        $mobile_marketing->synchronize_contacts();

        $product = new Product();
        $product_ids = array( 183, 17122, 18479, 18482, 40641, 40642, 42600, 42651, 42652, 42715, 42716, 42717, 42718, 42721, 42722, 42723, 42754, 42760, 42762, 42763, 42764, 42765, 44180, 44192, 44203, 44204, 44210, 44211, 45063, 45064, 45065, 46552, 48676, 50141, 50145, 55791, 55793, 55794, 55795, 55804, 55806, 55808, 55809, 55810, 55812, 55813, 55814, 55817, 55818, 55820, 55821, 55840, 55841, 55842, 55845, 55950, 55952, 55953, 55954, 55955, 55956, 55958, 55963, 55964, 55965, 55966, 55967, 55969, 55970, 55971, 55972, 55973, 55974, 55975, 55976, 55977, 55978, 56083, 56084, 56085, 56087, 56089, 56090, 56091, 56093, 56096, 56097, 56098, 56099, 56102, 56104, 56105, 56106, 56107, 56108, 56110, 56111, 56112, 56114, 56115, 56118, 56147, 56149, 56150, 56151, 56152, 56154, 56155, 56195, 56216, 59495, 59855, 59856, 59857, 59859, 59860 );

        foreach ( $product_ids as $pid ) {
            $product->clone_product( $pid, $this->user->id );
            $product->get( $product->id );
            $product->website_id = 651;
            $product->save();
        }*/

        return new HtmlResponse('eh');
    }
}