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

namespace Ksolves\Bankpay\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Order Class
*/
class Order extends \Ksolves\Bankpay\Controller\BaseController
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
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\UrlInterface
    */
    protected $_urlInterface;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param \Magento\Framework\UrlInterface $urlInterface  
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Ksolves\Bankpay\Logger\Logger $logger
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
        $successUrl = $this->_urlInterface->getUrl('bankpay/payment/success');
        $cancelUrl = $this->_urlInterface->getUrl('bankpay/payment/cancel');
        $frontendQuote = $this->getQuote();
        $payload_data = $this->dataHelper->merchantApiPayloadData($frontendQuote,$params['email'],$successUrl,$cancelUrl); //payload data
        $this->_logger->info('Frontend-> payload data ---');
        $this->_logger->info("<pre>+++");
        $this->_logger->info(print_r($payload_data,true));
        return $this->dataHelper->clientCurl("POST",$payload_data,$this->getQuote()->getId()); //api call
    }
}
