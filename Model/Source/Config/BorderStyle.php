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
namespace Ksolves\Fam\Model\Source\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BorderStyle
 */
class BorderStyle implements OptionSourceInterface
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
                'value'=>'default',
                'label'=>'Default'
            ],
            [
                'value'=>'square',
                'label'=>'Square'
            ],
            [
                'value'=>'pill',
                'label'=>'Pill'
            ],
            [
                'value'=>'none',
                'label'=>'None'
            ]
        ];
    }
}
