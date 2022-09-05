import { MapSVG } from "../Core/globals.js";
import { Controller } from "../Core/Controller";
import { Marker } from "../Marker/Marker";
import { Region } from "../Region/Region";
const $ = jQuery;
export class PopoverController extends Controller {
    constructor(options) {
        super(options);
        options.autoresize = true;
        this.point = options.point;
        this.yShift = options.yShift;
        this.mapObject = options.mapObject;
        this.id = this.mapObject.id + '_' + Math.random();
        $(this.containers.main).data('popover-id', this.id);
    }
    setPoint(point) {
        this.point = point;
    }
    getToolbarTemplate() {
        if (this.withToolbar)
            return '<div class="mapsvg-popover-close"></div>';
        else
            return '';
    }
    viewDidLoad() {
        super.viewDidLoad.call(this);
        var _this = this;
        if (MapSVG.isPhone && _this.mapsvg.options.popovers.mobileFullscreen && !this.mobileCloseBtn) {
            this.mobileCloseBtn = $('<button class="mapsvg-mobile-modal-close mapsvg-btn">' + _this.mapsvg.getData().options.mobileView.labelClose + '</button>')[0];
            $(this.containers.view).append(this.mobileCloseBtn);
        }
        this.adjustPosition();
        $(this.containers.main).toggleClass('mapsvg-popover-animate', true);
        $(this.containers.main).toggleClass('mapsvg-popover-visible', true);
        _this.adjustHeight();
        _this.updateScroll();
        this.autoresize && this.resizeSensor.setScroll();
        this.events.trigger('shown', _this, [_this.mapsvg]);
    }
    adjustHeight() {
        var _this = this;
        $(_this.containers.main).height($(_this.containers.main).find('.mapsvg-auto-height').outerHeight() + (_this.containers.toolbar ? $(_this.containers.toolbar).outerHeight() : 0));
    }
    adjustPosition() {
        var pos = this.mapsvg.convertSVGToPixel(this.point);
        pos.y -= this.yShift;
        pos.x = Math.round(pos.x);
        pos.y = Math.round(pos.y);
        $(this.containers.main).css({
            'transform': 'translateX(-50%) translate(' + pos.x + 'px,' + pos.y + 'px)'
        });
    }
    setEventHandlers() {
        var _this = this;
        $('body').off('.popover.mapsvg');
        $(this.containers.view).on('click touchend', '.mapsvg-popover-close, .mapsvg-mobile-modal-close', function (e) {
            e.stopImmediatePropagation();
            _this.close();
        });
        $('body').on('mouseup.popover.mapsvg touchend.popover.mapsvg', function (e) {
            if (_this.mapsvg.getData().isScrolling || $(e.target).closest('.mapsvg-directory').length || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
                return;
            _this.close();
        });
    }
    close() {
        var _this = this;
        if (($(this.containers.main).data('popover-id') != this.id) || !$(_this.containers.main).is(':visible'))
            return;
        _this.destroy();
        if (_this.mapObject instanceof Region) {
            _this.mapsvg.deselectRegion(_this.mapObject);
        }
        if (_this.mapObject instanceof Marker) {
            _this.mapsvg.deselectAllMarkers();
        }
        _this.events.trigger('closed', _this, [_this.mapsvg]);
    }
    destroy() {
        $(this.containers.main).toggleClass('mapsvg-popover-animate', false);
        $(this.containers.main).toggleClass('mapsvg-popover-visible', false);
        super.destroy.call(this);
    }
    show() {
        $(this.containers.main).toggleClass('mapsvg-popover-animate', true);
        $(this.containers.main).toggleClass('mapsvg-popover-visible', true);
    }
}
//# sourceMappingURL=Popover.js.map