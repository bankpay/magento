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

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Disable Class
 */
class Disable extends \Magento\Config\Block\System\Config\Form\Field
{
        
    /**
     * @var \Fam\Fam\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\Block\Template\Context $ksContext
     * @param \Fam\Fam\Model\Config $config
     * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Fam\Fam\Model\Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }
    
    /**
     * Function to set associate website in admin configuration
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $ksElement
     * @return void
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $value = $this->config->getWebsiteId();
        if (!empty($value)) {
            $element->setData('readonly', true);
        }
        return $element->getElementHtml();
    }
}