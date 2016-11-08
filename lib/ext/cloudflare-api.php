<?php

/**
 * CloudFlare API v4
 *
 * @author Kerry Jones
 * @date 2/19/2015
 * @url https://www.cloudflare.com/docs/next/
 */
class CloudFlareAPI {

    const HEADER_TYPE_PATCH = 'PATCH';
    const HEADER_TYPE_POST = 'POST';
    const HEADER_TYPE_GET = 'GET';
    const HEADER_TYPE_DELETE = 'DELETE';
    const HEADER_TYPE_PUT = 'PUT';

    const DEBUG = false;
    const URL = 'https://api.cloudflare.com/client/v4/';
    const API_KEY = '3ae56edd68513db53cdab4cb7e59f270df71c';
    const EMAIL = 'jeff@greysuitretail.com';

    /**
     * @var Account
     */
    protected $account;

    /**
     * Construct class
     *
     * @param Account $account This is for logging
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * A few variables that will determine the basic status
     */
    protected $message = null;
    protected $success = false;
    protected $request = null;
    protected $raw_response = null;
    protected $response = null;
    protected $error = null;
    protected $params = array();

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success()
    {
        return $this->success;
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get private raw_response variable
     *
     * @return string
     */
    public function raw_response()
    {
        return $this->raw_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * Get private error variable
     *
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Create Zone
     *
     * @date 2/19/2015
     *
     * @param string $domain
     * @param bool $jump_start (auto search DNS names)
     * @return bool
     */
    public function create_zone($domain, $jump_start = false)
    {
        $this->execute(self::HEADER_TYPE_POST, 'zones', array(
            'name'     => $domain
        , 'jump_start' => $jump_start
        ));

        return $this->response->result->id;
    }

    /**
     * Edit Zone
     *
     * @date 2/19/2015
     *
     * @param string $zone_id
     * @param string $plan_id
     * @return bool
     */
    public function edit_zone($zone_id, $plan_id)
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id, array(
            'plan' => $plan_id
        ));

        return $this->response->result->id;
    }

    /**
     * List Zones
     *
     * @date 2/23/2015
     *
     * @param int $per_page [optional]
     * @param int $page [optional]
     * @return array
     */
    public function list_zones($per_page = 20, $page = 1)
    {
        $this->execute(self::HEADER_TYPE_GET, 'zones', array(
            'per_page' => $per_page
        , 'page'       => $page
        ));

        return $this->response->result;
    }

    /**
     * Zone Details
     *
     * @date 2/20/2015
     *
     * @param string $zone_id
     * @return bool
     */
    public function zone_details($zone_id)
    {
        $this->execute(self::HEADER_TYPE_GET, 'zones/' . $zone_id);

        return $this->response->result;
    }

    /**
     * Purge
     *
     * @date 2/19/2015
     *
     * @param string $zone_id
     * @return bool
     */
    public function purge($zone_id)
    {
        $this->execute(self::HEADER_TYPE_DELETE, 'zones/' . $zone_id . '/purge_cache', array(
            'purge_everything' => true
        ));

        return $this->success;
    }

    /**
     * Purge URL
     *
     * @date 2/19/2015
     *
     * @param string $zone_id
     * @param string|array $url
     * @return bool
     */
    public function purge_url($zone_id, $url)
    {
        $this->execute(self::HEADER_TYPE_DELETE, 'zones/' . $zone_id . '/purge_cache', array(
            'files' => $url
        ));

        return $this->success;
    }

    /**
     * Available Plans
     *
     * @date 2/19/2015
     *
     * @param string $zone_id
     * @return bool
     */
    public function available_plans($zone_id)
    {
        $this->execute(self::HEADER_TYPE_GET, 'zones/' . $zone_id . '/available_plans');

        return $this->response->result;
    }

    /********** DNS SETTINGS **********/

    /**
     * Create DNS Record
     *
     * @date 2/23/2015
     * @version 1.1
     *
     * @param string $zone_id
     * @param string $type (A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF)
     * @param string $name
     * @param string $content
     * @param int $ttl [optional] 1 = automatic
     * @param string $domain
     * @return bool
     */
    public function create_dns_record($zone_id, $type, $name, $content, $ttl = 1, $domain = '')
    {
        $arguments = array(
            'type'  => $type
        , 'name'    => $name
        , 'content' => $content
        , 'ttl'     => $ttl
        );

        $full_domain = $domain . '.';

        switch (strtolower($type))
        {
            case 'mx':
                list ($priority, $content) = explode(' ', $content);
                $arguments['priority'] = $priority;

                if ('.' == substr($content, - 1))
                    $content = substr($content, 0, - 1);

                $arguments['content'] = $content;
                break;

            case 'a':
                if ($name == $full_domain)
                    $arguments['proxied'] = true;
                break;

            case 'cname':
                if ($content == $full_domain)
                    $arguments['proxied'] = true;
        }

        $this->execute(self::HEADER_TYPE_POST, 'zones/' . $zone_id . '/dns_records', $arguments);

        return $this->success;
    }

