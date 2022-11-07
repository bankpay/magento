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

namespace Ksolves\Bankpay\Block\Adminhtml\System\Config;

use Magento\Framework\Data\OptionSourceInterface;


/**
 * WebsiteList Class
 */
class WebsiteList implements OptionSourceInterface
{
    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
    */
    protected $_websiteCollectionFactory;

    /**
     * @param \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     */
    public function __construct( 
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
    ) {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }
 
    /**
     * Retrieve websites collection of system
     *
     * @return Website Collection
     */
    public function getWebsiteLists()
    {
        $collection = $this->_websiteCollectionFactory->create();
        return $collection;
    }

    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getWebsiteLists()  as $website) {
           $options[$website->getId()] = $website->getName();
        }

        return $options;
    }
}
