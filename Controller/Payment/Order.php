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

namespace Ftl\Fam\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Order Class
*/
class Order extends \Ftl\Fam\Controller\BaseController
{
    /**
     * @var \Magento\Quote\Model\Quote
    */
    protected $quote;

    /**
     * @var \Magento\Checkout\Model\Session
    */
    protected $checkoutSession;

    /**
     * @var \Ftl\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\UrlInterface
    */
    protected $_urlInterface;

    /**
     * @var \Ftl\Fam\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ftl\Fam\Helper\Data $dataHelper
     * @param \Magento\Framework\UrlInterface $urlInterface  
     * @param \Ftl\Fam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ftl\Fam\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Ftl\Fam\Logger\Logger $logger
    ) {
        parent::__construct($context,$checkoutSession);
        $this->dataHelper = $dataHelper;
        $this->_urlInterface = $urlInterface;
        $this->_logger = $logger;
    }
    
    /**
     * Marchant Api Response.
     *
     * @return json
    */
    public function execute()
    {
        $this->_logger->info('Frontend-> order controller call ---');
        $params = $this->getRequest()->getPostValue();
        if (isset($params) && $params) {
           return $this->checkoutTransactionsApi($params);
        }else{
            return "Data not found";
        } 
    }

    /**
     * @return merchant Api Call
     * @param array $params
    */
    protected function checkoutTransactionsApi($params)
    {
        $successUrl = $this->_urlInterface->getUrl('fam/payment/success');
        $cancelUrl = $this->_urlInterface->getUrl('fam/payment/cancel');
        $frontendQuote = $this->getQuote();
        $payload_data = $this->dataHelper->merchantApiPayloadData($frontendQuote,$params['email'],$successUrl,$cancelUrl,false); //payload data
        $this->_logger->info('Frontend-> payload data ---');
        $this->_logger->info("<pre>+++");
        $this->_logger->info(print_r($payload_data,true));
        return $this->dataHelper->clientCurl("POST",$payload_data,$this->getQuote()->getId()); //api call
    }
}
