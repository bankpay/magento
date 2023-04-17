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
 * Class ThemeOptions
 */
class ThemeOptions implements OptionSourceInterface
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
                'value'=>'light-colour',
                'label'=>'Light Colour'
            ],
            [
                'value'=>'light-mono',
                'label'=>'Light Mono'
            ],
            [
                'value'=>'dark-mono',
                'label'=>'Dark Mono'
            ],
            [
                'value'=>'dark-colour',
                'label'=>'Dark Colour'
            ]
        ];
    }
}
