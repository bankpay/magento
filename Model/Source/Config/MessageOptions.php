<?php
/**
 * Fam
 *
 * @category  Fam
 * @package   Ftl_Fam
 * @author    Fam Team
 * @copyright Copyright (c) Frictionless Technologies Ltd (https://www.joinfam.com/)
 * @license   https://joinfam.com/legal
 */ 
namespace Ftl\Fam\Model\Source\Config;

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
