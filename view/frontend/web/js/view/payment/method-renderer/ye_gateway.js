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
             * Provide redirect to page
             */
            redirectToCustomAction: function (url) {
                window.location.replace(url);
            },

            /**
             * @override
             */
            afterPlaceOrder: function () {
                var _this = this;
                _this.isPlaceOrderActionAllowed(false);
                fullScreenLoader.startLoader();
                console.log('=======data===>');
                var checkoutConfig = window.checkoutConfig;
                var paymentData = quote.billingAddress();
                var paystackConfiguration = checkoutConfig.payment.ye_gateway;
                var quoteId = checkoutConfig.quoteItemData[0].quote_id;
                var data = {
                    email: paymentData.email,
                    amount: Math.ceil(quote.totals().grand_total * 100), // get order total from quote for an accurate... quote
                    phone: paymentData.telephone,
                    currency: checkoutConfig.totalsData.quote_currency_code,
                    quoteId: quoteId,
                    notify_url: paystackConfiguration.notify_url,
                    return_url: paystackConfiguration.return_url,
                };
                $.ajax({
                    method: "POST",
                    url: paystackConfiguration.api_url + "V1/yexk/verify/"+ quoteId,
                    headers: {'Content-Type':'application/json'},
                    dataType: 'json',
                    data: JSON.stringify(data),
                }).success(function (data) {
                    fullScreenLoader.stopLoader();
                    var d = JSON.parse(data);
                    console.log('data',d);
                    if (d.status == '1') {
                        console.log('redirect_url', d.data.redirect_url);
                        _this.redirectToCustomAction(d.data.redirect_url);
                        // window.open(d.data.redirect_url, '_blank');
                        // setTimeout(function () {
                            // redirectOnSuccessAction.execute();
                        // }, 1000);
                        return;
                    }
                    _this.isPlaceOrderActionAllowed(true);
                    _this.messageContainer.addErrorMessage({
                        message: "Error, please try again, error:" + d.message
                    });
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