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
 * Class LogoTheme
 */
class LogoTheme implements OptionSourceInterface
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
                'value'=>'black',
                'label'=>'Black'
            ],
            [
                'value'=>'white',
                'label'=>'White'
            ],
            [
                'value'=>'yellow',
                'label'=>'Yellow'
            ]
        ];
    }
}
