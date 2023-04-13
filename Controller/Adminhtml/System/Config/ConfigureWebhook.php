<?php 
/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_Fam
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */

namespace Ksolves\Fam\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * ConfigureWebhook Class
 */
class ConfigureWebhook extends Action
{
    /**
     * @var JsonFactory
    */
    protected $resultJsonFactory;

    /**
     * @var \Ksolves\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\UrlInterface
    */
    protected $_storeManager;

    /**
     * @var \Ksolves\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
    */
    protected $curlClient;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
    */
    protected $jsonHelper;

    const CURL_WEBHOOK_STATUS = '200';

    /**
     * @var \Ksolves\Fam\Model\Config
    */
    protected $config;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Ksolves\Fam\Helper\Data $dataHelper
     * @param \Ksolves\Fam\Logger\Logger $logger 
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ksolves\Fam\Model\Config $config
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Ksolves\Fam\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ksolves\Fam\Logger\Logger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Ksolves\Fam\Model\Config $config

    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataHelper = $dataHelper;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        $this->curlClient = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            if (!empty($data)) {
                $payload_data = $this->webhookPayloadData($data['webhook_platform']);
                $webhookApiResponse = $this->webhookApiCurl($payload_data,$data);
                if ($webhookApiResponse['code'] == self::CURL_WEBHOOK_STATUS) {
                    $message = 'Webhook configure successfully';
                    $result = $this->resultJsonFactory->create();
                    return $result->setData(['message' => $message, 'code' => $webhookApiResponse['code']]);
                }else{
                    $message = 'Invalid Credentials';
                    $result = $this->resultJsonFactory->create();
                    return $result->setData(['message' => $message,'code' => $webhookApiResponse['code']]);
                }
            }else{
                $message = 'Data not found';
                $result = $this->resultJsonFactory->create();
                return $result->setData(['message' => $message, 'code' => 400]);
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
    }

    /**
     * webhookPayloadData
     * @param string $webhookPlatform
     * @return json
    */
    public function webhookPayloadData($webhookPlatform)
    {   
        $webhookUrl = $this->_storeManager->getStore()->getBaseUrl().'fam/payment/webhook';
        $payload_data = array(
            "callback_url" => $webhookUrl,
            "webhook_platform" => $webhookPlatform
        );
        $jsonParams = $this->jsonHelper->serialize($payload_data);
        return $jsonParams;
    }


     /**
     * @param $method
     * @param json $requestData
     * @param array $params
     * @return \Magento\Framework\HTTP\Client\Curl|string|void
    */
    public function webhookApiCurl($requestData,$params)
    {
        try {
            $this->_logger->info("Fam Webhook Api start---");
            $webhookUrl = $this->config->getClientUrl().'/api/v1/seller/webhook-detail';

            $this->curlClient->addHeader("Content-Type","application/json");
            $this->curlClient->addHeader("Connection","keep-alive"); 
            $this->curlClient->addHeader("MERCHANT-ID",$params['merchant_id']); 
            $this->curlClient->addHeader("API-SECRET",$params['secret_key']); 
            $this->curlClient->setOption(CURLOPT_RETURNTRANSFER,true);
            $this->curlClient->setOption(CURLOPT_TIMEOUT, 0); 
            $this->curlClient->setOption(CURLOPT_ENCODING, '');
            $startTime = microtime(true);
            $this->curlClient->post($webhookUrl, $requestData); //data post here
            $endTime = (microtime(true) - $startTime);
            $this->_logger->info("Fam Refund Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
     
            $httpStatusCode = $this->curlClient->getStatus();
            $response = $this->curlClient->getBody();

            $this->_logger->info("Fam Webhook Api response--- " . $response);
            return $this->jsonHelper->unserialize($response);

        } catch (\Exception $e) {
            $this->_logger->info("Fam Webhook error response api---" . $e);
            return [];
        }
    }
}