/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'helcim_api',
                component: 'Helcim_HelcimAPI/js/view/payment/method-renderer/helcim_api'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
