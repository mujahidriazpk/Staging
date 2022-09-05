import { MapSVG } from "../Core/globals.js";
import { Controller } from "../Core/Controller";
const $ = jQuery;
export class DetailsController extends Controller {
    constructor(options) {
        super(options);
        this.modal = options.modal;
    }
    getToolbarTemplate() {
        if (this.withToolbar)
            return '<div class="mapsvg-popover-close mapsvg-details-close"></div>';
        else
            return '';
    }
    ;
    viewDidLoad() {
        var _this = this;
        this.events.trigger('shown', _this, [_this.mapsvg]);
        if (this.modal && MapSVG.isPhone && this.mapsvg.options.detailsView.mobileFullscreen && !this.mobileCloseBtn) {
            this.mobileCloseBtn = $('<button class="mapsvg-mobile-modal-close mapsvg-btn">' + _this.mapsvg.options.mobileView.labelClose + '</button>')[0];
            this.containers.view.appendChild(this.mobileCloseBtn);
        }
    }
    ;
    setEventHandlers() {
        var _this = this;
        $(this.containers.toolbar).on('click', '.mapsvg-popover-close, .mapsvg-mobile-modal-close', function (e) {
            e.stopPropagation();
            _this.destroy();
            _this.events.trigger('closed', _this, [_this.mapsvg]);
        });
    }
}
//# sourceMappingURL=Details.js.map