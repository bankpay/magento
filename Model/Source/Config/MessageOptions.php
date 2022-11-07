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
namespace Ksolves\Bankpay\Model\Source\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MessageOptions
 */
class MessageOptions implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value'=>'product_detail_page',
                'label'=>'Product Detail Page'
            ],
            [
                'value'=>'mini_cart_popup',
                'label'=>'Mini Cart Popup'
            ],
            [
                'value'=>'cart_page',
                'label'=>'Cart Page'
            ],
            [
                'value'=>'checkout',
                'label'=>'Checkout Page'
            ]
        ];
    }
}
