<?php
namespace Moudarir\Payzone\Utils;

use InvalidArgumentException;
use Moudarir\Payzone\Config\Config;
use Moudarir\Payzone\Config\Constant;

class Validator {

    /**
     * @param array $data
     * @param bool $requirements
     * @throws InvalidArgumentException
     */
    public function validateData (array $data, bool $requirements = false) {
        if (empty($data)):
            throw new InvalidArgumentException('The Payment Data must not be empty.');
        endif;

        $collect = collect($data);
        $requiredFields = Config::REQUIRED_FIELDS;
        $fieldsValidation = Config::FIELDS_VALIDATION;

        if ($requirements):
            foreach ($requiredFields as $field):
                if ($collect->has($field) === false):
                    throw new InvalidArgumentException('The "'.$field.'" param is required.');
                endif;
            endforeach;
        endif;

        $collect->each(function ($item, $field) use ($requiredFields, $fieldsValidation) {
            if (array_key_exists($field, $requiredFields) && self::isEmpty($item)):
                throw new InvalidArgumentException('The "'.$field.'" param can\'t be empty.');
            endif;

            if (array_key_exists($field, $fieldsValidation) && !self::isEmpty($item)):
                $validation = $fieldsValidation[$field];
                if (array_key_exists('method', $validation)):
                    $method = $validation['method'];
                    $isMethod = call_user_func(array(self::class, $method), $item, $field);
                    if ($isMethod !== null):
                        throw new InvalidArgumentException($isMethod);
                    endif;
                endif;

                if (array_key_exists('length', $validation)):
                    $length = $validation['length'];
                    if (self::strLength($item) > $length):
                        throw new InvalidArgumentException('The max length for "'.$field.'" param is "'.$length.'" chars.');
                    endif;
                endif;
            endif;
        });
    }

    /**
     * @param mixed $field
     * @return bool
     */
    public static function isEmpty ($field): bool {
        return $field === '' || $field === null;
    }

    /**
     * @param string $str
     * @return int size of the string
     */
    public static function strLength ($str): int {
        if (function_exists('mb_strlen')):
            return mb_strlen($str, 'UTF-8');
        endif;

        return strlen($str);
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isValidApiVersion ($item, string $fieldName): ?string {
        $versions = Config::VALID_API_VERSIONS;
        return in_array($item, $versions) === false ? 'The "'.$fieldName.'" param is not valid.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isString ($item, string $fieldName): ?string {
        return is_string($item) === false ? 'The "'.$fieldName.'" param is not a valid string type.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isInt ($item, string $fieldName): ?string {
        $isInt = (is_int($item) || ctype_digit($item));

        return $isInt === false ? 'The "'.$fieldName.'" param is not a valid integer type.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isFloat ($item, string $fieldName): ?string {
        $isFloat = strval((float)$item) == strval($item);

        return $isFloat === false ? 'The "'.$fieldName.'" param is not a valid float type.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isBool ($item, string $fieldName): ?string {
        $isBool = is_null($item) || is_bool($item) || preg_match('/^0|1$/', $item);

        return $isBool === false ? 'The "'.$fieldName.'" param is not a valid Boolean type.' : null;
    }

    /**
     * @param string $json
     * @param bool $assoc
     * @return mixed|null
     */
    public static function isJson (string $json, bool $assoc = true) {
        $result = json_decode($json, $assoc);

        if (JSON_ERROR_NONE !== json_last_error()):
            return null;
        endif;

        return $result;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isEmail ($item, string $fieldName): ?string {
        $isEmail = preg_match('/^[a-z0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $item);

        return $isEmail === false ? 'The "'.$fieldName.'" param is not valid Email address.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isIpAddress ($item, string $fieldName): ?string {
        $isIpAddress = preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $item);

        return $isIpAddress === false ? 'The "'.$fieldName.'" param is not valid IP address.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isIsoCountryCode ($item, string $fieldName) {
        $isISO = preg_match('/^[a-zA-Z]{2}$/', $item);

        return $isISO === false ? 'The "'.$fieldName.'" param is not valid ISO Country code.' : null;
    }

    /**
     * @param mixed $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isCountryName ($item, string $fieldName) {
        $isCountryName = preg_match('/^[a-zA-Z -]+$/', $item);

        return $isCountryName === false ? 'The "'.$fieldName.'" param is not valid Country name.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isShippingType (string $item, string $fieldName): ?string {
        $isShippingType = ($item === Constant::SHIPPING_TYPE_PHYSICAL || $item === Constant::SHIPPING_TYPE_VIRTUAL || $item === Constant::SHIPPING_TYPE_ACCESS);

        return $isShippingType === false ? 'The "'.$fieldName.'" param is not valid Shipping Type.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isPayment (string $item, string $fieldName): ?string {
        $isPayment = ($item === Constant::PAYMENT_TYPE_CREDIT_CARD || $item === Constant::PAYMENT_TYPE_TODITO_CASH || $item === Constant::PAYMENT_TYPE_BANK_TRANSFER);

        return $isPayment === false ? 'The "'.$fieldName.'" param is not a valid Payment Type.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isOperation (string $item, string $fieldName): ?string {
        $isOperation = ($item === Constant::OPERATION_TYPE_SALE || $item === Constant::OPERATION_TYPE_AUTHORIZE);

        return $isOperation === false ? 'The "'.$fieldName.'" param is not a valid Operation Type.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isPaymentMode (string $item, string $fieldName): ?string {
        $isPaymentMode = ($item === Constant::PAYMENT_MODE_SINGLE || $item === Constant::PAYMENT_MODE_ON_SHIPPING || $item === Constant::PAYMENT_MODE_RECURRENT || $item === Constant::PAYMENT_MODE_INSTALMENTS);

        return $isPaymentMode === false ? 'The "'.$fieldName.'" param is not a valid Payment Mode.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isSubscriptionType (string $item, string $fieldName): ?string {
        $isSubscriptionType = ($item === Constant::SUBSCRIPTION_TYPE_NORMAL || $item === Constant::SUBSCRIPTION_TYPE_INFINITE || $item === Constant::SUBSCRIPTION_TYPE_ONETIME || $item === Constant::SUBSCRIPTION_TYPE_LIFETIME);

        return $isSubscriptionType === false ? 'The "'.$fieldName.'" param is not a valid Subscription Type.' : null;
    }

    /**
     * @param string $item
     * @param string $fieldName
     * @return string|null
     */
    public static function isAbsoluteUrl (string $item, string $fieldName): ?string {
        if (!empty($item)):
            $isAbsoluteUrl = preg_match('/^https?:\/\/[:#%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $item);

            return $isAbsoluteUrl === false ? 'The "'.$fieldName.'" param is not a valid URL.' : null;
        endif;

        return null;
    }

}