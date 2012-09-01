<?php

require_once 'base-database-test.php';

class ReportTest extends BaseDatabaseTest {
    /**
     * @var Report
     */
    private $report;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->report = new Report();
    }

    /**
     * Test search
     */
    public function testSearch() {
        // Declare Variables
        $where = ' AND ( e.`brand_id` IN( 8 ) )';

        // Get report
        $accounts = $this->report->search( $where );

        $this->assertTrue( isset( $accounts[0]->title ) );
    }

    /**
     * Test Autocomplete for brands
     */
    public function testAutocompleteBrands() {
        // Declare Variables
        $query = 'Ashley';

        // Get autocomplete entries
        $entries = $this->report->autocomplete_brands( $query );

        $this->assertTrue( stristr( $entries[0]['brand'], $query ) !== false );
    }

    /**
     * Test Autocomplete for online specialists
     */
    public function testAutocompleteOnlineSpecialists() {
        // Declare variables
        $query = 'Ashley';
        $where = '';

        // Get autocomplete entries
        $entries = $this->report->autocomplete_online_specialists( $query, $where );

        $this->assertTrue( stristr( $entries[0]['online_specialist'], $query ) !== false );
    }

    /**
     * Test Autocomplete for Marketing Specialists
     */
    public function testAutocompleteMarketingSpecialists() {
        // Declare Variables
        $query = 'David';

        // Get autocomplete entries
        $entries = $this->report->autocomplete_marketing_specialists( $query );

        $this->assertTrue( stristr( $entries[0]['marketing_specialist'], $query ) !== false );
    }

    /**
     * Test Autocomplete for Companies
     */
    public function testAutocompleteCompanies() {
        // Declare Variables
        $query = 'Imagine';
        $where = '';

        // Get autocomplete entries
        $entries = $this->report->autocomplete_companies( $query, $where );

        $this->assertTrue( stristr( $entries[0]['company'], $query ) !== false );
    }

    /**
     * Test Autocomplete for Company Packages
     */
    public function testAutocompleteCompanyPackages() {
        // Declare Variables
        $query = 'Furnish';
        $where = '';

        // Get autocomplete entries
        $entries = $this->report->autocomplete_company_packages( $query, $where );

        $this->assertTrue( stristr( $entries[0]['package'], $query ) !== false );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->report = null;
    }
}
