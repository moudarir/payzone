<?php
use GuzzleHttp\Exception\GuzzleException;
use Moudarir\Payzone\PayzoneGateway;

define('ROOT_PATH', dirname(__FILE__, 2).DIRECTORY_SEPARATOR);
require_once ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {
    //$payment = new PayzoneGateway('merchant', 'grEatPassw0rd');
    $payment = new PayzoneGateway('103227', 'Y6d@S52@31E49kK3', 'https://paiement.payzone.ma');
    try {
        $payment
            ->setApiVersion('002.50')
            ->setRequest('get', ['uri' => 'PAYMENT_STATUS', 'param' => '86NeXTOFsKciZt0QqgKtfA'])
            ->setResponse();

        $response = $payment->getResponse();
        dump($response);
    } catch (GuzzleException $e) {
        dump($e->getMessage());
    }
} catch (InvalidArgumentException $exception) {
    dump($exception->getMessage());
}