<?php
namespace Moudarir\Payzone;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Moudarir\Payzone\Config\Config;
use Moudarir\Payzone\Utils\PaymentHandler;
use Moudarir\Payzone\Utils\Validator;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

class PayzoneGateway {

    /**
     * @var Client|null
     */
    private static $client;

    /**
     * @var string|null
     */
    private static $merchant_username;

    /**
     * @var string|null
     */
    private static $merchant_password;

    /**
     * @var string|null
     */
    private static $base_uri;

    /**
     * @var mixed|ResponseInterface
     */
    private $request;

    /**
     * @var array|null
     */
    private $response;

    /**
     * @var string|null
     */
    private $api_version;

    /**
     * @var array
     */
    private $request_params = [];

    /**
     * @var array|null
     */
    private $request_options;

    /**
     * @var array|null
     */
    private $payment_status;

    /**
     * PayzoneGateway constructor.
     *
     * @param string $username
     * @param string $password
     * @param string|null $endpoint
     */
    public function __construct (string $username, string $password, ?string $endpoint = null) {
        self::setMerchantUsername($username);
        self::setMerchantPassword($password);
        self::setClient($endpoint);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function __get (string $key) {
        if (is_array($this->response)):
            if (array_key_exists($key, $this->response)):
                return $this->response[$key];
            endif;
        endif;

        return null;
    }

    /**
     * @param string $method GET | POST
     * @param mixed $route
     * @return self
     * @throws GuzzleException
     */
    public function setRequest (string $method, $route): self {
        $uri = $this->setRequestUri($route);
        $options = $this->setRequestOptions(!is_array($route));
        $this->request = self::getClient()->request($method, $uri, $options);

        return $this;
    }

    /**
     * @param string|null $version
     * @return self
     */
    public function setApiVersion (?string $version = null): self {
        if (!Validator::isEmpty($version) && is_string($version)):
            $this->api_version = $version;
            $this->request_params[Config::DEFAULT_PAYMENT_DATA_KEY][Config::API_VERSION_KEY] = $version;
        endif;

        return $this;
    }

    /**
     * @param array $params
     * @return self
     */
    public function setRequestParams (array $params): self {
        if (!empty($params)):
            $dataKeys = Config::PAYMENT_DATA_KEYS;
            $key = Config::DEFAULT_PAYMENT_DATA_KEY;

            foreach ($dataKeys as $dataKey):
                if (array_key_exists($dataKey, $params)):
                    if ($key !== $dataKey):
                        $params[$key] = $params[$dataKey];
                        unset($params[$dataKey]);
                    endif;
                    break;
                endif;
            endforeach;

            if (array_key_exists($key, $params) && is_array($params[$key])):
                if (!array_key_exists(Config::API_VERSION_KEY, $params[$key])):
                    if ($this->api_version !== null):
                        $params[$key][Config::API_VERSION_KEY] = $this->api_version;
                    endif;
                endif;
            endif;

            $this->request_params = array_merge($this->request_params, $params);
        endif;

        return $this;
    }

    /**
     * @return self
     */
    public function setResponse (): self {
        $requestBody = $this->request->getBody();
        if (array_key_exists('stream', $this->request_params) && $this->request_params['stream'] === true):
            $content = '';
            while (!$requestBody->eof()):
                $content .= $requestBody->read(1024);
            endwhile;
            $requestBody->close();
        else:
            $content = $requestBody->getContents();
        endif;

        $response = ['error' => false];

        if ($content === 'Access denied'):
            $response['error'] = true;
            $response['message'] = $content;
        else:
            $hasError = false;
            $jsonify = Validator::isJson($content, true);

            if (array_key_exists('code', $jsonify) || array_key_exists('errorCode', $jsonify)):
                if (array_key_exists('code', $jsonify)):
                    if ((int)$jsonify['code'] !== 200):
                        $hasError = true;
                        $response['error'] = true;
                        $response['message'] = $jsonify['message'];
                        $response['data'] = $jsonify;
                    endif;
                endif;

                if (array_key_exists('errorCode', $jsonify)):
                    $hasError = true;
                    $response['error'] = true;
                    $response['message'] = $jsonify['errorMessage'];
                    $response['data'] = $jsonify;
                endif;
            endif;

            if (!$hasError):
                $response = array_merge($response, $jsonify);
            endif;
        endif;

        $this->response = $response;

        return $this;
    }

    /**
     * @return Client|null
     */
    public static function getClient (): ?Client {
        return self::$client;
    }

    /**
     * @return string|null
     */
    public static function getMerchantUsername (): ?string {
        return self::$merchant_username;
    }

    /**
     * @return string|null
     */
    public static function getMerchantPassword (): ?string {
        return self::$merchant_password;
    }

    /**
     * @return string|null
     */
    public function getApiVersion (): ?string {
        return $this->api_version;
    }

    /**
     * @return array|null
     */
    public function getResponse (): ?array {
        return $this->response;
    }

    /**
     * Returns the URL to redirect the customer to after a transaction
     * creation.
     *
     * @param string|null $customerToken
     * @return string
     */
    public function getCustomerRedirectURL (?string $customerToken = null): string {
        $customerToken = $customerToken ?: $this->response['customerToken'];
        return self::$base_uri.str_replace(':param', $customerToken, Config::API_ROUTES['TRANS_DOPAY']);
    }

    /**
     * Handle the data received by the POST done when payment page redirects
     * the customer to the merchant website.
     * This will populate the status field that can be retrieved by calling
     * getStatus().
     *
     * @param string $encryptedData
     * @param string $merchantToken
     * @return boolean True on success or false on error
     */
    public function handleRedirectStatus ($encryptedData, $merchantToken) {
        $key = $this->urlSafeBase64Decode($merchantToken);
        $binData = $this->urlSafeBase64Decode($encryptedData);

        // Decrypting
        $json = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $binData, MCRYPT_MODE_ECB);

        if ($json):
            // Remove PKCS#5 padding
            $json = $this->pkcs5Unpad($json);
            $status = Validator::isJson($json);
            //$status = json_decode($json, false);

            if ($status !== null && is_object($status)):
                $this->initStatus($status);
                return true;
            endif;
        endif;

        return false;
    }

