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

namespace Fam\Fam\Block\MinicartPopup;

use Fam\Fam\Block\OnsiteMessage as OnsiteMessage;
use Magento\Catalog\Block\ShortcutInterface;

/**
 * Class Messages
 */
class Messages extends OnsiteMessage implements ShortcutInterface
{
    const ALIAS_ELEMENT_INDEX = 'alias';

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    //protected $_template = 'Fam_Fam::cart/minicart_message.phtml';

    /**
     * @var bool
     */
    private $_isMiniCart = true;

    /**
     * Get shortcut alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->getData(self::ALIAS_ELEMENT_INDEX);
    }

    /**
     * @param bool $isCatalog
     * @return $this
     */
    public function setIsInCatalogProductMessage($isCatalog)
    {
        $this->_isMiniCart = !$isCatalog;

        return $this;
    }
    
    /**
     * @param bool $isShoppingCart
     * @return templates
     */
    public function setIsShoppingCartMessage($isShoppingCart)
    {
        $this->isShoppingCart = $isShoppingCart;

        if ($isShoppingCart){
            $this->_template = 'Fam_Fam::cart/cart_message.phtml';
        }
        else
            $this->_template = 'Fam_Fam::cart/minicart_message.phtml';
    }

    /**
     * Is Should Rendered
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function shouldRender()
    {
        if ($this->getIsCart())
            return true;

        return $this->isEnabled("mini_cart_popup") && $this->_isMiniCart;
    }

    /**
     * Render the block if needed
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _toHtml()
    {
        if (!$this->shouldRender())
            return '';

        return parent::_toHtml();
    }
}
