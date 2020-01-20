<?php
namespace Moudarir\Payzone\Config;

class Constant {

    /**
     * Payment types
     */
    const PAYMENT_TYPE_CREDIT_CARD = 'CreditCard';
    const PAYMENT_TYPE_TODITO_CASH = 'ToditoCash';
    const PAYMENT_TYPE_BANK_TRANSFER = 'BankTransfer';

    /**
     * Payment providers
     */
    const PAYMENT_PROVIDER_SOFORT = 'Sofort';

    /**
     * Operation types
     */
    const OPERATION_TYPE_SALE = 'sale';
    const OPERATION_TYPE_AUTHORIZE = 'authorize';

    /**
     * Payment modes
     */
    const PAYMENT_MODE_SINGLE = 'Single';
    const PAYMENT_MODE_ON_SHIPPING = 'OnShipping';
    const PAYMENT_MODE_RECURRENT = 'Recurrent';
    const PAYMENT_MODE_INSTALMENTS = 'InstalmentsPayments';

    /**
     * Shipping types
     */
    const SHIPPING_TYPE_PHYSICAL = 'Physical';
    const SHIPPING_TYPE_ACCESS = 'Access';
    const SHIPPING_TYPE_VIRTUAL = 'Virtual';

    /**
     * Subscription types
     */
    const SUBSCRIPTION_TYPE_NORMAL = 'normal';
    const SUBSCRIPTION_TYPE_LIFETIME = 'lifetime';
    const SUBSCRIPTION_TYPE_ONETIME = 'onetime';
    const SUBSCRIPTION_TYPE_INFINITE = 'infinite';

    /**
     * Lang
     */
    const LANG_EN = 'en';
    const LANG_FR = 'fr';
    const LANG_ES = 'es';
    const LANG_IT = 'it';

    /**
     * ~~~~
     * Subscription cancel reasons
     * ~~~~
     */
    /**
     * Bank denial
     */
    const SUBSCRIPTION_CANCEL_BANK_DENIAL = 1000;

    /**
     * Canceled due to refund
     */
    const SUBSCRIPTION_CANCEL_REFUNDED = 1001;

    /**
     * Canceled due to retrieval request
     */
    const SUBSCRIPTION_CANCEL_RETRIEVAL = 1002;

    /**
     * Cancellation letter sent by bank
     */
    const SUBSCRIPTION_CANCEL_BANK_LETTER = 1003;

    /**
     * Charge back
     */
    const SUBSCRIPTION_CANCEL_CHARGE_BACK = 1004;

    /**
     * Company account closed
     */
    const SUBSCRIPTION_CANCEL_COMPANY_ACCOUNT_CLOSED = 1005;

    /**
     * Site account closed
     */
    const SUBSCRIPTION_CANCEL_WEBSITE_ACCOUNT_CLOSED = 1006;

    /**
     * Didn't like the site
     */
    const SUBSCRIPTION_CANCEL_DID_NOT_LIKE = 1007;

    /**
     * Disagree ('Did not do it' or 'Do not recognize the transaction')
     */
    const SUBSCRIPTION_CANCEL_DISAGREE = 1008;

    /**
     * Fraud from webmaster
     */
    const SUBSCRIPTION_CANCEL_WEBMASTER_FRAUD = 1009;

    /**
     * I could not get in to the site
     */
    const SUBSCRIPTION_CANCEL_COULD_NOT_GET_INTO = 1010;

    /**
     * No problem, just moving on
     */
    const SUBSCRIPTION_CANCEL_NO_PROBLEM = 1011;

    /**
     * Not enough updates
     */
    const SUBSCRIPTION_CANCEL_NOT_UPDATED = 1012;

    /**
     * Problems with the movies/videos
     */
    const SUBSCRIPTION_CANCEL_TECH_PROBLEM = 1013;

    /**
     * Site was too slow
     */
    const SUBSCRIPTION_CANCEL_TOO_SLOW = 1014;

    /**
     * The site did not work
     */
    const SUBSCRIPTION_CANCEL_DID_NOT_WORK = 1015;

    /**
     * Too expensive
     */
    const SUBSCRIPTION_CANCEL_TOO_EXPENSIVE = 1016;

    /**
     * Un-authorized signup by family member
     */
    const SUBSCRIPTION_CANCEL_UNAUTH_FAMILY = 1017;

    /**
     * Undetermined reasons
     */
    const SUBSCRIPTION_CANCEL_UNDETERMINED = 1018;

    /**
     * Webmaster requested to cancel
     */
    const SUBSCRIPTION_CANCEL_WEBMASTER_REQUESTED = 1019;

    /**
     * I haven't received my item
     */
    const SUBSCRIPTION_CANCEL_NOTHING_RECEIVED = 1020;

    /**
     * The item was damaged or defective
     */
    const SUBSCRIPTION_CANCEL_DAMAGED = 1021;

    /**
     * The box was empty
     */
    const SUBSCRIPTION_CANCEL_EMPTY_BOX = 1022;

    /**
     * The order was incomplete
     */
    const SUBSCRIPTION_CANCEL_INCOMPLETE_ORDER = 1023;

    /**
     * Field content constant
     */
    const UNAVAILABLE = 'NA';
    const UNAVAILABLE_COUNTRY = 'ZZ';

}