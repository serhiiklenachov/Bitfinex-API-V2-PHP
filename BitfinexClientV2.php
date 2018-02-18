<?php
/**
 * Bitfinex PHP API v2
 *
 * WIP: need to add more endpoints
 *
 * Access all features of https://www.bitfinex.com trading platform
 * Docs https://docs.bitfinex.com/v2/docs/rest-general
 *
 * @package  Bitfinex
 * @author   Serhii Klenachov (https://www.linkedin.com/in/serhiiklenachov)
 * @license  MIT
 */

namespace apis;

class BitfinexClientV2
{
    const CONNECT_TIMEOUT = 60;
    const API_URL = 'https://api.bitfinex.com';
    private $api_key = '';
    private $api_secret = '';
    private $api_version = 'v2';

    /**
     * @param string $api_key Your API key obtained from https://www.bitfinex.com/account/api
     * @param string $api_secret Your API secret obtained from https://www.bitfinex.com/account/api
     * @param string $api_version Bitfinex API version
     */
    public function __construct($api_key, $api_secret, $api_version = 'v2')
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->api_version = $api_version;
    }

    /**
     * Public endpoints
     * =================================================================
     */

    /**
     * Get the current status of the platform.
     *
     * @return int 1=operative, 0=maintenance
     */
    public function get_platform_status()
    {
        $request = $this->endpoint('platform', 'status');

        return $this->send_public_request($request);
    }

    /**
     * Get Tickers
     *
     * It shows you the current best bid and ask, as well as the last trade price.
     * It also includes information such as daily volume and how much the price has moved over the last day.
     *
     * @param string $symbol The symbols you want information about. ex: tBTCUSD,fUSD.
     * @return mixed
     */
    public function get_tickers($symbols = array('tBTCUSD'))
    {
        $request = $this->endpoint('tickers');
        $data = array('symbols' => implode(",", $symbols));

        return $this->send_public_request($request, $data);
    }

    public function get_tickers_formatted($symbols = array('tBTCUSD'))
    {
        $request = $this->endpoint('tickers');
        $data = array('symbols' => implode(",", $symbols));

        $tickers_pure = $this->send_public_request($request, $data);

        $tickers = array();
        foreach ($tickers_pure as $ticker_pure) {
            $formatted_ticker = array();

            if (substr($ticker_pure[0], 0, 1) == "t") {
                $formatted_ticker['ticker_type'] = 'trading';
                $formatted_ticker['symbol'] = $ticker_pure[0];
                $formatted_ticker['market'] = substr($ticker_pure[0], 1, strlen($ticker_pure[0]) - 1);
                $formatted_ticker['bid'] = $ticker_pure[1];
                $formatted_ticker['bid_size'] = $ticker_pure[2];
                $formatted_ticker['ask'] = $ticker_pure[3];
                $formatted_ticker['ask_size'] = $ticker_pure[4];
                $formatted_ticker['daily_change'] = $ticker_pure[5];
                $formatted_ticker['daily_change_perc'] = $ticker_pure[6];
                $formatted_ticker['last_price'] = $ticker_pure[7];
                $formatted_ticker['volume'] = $ticker_pure[8];
                $formatted_ticker['hight'] = $ticker_pure[9];
                $formatted_ticker['low'] = $ticker_pure[10];
            } elseif (substr($ticker_pure[0], 0, 1) == "f") {
                $formatted_ticker['ticker_type'] = 'funding';
                $formatted_ticker['symbol'] = $ticker_pure[0];
                $formatted_ticker['market'] = substr($ticker_pure[0], 1, strlen($ticker_pure[0]) - 1);
                $formatted_ticker['bid'] = $ticker_pure[1];
                $formatted_ticker['bid_size'] = $ticker_pure[2];
                $formatted_ticker['bid_period'] = $ticker_pure[3];
                $formatted_ticker['ask'] = $ticker_pure[4];
                $formatted_ticker['ask_size'] = $ticker_pure[5];
                $formatted_ticker['ask_period'] = $ticker_pure[6];
                $formatted_ticker['daily_change'] = $ticker_pure[7];
                $formatted_ticker['daily_change_perc'] = $ticker_pure[8];
                $formatted_ticker['last_price'] = $ticker_pure[9];
                $formatted_ticker['volume'] = $ticker_pure[10];
                $formatted_ticker['hight'] = $ticker_pure[11];
                $formatted_ticker['low'] = $ticker_pure[12];
            }
            array_push($tickers, $formatted_ticker);
        }

        return $tickers;
    }


    /**
     * Endpoint
     *
     * Construct an endpoint URL
     *
     * @param string $method
     * @param mixed $params
     * @return string
     */
    private function endpoint($method, $params = NULL)
    {
        $parameters = '';

        if ($params !== NULL) {
            $parameters = '/';
            if (is_array($params)) {
                $parameters .= implode('/', $params);
            } else {
                $parameters .= $params;
            }
        }

        return "/{$this->api_version}/$method$parameters";
    }

    /**
     * Prepare Header
     *
     * Add data to header for authentication purpose
     *
     * @param array $data
     * @return json
     */
    private function prepare_header($data)
    {
        $data['nonce'] = (string)number_format(round(microtime(true) * 100000), 0, '.', '');

        $payload = base64_encode(json_encode($data));
        $signature = hash_hmac('sha384', $payload, $this->api_secret);

        return array(
            'X-BFX-APIKEY: ' . $this->api_key,
            'X-BFX-PAYLOAD: ' . $payload,
            'X-BFX-SIGNATURE: ' . $signature
        );
    }

    /**
     * Curl Error
     *
     * Output curl error if possible
     *
     * @param array $data
     * @return json
     */
    private function curl_error($ch)
    {
        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Is Bitfinex Error
     *
     * Check whether bitfinex API returned an error message
     *
     * @param array $ch Curl resource
     * @return bool
     */
    private function is_bitfinex_error($ch)
    {
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code !== 200) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Output
     *
     * Prepare API output
     *
     * @param json $result
     * @param bool $is_error
     * @return array
     */
    private function output($result, $is_error = FALSE)
    {
        $out_array = json_decode($result, TRUE);

        if ($is_error) {
            $out_array['error'] = TRUE;
        }

        return $out_array;
    }

    /**
     * Send Signed Request
     *
     * Send a signed HTTP request
     *
     * @param array $data
     * @return mixed
     */
    private function send_auth_request($data)
    {
        $ch = curl_init();
        $url = self::API_URL . $data['request'];

        $headers = $this->prepare_header($data);

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_POSTFIELDS => ''
        ));

        if (!$result = curl_exec($ch)) {
            return $this->curl_error($ch);
        } else {
            return $this->output($result, $this->is_bitfinex_error($ch));
        }
    }

    /**
     * Send Unsigned Request
     *
     * Send an unsigned HTTP request
     *
     * @param string $request
     * @param array $params
     * @return mixed
     */
    private function send_public_request($request, $params = NULL)
    {
        $ch = curl_init();
        $query = '';

        if (count($params)) {
            $query = '?' . http_build_query($params);
        }

        $url = self::API_URL . $request . $query;

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
        ));

        if (!$result = curl_exec($ch)) {
            return $this->curl_error($ch);
        } else {
            return $this->output($result, $this->is_bitfinex_error($ch));
        }
    }
}