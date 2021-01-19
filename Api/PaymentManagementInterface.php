<?php
namespace YeThird\PayGateway\Api;

/**
 * PaymentManagementInterface
 *
 * @api
 */
interface PaymentManagementInterface
{
    /**
     * @param string $quoteId
     * @return bool
     */
    public function verifyPayment($quoteId);
}