    /**
     * Update DNS Record
     *
     * @date 2/20/2015
     *
     * @param string $zone_id
     * @param string $dns_zone_id
     * @param string $type (A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF)
     * @param string $name
     * @param string $content
     * @param int $ttl [optional] 1 = automatic
     * @return bool
     */
    public function update_dns_record($zone_id, $dns_zone_id, $type, $name, $content, $ttl = 1)
    {
        $this->execute(self::HEADER_TYPE_PUT, 'zones/' . $zone_id . '/dns_records/' . $dns_zone_id, array(
            'type'  => $type
        , 'name'    => $name
        , 'content' => $content
        , 'ttl'     => $ttl
        ));

        return $this->success;
    }

    /**
     * Delete DNS Records
     *
     * @date 2/20/2015
     *
     * @param string $zone_id
     * @param string $dns_zone_id
     * @return bool
     */
    public function delete_dns_record($zone_id, $dns_zone_id)
    {
        $this->execute(self::HEADER_TYPE_DELETE, 'zones/' . $zone_id . '/dns_records/' . $dns_zone_id);

        return $this->success;
    }

    /**
     * List DNS Records
     *
     * @date 2/20/2015
     *
     * @param string $zone_id
     * @return bool
     */
    public function list_dns_records($zone_id)
    {
        $this->execute(self::HEADER_TYPE_GET, 'zones/' . $zone_id . '/dns_records');

        return $this->response->result;
    }

    /********** CLOUDFLARE SETTINGS ***********/

    /**
     * Change IPV6 setting
     *
     * @date 2/23/2015
     *
     * @param string $zone_id
     * @param string $value [optional] ('off', 'on', 'safe')
     * @return bool
     */
    public function change_ipv6($zone_id, $value = 'on')
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id . '/settings/ipv6', compact('value'));

        return $this->success;
    }

    /**
     * Change Minify settings
     *
     * @date 2/23/2015
     *
     * @param string $zone_id
     * @param string $html [optional] ('on', 'off')
     * @param string $css [optional] ('on', 'off')
     * @param string $js [optional] ('on', 'off')
     * @return bool
     */
    public function change_minify($zone_id, $html = 'on', $css = 'off', $js = 'off')
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id . '/settings/minify', array(
            'value' => compact('css', 'html', 'js')
        ));

        return $this->success;
    }

    /**
     * Change Mirage Setting
     *
     * @date 2/23/2015
     *
     * @param string $zone_id
     * @param string $value [optional] ('off', 'on')
     * @return bool
     */
    public function change_mirage($zone_id, $value = 'on')
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id . '/settings/mirage', compact('value'));

        return $this->success;
    }

    /**
     * Change Security Level setting
     *
     * @date 2/23/2015
     *
     * @param string $zone_id
     * @param string $value [optional] ('essentially_off', 'low', 'medium', 'high', 'under_attack')
     * @return bool
     */
    public function change_security_level($zone_id, $value = 'medium')
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id . '/settings/security_level', compact('value'));

        return $this->success;
    }

    /**
     * Change Polish image optimization
     *
     * @date 2/23/2015
     *
     * @param string $zone_id
     * @param string $value [optional] ('off', 'lossless', 'lossy')
     * @return bool
     */
    public function change_polish($zone_id, $value = 'lossless')
    {
        $this->execute(self::HEADER_TYPE_PATCH, 'zones/' . $zone_id . '/settings/polish', compact('value'));

        return $this->success;
    }

    /**
     * This sends sends the actual call to the API Server and parses the response
     *
     * @param string $method The method being called
     * @param array $params an array of the parameters to be sent
     * @return stdClass object
     */
    public function execute($type, $method, $params = array())
    {
        // Initialize cURL and set options
        $ch = curl_init();

        $this->request = json_encode($params);

        $url = self::URL;
        $url .= $method;

        if ($type == self::HEADER_TYPE_GET && !empty($params))
            $url .= '?' . http_build_query($params);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Auth-Key: ' . self::API_KEY,
            'X-Auth-Email: ' . self::EMAIL
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Perform the request and get the response
        $this->raw_response = curl_exec($ch);

        // Decode the response
        $this->response = json_decode($this->raw_response);

        // Close cURL
        curl_close($ch);

        $this->success = $this->response->success;
        $this->message = implode("\n", $this->response->messages);
        $this->error = $this->response->errors;

        // If we're debugging lets give as much info as possible
        if (self::DEBUG)
        {
            echo "<h1>URL</h1>\n<p>", self::URL, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export($this->request, true), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export($this->response, true), "</pre>\n<hr />\n<br /><br />\n";
        }

        $api_log = new ApiExtLog();
        $api_log->website_id = $this->account->id;
        $api_log->api = 'CloudFlare API v4';
        $api_log->method = $method;
        $api_log->url = self::URL;
        $api_log->request = json_encode($this->request);
        $api_log->raw_request = 'N/A';
        $api_log->response = json_encode($this->response);
        $api_log->raw_response = $this->raw_response;
        $api_log->create();

        return $this->response;
    }
}