    /**
     * @param string|null $endpoint
     */
    private static function setClient (?string $endpoint = null): void {
        if (self::$client === null):
            self::$base_uri = $endpoint ?: Config::API_ENDPOINT;
            self::$client = new Client([
                'base_uri' => self::$base_uri
            ]);
        endif;
    }

    /**
     * @param string $merchantUsername
     * @throws InvalidArgumentException
     */
    private static function setMerchantUsername (string $merchantUsername): void {
        if (Validator::isEmpty($merchantUsername)):
            throw new InvalidArgumentException('Merchant Username must be set.');
        endif;

        if (self::$merchant_username === null):
            self::$merchant_username = $merchantUsername;
        endif;
    }

    /**
     * @param string $merchantPassword
     * @throws InvalidArgumentException
     */
    private static function setMerchantPassword (string $merchantPassword): void {
        if (Validator::isEmpty($merchantPassword)):
            throw new InvalidArgumentException('Merchant Password must be set.');
        endif;

        if (self::$merchant_password === null):
            self::$merchant_password = $merchantPassword;
        endif;
    }

    /**
     * @param mixed $route
     * @throws InvalidArgumentException
     * @return string
     */
    private function setRequestUri ($route): string {
        $routes = Config::API_ROUTES;
        $param = null;

        if (is_array($route)):
            $uri = $route['uri'];
            $param = $route['param'];
        else:
            $uri = $route;
        endif;

        if (!array_key_exists($uri, $routes)):
            throw new InvalidArgumentException('The Payment uri is not valid.');
        endif;

        return $param !== null ? str_replace(':param', $param, $routes[$uri]) : $routes[$uri];
    }

    /**
     * @param bool $requirements
     * @throws InvalidArgumentException
     * @return array
     */
    private function setRequestOptions (bool $requirements = false): array {
        if (!empty($this->request_params)):
            $key = Config::DEFAULT_PAYMENT_DATA_KEY;
            if (array_key_exists($key, $this->request_params)):
                (new Utils\Validator)->validateData($this->request_params[$key], $requirements);
            endif;
        endif;

        $default = [
            'auth' => [self::getMerchantUsername(), self::getMerchantPassword()],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];

        $this->request_options = array_merge($default, $this->request_params);

        return $this->request_options;
    }

    /**
     * @param string $str
     * @return string|null
     */
    private function urlSafeBase64Decode (string $str): ?string {
        $safe = base64_decode(strtr($str, '-_', '+/'));

        return $safe !== false ? $safe : null;
    }

    /**
     * @param string $text
     * @return string|null
     */
    private function pkcs5Unpad (string $text): ?string {
        $pad = ord($text{strlen($text) - 1});

        // The initial text was empty
        if ($pad > strlen($text)):
            return '';
        endif;

        // The length of the padding sequence is incorrect
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad):
            return null;
        endif;

        $str = substr($text, 0, -1 * $pad);

        return $str !== false ? $str : null;
    }

    private function initStatus($status) {
        if ($status != null && is_object($status)) {
            // Root element, PaymentStatus
            $this->status = new PaymentHandler();
            try {
                $reflector = new ReflectionClass('PaymentStatus');
                $this->copyScalarProperties($reflector->getProperties(), $status, $this->status);
            } catch (ReflectionException $e) {
            }


            // Transaction attempts
            if (isset($status->transactions) && is_array($status->transactions)) {
                $transactionAttempts = array();
                foreach ($status->transactions as $transaction) {
                    $transAttempt = new TransactionAttempt();

                    $reflector = new ReflectionClass('TransactionAttempt');
                    $this->copyScalarProperties($reflector->getProperties(), $transaction, $transAttempt);

                    // Set the shopper
                    if (isset($transaction->shopper) && is_object($transaction->shopper)) {
                        $shopper = new Shopper();
                        $reflector = new ReflectionClass('Shopper');
                        $this->copyScalarProperties($reflector->getProperties(), $transaction->shopper, $shopper);
                        $transAttempt->setShopper($shopper);
                    }

                    // Payment Mean Info
                    if (isset($transaction->paymentType) && isset($transaction->paymentMeanInfo) && is_object($transaction->paymentMeanInfo)) {
                        $paymentMeanInfo = null;
                        switch ($transaction->paymentType) {
                            case self::_PAYMENT_TYPE_CREDITCARD:
                                $paymentMeanInfo = $this->extractCreditCardPaymentMeanInfo($transaction->paymentMeanInfo);
                                break;
                            case self::_PAYMENT_TYPE_TODITOCASH:
                                $paymentMeanInfo = $this->extractToditoCashPaymentMeanInfo($transaction->paymentMeanInfo);
                                break;
                            case self::_PAYMENT_TYPE_BANKTRANSFER:
                                $paymentMeanInfo = $this->extractBankTransferPaymentMeanInfo($transaction->paymentMeanInfo);
                                break;
                        }

                        if ($paymentMeanInfo !== null) {
                            $transAttempt->setPaymentMeanInfo($paymentMeanInfo);
                        }
                    }

                    $transactionAttempts[] = $transAttempt;
                }

                $this->status->setTransactions($transactionAttempts);
            }
        }
    }

}