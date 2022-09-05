/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

const {
  registerPlugin
} = wp.plugins;
const {
  PluginSidebar
} = wp.editPost;
const {
  __
} = wp.i18n;
const {
  Component
} = wp.element;
const {
  addFilter
} = wp.hooks;
const {
  TextControl,
  PanelBody,
  ColorPicker
} = wp.components;
const {
  createHigherOrderComponent
} = wp.compose;
const {
  InspectorControls
} = wp.editor;
const filterStyle = {
  'margin-bottom': '200px'
};

class Test extends Component {
  render() {
    return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(PluginSidebar, {
      title: __('Location', 'textdomain')
    }, /*#__PURE__*/React.createElement("div", {
      id: "mapsvg"
    }), /*#__PURE__*/React.createElement("div", {
      id: "mapsvg-filters",
      style: filterStyle
    })));
  }

  loadMap() {
    // if(MapSVG.get(0)){
    //     MapSVG.get(0).destroy();
    // }
    window.MapSVG.markerImages = window.mapsvgMarkerImages;
    var mapOptions = {
      source: '/wp-content/plugins/mapsvg-dev/maps/geo-calibrated/usa.svg',
      events: {
        afterLoad: function () {
          var _mapsvg = this;

          var meta = wp.data.select('core/editor').getEditedPostAttribute('meta');
          var formData = {};

          if (meta.mapsvg_location) {
            var locationData = JSON.parse(meta.mapsvg_location);
            var location = new mapsvg.location(locationData);
            var marker = new mapsvg.marker({
              location: location,
              mapsvg: _mapsvg
            });

            _mapsvg.markerAdd(marker);

            formData['location'] = location;
          }

          var form = new mapsvg.formBuilder({
            container: jQuery('#mapsvg-filters')[0],
            schema: new mapsvg.schema({
              fields: [{
                type: 'location',
                name: "Location",
                label: "",
                parameterNameShort: 'location'
              }]
            }),
            editMode: false,
            showNames: false,
            filtersMode: true,
            mapsvg: _mapsvg,
            mediaUploader: null,
            data: formData,
            admin: null,
            closeOnSave: false,
            events: {
              save: function (data) {},
              close: function () {},
              init: function (data) {
                // var id = data.location && data.location.marker ? data.location.marker.id : null;
                // mapsvg.hideMarkersExceptOne(id);
                jQuery('button.mapsvg-marker-delete').html('<span class="dashicons dashicons-trash"></span>');
              },
              'changed.field': function (name, value) {
                console.log('changed.field');

                if (name === 'location') {
                  var location = JSON.stringify(value);
                  wp.data.dispatch('core/editor').editPost({
                    meta: {
                      mapsvg_location: location
                    }
                  });
                }
              }
            }
          });
        }
      }
    };
    var map = new mapsvg.map("mapsvg", {
      options: mapOptions
    });
  }

  componentDidMount() {
    var _this = this;

    setTimeout(function () {
      _this.loadMap();

      jQuery('button[aria-label="Location"]').on('click', function () {
        setTimeout(function () {
          _this.loadMap();
        }, 500);
      });
    }, 1000);
  }

}

registerPlugin('mapsvg-sidebar', {
  icon: 'location',
  render: Test
});

/***/ })
/******/ ]);