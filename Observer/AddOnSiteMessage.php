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

namespace Ksolves\Bankpay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * AddOnSiteMessage Class
 */
class AddOnSiteMessage implements ObserverInterface
{
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Block\ShortcutButtons $shortcutButtons */
        $shortcutButtons = $observer->getEvent()->getContainer();

        /** @var \Magento\Framework\View\Element\Template $shortcutMessage */
        $shortcutMessage = $shortcutButtons->getLayout()->createBlock(
            \Ksolves\Bankpay\Block\MinicartPopup\Messages::class,
            '',
            []
        );

        $shortcutMessage->setIsInCatalogProductMessage(
            $observer->getEvent()->getIsCatalogProduct()
        )->setShowOrPosition(
            $observer->getEvent()->getOrPosition()
        );

        $shortcutMessage->setIsShoppingCartMessage($observer->getEvent()->getIsShoppingCart());

        $shortcutMessage->setIsCart(get_class($shortcutButtons) == \Magento\Checkout\Block\QuoteShortcutButtons::class);

        $shortcutButtons->addShortcut($shortcutMessage);
    }
}
