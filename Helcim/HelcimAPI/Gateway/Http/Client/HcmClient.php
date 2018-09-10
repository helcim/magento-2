<?php
/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
namespace Helcim\HelcimAPI\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

class HcmClient implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {

        //
        // REQUEST
        //

        // SET CURL OPTIONS
        $curlOptions = array(CURLOPT_RETURNTRANSFER => 1,
                          CURLOPT_AUTOREFERER => TRUE,
                          CURLOPT_FRESH_CONNECT => FALSE,
                          CURLOPT_SSLVERSION => 6,
                          CURLOPT_HEADER => FALSE,
                          CURLOPT_POST => TRUE,
                          CURLOPT_POSTFIELDS => http_build_query($transferObject->getBody()),
                          CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
                          CURLOPT_COOKIE => 'cookie.txt',
                          CURLOPT_COOKIEJAR => 'cookie_jar.txt',
                          CURLOPT_FOLLOWLOCATION => TRUE,
                          CURLOPT_CUSTOMREQUEST => $transferObject->getMethod(),
                          CURLINFO_HEADER_OUT => FALSE,
                          CURLOPT_TIMEOUT => 30 );
        
        // INITIALIZE CURL
        $curl = curl_init($transferObject->getUri());
        curl_setopt_array($curl,$curlOptions);
        $stringXML = trim(curl_exec($curl));
        curl_close($curl);

        // LOG
        $this->logger->debug(
            [
                'request' => $transferObject->getBody(),
                'response' => $stringXML
            ]
        );

        //
        // RESPONSE
        //

        $response = $this->formatResponse($stringXML);

        if(!isset($response['response'])){
          $errorMessage = 'Missing Gateway Response';
        }elseif($response['response'] != self::SUCCESS){
          if(@$response['responseMessage'] != ''){
            $errorMessage = $response['responseMessage'];
          }else{
            $errorMessage = 'Declined By Helcim';
          }
        }
        if(isset($errorMessage)){
          $this->logger->debug(array('Transaction has been declined - '.$errorMessage));
          throw new \Magento\Payment\Gateway\Http\ClientException(__('Transaction has been declined - '.$errorMessage));
        }
        return $response;
    }

    /**
     * Format response
     *
     * @return array
     */
    protected function formatResponse($helcimResponse)
    {

        // SET DEFAULT
        $response = array();
        $response['response'] = self::FAILURE;

        // TRIM
        $helcimResponse = trim($helcimResponse);

        // CHECK IF VALID XML
        if(stripos($helcimResponse,'<?xml') !== 0){

            // ERROR
            $response['responseMessage'] = 'INVALID XML';
            return $response;

        }

        // CONVERT STRING TO XML
        $xml = simplexml_load_string($helcimResponse);

        // CHECK FOR RESPONSE
        if(!isset($xml->response)){

            // ERROR
            $response['responseMessage'] = 'RESPONSE NOT SET';
            return $response;

        }

        // SET RESULT CODE
        $response['response'] = (string)$xml->response == '1' ? self::SUCCESS : self::FAILURE;
        $response['responseMessage'] = (string)$xml->responseMessage;

        // CHECK AND SET TRANSACTION ID
        if(isset($xml->transaction->transactionId)){ $response['transactionId'] = (int)$xml->transaction->transactionId; }

        // RETURN
        return $response;

    }
}
