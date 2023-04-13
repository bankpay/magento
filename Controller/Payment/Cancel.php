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

namespace Ksolves\Fam\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

/**
 * Cancel Class
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Ksolves\Fam\Helper\Data
    */
    protected $dataHelper;

    /**
     * @var \Ksolves\Fam\Logger\Logger
    */
    protected $_logger;
   
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Ksolves\Fam\Helper\Data $dataHelper
     * @param \Ksolves\Fam\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ksolves\Fam\Helper\Data $dataHelper,
        \Ksolves\Fam\Logger\Logger $logger
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
            // $this->messageManager->addErrorMessage($e->getMessage());
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
