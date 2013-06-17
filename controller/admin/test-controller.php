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
         //Update Craigslist Stats
        //$craigslist = new Craigslist;
        //$craigslist->update_stats();
        //$ashley = new AshleyMasterProductFeedGateway();
        //$ashley->run();
        
        //$ashley_package_feed = new AshleyPackageProductFeedGateway();
        //$ashley_package_feed->run();
        
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

        //return new HtmlResponse('eh');
        // Get categories

        //$coaster = new CoasterProductFeedGateway();
        //$coaster->run();
		//$coaster->cleanup();
		//$ashley = new AshleyMasterProductFeedGateway();
        //$ashley->run();
		
		$website_ids = array(64,68,73,78,80,82,96,118,123,124,149,158,161,167,168,175,186,187,189,190,205,213,218,228,243,248,291,292,293,296,304,317,318,326,334,335,337,341,343,345,351,354,357,361,369,371,372,374,377,378,388,389,390,398,403,404,405,411,414,418,420,426,429,434,435,436,444,456,457,458,461,464,467,468,476,477,478,479,486,489,492,494,499,501,527,535,553,557,559,571,572,573,581,582,588,590,593,595,596,599,600,601,605,607,610,612,613,614,635,638,641,642,644,645,649,650,652,653,656,659,660,661,662,664,665,666,667,668,674,677,681,682,684,686,689,700,702,704,720,743,751,760,802,803,805,806,807,809,829,830,878,879,882,883,895,897,900,904,907,910,911,912,926,928,929,932,936,939,942,964,969,971,972,973,975,978,989,991,1011,1016,1017,1022,1032,1034,1042,1048,1058,1066,1067,1068,1071,1077,1078,1081,1091,1099,1100,1101,1105,1112,1113,1114,1116,1117,1118,1119,1120,1126,1133,1134,1137,1140,1141,1145,1148,1152,1156,1161,1176,1186,1188,1189,1191,1194,1196,1197,1198,1204,1206,1212,1216,1218,1219,1221,1222,1223,1227,1229,1230,1231,1232,1233,1235,1237,1238,1240,1244,1245,1247,1248,1251,1256,1257,1258,1259,1260,1262,1279,1280,1281,1283,1284,1291,1296,1297,1304,1305,1306,1307,1308,1310,1311,1313,1314,1324,1325,1326,1329);
		
		$account_category = new AccountCategory();
		$category = new Category();
		
		foreach ( $website_ids as $website_id ) {
			$account_category->reorganize_categories( $website_id, $category );
		}

        return new HtmlResponse( 'heh' );
    }
}