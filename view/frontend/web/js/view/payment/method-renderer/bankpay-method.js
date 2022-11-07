/**
 * Ksolves
 *
 * @category  Ksolves
 * @package   Ksolves_Bankpay
 * @author    Ksolves Team
 * @copyright Copyright (c) Ksolves India Limited (https://www.ksolves.com/)
 * @license   https://store.ksolves.com/magento-license
 */
 
 define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'jquery',
        'ko',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/set-payment-information',
        'mage/url',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/shipping-save-processor',
        'Magento_Customer/js/customer-data',
        "https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"
    ],
    function (Component, quote, $, ko, additionalValidators, setPaymentInformationAction, url, customer, placeOrderAction, fullScreenLoader, messageList, shippingSaveProcessor, customerData,cryptolib) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ksolves_Bankpay/payment/bankpay-form',
                bankpayDataFrameLoaded: false,
                jsLib : ''
            },
            getMerchantName: function() {
                return window.checkoutConfig.payment.bankpay.merchant_name;
            },

            getKeyId: function() {
                return window.checkoutConfig.payment.bankpay.key_id;
            },

            getTheme: function() {
                return window.checkoutConfig.payment.bankpay.theme;
            },

            getMode: function() {
                return window.checkoutConfig.payment.bankpay.mode;
            },

            getSubTotal: function() {
                var totals = quote.totals();
                return (totals ? totals : quote)['subtotal'];
            },

            context: function() {
                return this;
            },

            isShowLegend: function() {
                return true;
            },

            getCode: function() {
                return 'bankpay';
            },

            isActive: function() {
                return true;
            },

            isAvailable: function() {
                return this.bankpayDataFrameLoaded;
            },

            handleError: function (error) {
                if (_.isObject(error)) {
                    this.messageContainer.addErrorMessage(error);
                } else {
                    this.messageContainer.addErrorMessage({
                        message: error
                    });
                }
            },

            initObservable: function() {

                var self = this._super(); //Resolves UI Error on Checkout
                window.CryptoJS = cryptolib;
                if(!self.bankpayDataFrameLoaded) {
                    self.jsLib = $.getScript("https://cdnjs.bankpay.to/js/bankpay_checkout.js", function() {
                        
                    });
                }
                return self;
            },

            /**
             * @override
             */
            /** Process Payment */
            preparePayment: function (context, event) {

                if(!additionalValidators.validate()) {   //Resolve checkout aggreement accept error
                    return false;
                }
                fullScreenLoader.startLoader();
                this.messageContainer.clear();
                
                var self = this;
                if (!customer.isLoggedIn()) {
                    var email_address = quote.guestEmail;
                }
                else
                {
                    var email_address = customer.customerData.email;
                }

                $.ajax({
                    type: 'POST',
                    url: url.build('bankpay/payment/order'),
                    data: {
                        email: email_address,
                        shipping_address: JSON.stringify(quote.shippingAddress()),
                        billing_address: JSON.stringify(quote.billingAddress())
                    },

                    /**
                     * Success callback
                     * @param {Object} response
                     */
                    success: function (response) {
                        fullScreenLoader.stopLoader();
                        console.log(response);
                        if (response.code==201) {
                            doCheckOut(response.data.txn_id);
                        } else {
                            console.log('error');
                        }
                    },

                    /**
                     * Error callback
                     * @param {*} response
                     */
                    error: function (response) {
                        fullScreenLoader.stopLoader();
                    }
                });
            }
        });
    }
);
