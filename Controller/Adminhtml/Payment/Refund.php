<?php
/**
 * Fam
 *
 * @category  Fam
 * @package   Ftl_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 

namespace Ftl\Fam\Controller\Adminhtml\Payment;

use Magento\Backend\App\Action\Context;

/**
 * Class Refund Controller
 */
class Refund extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
    */
    protected $backendQuoteSession;

    /**
     * @var \Ftl\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ftl\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Ftl\Fam\Model\Config
    */
    protected $config;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
    */
    protected $curlClient;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
    */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
    */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository
    */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
    */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Sales\Model\Order
    */
    protected $_order;

    /**
     * @var \Magento\Sales\Model\Order\Invoice
    */
    protected $_invoice;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoFactory
    */
    protected $_creditmemoFactory;

    /**
     * @var \Magento\Sales\Model\Service\CreditmemoService
    */
    protected $_creditmemoService;

    const CURL_REFUND_STATUS = '201';


    /**
     * Initialize Controller
     *
     * @param Context $context
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param \Ftl\Fam\Logger\Logger $logger
     * @param \Ftl\Fam\Model\Config $config
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Ftl\Fam\Helper\Data $dataHelper,
        \Ftl\Fam\Logger\Logger $logger,
        \Ftl\Fam\Model\Config $config,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
    ) {
        parent::__construct($context);
        $this->backendQuoteSession = $backendQuoteSession; 
        $this->dataHelper = $dataHelper;
        $this->_logger = $logger;
        $this->config = $config;
        $this->curlClient = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_orderRepository = $orderRepository;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->_order = $order;
        $this->_invoice = $invoice;
        $this->_creditmemoFactory = $creditmemoFactory;
        $this->_creditmemoService = $creditmemoService;
    }

    /**
     * execute action
    */
    public function execute()
    {
        $this->_logger->info('Admin-> refund controller call ***');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $creditmemo = $this->creditmemoLoader->load();
            $orderId = $this->getRequest()->getParam('order_id');
            if ($orderId) {
                $payload_data = $this->refundPayloadData($orderId);
                $transactionDetails = $this->dataHelper->getTransactionData($orderId);
                $refundApi = $this->refundApiCurl($payload_data,$transactionDetails['transaction_id']);

                if ($refundApi['code'] == self::CURL_REFUND_STATUS) {
                   $this->createCreditMemo($orderId);
                   $this->_logger->info("Creditmemo created successfully--- " . $orderId);
                   $this->messageManager->addSuccessMessage(__('You created the credit memo.'));
                   $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
                   return $resultRedirect;
                }else{
                    $this->messageManager->addErrorMessage(__('Can\'t initiate refund now.'));
                    $resultRedirect->setPath('sales/order_creditmemo/new', ['order_id' => $orderId]);
                    return $resultRedirect;
                }
            }else{
                $this->messageManager->addErrorMessage(__('Order Id not found.'));
                $resultRedirect->setPath('sales/order_creditmemo/new', ['order_id' => $orderId]);
                return $resultRedirect;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_getSession()->setFormData($data);
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->messageManager->addErrorMessage(__('We can\'t save the credit memo right now.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/new', ['_current' => true]);
        return $resultRedirect;
    }


    /**
     * refundPayloadData
     * @param int $orderId
     * @return json
    */
    public function refundPayloadData($orderId)
    {
        $orderDetails = $this->_orderRepository->get($orderId);
        $payload_data =array(
            "amount" =>array(
                "amount"=> number_format($orderDetails->getGrandTotal(), 2, ".", "") * 100,
                "currency"=> $orderDetails->getOrderCurrencyCode()
            ),
            "reason"=> "DUPLICATE",
            "other_info"=> "testing"
        );
        $jsonParams = $this->jsonHelper->serialize($payload_data);
        
        return $jsonParams;
    }

    /**
     * @param $method
     * @param json $requestData
     * @param int $transactionId
     * @return \Magento\Framework\HTTP\Client\Curl|string|void
    */
    public function refundApiCurl($requestData,$transactionId)
    {
        try {
            $this->_logger->info("Fam Refund Api start---");
            $apiUrl = $this->config->getClientUrl().'/v1/orders/'.$transactionId.'/refund/';

            $this->curlClient->addHeader("Content-Type","application/json");
            $this->curlClient->addHeader("MERCHANT-ID",$this->config->getKeyId()); 
            $this->curlClient->addHeader("API-SECRET",$this->config->getSecretKey()); 
            $this->curlClient->setOption(CURLOPT_RETURNTRANSFER,true);
            $this->curlClient->setOption(CURLOPT_TIMEOUT, 0); 
            $this->curlClient->setOption(CURLOPT_ENCODING, '');
            $startTime = microtime(true);
            $this->curlClient->post($apiUrl, $requestData); //data post here
            $endTime = (microtime(true) - $startTime);
            $this->_logger->info("Fam Refund Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
     
            $httpStatusCode = $this->curlClient->getStatus();
            $response = $this->curlClient->getBody();

            $this->_logger->info("Fam Refund Api response--- " . $response);
            return $this->jsonHelper->unserialize($response);

        } catch (\Exception $e) {
            $this->_logger->info("Fam refund error response api---" . $e);
            return [];
        }
    }
    
    /**
     * @param int $orderId
     * @return void
    */
    public function createCreditMemo($orderId)
    {
        try {
            $orderDetails = $this->_orderRepository->get($orderId);
            $incrementId = $orderDetails->getIncrementId(); //Increment Id

            $order = $this->_order->loadByAttribute('increment_id', $incrementId);
            $invoices = $order->getInvoiceCollection();
            foreach ($invoices as $invoice) {
                $invoiceincrementid = $invoice->getIncrementId();
            }

            $invoiceobj = $this->_invoice->loadByIncrementId($invoiceincrementid);
            $creditmemo = $this->_creditmemoFactory->createByOrder($order);

            // Don't set invoice if you want to do offline refund
            $creditmemo->setInvoice($invoiceobj);

            $this->_creditmemoService->refund($creditmemo); 

        } catch (\Exception $e) {
            $this->_logger->info("We can\'t save the credit memo right now." . $e);
            return [];
        }
    }
}
