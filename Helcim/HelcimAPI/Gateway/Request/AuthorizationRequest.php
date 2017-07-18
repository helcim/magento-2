<?php
/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
namespace Helcim\HelcimAPI\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AuthorizationRequest implements BuilderInterface
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
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        if(is_object($order)){
            $billing = $order->getBillingAddress();
            $shipping = $order->getShippingAddress();
            $items = $order->getItems();
        }

        // SET
        $request = [
            'accountId'         => $this->config->getValue('account_id'),
            'apiToken'          => $this->config->getValue('api_token'),
            'transactionType'   => 'preauth',
            'test'              => $this->config->getValue('test')
            ];

        // UPDATE REQUEST
        $request = array_merge($request,$this->addOrderFields($order));
        $request = array_merge($request,$this->addCardFields($payment));
        $request = array_merge($request,$this->addBillingFields($billing));
        $request = array_merge($request,$this->addShippingFields($shipping));
        $request = array_merge($request,$this->addItemsFields($items));

        // RETURN
        return $request;
    }

    /**
     * ORDER FIELDS
     *
     * @param object $order
     * @return array
     */
    private function addOrderFields($order)
    {
        // DEFAULT
        $result = array();

        if(is_object($order)){
            if($order->getCurrencyCode() != ''){
                $result['currency'] = $order->getCurrencyCode();
            }
            if($order->getGrandTotalAmount() != ''){
                $result['amount'] = $order->getGrandTotalAmount();
            }
            if($order->getCustomerId() != ''){
                $result['customerCode'] = $order->getCustomerId();
            }
            if($order->getOrderIncrementId() != ''){
                $result['orderNumber'] = $order->getOrderIncrementId();
            }
            $result['amountShipping'] = '';
            $result['amountTax'] = '';
            $result['amountDiscount'] = '';
            $result['comments'] = 'Created In Magento';
        }

        // RETURN
        return $result;
    }

    /**
     * CARD FIELDS
     *
     * @param object $payment
     * @return array
     */
    private function addCardFields($payment)
    {
        // DEFAULT
        $result = array();

        if(is_object($payment)){
            if($payment->getAdditionalInformation('cc_number') != ''){
                $result['cardNumber'] = $payment->getAdditionalInformation('cc_number');
            }
            if($payment->getAdditionalInformation('cc_exp_month') != '' and $payment->getAdditionalInformation('cc_exp_year') != ''){
                $month = str_pad($payment->getAdditionalInformation('cc_exp_month'),2,'0',STR_PAD_LEFT);
                $year = substr($payment->getAdditionalInformation('cc_exp_year'),-2);
                $result['cardExpiry'] = $month.$year;
            }
            if($payment->getAdditionalInformation('cc_cid') != ''){
                $result['cardCVV'] = $payment->getAdditionalInformation('cc_cid');
            }
            if($payment->getData('shipping_amount') != ''){
                $result['amountShipping'] = $payment->getData('shipping_amount');
            }
            $result['cardHolderName'] = '';
            $result['cardHolderAddress'] = '';
            $result['cardHolderPostalCode'] = '';
        }

        // RETURN
        return $result;
    }

    /**
     * BILLING FIELDS
     *
     * @param object $billing
     * @return array
     */
    private function addBillingFields($billing)
    {
        // DEFAULT
        $result = array();

        if(is_object($billing)){
            if($billing->getFirstname() != '' or $billing->getLastname() != ''){
                $result['billing_contactName'] = trim($billing->getFirstname().' '.$billing->getLastname());
                $result['cardHolderName'] = $result['billing_contactName'];
            }
            if($billing->getCompany() != ''){
                $result['billing_businessName'] = $billing->getCompany();
            }
            if($billing->getStreetLine1() != ''){
                $result['billing_street1'] = $billing->getStreetLine1();
                $result['cardHolderAddress'] = $result['billing_street1'];
            }
            if($billing->getStreetLine2() != ''){
                $result['billing_street2'] = $billing->getStreetLine2();
            }
            if($billing->getCity() != ''){
                $result['billing_city'] = $billing->getCity();
            }
            if($billing->getRegionCode() != ''){
                $result['billing_province'] = $billing->getRegionCode();
            }
            if($billing->getPostcode() != ''){
                $result['billing_postalCode'] = $billing->getPostcode();
                $result['cardHolderPostalCode'] = $result['billing_postalCode'];
            }
            if($billing->getCountryId() != ''){
                $result['billing_country'] = $billing->getCountryId();
            }
            if($billing->getTelephone() != ''){
                $result['billing_phone'] = $billing->getTelephone();
            }
            if($billing->getEmail() != ''){
                $result['billing_email'] = $billing->getEmail();
            }
            if($billing->getCustomerId() != ''){
                $result['billing_customer_id'] = $billing->getCustomerId();
            }
        }

        // RETURN
        return $result;
    }

    /**
     * SHIPPING FIELDS
     *
     * @param object $shipping
     * @return array
     */
    private function addShippingFields($shipping)
    {
        // DEFAULT
        $result = array();

        if(is_object($shipping)){
            if($shipping->getFirstname() != '' or $shipping->getLastname() != ''){
                $result['shipping_contactName'] = trim($shipping->getFirstname().' '.$shipping->getLastname());
            }
            if($shipping->getCompany() != ''){
                $result['shipping_businessName'] = $shipping->getCompany();
            }
            if($shipping->getStreetLine1() != ''){
                $result['shipping_street1'] = $shipping->getStreetLine1();
            }
            if($shipping->getStreetLine2() != ''){
                $result['shipping_street2'] = $shipping->getStreetLine2();
            }
            if($shipping->getCity() != ''){
                $result['shipping_city'] = $shipping->getCity();
            }
            if($shipping->getRegionCode() != ''){
                $result['shipping_province'] = $shipping->getRegionCode();
            }
            if($shipping->getPostcode() != ''){
                $result['shipping_postalCode'] = $shipping->getPostcode();
            }
            if($shipping->getCountryId() != ''){
                $result['shipping_country'] = $shipping->getCountryId();
            }
            if($shipping->getTelephone() != ''){
                $result['shipping_phone'] = $shipping->getTelephone();
            }
            if($shipping->getEmail() != ''){
                $result['shipping_email'] = $shipping->getEmail();
            }
            if($shipping->getCustomerId() != ''){
                $result['shipping_customer_id'] = $shipping->getCustomerId();
            }
        }

        // RETURN
        return $result;
    }

    /**
     * ITEMS FIELDS
     *
     * @param array $items
     * @return array
     */
    private function addItemsFields($items)
    {
        // DEFAULT
        $result = array();
        $result['amountTax'] = 0;

        if(is_array($items)){
            $item_counter = 1;
            foreach($items as $item){
                $result['itemSKU'.$item_counter] = $item->getSku();
                $result['itemDescription'.$item_counter] = $item->getName();
                // $result['itemSerialNumber'.$item_counter] = $item->getCustomerId();
                $result['itemQuantity'.$item_counter] = $item->getQtyOrdered();
                $result['itemPrice'.$item_counter] = $item->getPrice();
                $result['itemTotal'.$item_counter] = $item->getRowTotal();

                $result['amountTax'] += $item->getTaxAmount();
                
                $item_counter++;
            }
        }

        // RETURN
        return $result;
    }

}
