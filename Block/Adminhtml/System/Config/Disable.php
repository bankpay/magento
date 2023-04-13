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

namespace Ksolves\Fam\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Disable Class
 */
class Disable extends \Magento\Config\Block\System\Config\Form\Field
{
        
    /**
     * @var \Ksolves\Fam\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\Block\Template\Context $ksContext
     * @param \Ksolves\Fam\Model\Config $config
     * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ksolves\Fam\Model\Config $config,
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