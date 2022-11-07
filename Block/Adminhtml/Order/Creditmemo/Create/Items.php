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

namespace Ksolves\Bankpay\Block\Adminhtml\Order\Creditmemo\Create;

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
        $bankpayOnclick = 'setLocation(\'' . $this->getBankpayUrl() . '\')';

        if ($paymentMethodName == 'bankpay') {
            $this->addChild(
                'submit_button',
                \Magento\Backend\Block\Widget\Button::class,
                [
                    'label' => __('Bankpay Refund'),
                    'class' => 'save submit-button refund primary',
                    'onclick' => $bankpayOnclick
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
     * Get bankpay url
     *
     * @return string
     */
    public function getBankpayUrl()
    {
        return $this->getUrl(
            'bankpay/payment/refund',
            [
                'order_id' => $this->getCreditmemo()->getOrderId()
            ]
        );
    }
}