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

namespace Fam\Fam\Block\Adminhtml\System\Config;

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
