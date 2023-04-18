<?php
/**
 * Fam
 *
 * @category  Fam
 * @package   Fam_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 

namespace Fam\Fam\Helper;

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
     * @var \Fam\Fam\Model\Config
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
     * @var \Fam\Fam\Logger\Logger
    */
    protected $_logger;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;
    
    const CURL_SUCCESS_STATUS = '201';

    const CURL_PUT_SUCCESS_STATUS = '200';

    /**
     * @var \Fam\Fam\Model\FamFactory
     */
    protected $famFactory;

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
     * @var \Fam\Fam\Model\ResourceModel\Fam\CollectionFactory
    */
    protected $famCollectionFactory;

    /**
     * @var  \Magento\Framework\App\ProductMetadataInterface
    */
    protected $productMetadata;
    
    /**
     * AbstractData constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Catalog\Model\ProductCategoryList $productCategoryList
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Fam\Fam\Model\Config $config
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Fam\Fam\Logger\Logger $logger
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Fam\Fam\Model\FamFactory $famFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\Order $order
     * @param \Fam\Fam\Model\ResourceModel\Fam\CollectionFactory $famCollectionFactory
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
        \Fam\Fam\Model\Config $config,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Fam\Fam\Logger\Logger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Fam\Fam\Model\FamFactory $famFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order $order,
        \Fam\Fam\Model\ResourceModel\Fam\CollectionFactory $famCollectionFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Store\Model\StoreManagerInterface $storeManager
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
        $this->famFactory = $famFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
        $this->_invoiceSender = $invoiceSender;
        $this->_order = $order;
        $this->famCollectionFactory = $famCollectionFactory;
        $this->_logger = $logger;
        $this->productMetadata = $productMetadata;
        $this->storeManager = $storeManager;
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
            if(!($_item->getProductType() == "configurable")) {
                $price = 0;
                
                if($_item->getParentItem() && $_item->getParentItem()->getProductType()=="configurable"){
                    $price = number_format($_item->getParentItem()->getPrice(), 2, ".", "")*100;
                    $quantity =  $_item->getParentItem()->getQty();
                } else {
                    $price = number_format($_item->getPrice(), 2, ".", "")*100;
                    $quantity =  $_item->getQty();
                }
                
                $itemData = array(
                    'name' => $_item->getName(),
                    'sku' => $_item->getSku(),
                    'quantity' =>  $quantity,
                    'price' =>  [
                        'amount' => $price,
                        'currency' =>  $quote->getStoreCurrencyCode(),
                    ],
                    'currency' =>  $quote->getStoreCurrencyCode(),
                    'image_url' => $this->getProductImageUrl($_item->getProductId()),
                    'category' => $this->getCategoryName($_item->getProductId())
                );
                $itemsArray[] = $itemData;
            }
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
            $this->_logger->info("Fam Api start---");
            $apiUrl = $this->config->getClientUrl().'/v1/orders/';
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
                $this->_logger->info("Fam Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
            }
    
            $response = $this->curlClient->getBody();

            $this->_logger->info("Fam Api response--- " . $response);
  
            $json_decode = $this->jsonHelper->unserialize($response);

            if ((string)$json_decode['code'] == self::CURL_SUCCESS_STATUS) {
                $this->_logger->info(print_r($json_decode,true));
                $transactionId = $json_decode['data']['checkout_id'];
                $quoteUpdate = $this->quoteRepository->get($quoteId); // Get quote by id
                $quoteUpdate->setData('fam_checkout_id', $transactionId); // Fill data
                $this->quoteRepository->save($quoteUpdate); // update quote 
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($json_decode);
                return $resultJson;
            }else {
                return [];
            }
          
        } catch (\Exception $e) {
            $this->_logger->info("Fam error response api---" . $e);
            return [];
        }
    }

    /**
     * merchantApiPayloadData
     * @param object $quote
     * @param string $email
     * @param string $successUrl
     * @param string $cancelUrl
     * @param boolean $isAdmin
     * @return array
    */
    public function merchantApiPayloadData($quote,$email,$successUrl,$cancelUrl,$isAdmin)
    {
        // shipping info
        $shippingName = $quote->getShippingAddress()->getFirstname() . ' ' . $quote->getShippingAddress()->getLastname();
        $shippingStreet = $quote->getShippingAddress()->getStreet();

        if (isset($shippingStreet) && count($shippingStreet)) {
            $shippingStreet1 = $shippingStreet[0];
            $shippingStreet2 = '';
        }
        
        if($isAdmin){
            $webhookUrl = $this->storeManager->getStore()->getBaseUrl()."fam/payment/adminwebhook";
        } else {
            $webhookUrl = $this->storeManager->getStore()->getBaseUrl()."fam/payment/webhook";
        }

        // billing info
        $billingName = $quote->getBillingAddress()->getFirstname() . ' ' . $quote->getBillingAddress()->getLastname();
        $billingStreet = $quote->getBillingAddress()->getStreet();
        if (isset($billingStreet) && count($billingStreet)) {
            $billingStreet1 = $billingStreet[0];
            $billingStreet2 = '';
        }
        $payload_data = array(
            "amounts" =>array(
                "total"=>  number_format($quote->getSubtotal() + $quote->getShippingAddress()->getShippingAmount() +$quote->getShippingAddress()->getTaxAmount(), 2, ".", "")*100,
                "subtotal"=> number_format($quote->getSubtotal(), 2, ".", "")*100,
                "tax"=>  number_format($quote->getShippingAddress()->getTaxAmount(), 2, ".", "")*100,
                "shipping"=> number_format($quote->getShippingAddress()->getShippingAmount(), 2, ".", "")*100,
                "currency"=> $quote->getQuoteCurrencyCode(),
                "merchant_discount" => 0000
            ),
            "consumer" =>array(
                "first_name"=> $quote->getBillingAddress()->getFirstname(),
                "last_name"=> $quote->getBillingAddress()->getLastname(),
                "email"=> $email,
                "phone_number"=> $quote->getBillingAddress()->getTelephone()
            ),
            "shipping" =>array(
                "full_name"=> $shippingName,
                "address_1"=> $shippingStreet1,
                "address_2"=> $shippingStreet2,
                "city"=> $quote->getShippingAddress()->getCity(),
                "post_code"=> $quote->getShippingAddress()->getPostcode(),
                "region"=> $quote->getShippingAddress()->getRegion(),
                "country"=> $quote->getShippingAddress()->getCountryId(),
                "phone_number"=> $quote->getShippingAddress()->getTelephone()
            ),
            "billing" =>array(
                "full_name"=> $billingName,
                "address_1"=> $billingStreet1,
                "address_2"=> $billingStreet2,
                "city"=> $quote->getBillingAddress()->getCity(),
                "post_code"=> $quote->getBillingAddress()->getPostcode(),
                "region"=> $quote->getBillingAddress()->getRegion(),
                "country"=> $quote->getBillingAddress()->getCountryId(),
                "phone_number"=> $quote->getBillingAddress()->getTelephone()
            ),
            "items" => $this->getQuoteItems($quote->getId()),
            "merchant_checkout_id"=> $quote->getId(),
            "merchant_enduser_id"=>"1234",
            "success_url"=> $successUrl,
            "cancel_url"=> $cancelUrl,
            "callback_url"=> $webhookUrl,
            "platform"=>array(
                "name"=> "Magento",
                "version" => $this->productMetadata->getVersion()
            )
        );
        return $payload_data;
    }

    public function updateApiPayloadData(){
        $payload_data = array(
            "order_id" => "663821",
            "merchant_order_id"=>"123456"

        );
        return $payload_data;
    }
    
    public function updateCurl($method,$requestData,$orderId){
        try {
            $this->_logger->info("Fam Update Api start---");
            $apiUrl = $this->config->getClientUrl().'/v1/payment/transactions/'.$orderId;
            $jsonParams = $this->jsonHelper->serialize($requestData);

            $this->curlClient->addHeader("Content-Type","application/json");
            $this->curlClient->addHeader("MERCHANT-ID",$this->config->getKeyId()); 
            $this->curlClient->addHeader("API-SECRET",$this->config->getSecretKey()); 
            $this->curlClient->setOption(CURLOPT_RETURNTRANSFER,true);
            $this->curlClient->setOption(CURLOPT_TIMEOUT, 0); 
            $this->curlClient->setOption(CURLOPT_ENCODING, '');
            $startTime = microtime(true);

            if (strtoupper($method) == 'PUT') {
                $this->curlClient->post($apiUrl, $jsonParams);
                $endTime = (microtime(true) - $startTime);
                $this->_logger->info("Fam Api performance--- " . "Elapsed time is: ". $endTime . " seconds");
            }
    
            $response = $this->curlClient->getBody();

            $this->_logger->info("Fam Api response--- " . $response);
  
            $json_decode = $this->jsonHelper->unserialize($response);

            if ((string)$json_decode['code'] == self::CURL_PUT_SUCCESS_STATUS) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($json_decode);
                return $resultJson;
            }else {
                return [];
            }
        } catch (\Exception $e) {
            $this->_logger->info("Fam error response api---" . $e);
            return [];
        }
    }
    
    /**
     * Save the  fam_transaction history in fam_transaction table
     * @param int $transactionid
     * @param int $quoteId
     * @param int $orderId
     * @param string $status
     * @return true
    */
    public function saveTransactionHistory($transactionid,$quoteId,$orderId,$status,$checkoutid)
    {
        $famFactory = $this->famFactory->create();
        $famFactory->setData('quote_id', $quoteId);
        $famFactory->setData('transaction_id', $transactionid);
        $famFactory->setData('fam_checkout_id', $checkoutid);
        $famFactory->setData('order_id', $orderId);
        $famFactory->setData('transaction_status', $status);
        $famFactory->save();
    } 


    /**
     * Get quoteId using fam_transaction_id from quote table
     * @param int $checkoutid
     * @return int
    */
    public function getQuoteId($checkoutid)
    {
        $quoteCollectionFactory = $this->quoteCollectionFactory->create();
        $quoteCollectionFactory->addFieldToFilter('fam_checkout_id', $checkoutid);
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
     * Get data using order_id from fam_transaction table
     * @param int $orderId
     * @return array
    */
    public function getTransactionData($orderId)
    {
        $famCollectionFactory = $this->famCollectionFactory->create();
        $famCollectionFactory->addFieldToFilter('order_id', $orderId);
        $result = [];
        if (count($famCollectionFactory) > 0) {
           foreach ($famCollectionFactory as $_famCollectionFactory) {
                $result['transaction_id'] = $_famCollectionFactory->getTransactionId();
                $result['transaction_status'] = $_famCollectionFactory->getTransactionStatus();
           }
        }
        return $result;
    }  

    /**
     * Get OrderId from fam_transaction table using transaction id
     * @param int $transactionId
     * @return array
    */
    public function getOrderData($transactionId)
    {
        $famCollectionFactory = $this->famCollectionFactory->create();
        $famCollectionFactory->addFieldToFilter('fam_checkout_id', $transactionId);
        $result = [];
        if (count($famCollectionFactory) > 0) {
           foreach ($famCollectionFactory as $_famCollectionFactory) {
                $result['order_id'] = $_famCollectionFactory->getOrderId();
                $result['quote_id'] = $_famCollectionFactory->getQuoteId();
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
     * Get table row id using transaction_id from fam_transaction table
     * @param int $transactionid
     * @return bool
    */
    public function getRowId($transactionid)
    {
        $famCollectionFactory = $this->famCollectionFactory->create();
        $famCollectionFactory->addFieldToFilter('fam_checkout_id', $transactionid);
        $rowId = null;
        foreach ($famCollectionFactory as $_famCollectionFactory) {
            $rowId = $_famCollectionFactory->getId();
        }
        if ($rowId) {
            return $rowId;
        }else{
            return false;
        }
    }   

    /**
     * update fam_transaction table using row id
     * @param int $Id
     * @param string $transactionStatus
     * @return bool
    */
    public function updateTransactionData($Id,$transactionStatus)
    {
        $famFactory = $this->famFactory->create()->load($Id);
        $famFactory->setData('transaction_status', $transactionStatus);
        if ($famFactory->save()) {
            return true;
        }else{
            return false;
        }
    } 
}
