<?php
namespace Moudarir\Payzone\Utils;

use Tightenco\Collect\Support\Collection;

class PaymentData {

    /**
     * @var Collection|null
     */
    private $payment_data;

    /**
     * Status of the payment: "Authorized", "Not authorized", "Expired", "Call failed", "Pending" or "Not processed"
     *
     * @var string|null
     */
    private $status;

    /**
     * The merchant token of this payment
     *
     * @var string|null
     */
    private $merchantToken;

    /**
     * Type of operation for the last transaction done for this payment: Can be sale or authorize.
     *
     * @var string|null
     */
    private $operation;

    /**
     * Result code of the last transaction done for this payment
     *
     * @var string|int|null
     */
    private $errorCode;

    /**
     * Error message of the last transaction done for this payment
     *
     * @var string|null
     */
    private $errorMessage;

    /**
     * The order ID of the payment
     *
     * @var string|null
     */
    private $orderID;

    /**
     * Currency for the payment
     *
     * @var string|null
     */
    private $currency;

    /**
     * Amount of the payment in cents (1.00€ => 100)
     *
     * @var int|null
     */
    private $amount;

    /**
     * Custom data provided by merchant at payment creation.
     *
     * @var string|null
     */
    private $ctrlCustomData;

    /**
     * The list of transactions done to complete this payment
     *
     * @var Collection|null
     */
    private $transactions;

    /**
     * PaymentStatus constructor.
     *
     * @param Collection $data
     */
    public function __construct (Collection $data) {
        $this->payment_data = $data;
    }

    /**
     * @return string|null
     */
    public function getStatus (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('status')):
            $this->status = $this->payment_data->get('status');
        endif;

        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getMerchantToken (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('merchantToken')):
            $this->merchantToken = $this->payment_data->get('merchantToken');
        endif;

        return $this->merchantToken;
    }

    /**
     * @return string|null
     */
    public function getOperation (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('operation')):
            $this->operation = $this->payment_data->get('operation');
        endif;

        return $this->operation;
    }

    /**
     * @return string|int|null
     */
    public function getErrorCode () {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('errorCode')):
            $code = $this->payment_data->get('errorCode');
            $this->errorCode = $code !== '000' ? (int)$code : $code;
        endif;

        return $this->errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('errorMessage')):
            $this->errorMessage = $this->payment_data->get('errorMessage');
        endif;

        return $this->errorMessage;
    }

    /**
     * @return string|null
     */
    public function getOrderID (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('orderID')):
            $this->orderID = $this->payment_data->get('orderID');
        endif;

        return $this->orderID;
    }

    /**
     * @return string|null
     */
    public function getCurrency (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('currency')):
            $this->currency = $this->payment_data->get('currency');
        endif;

        return $this->currency;
    }

    /**
     * @return int|null
     */
    public function getAmount (): ?int {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('amount')):
            $this->amount = (int)$this->payment_data->get('amount');
        endif;

        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function getCtrlCustomData (): ?string {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('ctrlCustomData')):
            $this->ctrlCustomData = $this->payment_data->get('ctrlCustomData');
        endif;

        return $this->ctrlCustomData;
    }

    /**
     * @return Collection|null
     */
    public function getTransactions (): ?Collection {
        if ($this->payment_data->isNotEmpty() && $this->payment_data->has('transactions')):
            $trans = $this->payment_data->get('transactions');
            $transactions = (is_array($trans) && !empty($trans)) ? collect($trans) : [];
            $this->transactions = collect($transactions);
        endif;

        return $this->transactions;
    }

}