<?php
namespace Moudarir\Payzone\Utils;

use Exception;
use Tightenco\Collect\Support\Collection;

class PaymentHandler {

    /**
     * @var Collection|null
     */
    private static $handler_data;

    /**
     * @var PaymentData|null
     */
    private $payment_data;

    /**
     * PaymentHandler constructor.
     *
     * @param string $encrypted
     * @param string $merchantToken
     * @throws Exception
     */
    public function __construct (string $encrypted, string $merchantToken) {
        if (self::$handler_data === null || !is_array(self::$handler_data)):
            $key = self::urlSafeBase64Decode($merchantToken);
            $binData = self::urlSafeBase64Decode($encrypted);

            // Decrypting
            $decrypt = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $binData, MCRYPT_MODE_ECB);

            if ($decrypt):
                // Remove PKCS#5 padding
                $json = $this->pkcs5Unpad($decrypt);
                $data = Validator::isJson($json, true);

                if (is_array($data)):
                    throw new Exception('Error: invalid Json format.');
                endif;

                self::$handler_data = collect($data);
            else:
                throw new Exception('Error occurred during decryption.');
            endif;
        endif;
    }

    /**
     * @return Collection|null
     */
    public static function getHandlerData (): ?Collection {
        return self::$handler_data;
    }

    /**
     * @return PaymentData|null
     */
    public function getPaymentData (): ?PaymentData {
        if (self::$handler_data->isNotEmpty()):
            $this->payment_data = new PaymentData(self::$handler_data);
        endif;

        return $this->payment_data;
    }

    /**
     * @param string $str
     * @return string|null
     */
    private static function urlSafeBase64Decode (string $str): ?string {
        $safe = base64_decode(strtr($str, '-_', '+/'));

        return $safe !== false ? $safe : null;
    }

    /**
     * @param string $text
     * @return string|null
     */
    private static function pkcs5Unpad (string $text): ?string {
        $pad = ord($text{strlen($text) - 1});

        // The initial text was empty
        if ($pad > strlen($text)):
            return null;
        endif;

        // The length of the padding sequence is incorrect
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad):
            return null;
        endif;

        $str = substr($text, 0, -1 * $pad);

        return $str !== false ? $str : null;
    }

    /**
     * @param $text
     * @return string
     */
    private function pkcs5Pad ($text) {
        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $pad = $blockSize - (strlen($text) % $blockSize);

        return $text.str_repeat(chr($pad), $pad);
    }

}