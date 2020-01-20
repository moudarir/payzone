<?php
namespace Moudarir\Payzone\Config;

class Config {

    /**
     * Valid API Versions
     */
    const VALID_API_VERSIONS = ['002', '002.01', '002.02', '002.03', '002.50'];

    /**
     * API Version
     */
    const DEFAULT_API_VERSION = '002.50';

    /**
     * API Version
     */
    const API_VERSION_KEY = 'apiVersion';

    /**
     * API Endpoint
     */
    const API_ENDPOINT = 'https://connect2.payxpert.com';

    /**
     * Date Timezone
     */
    const TIMEZONE = 'GMT';

    /**
     * Default Payment data key
     */
    const DEFAULT_PAYMENT_DATA_KEY = 'json';

    /**
     * Payment data keys
     */
    const PAYMENT_DATA_KEYS = ['form_params', 'query', 'json', 'multipart'];

    /**
     * API calls routes
     */
    const API_ROUTES = [
        'TRANS_PREPARE' => '/transaction/prepare',
        'PAYMENT_PREPARE' => '/payment/prepare',
        'TRANS_STATUS' => '/transaction/:param/status', // :merchantToken
        'PAYMENT_STATUS' => '/payment/:param/status', // :merchantToken
        'TRANS_REFUND' => '/transaction/:param/refund', // :transactionID
        'TRANS_DOPAY' => '/payment/:param', // :customerToken
        'SUB_CANCEL' => '/subscription/:param/cancel' // :subscriptionID
    ];

    /**
     * Required fields for payment creation
     */
    const REQUIRED_FIELDS = ['orderID', 'currency', 'amount', 'shippingType', 'paymentMode'];

    /**
     * Fields validation
     */
    const FIELDS_VALIDATION = [
        'apiVersion' => ['method' => 'isValidApiVersion'],
        'shopperID' => ['length' => 32, 'method' => 'isString'],
        'shopperEmail' => ['length' => 100, 'method' => 'isEmail'],
        'shipToCountryCode' => ['length' => 2, 'method' => 'isIsoCountryCode'],
        'shopperCountryCode' => ['length' => 2, 'method' => 'isIsoCountryCode'],
        'orderID' => ['length' => 100, 'method' => 'isString'],
        'orderDescription' => ['length' => 500, 'method' => 'isString'],
        'currency' => ['length' => 3, 'method' => 'isString'],
        'amount' => ['method' => 'isInt'],
        'orderTotalWithoutShipping' => ['method' => 'isInt'],
        'orderShippingPrice' => ['method' => 'isInt'],
        'orderDiscount' => ['method' => 'isInt'],
        'orderFOLanguage' => ['length' => 50, 'method' => 'isString'],
        'shippingType' => ['length' => 50, 'method' => 'isShippingType'],
        'shippingName' => ['length' => 50, 'method' => 'isString'],
        'paymentType' => ['length' => 32, 'method' => 'isPayment'],
        'operation' => ['length' => 32, 'method' => 'isOperation'],
        'paymentMode' => ['length' => 30, 'method' => 'isPaymentMode'],
        'offerID' => ['method' => 'isInt'],
        'subscriptionType' => ['length' => 32, 'method' => 'isSubscriptionType'],
        'trialPeriod' => ['length' => 10, 'method' => 'isString'],
        'rebillAmount' => ['method' => 'isInt'],
        'rebillPeriod' => ['length' => 10, 'method' => 'isString'],
        'rebillMaxIteration' => ['method' => 'isInt'],
        'ctrlRedirectURL' => ['length' => 2048, 'method' => 'isAbsoluteUrl'],
        'ctrlCallbackURL' => ['length' => 2048, 'method' => 'isAbsoluteUrl'],
        'ctrlCustomData' => ['length' => 2048],
        'timeOut' => ['length' => 10, 'method' => 'isString'],
        'merchantNotification' => ['method' => 'isBool'],
        'merchantNotificationTo' => ['length' => 100, 'method' => 'isEmail'],
        'merchantNotificationLang' => ['length' => 2, 'method' => 'isString'],
        'themeID' => ['method' => 'isInt']
    ];

    /**
     * Fields to be included in JSON
     */
    const FIELDS_KEYS = [
        'apiVersion',
        'shopperID',
        'shopperEmail',
        'shipToFirstName',
        'shipToLastName',
        'shipToCompany',
        'shipToPhone',
        'shipToAddress',
        'shipToState',
        'shipToZipcode',
        'shipToCity',
        'shipToCountryCode',
        'shopperFirstName',
        'shopperLastName',
        'shopperPhone',
        'shopperAddress',
        'shopperState',
        'shopperZipcode',
        'shopperCity',
        'shopperCountryCode',
        'shopperBirthDate',
        'shopperIDNumber',
        'shopperCompany',
        'shopperLoyaltyProgram',
        'orderID',
        'orderDescription',
        'currency',
        'amount',
        'orderTotalWithoutShipping',
        'orderShippingPrice',
        'orderDiscount',
        'orderFOLanguage',
        'orderCartContent',
        'shippingType',
        'shippingName',
        'paymentType',
        'operation',
        'paymentMode',
        'secure3d',
        'offerID',
        'subscriptionType',
        'trialPeriod',
        'rebillAmount',
        'rebillPeriod',
        'rebillMaxIteration',
        'ctrlCustomData',
        'ctrlRedirectURL',
        'ctrlCallbackURL',
        'timeOut',
        'merchantNotification',
        'merchantNotificationTo',
        'merchantNotificationLang',
        'themeID'
    ];
}