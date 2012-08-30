<?php

class CsvResponse extends DownloadResponse {
    protected $array;

    /**
     * Get the file name
     *
     * @param array $array
     * @param string $file_name
     */
    public function __construct( array $array, $file_name ) {
        // Tell them what filename we're going for
        parent::__construct( $file_name );

        // Set the array
        $this->array = $array;
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    public function has_error() {
        return false;
    }

    /**
     * Spit out the json response
     */
    protected function respond() {
        // Set it to csv
        header::type('csv');

        // Set it so we can use fputcsv
        $output_stream = fopen("php://output", 'w');

        // Put the rest of the items
        foreach ( $this->array as $array ) {
            fputcsv( $output_stream, $array );
        }

        fclose( $output_stream );
    }
}
