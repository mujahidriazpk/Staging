/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./modules/stripe-express/assets/src/js/admin-settings.js":
/*!****************************************************************!*\
  !*** ./modules/stripe-express/assets/src/js/admin-settings.js ***!
  \****************************************************************/
/***/ (() => {

eval(";\n\n(function ($) {\n  'use strict';\n\n  var dokanStripeExpressAdmin = {\n    init: function init() {\n      dokanStripeExpressAdmin.switchMode($('#woocommerce_dokan_stripe_express_testmode').is(':checked'));\n      dokanStripeExpressAdmin.toggleDelayPeriodField($('#woocommerce_dokan_stripe_express_disburse_mode').val());\n      dokanStripeExpressAdmin.toggleIntervalField($('#woocommerce_dokan_stripe_express_announcement_to_sellers').is(':checked'));\n      $('#woocommerce_dokan_stripe_express_testmode').on('change', function () {\n        dokanStripeExpressAdmin.switchMode($(this).is(':checked'));\n      });\n      $('#woocommerce_dokan_stripe_express_disburse_mode').on('change', function () {\n        dokanStripeExpressAdmin.toggleDelayPeriodField($(this).val());\n      });\n      $('#woocommerce_dokan_stripe_express_announcement_to_sellers').on('change', function () {\n        dokanStripeExpressAdmin.toggleIntervalField($(this).is(':checked'));\n      });\n    },\n    switchMode: function switchMode(testMode) {\n      if (testMode) {\n        $('#woocommerce_dokan_stripe_express_test_publishable_key').closest('tr').show();\n        $('#woocommerce_dokan_stripe_express_test_secret_key').closest('tr').show();\n        $('#woocommerce_dokan_stripe_express_test_webhook_key').closest('tr').show();\n        $('#woocommerce_dokan_stripe_express_webhook_key').closest('tr').hide();\n      } else {\n        $('#woocommerce_dokan_stripe_express_test_publishable_key').closest('tr').hide();\n        $('#woocommerce_dokan_stripe_express_test_secret_key').closest('tr').hide();\n        $('#woocommerce_dokan_stripe_express_test_webhook_key').closest('tr').hide();\n        $('#woocommerce_dokan_stripe_express_webhook_key').closest('tr').show();\n      }\n    },\n    toggleDelayPeriodField: function toggleDelayPeriodField(disburseMode) {\n      if (disburseMode === 'DELAYED') {\n        $('#woocommerce_dokan_stripe_express_disbursement_delay_period').closest('tr').show();\n      } else {\n        $('#woocommerce_dokan_stripe_express_disbursement_delay_period').closest('tr').hide();\n      }\n    },\n    toggleIntervalField: function toggleIntervalField(noticeEnabled) {\n      if (noticeEnabled) {\n        $('#woocommerce_dokan_stripe_express_notice_interval').closest('tr').show();\n      } else {\n        $('#woocommerce_dokan_stripe_express_notice_interval').closest('tr').hide();\n      }\n    }\n  };\n  $(document).ready(function () {\n    dokanStripeExpressAdmin.init();\n  });\n})(jQuery);\n\n//# sourceURL=webpack://dokan-pro/./modules/stripe-express/assets/src/js/admin-settings.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./modules/stripe-express/assets/src/js/admin-settings.js"]();
/******/ 	
/******/ })()
;