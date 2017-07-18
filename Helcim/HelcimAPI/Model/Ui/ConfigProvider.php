<?php
/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
namespace Helcim\HelcimAPI\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Helcim\HelcimAPI\Gateway\Http\Client\HcmClient;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Model\CcConfig;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'helcim_api';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CcConfig
     */
    protected $ccConfig;

    /**
     * @param ConfigInterface $config
     * @param CcConfig $ccConfig
     */
    public function __construct(
        ConfigInterface $config,
        CcConfig $ccConfig
    ) {
        $this->config = $config;
        $this->ccConfig = $ccConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'ccform' => [
                    'availableTypes' => [
                        self::CODE => $this->getCardTypes()
                    ],
                    'months' => [
                        self::CODE => $this->getMonths()
                    ],
                    'years' => [
                        self::CODE => $this->getYears()
                    ],
                    'hasVerification' => [
                        self::CODE => $this->config->getValue('enable_cvv') ? true : false
                    ],
                    'cvvImageUrl' => [
                        self::CODE => $this->getCvvImageUrl()
                    ]
                ]
            ]
        ];
        return $config;
    }

    /**
     * Get Years
     *
     * @return array
     */
    private function getYears()
    {
        $years = array();
        $year_options = 10;
        $year_counter = 1;
        $year = (int)date('Y');
        while($year_counter <= $year_options){
            $years[$year] = $year;
            $year++;
            $year_counter++;
        }
        return $years;
    }

    /**
     * Get Months
     *
     * @return array
     */
    private function getMonths()
    {
        return [
            1   => '01 - January',
            2   => '02 - February',
            3   => '03 - March',
            4   => '04 - April',
            5   => '05 - May',
            6   => '06 - June',
            7   => '07 - July',
            8   => '08 - August',
            9   => '09 - September',
            10  => '10 - October',
            11  => '11 - November',
            12  => '12 - December'
        ];
    }

    /**
     * Get Card Types
     *
     * @return array
     */
    private function getCardTypes()
    {
        $card_types = array();
        $type_list =[
            'AE' => 'American Express',
            'VI' => 'Visa',
            'MC' => 'MasterCard',
            'DI' => 'Discover'
        ];
        if($this->config->getValue('cctypes') != ''){
            $card_types_array = explode(',',$this->config->getValue('cctypes'));
            if(is_array($card_types_array)){
                foreach($card_types_array as $card_type){
                    $card_types[$card_type] = @$type_list[$card_type];
                }
            }
        }
        return $card_types;
    }

    /**
     * Get CVV Image URL
     *
     * @return string
     */
    private function getCvvImageUrl()
    {
        return $this->ccConfig->getCvvImageUrl();
    }
}