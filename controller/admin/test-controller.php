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
     *
     * @return TemplateResponse
     */
    protected function index() {
        $website_ids = array(64,68,71,78,96,118,123,124,134,158,161,167,168,175,187,190,205,218,228,243,291,292,293,296,304,317,318,326,330,334,335,337,341,343,345,351,354,357,361,377,378,398,403,404,405,418,420,428,429,434,435,456,457,458,461,464,468,476,477,478,479,486,489,492,494,499,501,535,559,571,572,582,593,596,599,600,601,605,607,613,614,638,641,644,645,649,650,652,659,661,662,664,665,667,681,682,684,700,704,720,805,806,807,809,816,819,826,829,866,878,879,882,883,895,900,904,912,920,926,928,929,932,936,939,942,954,956,975,978,1016,1017,1022,1032,1034,1037,1042,1047,1048,1058,1066,1067,1068,1071,1077,1078,1091,1099,1100,1101,1105,1106,1107,1112,1113,1115,1116,1117,1118,1126,1133,1134,1137,1140,1141,1148,1188,1191,1197,1198,1206,1218,1219,1221,1222,1223,1227,1229,1232,1233,1240,1245,1248,1251,1257,1258,1259,1281,1283,1291,1297,1304,1305,1306,1307,1308,1310,1313,1314,1324,1325,1329,1330,1331,1334,1335,1339,1342,1344,1346,1347,1348,1350,1351,1352,1354,1356,1358,1359,1362,1363,1367,1369,1372,1373,1380,1381,1389,1392,1400,1403,1404,1405,1406,1409,1410,1411,1412,1414,1417,1419,1421,1423,1425,1427,1431,1432,1433);
        $account_category = new AccountCategory();
        $category = new Category();

        foreach ( $website_ids as $website_id ) {
            $account_category->reorganize_categories( $website_id, $category );
        }

        return new HtmlResponse( 'heh' );
    }

    /**
     * Login
     *
     * @return bool
     */
