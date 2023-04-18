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

namespace Fam\Fam\Block\Adminhtml\Order\Creditmemo\Create;

/**
 * Adminhtml credit memo items grid
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items
{
    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('creditmemo_item_container'),'" . $this->getUpdateUrl() . "')";
        $this->addChild(
            'update_button1',
            \Magento\Backend\Block\Widget\Button::class,
            ['label' => __('Update Qty\'s'), 'class' => 'update-button', 'onclick' => $onclick]
        );
        
        $paymentMethodName = $this->getCreditmemo()->getOrder()->getPayment()->getMethod();
        $famOnclick = 'setLocation(\'' . $this->getFamUrl() . '\')';

        if ($paymentMethodName == 'fam') {
            $this->addChild(
                'submit_button',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Fam Refund'),
                    'class' => 'save submit-button refund primary',
                    'onclick' => $famOnclick
                ]
            );
        }elseif ($this->getCreditmemo()->canRefund()) {
            if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
                $this->addChild(
                    'submit_button',
                    \Magento\Backend\Block\Widget\Button::class,
                    [
                        'label' => __('Refund'),
                        'class' => 'save submit-button refund primary',
                        'onclick' => 'disableElements(\'submit-button\');submitCreditMemo()'
                    ]
                );
            }
            $this->addChild(
                'submit_offline',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Refund Offline'),
                    'class' => 'save submit-button primary',
                    'onclick' => 'disableElements(\'submit-button\');submitCreditMemoOffline()'
                ]
            );
        } else {
            $this->addChild(
                'submit_button',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Refund Offline'),
                    'class' => 'save submit-button primary',
                    'onclick' => 'disableElements(\'submit-button\');submitCreditMemoOffline()'
                ]
            );
        }
    }

    /**
     * Get fam url
     *
     * @return string
     */
    public function getFamUrl()
    {
        return $this->getUrl(
            'fam/payment/refund',
            [
                'order_id' => $this->getCreditmemo()->getOrderId()
            ]
        );
    }
}