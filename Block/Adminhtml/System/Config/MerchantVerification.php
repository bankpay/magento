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

use Magento\Backend\Block\Template\Context;

/**
 * MerchantVerification Class
 */
class MerchantVerification extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     * @var string
     */
    protected $_template = 'Fam_Fam::system/config/toggle_button.phtml';

    protected $_configPath = 'payment/fam/active';

    protected $_groupName = 'fam';

    protected $_fieldName = 'active';

    /**
     * @var \Fam\Fam\Model\Config
    */
    protected $config;

    /**
     * @param Context $context
     * @param \Fam\Fam\Model\Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Fam\Fam\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }
    
     /**
      * Render fieldset html
      *
      * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
      * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_decorateRowHtml($element, "<td class='label'>Enable </td><td>" . $this->toHtml() . '</td><td></td>');
    }
    
    /**
     * get config path
     * @return string
     */
    public function getConfigPath(){
        return $this->_configPath;
    }
    
    /**
     * get group id
     * @return string
     */
    public function getGroupName(){
        return $this->_groupName;
    }
    
    /**
     * get field id
     * @return string
     */
    public function getFieldName(){
        return $this->_fieldName;
    }

    /**
     * Return ajax url for varification toggle
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('fam/system_config/configureWebhook');
    }

    /**
     * Returns merchant id
     *
     * @return mixed
    */
    public function getMerchantId()
    {
        return $this->config->getKeyId();
    }

    /**
     * Returns theme colour
     *
     * @return string
    */
    public function getSecretKey()
    {
        return $this->config->getSecretKey();
    }

    /**
     * Returns Merchant Name
     *
     * @return string
    */
    public function getMerchantName()
    {
        $merchantName = $this->config->getMerchantName();
        if (!empty($merchantName)) {
            return $merchantName;
        }else{
            return 'magento';
        }
    }
}