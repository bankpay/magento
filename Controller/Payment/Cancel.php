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
 * Cancel Class
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ksolves\Bankpay\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ksolves\Bankpay\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Ksolves\Bankpay\Helper\Data $dataHelper
     * @param \Ksolves\Bankpay\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ksolves\Bankpay\Helper\Data $dataHelper,
        \Ksolves\Bankpay\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->_logger = $logger;
    }
    
    /**
     * Marchant Api Cancel Response.
     *
     * @return data
    */
    public function execute()
    {
        $this->_logger->info('Frontend-> cancel controller call ===');
        try {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
