<?php
/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_Bankpay
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */

namespace Ksolves\Bankpay\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Data Class
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
    */
    protected $productRepositoryInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManagerInterface;
    
    /**
     * @var \Magento\Catalog\Model\ProductCategoryList
    */
    protected $productCategoryList;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
    */
    protected $categoryFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
    */
    protected $quoteFactory;

    /**
     * @var \Ksolves\Bankpay\Model\Config
    */
    protected $config;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
    */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
    */
    protected $curlClient;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;
    
    const CURL_SUCCESS_STATUS = '201';

    /**
     * @var \Ksolves\Bankpay\Model\BankpayFactory
     */
    protected $bankpayFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
    */
    protected $quoteCollectionFactory;

     /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * @var \Magento\Sales\Model\Order
    */
    protected $_order;

    /**
     * @var \Ksolves\Bankpay\Model\ResourceModel\Bankpay\CollectionFactory
    */
    protected $bankpayCollectionFactory;
    
    /**
     * AbstractData constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Catalog\Model\ProductCategoryList $productCategoryList
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Ksolves\Bankpay\Model\Config $config
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Ksolves\Bankpay\Model\BankpayFactory $bankpayFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\Order $order
     * @param \Ksolves\Bankpay\Model\ResourceModel\Bankpay\CollectionFactory $bankpayCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Catalog\Model\ProductCategoryList $productCategoryList,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Ksolves\Bankpay\Model\Config $config,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Ksolves\Bankpay\Logger\Logger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Ksolves\Bankpay\Model\BankpayFactory $bankpayFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order $order,
        \Ksolves\Bankpay\Model\ResourceModel\Bankpay\CollectionFactory $bankpayCollectionFactory
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->productCategoryList = $productCategoryList;
        $this->categoryFactory = $categoryFactory;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
        $this->quoteFactory = $quoteFactory;
        $this->config = $config;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->curlClient = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->bankpayFactory = $bankpayFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_invoiceSender = $invoiceSender;
        $this->_order = $order;
        $this->bankpayCollectionFactory = $bankpayCollectionFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }
    
    /**
     * get product url by productId
     * @param $productId
     * @return string
    */
    public function getProductUrl($productId)
    {
       return $this->productRepositoryInterface->getById($productId)->getProductUrl();
    }

    /**
     * Get place holder image of a product for small_image
     *
     * @return string
    */
    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
    
    /**
     * get product image url by productId
     * @param $productId
     * @return string
    */
    public function getProductImageUrl($productId)
    {
        $product = $this->productRepositoryInterface->getById($productId);
        $store = $this->storeManagerInterface->getStore();
        $productUrl  = $product->getProductUrl();

        if ($product->getImage()) {
            $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        }else{
            $productImageUrl = $this->getPlaceHolderImage();
        }
        return $productImageUrl;
    }
    
    /**
     * get product category name by productId
     * @param $productId
     * @return array
    */
    public function getCategoryName($productId)
    {
        $categoryIds = $this->productCategoryList->getCategoryIds($productId);
        $category = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }
        
        $category_name = [];
        foreach ($category as $categoryId) {
            $category = $this->categoryFactory->create()->load($categoryId);
            $category_name[] = $category->getName();
        }

        return $category_name;
    }

    /**
     * get product collection by quote id
     * @param $quoteId
     * @return array
    */
    public function getQuoteItems($quoteId)
    {
        $quote = $this->quoteFactory->create()->load($quoteId);
        $items = $quote->getAllItems();

        $itemsArray = [];
        foreach ($items as $_item) {
            $itemData = array(
                'itemName' => $_item->getName(),
                'sku' => $_item->getSku(),
                'quantity' =>  $_item->getQty(),
                'unitPrice' =>  (int) number_format($_item->getPrice(), 2, ".", ""),
                'currency' =>  $quote->getStoreCurrencyCode(),
                'pageURL' =>  $this->getProductUrl($_item->getProductId()),
                'imageURL' => $this->getProductImageUrl($_item->getProductId()),
                'category' => $this->getCategoryName($_item->getProductId())
            );
            $itemsArray[] = $itemData;
        }
        return $itemsArray;
    }

    /**
     * @param $method
     * @param array $requestData
     * @param int $quoteId
     * @return \Magento\Framework\HTTP\Client\Curl|string|void
    */
    public function clientCurl($method,$requestData,$quoteId)
    {
        try {
            $this->_logger->info("Bankpay Api start---");
            $apiUrl = $this->config->getClientUrl().'/v1/payment/transactions/';
            $jsonParams = $this->jsonHelper->serialize($requestData);

            $this->curlClient->addHeader("Content-Type","application/json");
            $this->curlClient->addHeader("MERCHANT-ID",$this->config->getKeyId()); 
            $this->curlClient->addHeader("API-SECRET",$this->config->getSecretKey()); 
            $this->curlClient->setOption(CURLOPT_RETURNTRANSFER,true);
            $this->curlClient->setOption(CURLOPT_TIMEOUT, 0); 
            $this->curlClient->setOption(CURLOPT_ENCODING, '');
            $startTime = microtime(true);

            if (strtoupper($method) == 'POST') {
                $this->curlClient->post($apiUrl, $jsonParams);
                $endTime = (microtime(true) - $startTime);
                $this->_logger->info("Bankpay Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
            }
    
            $response = $this->curlClient->getBody();

            $this->_logger->info("Bankpay Api response--- " . $response);
  
            $json_decode = $this->jsonHelper->unserialize($response);

            if ((string)$json_decode['code'] == self::CURL_SUCCESS_STATUS) {
                $transactionId = $json_decode['data']['txn_id'];
                $quoteUpdate = $this->quoteRepository->get($quoteId); // Get quote by id
                $quoteUpdate->setData('bankpay_transaction_id', $transactionId); // Fill data
                $this->quoteRepository->save($quoteUpdate); // update quote 
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($json_decode);
                return $resultJson;
            }else {
                return [];
            }
          
        } catch (\Exception $e) {
            $this->_logger->info("Bankpay error response api---" . $e);
            return [];
        }
    }

    /**
     * merchantApiPayloadData
     * @param object $quote
     * @param string $email
     * @param string $successUrl
     * @param string $cancelUrl
     * @return array
    */
    public function merchantApiPayloadData($quote,$email,$successUrl,$cancelUrl)
    {
        // shipping info
        $shippingName = $quote->getShippingAddress()->getFirstname() . ' ' . $quote->getShippingAddress()->getLastname();
        $shippingStreet = $quote->getShippingAddress()->getStreet();

        if (isset($shippingStreet) && count($shippingStreet)) {
            $shippingStreet1 = $shippingStreet[0];
            $shippingStreet2 = '';
        }

        // billing info
        $billingName = $quote->getBillingAddress()->getFirstname() . ' ' . $quote->getBillingAddress()->getLastname();
        $billingStreet = $quote->getBillingAddress()->getStreet();
        if (isset($billingStreet) && count($billingStreet)) {
            $billingStreet1 = $billingStreet[0];
            $billingStreet2 = '';
        }

        $payload_data =array(
            "merchantReference"=> "Nike India",
            "amount" =>array(
                "amount"=> (int) number_format($quote->getGrandTotal(), 2, ".", ""),
                "currency"=> $quote->getQuoteCurrencyCode()
            ),
            "successUrl"=> $successUrl,
            "cancelUrl"=> $cancelUrl,
            "estimatedShipmentDate"=> "2022-07-20 11:11:11",
            "consumer" =>array(
                "givenNames"=> $quote->getBillingAddress()->getFirstname(),
                "surname"=> $quote->getBillingAddress()->getLastname(),
                "email"=> $email,
                "phoneNumber"=> $quote->getBillingAddress()->getTelephone()
            ),
            "shipping" =>array(
                "name"=> $shippingName,
                "line1"=> $shippingStreet1,
                "line2"=> $shippingStreet2,
                "city"=> $quote->getShippingAddress()->getCity(),
                "postcode"=> $quote->getShippingAddress()->getPostcode(),
                "state"=> $quote->getShippingAddress()->getRegion(),
                "country"=> $quote->getShippingAddress()->getCountryId(),
                "phoneNumber"=> $quote->getShippingAddress()->getTelephone()
            ),
            "billing" =>array(
                "name"=> $billingName,
                "line1"=> $billingStreet1,
                "line2"=> $billingStreet2,
                "city"=> $quote->getBillingAddress()->getCity(),
                "postcode"=> $quote->getBillingAddress()->getPostcode(),
                "state"=> $quote->getBillingAddress()->getRegion(),
                "country"=> $quote->getBillingAddress()->getCountryId(),
                "phoneNumber"=> $quote->getBillingAddress()->getTelephone()
            ),
            "taxAmount" =>array(
                "amount"=> (int) number_format($quote->getShippingAddress()->getTaxAmount(), 2, ".", ""),
                "currency"=> $quote->getQuoteCurrencyCode()
            ),
            "shippingAmount" =>array(
                "shippingAmount"=> (int) number_format($quote->getShippingAddress()->getShippingAmount(), 2, ".", ""),
                "currency"=> $quote->getQuoteCurrencyCode()
            ),
            "items" => $this->getQuoteItems($quote->getId())
        );
        return $payload_data;
    }
    
    /**
     * Save the  bankpay_transaction history in bankpay_transaction table
     * @param int $transactionid
     * @param int $quoteId
     * @param int $orderId
     * @param string $status
     * @return true
    */
    public function saveTransactionHistory($transactionid,$quoteId,$orderId,$status)
    {
        $bankpayFactory = $this->bankpayFactory->create();
        $bankpayFactory->setData('quote_id', $quoteId);
        $bankpayFactory->setData('transaction_id', $transactionid);
        $bankpayFactory->setData('order_id', $orderId);
        $bankpayFactory->setData('transaction_status', $status);
        $bankpayFactory->save();
    } 


    /**
     * Get quoteId using bankpay_transaction_id from quote table
     * @param int $transactionid
     * @return int
    */
    public function getQuoteId($transactionid)
    {
        $quoteCollectionFactory = $this->quoteCollectionFactory->create();
        $quoteCollectionFactory->addFieldToFilter('bankpay_transaction_id', $transactionid);
        foreach ($quoteCollectionFactory as $_quoteCollectionFactory) {
            $quoteId = $_quoteCollectionFactory->getId();
        }
        if ($quoteId) {
           return $quoteId;
        }else{
            return false;
        }
    }

    /**
     * create invoice using order id
     * @param int $orderId
     * @return true
    */
    public function createInvoice($orderId)
    {
        $order = $this->_orderRepository->get($orderId); //order id for which want to create invoice
        if($order->canInvoice()) {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->_transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            $this->_invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getId())
            )
            ->setIsCustomerNotified(true)
            ->save();
            // change the order status once invoice is created
            if ($order) {
                $orderObj = $this->_order->load($orderId);
                $orderObj->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                $orderObj->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $orderObj->addStatusToHistory($orderObj->getStatus(), 'Order processed successfully with reference');
                $orderObj->save();
            }
        }
    } 

    /**
     * Get data using order_id from bankpay_transaction table
     * @param int $orderId
     * @return array
    */
    public function getTransactionData($orderId)
    {
        $bankpayCollectionFactory = $this->bankpayCollectionFactory->create();
        $bankpayCollectionFactory->addFieldToFilter('order_id', $orderId);
        $result = [];
        if (count($bankpayCollectionFactory) > 0) {
           foreach ($bankpayCollectionFactory as $_bankpayCollectionFactory) {
                $result['transaction_id'] = $_bankpayCollectionFactory->getTransactionId();
                $result['transaction_status'] = $_bankpayCollectionFactory->getTransactionStatus();
           }
        }
        return $result;
    }  

    /**
     * Get OrderId from bankpay_transaction table using transaction id
     * @param int $transactionId
     * @return array
    */
    public function getOrderData($transactionId)
    {
        $bankpayCollectionFactory = $this->bankpayCollectionFactory->create();
        $bankpayCollectionFactory->addFieldToFilter('transaction_id', $transactionId);
        $result = [];
        if (count($bankpayCollectionFactory) > 0) {
           foreach ($bankpayCollectionFactory as $_bankpayCollectionFactory) {
                $result['order_id'] = $_bankpayCollectionFactory->getOrderId();
                $result['quote_id'] = $_bankpayCollectionFactory->getQuoteId();
           }
        }
        return $result;
    }

    /**
     * Get incrementId using orderId
     * @param int $orderId
     * @return int
    */
    public function getOrderIncrementId($orderId)
    {
        $orderObj = $this->_order->load($orderId);           
        return $orderObj->getIncrementId();
    }

    /**
     * Get order status using orderId
     * @param int $orderId
     * @return string
    */
    public function getOrderStatus($orderId)
    {
        $orderObj = $this->_order->load($orderId);           
        return $orderObj->getStatus();
    }
    
    /**
     * Get table row id using transaction_id from bankpay_transaction table
     * @param int $transactionid
     * @return bool
    */
    public function getRowId($transactionid)
    {
        $bankpayCollectionFactory = $this->bankpayCollectionFactory->create();
        $bankpayCollectionFactory->addFieldToFilter('transaction_id', $transactionid);
        foreach ($bankpayCollectionFactory as $_bankpayCollectionFactory) {
            $rowId = $_bankpayCollectionFactory->getId();
        }
        if ($rowId) {
            return $rowId;
        }else{
            return false;
        }
    }   

    /**
     * update bankpay_transaction table using row id
     * @param int $Id
     * @param string $transactionStatus
     * @return bool
    */
    public function updateTransactionData($Id,$transactionStatus)
    {
        $bankpayFactory = $this->bankpayFactory->create()->load($Id);
        $bankpayFactory->setData('transaction_status', $transactionStatus);
        if ($bankpayFactory->save()) {
            return true;
        }else{
            return false;
        }
    } 
}
