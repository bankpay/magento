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
