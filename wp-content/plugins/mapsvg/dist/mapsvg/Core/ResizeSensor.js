export class ResizeSensor {
    constructor(element, callback) {
        var _this = this;
        _this.element = element;
        _this.callback = callback;
        var style = getComputedStyle(element);
        var zIndex = parseInt(style.zIndex);
        if (isNaN(zIndex)) {
            zIndex = 0;
        }
        ;
        zIndex--;
        _this.expand = document.createElement('div');
        _this.expand.style.position = "absolute";
        _this.expand.style.left = "0px";
        _this.expand.style.top = "0px";
        _this.expand.style.right = "0px";
        _this.expand.style.bottom = "0px";
        _this.expand.style.overflow = "hidden";
        _this.expand.style.zIndex = zIndex.toString();
        _this.expand.style.visibility = "hidden";
        var expandChild = document.createElement('div');
        expandChild.style.position = "absolute";
        expandChild.style.left = "0px";
        expandChild.style.top = "0px";
        expandChild.style.width = "10000000px";
        expandChild.style.height = "10000000px";
        _this.expand.appendChild(expandChild);
        _this.shrink = document.createElement('div');
        _this.shrink.style.position = "absolute";
        _this.shrink.style.left = "0px";
        _this.shrink.style.top = "0px";
        _this.shrink.style.right = "0px";
        _this.shrink.style.bottom = "0px";
        _this.shrink.style.overflow = "hidden";
        _this.shrink.style.zIndex = zIndex.toString();
        _this.shrink.style.visibility = "hidden";
        var shrinkChild = document.createElement('div');
        shrinkChild.style.position = "absolute";
        shrinkChild.style.left = "0px";
        shrinkChild.style.top = "0px";
        shrinkChild.style.width = "200%";
        shrinkChild.style.height = "200%";
        _this.shrink.appendChild(shrinkChild);
        _this.element.appendChild(_this.expand);
        _this.element.appendChild(_this.shrink);
        var size = element.getBoundingClientRect();
        _this.currentWidth = size.width;
        _this.currentHeight = size.height;
        _this.setScroll();
        _this.expand.addEventListener('scroll', function () {
            _this.onScroll();
        });
        _this.shrink.addEventListener('scroll', function () {
            _this.onScroll();
        });
    }
    ;
    onScroll() {
        var _this = this;
        var size = _this.element.getBoundingClientRect();
        var newWidth = size.width;
        var newHeight = size.height;
        if (newWidth != _this.currentWidth || newHeight != _this.currentHeight) {
            _this.currentWidth = newWidth;
            _this.currentHeight = newHeight;
            _this.callback();
        }
        this.setScroll();
    }
    ;
    setScroll() {
        this.expand.scrollLeft = 10000000;
        this.expand.scrollTop = 10000000;
        this.shrink.scrollLeft = 10000000;
        this.shrink.scrollTop = 10000000;
    }
    ;
    destroy() {
        this.expand.remove();
        this.shrink.remove();
    }
    ;
}
//# sourceMappingURL=ResizeSensor.js.map