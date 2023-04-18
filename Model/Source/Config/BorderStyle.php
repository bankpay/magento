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
namespace Fam\Fam\Model\Source\Config;

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
