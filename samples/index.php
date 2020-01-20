<?php
use GuzzleHttp\Exception\GuzzleException;
use Moudarir\Payzone\Config\Constant;
use Moudarir\Payzone\PayzoneGateway;

define('ROOT_PATH', dirname(__FILE__, 2).DIRECTORY_SEPARATOR);
require_once ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

session_start();

/**
 * "customerToken" => "fbhqxf668o"
 * "merchantToken" => "hg2pMd2UzjMue9h6cVrjpw"
 */

try {
    //$payment = new PayzoneGateway('merchant', 'grEatPassw0rd');
    $payment = new PayzoneGateway('103227', 'Y6d@S52@31E49kK3', 'https://paiement.payzone.ma');

    if (isset($_SESSION['payment']) && $_SESSION['payment'] !== null):
        dd($_SESSION['payment']);
    else:
        try {
            $params = [
                //'http_errors' => false,
                'json' => [
                    'apiVersion' => '002.50',
                    'secure3d' => true,
                    'amount' => 3200,
                    'shopperID' => 'shopperID-'.time(),
                    'shopperFirstName' => 'John',
                    'shopperLastName' => 'DOE',
                    'shopperAddress' => 'This is the shopper address',
                    'shopperZipcode' => '20200',
                    'shopperCity' => 'Casablanca',
                    'shopperState' => 'Casablanca',
                    'shopperCountryCode' => 'MA',
                    'shopperPhone' => '+212661909090',
                    'shopperEmail' => 'shopper.email@example.com',
                    'orderID' => 'orderID-'.time(),
                    'orderDescription' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. A amet consequuntur eligendi excepturi fuga id incidunt ipsam iste, laborum, officia quaerat quas ratione sapiente similique sunt velit veniam vero voluptas?',
                    'currency' => 'MAD',
                    'shippingType' => Constant::SHIPPING_TYPE_VIRTUAL,
                    'paymentType' => Constant::PAYMENT_TYPE_CREDIT_CARD,
                    'operation' => Constant::OPERATION_TYPE_AUTHORIZE,
                    'paymentMode' => Constant::PAYMENT_MODE_SINGLE,
                    'ctrlRedirectURL' => 'http://payzone.api/samples/payment-return.php',
                    'ctrlCallbackURL' => 'http://payzone.api/samples/payment-callback.php',
                    'merchantNotification' => true,
                    'merchantNotificationTo' => 'tkharbi9@gmail.com',
                    'merchantNotificationLang' => 'fr'
                ]
            ];
            $payment
                ->setRequestParams($params)
                ->setRequest('post', 'PAYMENT_PREPARE')
                ->setResponse();

            $response = $payment->getResponse();
            if ($response['error'] === true):
                dd($response);
            else:
                $_SESSION['payment'] = $response;
                header('Location: ' . $payment->getCustomerRedirectURL());
                exit();
            endif;
        } catch (GuzzleException $e) {
            dump($e->getMessage());
        }
    endif;


} catch (InvalidArgumentException $exception) {
    dump($exception->getMessage());
}

