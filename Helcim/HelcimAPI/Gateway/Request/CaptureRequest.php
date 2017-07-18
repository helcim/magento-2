<?php
/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
namespace Helcim\HelcimAPI\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class CaptureRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();

        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        return [
            'accountId'         => $this->config->getValue('account_id',$order->getStoreId()),
            'apiToken'          => $this->config->getValue('api_token',$order->getStoreId()),
            'transactionId'     => $payment->getLastTransId(),
            'transactionType'   => 'capture',
            'test'              => $this->config->getValue('test',$order->getStoreId()),
            'currency'          => $order->getCurrencyCode(),
            'amount'            => $order->getGrandTotalAmount(),
            ];
    }
}
