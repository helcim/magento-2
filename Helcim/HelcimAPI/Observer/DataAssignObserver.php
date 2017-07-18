<?php
/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
namespace Helcim\HelcimAPI\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additional_data = $data->getData('additional_data');

        if(is_array($additional_data)){

            $method = $this->readMethodArgument($observer);
            $paymentInfo = $method->getInfoInstance();

            if(isset($additional_data['cc_number'])){
                $paymentInfo->setAdditionalInformation(
                    'cc_number',
                    $additional_data['cc_number']
                );
            }
            if(isset($additional_data['cc_exp_month'])){
                $paymentInfo->setAdditionalInformation(
                    'cc_exp_month',
                    $additional_data['cc_exp_month']
                );
            }
            if(isset($additional_data['cc_exp_year'])){
                $paymentInfo->setAdditionalInformation(
                    'cc_exp_year',
                    $additional_data['cc_exp_year']
                );
            }
            if(isset($additional_data['cc_cid'])){
                $paymentInfo->setAdditionalInformation(
                    'cc_cid',
                    $additional_data['cc_cid']
                );
            }
        }
    }
}