//    protected function get_logged_in_user() {
//        if ( defined('CLI') && true == CLI ) {
//            $this->user = new User();
//            return true;
//        }
//
//        return false;
//    }

    protected function route53_replace() {

        $replace_search_1 = '199.79.48.137';
        $replace_search_2 = '199.79.48.138';

        if ( $this->verified() ) {

            $domain_list = explode("\n", trim( $_POST['domains'] ) );

            library('r53');
            $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

            $marker = null;
			$account = new Account();
			
            foreach ($domain_list as $domain) {
				$account = new Account();
				$account = $account->prepare( 'SELECT *, `website_id` AS id FROM `websites` WHERE `domain` LIKE :domain', 'i', array( ':domain' => trim( '%' . $domain ) ) )->get_row( PDO::FETCH_INTO, $account );
				
				if ( !$account->id )
					continue;
				
				$zone_id = $account->get_settings( 'r53-zone-id' );
				$records_result = $r53->listResourceRecordSets( $zone_id  );
				$dns_changes = array();
				
				foreach ( $records_result['ResourceRecordSets'] as $record ) {
					$changed = false;
					foreach ( $record['ResourceRecords'] as &$record_value ) {
						if ( strpos( $record_value, $replace_search_1 ) !== false ) {
							$record_value = str_replace( $replace_search_1, $_POST['replace-1'], $record_value ) ;
							$changed = true;
						} else if ( strpos( $record_value, $replace_search_2 ) !== false ) {
							$record_value = str_replace( $replace_search_2, $_POST['replace-2'], $record_value ) ;
							$changed = true;
						}
					}
					
					if ( $changed ) {
						$dns_changes[] = $r53->prepareChange( 'UPSERT', $record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords']  );

						echo "Update for {$domain}: " . json_encode( array( 'UPSERT', $record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords']  ) ) . "<br />";
					}
					
				}
				
				if ( !empty( $dns_changes ) ) {
					echo "...running all from {$domain}...";
					$change_result = $r53->changeResourceRecordSets( $zone_id, $dns_changes, 'gsr/route53-replace ' . date('Y-m-d H:i:s') );
					echo json_encode( $change_result ) . "<br /><hr />";
				} else {
					echo "No matching records found for {$domain}<br /><hr />";
				}
            }
			
            die('Finished!');

        }

        return $this->get_template_response( 'route53-replace' )->set( compact( 'replace_search_1', 'replace_search_2' ) );
    }

    /**
     * Recompile All LESS
     * @return HtmlResponse
     */
    protected function recompile_all_less() {
        set_time_limit(3600);

        // Get account
        $unlocked = new Account();
        $unlocked->get( Account::TEMPLATE_UNLOCKED );
        $unlocked_less = $unlocked->get_settings('less');

        library('lessc.inc');

        $account = new Account();
        $less_accounts = $account->get_less_sites();

        /**
         * @var Account $less_account
         * @var string $unlocked_less
         */
        foreach ( $less_accounts as $less_account ) {
            if ( $less_account->id == Account::TEMPLATE_UNLOCKED )
                continue;

            echo "Compiling LESS for {$less_account->website_id} {$less_account->title}...<br>\n";
            flush();

            $less = new lessc;
            $less->setFormatter("compressed");

            $site_less = $less_account->get_settings('less');

            $less_account->set_settings( array(
                'css' => $less->compile( $unlocked_less . $site_less )
            ));

            unset( $less );
            unset( $site_less );
            unset( $less_account );
            gc_collect_cycles();
        }

        return new HtmlResponse( 'All LESS sites Recompiled' );
    }

    /**
     * Reorganize Categories ALL
     *
     *
     * @return HtmlResponse
     */
    protected function reorganize_categories_all() {
        $account = new Account();
        $accounts = $account->list_all( array(' AND a.status=1 ', '', '', 10000 ) );
        $account_category = new AccountCategory();
        $category = new Category();

        foreach ( $accounts as $a ) {
            echo "#{$a->id}...<br>\n";
            flush();
            $account_category->reorganize_categories( $a->website_id, $category );
        }

        return new HtmlResponse( 'Finished' );
    }

    protected function route53_spf() {

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

        $marker = null;
        do {
            $response = $r53->listHostedZones( $marker, 100 );
            $hosted_zones = $response['HostedZone'];
            $marker = $response['NextMarker'];

            foreach ( $hosted_zones as $zone ) {

                $zone_id = $zone['Id'];
                $records_result = $r53->listResourceRecordSets( $zone_id  );
                $dns_changes = array();
                $mail_record_in_our_servers = false;

                echo $zone['Name'].'<br>';

                foreach ( $records_result['ResourceRecordSets'] as $record ) {

                    if ( $record['Type'] == 'TXT' ) {
                        foreach( $record['ResourceRecords'] as $rr_key => $rr_value ) {
                            if ( stripos($rr_value, 'spf') !== FALSE && ( stripos( $rr_value, '162.218.139.218' ) !== FALSE ||stripos( $rr_value, '162.218.139.219' ) !== FALSE ) ) {
                                // echo $record['ResourceRecords'][$rr_key] . "<br>" . '"v=spf1 a mx ip4:199.79.48.137 ip4:208.53.48.135 ip4:199.79.48.25 ip4:162.218.139.218 ip4:162.218.139.219 ~all"<br>';
                                $record['ResourceRecords'][$rr_key] = '"v=spf1 a mx ip4:199.79.48.137 ip4:208.53.48.135 ip4:199.79.48.25 ip4:162.218.139.218 ip4:162.218.139.219 ~all"';
                                $dns_changes[] = $r53->prepareChange( 'UPSERT', $record['Name'], $record['Type'], $record['TTL'], $record['ResourceRecords']  );
                            }
                        }
                    }

                    if ( stripos( $record['Name'], 'mail.' ) === 0 ) {
                        $mail_record_values = implode(',', $record['ResourceRecords']);
                        $mail_record_in_our_servers =
                            strpos( $mail_record_values, '199.79.48.137' ) !== FALSE ||
                            strpos( $mail_record_values, '208.53.48.135' ) !== FALSE ||
                            strpos( $mail_record_values, '199.79.48.25' ) !== FALSE ||
                            strpos( $mail_record_values, '162.218.139.218' ) !== FALSE ||
                            strpos( $mail_record_values, '162.218.139.219' ) !== FALSE;
                    }
                }

                if ( $mail_record_in_our_servers ) {
                    if ( !empty( $dns_changes ) ) {
                        echo "Value for mail.[domain]: {$mail_record_values}<br>\n";
                        echo "Update TXT SPF Record required running " . count($dns_changes) . " changes from {$zone['Name']}...";
                        $change_result = array(); // $r53->changeResourceRecordSets( $zone_id, $dns_changes, 'gsr/route53-spf-update ' . date('Y-m-d H:i:s') );
                        echo json_encode( $change_result ) . "<hr />\n";
                    } else {
                        echo "No matching records found for {$zone['Name']}<hr />\n";
                    }
                } else {
                    echo "mail.{$zone['Name']} record NOT in our servers - {$mail_record_values}<br><hr>\n";
                }
                flush();
            }

        } while ( $marker != NULL );

        die('Finished!');

    }

    public function butler() {
        $butler = new ButlerFeedGateway();
        $butler->run();
    }

}