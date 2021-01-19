/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        "jquery",
        'mage/url',
        "Magento_Checkout/js/view/payment/default",
        "Magento_Checkout/js/action/place-order",
        "Magento_Checkout/js/model/payment/additional-validators",
        "Magento_Checkout/js/model/quote",
        "Magento_Checkout/js/model/full-screen-loader",
        "Magento_Checkout/js/action/redirect-on-success",
    ],
    function (
        $,
        mageUrl,
        Component,
        placeOrderAction,
        additionalValidators,
        quote,
        fullScreenLoader,
        redirectOnSuccessAction
        ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'YeThird_PayGateway/payment/form',
                transactionResult: ''
            },
            
            redirectAfterPlaceOrder: false,

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            /**
             * @override
             */
            afterPlaceOrder: function () {
                console.log('=======data===>');
                var checkoutConfig = window.checkoutConfig;
                var paymentData = quote.billingAddress();
                var paystackConfiguration = checkoutConfig.payment.ye_gateway;
                var quoteId = checkoutConfig.quoteItemData[0].quote_id;
                $.ajax({
                    method: "GET",
                    url: paystackConfiguration.api_url + "V1/yexk/verify/"+ quoteId
                }).success(function (data) {
                    data = JSON.parse(data);
                    console.log(data);
                });
            },
            getCode: function() {
                return 'ye_gateway';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.ye_gateway.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);