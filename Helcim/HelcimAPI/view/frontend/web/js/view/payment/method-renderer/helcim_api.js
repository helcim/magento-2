/**
 * Copyright © 2017 Helcim Inc. All rights reserved.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'mage/translate'
    ],
    function (Component,$, creditCardData, cardNumberValidator, $t) {

        return Component.extend({
            defaults: {
                template: 'Helcim_HelcimAPI/payment/form',
                timeoutMessage: 'Sorry, but something went wrong. Please contact the seller.',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardVerificationNumber: '',
                selectedCardType: null
            },
            /**
             * Inits
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'selectedCardType',
                        'creditCardToken'
                    ]);
                return this;
            },
            initialize: function () {
                self = this;
                this._super();

                //Set credit card number to credit card data object
                this.creditCardNumber.subscribe(function(value) {
                    var result;
                    self.selectedCardType(null);
 
                    if (value == '' || value == null) {
                        return false;
                    }
                    result = cardNumberValidator(value);
 
                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }
                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }
 
                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });
 
                //Set expiration year to credit card data object
                this.creditCardExpYear.subscribe(function(value) {
                    creditCardData.expirationYear = value;
                });
 
                //Set expiration month to credit card data object
                this.creditCardExpMonth.subscribe(function(value) {
                    creditCardData.expirationMonth = value;
                });
 
                //Set cvv code to credit card data object
                this.creditCardVerificationNumber.subscribe(function(value) {
                    creditCardData.cvvCode = value;
                });

                // INCLUDE HELCIM JS
                jQuery.ajax({
                    async:false,
                    type:'GET',
                    url:'https://secure.myhelcim.com/js/version2.js',
                    data:null,
                    success:function(result) {
                        
                    },
                    dataType:'script',
                    error: function(xhr, textStatus, errorThrown) {
                        // Look at the `textStatus` and/or `errorThrown` properties.
                        
                        
                    }
                });

            },

            getCode: function() {

                return 'helcim_api';
            },

            isActive: function () {
                return true;
            },
 
            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.ccform.availableTypes['helcim_api'];
            },
 
            getCcMonths: function() {
                return window.checkoutConfig.payment.ccform.months['helcim_api'];
            },
 
            getCcYears: function() {
                return window.checkoutConfig.payment.ccform.years['helcim_api'];
            },
 
            hasVerification: function() {
                return window.checkoutConfig.payment.ccform.hasVerification['helcim_api'];
            },
 
            getCcAvailableTypesValues: function() {
                return _.map(this.getCcAvailableTypes(), function(value, key) {
                    return {
                        'value': key,
                        'type': value
                    }
                });
            },
            getCcMonthsValues: function() {
                return _.map(this.getCcMonths(), function(value, key) {
                    return {
                        'value': key,
                        'month': value
                    }
                });
            },
            getCcYearsValues: function() {
                return _.map(this.getCcYears(), function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    }
                });
            },

            placeOrder: function (){

                if(document.getElementById("helcimForm2") != null){ document.getElementById("helcimForm2").remove(); }

                // GET JS TOKEN
                var jsToken = window.checkoutConfig.payment.ccform.jsToken.helcim_api;

                // CHECK FOR RESULT
                if((typeof window.helcimResult !== 'undefined' && window.helcimResult != null) || jsToken == null){

                    // CONTINUE LOGIC
                    self._super();

                }else{

                    // GET VALIDATION
                    var validation = true;

                    // GET CARD NUMBER
                    var cardNumber = document.getElementsByName('payment[cc_number]')[0].value;

                    // VALIDATION
                    if(validation && cardNumber.length > 0){

                        // GET DATA
                        var expiryMonth = document.getElementsByName('payment[cc_exp_month]')[0].value;
                        var expiryYear = document.getElementsByName('payment[cc_exp_year]')[0].value;
                        var cvv = document.getElementsByName('payment[cc_cid]')[0].value;
                        var customerId = window.checkoutConfig.customerData.id;

                        // CREATE HIDDEN FORM FOR CARD TOKENIZATION
                        document.querySelector('footer').innerHTML += `
                        
                        <form style="display:none;" name="helcimForm2" id="helcimForm2" method="POST">
                        <!--FORM-->
                            <!--RESULTS-->
                            <div style="display:none;" id="helcimResults"></div>
                        
                            <!--SETTINGS-->
                            <input type="hidden" id="token" value="${jsToken}">
                            <input type="hidden" id="language" value="en">
                            <input type="hidden" id="dontSubmit" value="1">
                            <input type="hidden" id="test" value="1">
                            <input type="hidden" id="xml" value="1">
                        
                            <!--CARD-INFORMATION-->
                            <input type="hidden" id="cardNumber" value="${cardNumber}"><br/>
                            <input type="hidden" id="cardExpiryMonth" value="${expiryMonth.padStart(2, '0')}"> <input type="hidden" id="cardExpiryYear" value="${expiryYear.slice(-2)}"><br/>
                            <input type="hidden" id="cardCVV" value="${cvv}"><br/>
                            <input type="hidden" id="amount" value="0"><br/>
                            <input type="hidden" id="customerCode" value="${customerId}"><br/>
                        
                        </form>
                        
                    
                        `;

                        // CLEAR CREDIT CARD DATA
                        document.getElementsByName('payment[cc_number]')[0].value = '';
                        document.getElementsByName('payment[cc_exp_month]')[0].value = '';
                        document.getElementsByName('payment[cc_exp_year]')[0].value = '';
                        document.getElementsByName('payment[cc_cid]')[0].value = '';
                        $('#helcim_api_cc_number').change();
                        $('#helcim_api_cc_exp_month').change();
                        $('#helcim_api_cc_exp_year').change();
                        $('#helcim_api_cc_cid').change(); 
                        var element = document.getElementsByName('payment[cc_exp_month]')[0];
                        var event = new Event('change');
                        element.dispatchEvent(event);
                        var element = document.getElementsByName('payment[cc_exp_year]')[0];
                        var event = new Event('change');
                        element.dispatchEvent(event);

                        // VERIFY CARD
                        helcimProcess().then(function(result) {

                            // CHECK RESULT
                            if(typeof result !== 'undefined' || result != ''){

                                // GET DATA
                                const regexToken = /(?<=id="cardToken" value=")[^\"]*/gm;
                                const regexMaskedNumber = /(?<=id="cardNumber" value=")([\d"]*)[^\d]*([\d]*)/gm;
                                var foundToken = result.match(regexToken);
                                var foundMaskedNumber  = regexMaskedNumber.exec(result);
                                
                                // CHECK FOUND DATA
                                if(foundToken[0] != null && foundToken[0] != '' 
                                    && foundMaskedNumber[1] != null && foundMaskedNumber[1] != ''
                                    && foundMaskedNumber[2] != null && foundMaskedNumber[2] != '' ){
                                    
                                    // BUILD DATA STRING
                                    var data = foundToken[0]+'-'+foundMaskedNumber[1]+'-'+foundMaskedNumber[2];

                                    // ASSIGN TOKEN TO CREDIT CARD FIELD
                                    document.getElementsByName('payment[cc_number]')[0].value = data;
                                    $('#helcim_api_cc_number').change();

                                    // WIPE VISABLE DATA
                                    document.getElementsByName('payment[cc_number]')[0].value = '';

                                }

                                // ASSIGN WINDOW RESULT
                                window.helcimResult = result;
                            }
            
                            self.placeOrder();
                            
                        }).catch(function(error){

                            // ALERT
                            alert('Tokenization error. '+error);
                        
                        });


                    }

                }
  
            },

            validate: function() {

                // CLEAR RESULT
                window.helcimResult = null;
                return true;

            }

        });
    }
);


