/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component,$) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Helcim_HelcimAPI/payment/form',
                timeoutMessage: 'Sorry, but something went wrong. Please contact the seller.'
            },

            getCode: function() {
                return 'helcim_api';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }

        });
    }
);