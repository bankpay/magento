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
 * Class Mode
 */
class Mode implements OptionSourceInterface
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
                'value'=>'sandbox',
                'label'=>'Sandbox'
            ],
            [
                'value'=>'live',
                'label'=>'Live'
            ]
        ];
    }
}
