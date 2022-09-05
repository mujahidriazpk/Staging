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

/***/ "./modules/stripe-express/assets/src/js/vendor-dashboard.js":
/*!******************************************************************!*\
  !*** ./modules/stripe-express/assets/src/js/vendor-dashboard.js ***!
  \******************************************************************/
/***/ (() => {

eval(";\n\n(function ($) {\n  'use strict';\n\n  var dokanStripeExpressVendor = {\n    init: function init() {\n      var self = this;\n      $('#dokan-stripe-express-account-connect').click(self.signUp);\n      $('#dokan-stripe-express-dashboard-login').click(self.expressLogin);\n      $('#dokan-stripe-express-account-disconnect').click(self.disconnect);\n    },\n    signUp: function signUp(e) {\n      e.preventDefault();\n      dokanStripeExpressVendor.hideMessage();\n      dokanStripeExpressVendor.showProcessing();\n      $.post(dokanStripeExpressData.ajaxurl, {\n        user_id: $(this).data('user'),\n        action: 'dokan_stripe_express_vendor_signup',\n        url_args: window.location.search,\n        _wpnonce: dokanStripeExpressData.nonce\n      }, function (response) {\n        if (response.success) {\n          window.location.replace(response.data.url);\n        } else {\n          dokanStripeExpressVendor.hideProcessing();\n          dokanStripeExpressVendor.showMessage(response.data, true);\n        }\n      });\n    },\n    expressLogin: function expressLogin(e) {\n      e.preventDefault();\n      dokanStripeExpressVendor.hideMessage();\n      dokanStripeExpressVendor.showProcessing();\n      $.post(dokanStripeExpressData.ajaxurl, {\n        user_id: $(this).data('user'),\n        action: 'dokan_stripe_express_get_login_url',\n        _wpnonce: dokanStripeExpressData.nonce\n      }, function (response) {\n        if (response.success) {\n          window.open(response.data.url, '_blank');\n        } else {\n          dokanStripeExpressVendor.showMessage(response.data, true);\n        }\n\n        dokanStripeExpressVendor.hideProcessing();\n      });\n    },\n    disconnect: function disconnect(e) {\n      e.preventDefault();\n      dokanStripeExpressVendor.hideMessage();\n      dokanStripeExpressVendor.showProcessing();\n      $.post(dokanStripeExpressData.ajaxurl, {\n        user_id: $(this).data('user'),\n        action: 'dokan_stripe_express_vendor_disconnect',\n        _wpnonce: dokanStripeExpressData.nonce\n      }, function (response) {\n        if (response.success) {\n          dokanStripeExpressVendor.showMessage(response.data);\n          window.location.reload(true);\n        } else {\n          dokanStripeExpressVendor.showMessage(response.data, true);\n        }\n\n        dokanStripeExpressVendor.hideProcessing();\n      });\n    },\n    showProcessing: function showProcessing() {\n      $('#dokan-stripe-express-payment').block({\n        message: null,\n        overlayCSS: {\n          background: '#fff',\n          opacity: 0.6\n        }\n      });\n    },\n    hideProcessing: function hideProcessing() {\n      $('#dokan-stripe-express-payment').unblock();\n    },\n    showMessage: function showMessage(message, error) {\n      var $element = error ? $('#dokan-stripe-express-signup-error') : $('#dokan-stripe-express-signup-message');\n      $element.html(message);\n      $element.show();\n    },\n    hideMessage: function hideMessage() {\n      $('#dokan-stripe-express-payment .signup-message').each(function () {\n        $(this).html('');\n        $(this).hide();\n      });\n    }\n  };\n  $(document).ready(function () {\n    dokanStripeExpressVendor.init();\n  });\n})(jQuery);\n\n//# sourceURL=webpack://dokan-pro/./modules/stripe-express/assets/src/js/vendor-dashboard.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./modules/stripe-express/assets/src/js/vendor-dashboard.js"]();
/******/ 	
/******/ })()
;