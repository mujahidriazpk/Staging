import { MapSVG } from "../Core/globals";
import tinycolor from "../Vendor/tinycolor.js";
import { MapsRepository } from "./MapsRepository";
import Handlebars from "../../handlebars.js";
import { ResizeSensor } from '../Core/ResizeSensor';
import { ViewBox, GeoViewBox } from "./MapOptionsInterface";
import extend from "../Core/extend";
import * as $ from 'jquery';
import { GeoPoint, ScreenPoint, SVGPoint } from "../Location/Location";
import { Marker } from "../Marker/Marker";
import { MarkerCluster } from "../MarkerCluster/MarkerCluster";
import { Region } from "../Region/Region";
import { Query } from "../Infrastructure/Server/Query";
import { Schema } from "../Infrastructure/Server/Schema";
import { DirectoryController } from "../Directory/Directory";
import { Events } from "../Core/Events";
import { Repository } from "../Core/Repository";
import { FiltersController } from "../Filters/Filters";
export class MapSVGMap {
    constructor(containerId, options) {
        this.markerOptions = { 'src': MapSVG.urls.root + 'markers/pin1_red.png' };
        this.updateOutdatedOptions(this.options);
        this.containerId = containerId;
        this.options = extend(true, {}, this.defaults, options);
        if (this.options.source.indexOf('//') === 0)
            this.options.source = this.options.source.replace(/^\/\/[^\/]+/, '').replace('//', '/');
        else
            this.options.source = this.options.source.replace(/^.*:\/\/[^\/]+/, '').replace('//', '/');
        this.editMode = this.options.editMode;
        delete this.options.editMode;
        this.id = this.options.db_map_id;
        if (this.id == 'new')
            this.id = null;
        this.regions = new Map();
        this.objects = new Map();
        this.highlightedRegions = [];
        this.editRegions = { on: false };
        this.editMarkers = { on: false };
        this.editData = { on: false };
        this.controllers = {};
        this.containers.map = document.getElementById(this.containerId);
        this.containers.scrollpane = $('<div class="mapsvg-scrollpane"></div>')[0];
        this.containers.map.appendChild(this.containers.scrollpane);
        this.containers.layers = $('<div class="mapsvg-layers-wrap"></div>')[0];
        this.containers.scrollpane.appendChild(this.containers.layers);
        this.whRatio = 0;
        this.isScrolling = false;
        this.markerOptions = {};
        this.svgDefault = {};
        this.scale = 1;
        this._scale = 1;
        this.selected_id = [];
        this.regions = new Map();
        this.regionsRepository = new Repository('regions');
        this.objectsRepository = new Repository('objects');
        this.markers = new Map();
        this.markersClusters = new Map();
        this._viewBox = new ViewBox(0, 0, 0, 0);
        this.viewBox = new ViewBox(0, 0, 0, 0);
        this.zoomLevel = 0;
        this.scroll = {
            tx: 0, ty: 0,
            vxi: 0, vyi: 0,
            x: 0, y: 0,
            dx: 0, dy: 0,
            vx: 0, vy: 0,
            gx: 0, gy: 0,
            touchScrollStart: 0
        };
        this.layers = {};
        this.geoCoordinates = false;
        this.geoViewBox = new GeoViewBox(new GeoPoint(0, 0), new GeoPoint(0, 0));
        this.eventsPreventList = {};
        this.googleMaps = { loaded: false, initialized: false, map: null, zoomLimit: true };
        this.init();
    }
    setGroups() {
        let _this = this;
        _this.groups = _this.options.groups;
        _this.groups.forEach(function (g) {
            g.objects && g.objects.length && g.objects.forEach(function (obj) {
                _this.containers.svg.querySelector('#' + obj.value).classList.toggle('mapsvg-hidden', !g.visible);
            });
        });
    }
    setLayersControl(options) {
        var _this = this;
        if (options)
            extend(true, this.options.layersControl, options);
        if (this.options.layersControl.on) {
            if (!this.containers.layersControl) {
                this.containers.layersControl = document.createElement('div');
                this.containers.layersControl.classList.add('mapsvg-layers-control');
                this.containers.layersControlLabel = document.createElement('div');
                this.containers.layersControlLabel.classList.add('mapsvg-layers-label');
                this.containers.layersControl.appendChild(this.containers.layersControlLabel);
                let layersControlWrap = document.createElement('div');
                layersControlWrap.classList.add('mapsvg-layers-list-wrap');
                this.containers.layersControl.appendChild(layersControlWrap);
                this.containers.layersControlListNano = document.createElement('div');
                this.containers.layersControlListNano.classList.add('nano');
                layersControlWrap.appendChild(this.containers.layersControlListNano);
                this.containers.layersControlList = document.createElement('div');
                this.containers.layersControlList.classList.add('mapsvg-layers-list');
                this.containers.layersControlList.classList.add('nano-content');
                this.containers.layersControlListNano.appendChild(this.containers.layersControlList);
                this.containers.mapContainer.appendChild(this.containers.layersControl);
            }
            this.containers.layersControl.style.display = 'block';
            this.containers.layersControlLabel.innerHTML = this.options.layersControl.label;
            this.containers.layersControlLabel.style.display = 'block';
            this.containers.layersControlList.innerHTML = '';
            while (this.containers.layersControlList.firstChild) {
                this.containers.layersControlList.removeChild(this.containers.layersControlList.firstChild);
            }
            this.containers.layersControl.classList.remove('mapsvg-top-left', 'mapsvg-top-right', 'mapsvg-bottom-left', 'mapsvg-bottom-right');
            this.containers.layersControl.classList.add('mapsvg-' + this.options.layersControl.position);
            if (this.options.menu.on && !this.options.menu.customContainer && this.options.layersControl.position.indexOf('left') !== -1) {
                this.containers.layersControl.style.left = this.options.menu.width;
            }
            this.containers.layersControl.style.maxHeight = this.options.layersControl.maxHeight;
            this.options.groups.forEach((g) => {
                let item = document.createElement('div');
                item.classList.add('mapsvg-layers-item');
                item.setAttribute('data-group-id', g.id);
                item.innerHTML = '<input type="checkbox" class="ios8-switch ios8-switch-sm" ' + (g.visible ? 'checked' : '') + ' /><label>' + g.title + '</label>';
                this.containers.layersControlList.appendChild(item);
            });
            $(this.containers.layersControlListNano).nanoScroller({
                preventPageScrolling: true,
                iOSNativeScrolling: true
            });
            $(this.containers.layersControl).off();
            $(this.containers.layersControl).on('click', '.mapsvg-layers-item', function () {
                var id = $(this).data('group-id');
                var input = $(this).find('input');
                input.prop('checked', !input.prop('checked'));
                _this.options.groups.forEach(function (g) {
                    if (g.id === id)
                        g.visible = !g.visible;
                });
                _this.setGroups();
            });
            $(this.containers.layersControlLabel).on('click', () => {
                $(_this.containers.layersControlLabel).toggleClass('closed');
            });
            $(this.containers.layersControlLabel).toggleClass('closed', !this.options.layersControl.expanded);
        }
        else {
            if (this.containers.layersControl) {
                this.containers.layersControl.style.display = 'none';
            }
        }
    }
    loadDataObjects(params) {
        return this.objectsRepository.find(params);
    }
    loadDirectory() {
        if (this.options.menu.source === 'database' && this.objects.size === 0) {
            return false;
        }
        if (this.options.menu.on) {
            this.controllers.directory.loadItemsToDirectory();
        }
        this.setPagination();
    }
    setPagination() {
        var _this = this;
        (this.containers.pagerMap) && $(this.containers.pagerMap).empty().remove();
        (this.containers.pagerDir) && $(this.containers.pagerDir).empty().remove();
        if (_this.options.database.pagination.on && _this.options.database.pagination.perpage !== 0) {
            this.containers.directory.classList.toggle('mapsvg-with-pagination', (['directory', 'both'].indexOf(_this.options.database.pagination.showIn) !== -1));
            this.containers.map.classList.toggle('mapsvg-with-pagination', (['map', 'both'].indexOf(_this.options.database.pagination.showIn) !== -1));
            if (_this.options.menu.on) {
                this.containers.pagerDir = _this.getPagination();
                _this.controllers.directory.addPagination(this.containers.pagerDir);
            }
            this.containers.pagerMap = _this.getPagination();
            this.containers.map.appendChild(this.containers.pagerMap);
        }
    }
    getPagination(callback) {
        var _this = this;
        var pager = $('<nav class="mapsvg-pagination"><ul class="pager"><!--<li class="mapsvg-first"><a href="#">First</a></li>--><li class="mapsvg-prev"><a href="#">&larr; ' + _this.options.database.pagination.prev + ' ' + _this.options.database.pagination.perpage + '</a></li><li class="mapsvg-next"><a href="#">' + _this.options.database.pagination.next + ' ' + _this.options.database.pagination.perpage + ' &rarr;</a></li><!--<li class="mapsvg-last"><a href="#">Last</a></li>--></ul></nav>');
        if (this.objectsRepository.onFirstPage() && this.objectsRepository.onLastPage()) {
            pager.hide();
        }
        else {
            pager.find('.mapsvg-prev').removeClass('disabled');
            pager.find('.mapsvg-first').removeClass('disabled');
            pager.find('.mapsvg-last').removeClass('disabled');
            pager.find('.mapsvg-next').removeClass('disabled');
            this.objectsRepository.onLastPage() &&
                (pager.find('.mapsvg-next').addClass('disabled') && pager.find('.mapsvg-last').addClass('disabled'));
            this.objectsRepository.onFirstPage() &&
                (pager.find('.mapsvg-prev').addClass('disabled') && pager.find('.mapsvg-first').addClass('disabled'));
        }
        pager.on('click', '.mapsvg-next:not(.disabled)', (e) => {
            e.preventDefault();
            if (this.objectsRepository.onLastPage())
                return;
            var query = new Query({ page: this.objectsRepository.query.page + 1 });
            this.objectsRepository.find(query).done(function () {
                callback && callback();
            });
        }).on('click', '.mapsvg-prev:not(.disabled)', function (e) {
            e.preventDefault();
            if (_this.objectsRepository.onFirstPage())
                return;
            var query = new Query({ page: _this.objectsRepository.query.page - 1 });
            _this.objectsRepository.find(query).done(function () {
                callback && callback();
            });
        }).on('click', '.mapsvg-first:not(.disabled)', function (e) {
            e.preventDefault();
            if (_this.objectsRepository.onFirstPage())
                return;
            var query = new Query({ page: 1 });
            _this.objectsRepository.find(query).done(function () {
                callback && callback();
            });
        }).on('click', '.mapsvg-last:not(.disabled)', function (e) {
            e.preventDefault();
            if (_this.objectsRepository.onLastPage())
                return;
            let query = new Query({ lastpage: true });
            _this.objectsRepository.find(query).done(function () {
                callback && callback();
            });
        });
        return pager[0];
    }
    deleteMarkers() {
        this.markers.clear();
    }
    deleteClusters() {
        if (this.markersClusters) {
            this.markersClusters.forEach(function (markerCluster) {
                markerCluster.destroy();
            });
            this.markersClusters.clear();
        }
    }
    addLocations() {
        var _this = this;
        this.firstDataLoad = this.firstDataLoad === undefined;
        var locationField = this.objectsRepository.getSchema().getFieldByType('location');
        if (!locationField) {
            return false;
        }
        locationField = locationField.name;
        if (locationField) {
            if (this.firstDataLoad) {
                this.setMarkerImagesDependency();
            }
            _this.deleteMarkers();
            _this.deleteClusters();
            _this.clusters = {};
            _this.clustersByZoom = [];
            _this.deleteClusters();
            if (this.objects && this.objects.size > 0) {
                this.objects.forEach(function (object) {
                    if (object[locationField]) {
                        if (object[locationField].geoPoint || object[locationField].screenPoint) {
                            new Marker({
                                location: object[locationField],
                                object: object,
                                mapsvg: _this
                            });
                        }
                    }
                });
                if (_this.options.clustering.on) {
                    _this.startClusterizer();
                }
                else {
                    this.objects.forEach(function (object) {
                        if (object.location && object.location.marker) {
                            _this.markerAdd(object.location.marker);
                        }
                    });
                    _this.mayBeFitMarkers();
                }
            }
        }
    }
    addClustersFromWorker(zoomLevel, clusters) {
        var _this = this;
        _this.clustersByZoom[zoomLevel] = [];
        for (var cell in clusters) {
            var markers = clusters[cell].markers.map(function (marker) {
                return _this.objects.get(marker.id).location.marker;
            });
            _this.clustersByZoom[zoomLevel].push(new MarkerCluster({
                markers: markers,
                x: clusters[cell].x,
                y: clusters[cell].y,
                cellX: clusters[cell].cellX,
                cellY: clusters[cell].cellY
            }, _this));
        }
        if (_this.zoomLevel === zoomLevel) {
            _this.clusterizeMarkers();
        }
    }
    startClusterizer() {
        var _this = this;
        if (!_this.objectsRepository || _this.objects.size === 0) {
            return;
        }
        var locationField = _this.objectsRepository.getSchema().getFieldByType('location');
        if (!locationField) {
            return false;
        }
        if (!_this.clusterizerWorker) {
            _this.clusterizerWorker = new Worker(MapSVG.urls.root + "js/clustering.js");
            _this.clusterizerWorker.onmessage = function (evt) {
                if (evt.data.clusters) {
                    _this.addClustersFromWorker(evt.data.zoomLevel, evt.data.clusters);
                }
            };
        }
        var objectsData = [];
        _this.objects.forEach(function (o) {
            objectsData.push({ id: o.id, x: o.location ? o.location.marker.svgPoint.x : null, y: o.location ? o.location.marker.svgPoint.y : null });
        });
        _this.clusterizerWorker.postMessage({
            objects: objectsData,
            cellSize: 50,
            mapWidth: $(_this.containers.map).width(),
            zoomLevels: _this.zoomLevels,
            zoomLevel: _this.zoomLevel,
            zoomDelta: _this.zoomDelta,
            svgViewBox: _this.svgDefault.viewBox
        });
        _this.events.on("zoom", function () {
            _this.clusterizerWorker.postMessage({
                message: "zoom",
                zoomLevel: _this.zoomLevel
            });
        });
    }
    clusterizeMarkers(skipFitMarkers) {
        var _this = this;
        $(_this.layers.markers).children().each(function (i, obj) {
            $(obj).detach();
        });
        _this.markers.clear();
        _this.markersClusters.clear();
        _this.clustersByZoom && _this.clustersByZoom[_this.zoomLevel] && _this.clustersByZoom[_this.zoomLevel].forEach(function (cluster) {
            if (_this.options.googleMaps.on && _this.googleMaps.map && _this.googleMaps.map.getZoom() >= 17) {
                _this.markerAdd(cluster.markers[0]);
            }
            else {
                if (cluster.markers.length > 1) {
                    _this.markersClusterAdd(cluster);
                }
                else {
                    _this.markerAdd(cluster.markers[0]);
                }
            }
        });
        if (_this.editingMarker) {
            _this.markerAdd(_this.editingMarker);
        }
        if (!skipFitMarkers) {
            _this.mayBeFitMarkers();
        }
        if (_this.options.labelsMarkers.on) {
            _this.setLabelsMarkers();
        }
    }
    getCssUrl() {
        var _this = this;
        return MapSVG.urls.root + 'css/mapsvg.css';
    }
    isGeo() {
        var _this = this;
        return _this.mapIsGeo;
    }
    functionFromString(string) {
        var _this = this;
        var func;
        var error = { error: '' };
        var fn = string.trim();
        if (fn.indexOf("{") == -1 || fn.indexOf("function") !== 0 || fn.indexOf("(") == -1) {
            return { error: "MapSVG user function error: no function body." };
        }
        var fnBody = fn.substring(fn.indexOf("{") + 1, fn.lastIndexOf("}"));
        var params = fn.substring(fn.indexOf("(") + 1, fn.indexOf(")"));
        try {
            func = new Function(params, fnBody);
        }
        catch (err) {
            error = err;
        }
        if (!error.error)
            return func;
        else
            return error;
    }
    getOptions(forTemplate, forWeb, optionsDelta) {
        var _this = this;
        var options = $.extend(true, {}, _this.options);
        $.extend(true, options, optionsDelta);
        options.viewBox = _this._viewBox.toArray();
        options.filtersSchema = _this.filtersSchema.getFieldsAsArray();
        if (options.filtersSchema.length > 0) {
            options.filtersSchema.forEach(function (field) {
                if (field.type === 'distance') {
                    field.value = '';
                }
            });
        }
        delete options.markers;
        if (forTemplate) {
            options.svgFilename = options.source.split('/').pop();
            options.svgFiles = MapSVG.svgFiles;
        }
        if (forWeb)
            $.each(options, function (key, val) {
                if (JSON.stringify(val) == JSON.stringify(_this.defaults[key]))
                    delete options[key];
            });
        delete options.backend;
        return options;
    }
    setEvents(functions) {
        var _this = this;
        if (Object.keys(_this.events).length === 0) {
            _this.events = new Events(_this);
        }
        for (var eventName in functions) {
            if (typeof functions[eventName] === 'string') {
                var func = functions[eventName] != "" ? this.functionFromString(functions[eventName]) : null;
                if (func && !func.error && !(func instanceof TypeError || func instanceof SyntaxError)) {
                    _this.events[eventName] = func;
                }
                else {
                    _this.events[eventName] = null;
                }
            }
            else if (typeof functions[eventName] === 'function') {
                _this.events[eventName] = functions[eventName];
            }
            if (eventName.indexOf('directory') !== -1) {
                var event = eventName.split('.')[0];
                if (_this.controllers && _this.controllers.directory) {
                    _this.controllers.directory.events[event] = _this.events[eventName];
                }
            }
        }
        $.extend(true, _this.options.events, functions);
    }
    setActions(options) {
        var _this = this;
        $.extend(true, _this.options.actions, options);
    }
    setDetailsView(options) {
        var _this = this;
        options = options || _this.options.detailsView;
        $.extend(true, _this.options.detailsView, options);
        if (_this.options.detailsView.location === 'top' && _this.options.menu.position === 'left') {
            _this.options.detailsView.location = 'leftSidebar';
        }
        else if (_this.options.detailsView.location === 'top' && _this.options.menu.position === 'right') {
            _this.options.detailsView.location = 'rightSidebar';
        }
        if (_this.options.detailsView.location === 'near') {
            _this.options.detailsView.location = 'map';
        }
        if (!$(_this.containers.detailsView)) {
            _this.containers.detailsView = $('<div class="mapsvg-details-container"></div>')[0];
        }
        $(_this.containers.detailsView).toggleClass('mapsvg-details-container-relative', !(MapSVG.isPhone && _this.options.detailsView.mobileFullscreen) && !_this.shouldBeScrollable(_this.options.detailsView.location));
        if (_this.options.detailsView.location === 'custom') {
            $('#' + _this.options.detailsView.containerId).append($(_this.containers.detailsView));
        }
        else {
            if (MapSVG.isPhone && _this.options.detailsView.mobileFullscreen) {
                $('body').append($(_this.containers.detailsView));
                $(_this.containers.detailsView).addClass('mapsvg-container-fullscreen');
            }
            else {
                var $cont = '$' + _this.options.detailsView.location;
                _this[$cont].append($(_this.containers.detailsView));
            }
            if (_this.options.detailsView.margin) {
                $(_this.containers.detailsView).css('margin', _this.options.detailsView.margin);
            }
            $(_this.containers.detailsView).css('width', _this.options.detailsView.width);
        }
    }
    setMobileView(options) {
        var _this = this;
        $.extend(true, _this.options.mobileView, options);
    }
    attachDataToRegions(object) {
        var _this = this;
        if (object) {
            if (object.regions && object.regions.length) {
                if (typeof object.regions == 'object') {
                    object.regions.forEach(function (region) {
                        var r = _this.getRegion(region.id);
                        if (r)
                            r.objects.push(object);
                    });
                }
            }
        }
        else {
            _this.regions.forEach(function (region) {
                region.objects = [];
            });
            _this.objects.forEach(function (obj, index) {
                if (obj.regions && obj.regions.length) {
                    if (typeof obj.regions == 'object') {
                        obj.regions.forEach(function (region) {
                            var r = _this.getRegion(region.id);
                            if (r)
                                r.objects.push(obj);
                        });
                    }
                }
            });
        }
    }
    setTemplates(templates) {
        var _this = this;
        _this.templates = _this.templates || {};
        for (var name in templates) {
            if (name != undefined) {
                _this.options.templates[name] = templates[name];
                var t = _this.options.templates[name];
                if (name == 'directoryItem' || name == 'directoryCategoryItem') {
                    var dirItemTemplate = _this.options.templates.directoryItem;
                    t = '{{#each items}}<div id="mapsvg-directory-item-{{id}}" class="mapsvg-directory-item" data-object-id="{{id}}">' + dirItemTemplate + '</div>{{/each}}';
                    if (_this.options.menu.categories && _this.options.menu.categories.on && _this.options.menu.categories.groupBy) {
                        var t2 = _this.options.templates['directoryCategoryItem'];
                        t = '{{#each items}}{{#with category}}<div id="mapsvg-category-item-{{value}}" class="mapsvg-category-item" data-category-value="{{value}}">' + t2 + '</div><div class="mapsvg-category-block" data-category-id="{{value}}">{{/with}}' + t + '</div>{{/each}}';
                    }
                    name = 'directory';
                }
                try {
                    _this.templates[name] = Handlebars.compile(t, { strict: false });
                }
                catch (err) {
                    console.error(err);
                    _this.templates[name] = Handlebars.compile("", { strict: false });
                }
                if (_this.editMode && ((name == 'directory' || name == 'directoryCategoryItem') && _this.controllers && _this.controllers.directory)) {
                    _this.controllers.directory.templates.main = _this.templates[name];
                    _this.loadDirectory();
                }
            }
        }
    }
    setRegionStatus(region, status) {
        var _this = this;
        var _status = _this.regionsRepository.getSchema().getField('status').options.get(status.toString());
        if (_status.disabled)
            region.setDisabled(true);
        else
            region.setDisabled(false);
        if (_status.color) {
            region.setFill(_status.color);
        }
        else
            region.setFill();
    }
    update(options) {
        var _this = this;
        for (var key in options) {
            if (key == "regions") {
                $.each(options.regions, function (id, regionOptions) {
                    var region = _this.getRegion(id);
                    region && region.update(regionOptions);
                    if (regionOptions.gaugeValue != undefined) {
                        _this.updateGaugeMinMax();
                        _this.regionsRedrawColors();
                    }
                    if (regionOptions.disabled != undefined) {
                        _this.deselectRegion(region);
                        _this.options.regions[id] = _this.options.regions[id] || {};
                        _this.options.regions[id].disabled = region.disabled;
                    }
                });
            }
            else if (key == "markers") {
                $.each(options.markers, function (id, markerOptions) {
                    var marker = _this.getMarker(id);
                    marker && marker.update(markerOptions);
                });
            }
            else {
                var setter = 'set' + MapSVG.ucfirst(key);
                if (typeof _this[setter] == 'function')
                    this[setter](options[key]);
                else {
                    _this.options[key] = options[key];
                }
            }
        }
    }
    setTitle(title) {
        title && (this.options.title = title);
    }
    setExtension(extension) {
        var _this = this;
        if (extension) {
            _this.options.extension = extension;
        }
        else {
            delete _this.options.extension;
        }
    }
    setDisableLinks(on) {
        var _this = this;
        on = MapSVG.parseBoolean(on);
        if (on) {
            $(_this.containers.map).on('click.a.mapsvg', 'a', function (e) {
                e.preventDefault();
            });
        }
        else {
            $(_this.containers.map).off('click.a.mapsvg');
        }
        _this.disableLinks = on;
    }
    setLoadingText(val) {
        var _this = this;
        _this.options.loadingText = val;
    }
    setLockAspectRatio(onoff) {
        var _this = this;
        _this.options.lockAspectRatio = MapSVG.parseBoolean(onoff);
    }
    setMarkerEditHandler(handler) {
        var _this = this;
        _this.markerEditHandler = handler;
    }
    setRegionChoroplethField(field) {
        var _this = this;
        _this.options.regionChoroplethField = field;
        _this.redrawGauge();
    }
    setRegionEditHandler(handler) {
        this.regionEditHandler = handler;
    }
    setDisableAll(on) {
        on = MapSVG.parseBoolean(on);
        $.extend(true, this.options, { disableAll: on });
        $(this.containers.map).toggleClass('mapsvg-disabled-regions', on);
    }
    setRegionStatuses(_statuses) {
        var _this = this;
        _this.options.regionStatuses = _statuses;
        var colors = {};
        for (var status in _this.options.regionStatuses) {
            colors[status] = _this.options.regionStatuses[status].color.length ? _this.options.regionStatuses[status].color : undefined;
        }
        _this.setColors({ status: colors });
    }
    setColorsIgnore(val) {
        var _this = this;
        _this.options.colorsIgnore = MapSVG.parseBoolean(val);
        _this.regionsRedrawColors();
    }
    fixColorHash(color) {
        var hexColorNoHash = new RegExp(/^([0-9a-f]{3}|[0-9a-f]{6})$/i);
        if (color && color.match(hexColorNoHash) !== null) {
            color = '#' + color;
        }
        return color;
    }
    setColors(colors) {
        var _this = this;
        for (var i in colors) {
            if (i === 'status') {
                for (var s in colors[i]) {
                    _this.fixColorHash(colors[i][s]);
                }
            }
            else {
                if (typeof colors[i] == 'string') {
                    _this.fixColorHash(colors[i]);
                }
            }
        }
        $.extend(true, _this.options, { colors: colors });
        if (colors && colors.status)
            _this.options.colors.status = colors.status;
        if (_this.options.colors.markers) {
            for (var z in _this.options.colors.markers) {
                for (var x in _this.options.colors.markers[z]) {
                    _this.options.colors.markers[z][x] = parseInt(_this.options.colors.markers[z][x]);
                }
            }
        }
        if (_this.options.colors.background)
            $(_this.containers.map).css({ 'background': _this.options.colors.background });
        if (_this.options.colors.hover) {
            _this.options.colors.hover = (_this.options.colors.hover_this.options.colors.hover == "" + parseInt(_this.options.colors.hover)) ? parseInt(_this.options.colors.hover) : _this.options.colors.hover;
        }
        if (_this.options.colors.selected) {
            _this.options.colors.selected = (_this.options.colors.selected == "" + parseInt(_this.options.colors.selected)) ? parseInt(_this.options.colors.selected) : _this.options.colors.selected;
        }
        $(_this.containers.leftSidebar).css({ 'background-color': _this.options.colors.leftSidebar });
        $(_this.containers.rightSidebar).css({ 'background-color': _this.options.colors.rightSidebar });
        $(_this.containers.header).css({ 'background-color': _this.options.colors.header });
        $(_this.containers.footer).css({ 'background-color': _this.options.colors.footer });
        if ($(_this.containers.detailsView) && _this.options.colors.detailsView !== undefined) {
            $(_this.containers.detailsView).css({ 'background-color': _this.options.colors.detailsView });
        }
        if ($(_this.containers.directory) && _this.options.colors.directory !== undefined) {
            $(_this.containers.directory).css({ 'background-color': _this.options.colors.directory });
        }
        if ($(_this.containers.filtersModal) && _this.options.colors.modalFilters !== undefined) {
            $(_this.containers.filtersModal).css({ 'background-color': _this.options.colors.modalFilters });
        }
        if ($(_this.containers.filters) && _this.options.colors.directorySearch) {
            $(_this.containers.filters).css({
                'background-color': _this.options.colors.directorySearch
            });
        }
        else if ($(_this.containers.filters)) {
            $(_this.containers.filters).css({
                'background-color': ''
            });
        }
        if (!_this.containers.clustersCss) {
            _this.containers.clustersCss = $('<style></style>').appendTo('body')[0];
        }
        var css = '';
        if (_this.options.colors.clusters) {
            css += "background-color: " + _this.options.colors.clusters + ";";
        }
        if (_this.options.colors.clustersBorders) {
            css += "border-color: " + _this.options.colors.clustersBorders + ";";
        }
        if (_this.options.colors.clustersText) {
            css += "color: " + _this.options.colors.clustersText + ";";
        }
        $(_this.containers.clustersCss).html(".mapsvg-marker-cluster {" + css + "}");
        if (!_this.containers.clustersHoverCss) {
            _this.containers.clustersHoverCss = $('<style></style>').appendTo('body')[0];
        }
        var cssHover = "";
        if (_this.options.colors.clustersHover) {
            cssHover += "background-color: " + _this.options.colors.clustersHover + ";";
        }
        if (_this.options.colors.clustersHoverBorders) {
            cssHover += "border-color: " + _this.options.colors.clustersHoverBorders + ";";
        }
        if (_this.options.colors.clustersHoverText) {
            cssHover += "color: " + _this.options.colors.clustersHoverText + ";";
        }
        $(_this.containers.clustersHoverCss).html(".mapsvg-marker-cluster:hover {" + cssHover + "}");
        if (!_this.containers.markersCss) {
            _this.containers.markersCss = $('<style></style>').appendTo('head')[0];
        }
        var markerCssText = '.mapsvg-with-marker-active .mapsvg-marker {\n' +
            '  opacity: ' + _this.options.colors.markers.inactive.opacity / 100 + ';\n' +
            '  -webkit-filter: grayscale(' + (100 - _this.options.colors.markers.inactive.saturation) + '%);\n' +
            '  filter: grayscale(' + (100 - _this.options.colors.markers.inactive.saturation) + '%);\n' +
            '}\n' +
            '.mapsvg-with-marker-active .mapsvg-marker-active {\n' +
            '  opacity: ' + _this.options.colors.markers.active.opacity / 100 + ';\n' +
            '  -webkit-filter: grayscale(' + (100 - _this.options.colors.markers.active.saturation) + '%);\n' +
            '  filter: grayscale(' + (100 - _this.options.colors.markers.active.saturation) + '%);\n' +
            '}\n' +
            '.mapsvg-with-marker-hover .mapsvg-marker {\n' +
            '  opacity: ' + _this.options.colors.markers.unhovered.opacity / 100 + ';\n' +
            '  -webkit-filter: grayscale(' + (100 - _this.options.colors.markers.unhovered.saturation) + '%);\n' +
            '  filter: grayscale(' + (100 - _this.options.colors.markers.unhovered.saturation) + '%);\n' +
            '}\n' +
            '.mapsvg-with-marker-hover .mapsvg-marker-hover {\n' +
            '  opacity: ' + _this.options.colors.markers.hovered.opacity / 100 + ';\n' +
            '  -webkit-filter: grayscale(' + (100 - _this.options.colors.markers.hovered.saturation) + '%);\n' +
            '  filter: grayscale(' + (100 - _this.options.colors.markers.hovered.saturation) + '%);\n' +
            '}\n';
        $(_this.containers.markersCss).html(markerCssText);
        $.each(_this.options.colors, function (key, color) {
            if (color === null || color == "")
                delete _this.options.colors[key];
        });
        _this.regionsRedrawColors();
    }
    setTooltips(options) {
        var _this = this;
        if (options.on !== undefined)
            options.on = MapSVG.parseBoolean(options.on);
        $.extend(true, _this.options, { tooltips: options });
        _this.tooltip = _this.tooltip || { posOriginal: {}, posShifted: {}, posShiftedPrev: {}, mirror: {} };
        _this.tooltip.posOriginal = {};
        _this.tooltip.posShifted = {};
        _this.tooltip.posShiftedPrev = {};
        _this.tooltip.mirror = {};
        if (_this.containers.tooltip) {
            _this.containers.tooltip.className = _this.containers.tooltip.className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
        }
        else {
            _this.containers.tooltip = $('<div />').addClass('mapsvg-tooltip')[0];
            $(_this.containers.map).append(_this.containers.tooltip);
        }
        var ex = _this.options.tooltips.position.split('-');
        if (ex[0].indexOf('top') != -1 || ex[0].indexOf('bottom') != -1) {
            _this.tooltip.posOriginal.topbottom = ex[0];
        }
        if (ex[0].indexOf('left') != -1 || ex[0].indexOf('right') != -1) {
            _this.tooltip.posOriginal.leftright = ex[0];
        }
        if (ex[1]) {
            _this.tooltip.posOriginal.leftright = ex[1];
        }
        var event = 'mousemove.tooltip.mapsvg-' + $(_this.containers.map).attr('id');
        $(_this.containers.tooltip).addClass('mapsvg-tt-' + _this.options.tooltips.position);
        $(_this.containers.tooltip).css({ 'min-width': _this.options.tooltips.minWidth + 'px', 'max-width': _this.options.tooltips.maxWidth + 'px' });
        $('body').off(event).on(event, function (e) {
            MapSVG.mouse = MapSVG.mouseCoords(e);
            _this.containers.tooltip.style.left = (e.clientX + $(window).scrollLeft() - $(_this.containers.map).offset().left) + 'px';
            _this.containers.tooltip.style.top = (e.clientY + $(window).scrollTop() - $(_this.containers.map).offset().top) + 'px';
            var m = new ScreenPoint(e.clientX + $(window).scrollLeft(), e.clientY + $(window).scrollTop());
            var _tbbox = _this.containers.tooltip.getBoundingClientRect();
            var _mbbox = _this.containers.wrap.getBoundingClientRect();
            var tbbox = {
                top: _tbbox.top + $(window).scrollTop(),
                bottom: _tbbox.bottom + $(window).scrollTop(),
                left: _tbbox.left + $(window).scrollLeft(),
                right: _tbbox.right + $(window).scrollLeft(),
                width: _tbbox.width,
                height: _tbbox.height
            };
            var mbbox = {
                top: _mbbox.top + $(window).scrollTop(),
                bottom: _mbbox.bottom + $(window).scrollTop(),
                left: _mbbox.left + $(window).scrollLeft(),
                right: _mbbox.right + $(window).scrollLeft(),
                width: _mbbox.width,
                height: _mbbox.height
            };
            if (m.x > mbbox.right || m.y > mbbox.bottom || m.x < mbbox.left || m.y < mbbox.top) {
                return;
            }
            if (_this.tooltip.mirror.top || _this.tooltip.mirror.bottom) {
                if (_this.tooltip.mirror.top && m.y > _this.tooltip.mirror.top) {
                    _this.tooltip.mirror.top = 0;
                    delete _this.tooltip.posShifted.topbottom;
                }
                else if (_this.tooltip.mirror.bottom && m.y < _this.tooltip.mirror.bottom) {
                    _this.tooltip.mirror.bottom = 0;
                    delete _this.tooltip.posShifted.topbottom;
                }
            }
            else {
                if (tbbox.bottom < mbbox.top + tbbox.height) {
                    _this.tooltip.posShifted.topbottom = 'bottom';
                    _this.tooltip.mirror.top = m.y;
                }
                else if (tbbox.top > mbbox.bottom - tbbox.height) {
                    _this.tooltip.posShifted.topbottom = 'top';
                    _this.tooltip.mirror.bottom = m.y;
                }
            }
            if (_this.tooltip.mirror.right || _this.tooltip.mirror.left) {
                if (_this.tooltip.mirror.left && m.x > _this.tooltip.mirror.left) {
                    _this.tooltip.mirror.left = 0;
                    delete _this.tooltip.posShifted.leftright;
                }
                else if (_this.tooltip.mirror.right && m.x < _this.tooltip.mirror.right) {
                    _this.tooltip.mirror.right = 0;
                    delete _this.tooltip.posShifted.leftright;
                }
            }
            else {
                if (tbbox.right < mbbox.left + tbbox.width) {
                    _this.tooltip.posShifted.leftright = 'right';
                    _this.tooltip.mirror.left = m.x;
                }
                else if (tbbox.left > mbbox.right - tbbox.width) {
                    _this.tooltip.posShifted.leftright = 'left';
                    _this.tooltip.mirror.right = m.x;
                }
            }
            var pos = $.extend({}, _this.tooltip.posOriginal, _this.tooltip.posShifted);
            var _pos = [];
            pos.topbottom && _pos.push(pos.topbottom);
            pos.leftright && _pos.push(pos.leftright);
            pos = _pos.join('-');
            if (_this.tooltip.posShifted.topbottom != _this.tooltip.posOriginal.topbottom || _this.tooltip.posShifted.leftright != _this.tooltip.posOriginal.leftright) {
                _this.containers.tooltip.className = _this.containers.tooltip.className.replace(/(^|\s)mapsvg-tt-\S+/g, '');
                $(_this.containers.tooltip).addClass('mapsvg-tt-' + pos);
                _this.tooltip.posShiftedPrev = pos;
            }
        });
    }
    setPopovers(options) {
        var _this = this;
        if (options.on !== undefined)
            options.on = MapSVG.parseBoolean(options.on);
        $.extend(_this.options.popovers, options);
        if (!_this.containers.popover) {
            _this.containers.popover = $('<div />').addClass('mapsvg-popover')[0];
            _this.layers.popovers.append(_this.containers.popover);
        }
        $(_this.containers.popover).css({
            width: _this.options.popovers.width + (_this.options.popovers.width == 'auto' ? '' : 'px'),
            'max-width': _this.options.popovers.maxWidth + '%',
            'max-height': _this.options.popovers.maxHeight * $(_this.containers.wrap).outerHeight() / 100 + 'px'
        });
        if (_this.options.popovers.mobileFullscreen && MapSVG.isPhone) {
            $('body').toggleClass('mapsvg-fullscreen-popovers', true);
            $(_this.containers.popover).appendTo('body');
        }
    }
    setRegionPrefix(prefix) {
        var _this = this;
        _this.options.regionPrefix = prefix;
    }
    setInitialViewBox(v) {
        var _this = this;
        if (typeof v == 'string')
            v = v.trim().split(' ').map(function (v) { return parseFloat(v); });
        _this._viewBox = new ViewBox(v);
        if (_this.options.googleMaps.on) {
            _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
            _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
        }
        _this.zoomLevel = 0;
    }
    setViewBoxOnStart() {
        var _this = this;
        _this.viewBoxFull = _this.svgDefault.viewBox;
        _this.viewBoxFake = _this.viewBox;
        _this.whRatioFull = _this.viewBoxFull.width / _this.viewBox.width;
        _this.containers.svg.setAttribute('viewBox', _this.viewBoxFull.toString());
        _this.containers.svg.style.width = _this.svgDefault.viewBox.width + 'px';
        _this.vbStart = true;
    }
    setViewBox(viewBox, skipAdjustments) {
        var _this = this;
        let initial = false;
        if (typeof viewBox === 'undefined') {
            viewBox = _this.svgDefault.viewBox;
            initial = true;
        }
        var isZooming = viewBox.width != _this.viewBox.width || viewBox.height != _this.viewBox.height;
        _this.viewBox = viewBox;
        _this.whRatio = _this.viewBox.width / _this.viewBox.height;
        !_this.vbStart && _this.setViewBoxOnStart();
        if (initial) {
            _this._viewBox = _this.viewBox;
            _this._scale = 1;
        }
        var p = _this.options.padding;
        if (p.top) {
            _this.viewBox.y -= p.top;
            _this.viewBox.height += p.top;
        }
        if (p.right) {
            _this.viewBox.width += p.right;
        }
        if (p.bottom) {
            _this.viewBox.height += p.bottom;
        }
        if (p.left) {
            _this.viewBox.x -= p.left;
            _this.viewBox.width += p.left;
        }
        _this.scale = _this.getScale();
        _this.superScale = _this.whRatioFull * _this.svgDefault.viewBox.width / _this.viewBox.width;
        var w = _this.svgDefault.viewBox.width / $(_this.containers.map).width();
        _this.superScale = _this.superScale / w;
        _this.scroll.tx = (_this.svgDefault.viewBox.x - _this.viewBox.x) * _this.scale;
        _this.scroll.ty = (_this.svgDefault.viewBox.y - _this.viewBox.y) * _this.scale;
        if (isZooming) {
            if (!_this.options.googleMaps.on) {
                _this.enableMarkersAnimation();
            }
        }
        _this.containers.scrollpane.style.transform = 'translate(' + _this.scroll.tx + 'px,' + _this.scroll.ty + 'px)';
        _this.containers.svg.style.transform = 'scale(' + _this.superScale + ')';
        if (isZooming && !skipAdjustments) {
            _this.updateSize();
        }
        if (isZooming) {
            if (!_this.options.googleMaps.on) {
                setTimeout(function () {
                    _this.disableMarkersAnimation();
                }, 400);
            }
            if (_this.options.clustering.on) {
                _this.throttle(_this.clusterizeOnZoom, 400, _this);
            }
            else {
                _this.events.trigger('zoom');
            }
        }
        return true;
    }
    enableMarkersAnimation() {
        $(this.containers.map).removeClass('no-transitions-markers');
    }
    disableMarkersAnimation() {
        $(this.containers.map).addClass('no-transitions-markers');
    }
    clusterizeOnZoom() {
        if (this.options.googleMaps.on && this.googleMaps.map && this.zoomDelta) {
            this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
        }
        this.events.trigger('zoom');
        this.clusterizeMarkers(true);
    }
    throttle(method, delay, scope, params) {
        clearTimeout(method._tId);
        method._tId = setTimeout(function () {
            method.apply(scope, params);
        }, delay);
    }
    setViewBoxByGoogleMapBounds() {
        var _this = this;
        var googleMapBounds = _this.googleMaps.map.getBounds();
        if (!googleMapBounds)
            return;
        var googleMapBoundsJSON = googleMapBounds.toJSON();
        if (googleMapBoundsJSON.west == -180 && googleMapBoundsJSON.east == 180) {
            var center = _this.googleMaps.map.getCenter().toJSON();
        }
        var ne = new GeoPoint(googleMapBounds.getNorthEast().lat(), googleMapBounds.getNorthEast().lng());
        var sw = new GeoPoint(googleMapBounds.getSouthWest().lat(), googleMapBounds.getSouthWest().lng());
        var xyNE = _this.convertGeoToSVG(ne);
        var xySW = _this.convertGeoToSVG(sw);
        if (xyNE.x < xySW.y) {
            var mapPointsWidth = (_this.svgDefault.viewBox.width / _this.mapLonDelta) * 360;
            xySW.x = -(mapPointsWidth - xySW.y);
        }
        var width = xyNE.x - xySW.x;
        var height = xySW.y - xyNE.y;
        var viewBox = new ViewBox(xySW.x, xyNE.y, width, height);
        _this.setViewBox(viewBox);
    }
    redraw() {
        var _this = this;
        if (MapSVG.browser.ie) {
            $(_this.containers.svg).css({ height: _this.svgDefault.viewBox.height });
        }
        if (_this.options.googleMaps.on && _this.googleMaps.map) {
            google.maps.event.trigger(_this.googleMaps.map, 'resize');
        }
        else {
            _this.setViewBox(_this.viewBox);
        }
        $(_this.containers.popover) && $(_this.containers.popover).css({
            'max-height': _this.options.popovers.maxHeight * $(_this.containers.wrap).outerHeight() / 100 + 'px'
        });
        if (this.controllers && this.controllers.directory) {
            this.controllers.directory.updateTopShift();
            this.controllers.directory.updateScroll();
        }
        _this.updateSize();
    }
    setPadding(options) {
        var _this = this;
        options = options || _this.options.padding;
        for (var i in options) {
            options[i] = options[i] ? parseInt(options[i]) : 0;
        }
        $.extend(_this.options.padding, options);
        _this.setViewBox();
        _this.events.trigger('sizeChange');
    }
    setSize(width, height, responsive) {
        var _this = this;
        _this.options.width = width;
        _this.options.height = height;
        _this.options.responsive = responsive != null && responsive != undefined ? MapSVG.parseBoolean(responsive) : _this.options.responsive;
        if ((!_this.options.width && !_this.options.height)) {
            _this.options.width = _this.svgDefault.width;
            _this.options.height = _this.svgDefault.height;
        }
        else if (!_this.options.width && _this.options.height) {
            _this.options.width = _this.options.height * _this.svgDefault.width / _this.svgDefault.height;
        }
        else if (_this.options.width && !_this.options.height) {
            _this.options.height = _this.options.width * _this.svgDefault.height / _this.svgDefault.width;
        }
        _this.whRatio = _this.options.width / _this.options.height;
        _this.scale = _this.getScale();
        _this.setResponsive(responsive);
        if (_this.markers)
            _this.markersAdjustPosition();
        if (_this.options.labelsRegions.on) {
            _this.labelsRegionsAdjustPosition();
        }
        return [_this.options.width, _this.options.height];
    }
    setResponsive(on) {
        var _this = this;
        on = on != undefined ? MapSVG.parseBoolean(on) : _this.options.responsive;
        $(_this.containers.map).css({
            'width': '100%',
            'height': '0',
            'padding-bottom': (_this.viewBox.height * 100 / _this.viewBox.width) + '%'
        });
        if (on) {
            $(_this.containers.wrap).css({
                'width': '100%',
                'height': 'auto'
            });
        }
        else {
            $(_this.containers.wrap).css({
                'width': _this.options.width + 'px',
                'height': _this.options.height + 'px'
            });
        }
        $.extend(true, _this.options, { responsive: on });
        if (!_this.resizeSensor) {
            _this.resizeSensor = new ResizeSensor(_this.containers.map, function () {
                _this.redraw();
            });
        }
        _this.redraw();
    }
    setScroll(options, skipEvents) {
        var _this = this;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.limit != undefined && (options.limit = MapSVG.parseBoolean(options.limit));
        $.extend(true, _this.options, { scroll: options });
        !skipEvents && _this.setEventHandlers();
    }
    setZoom(options) {
        var _this = this;
        options = options || {};
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.fingers != undefined && (options.fingers = MapSVG.parseBoolean(options.fingers));
        options.mousewheel != undefined && (options.mousewheel = MapSVG.parseBoolean(options.mousewheel));
        options.delta = 2;
        if (options.limit) {
            if (typeof options.limit == 'string')
                options.limit = options.limit.split(';');
            options.limit = [parseInt(options.limit[0]), parseInt(options.limit[1])];
        }
        if (!_this.zoomLevels) {
            _this.setZoomLevels();
        }
        $.extend(true, _this.options, { zoom: options });
        $(_this.containers.map).off('mousewheel.mapsvg');
        if (_this.options.zoom.mousewheel) {
            if (MapSVG.browser.firefox) {
                _this.firefoxScroll = { insideIframe: false, scrollX: 0, scrollY: 0 };
                $(_this.containers.map).on('mouseenter', function () {
                    _this.firefoxScroll.insideIframe = true;
                    _this.firefoxScroll.scrollX = window.scrollX;
                    _this.firefoxScroll.scrollY = window.scrollY;
                }).on('mouseleave', function () {
                    _this.firefoxScroll.insideIframe = false;
                });
                $(document).scroll(function () {
                    if (_this.firefoxScroll.insideIframe)
                        window.scrollTo(_this.firefoxScroll.scrollX, _this.firefoxScroll.scrollY);
                });
            }
            $(_this.containers.map).on('mousewheel.mapsvg', function (event, delta, deltaX, deltaY) {
                if ($(event.target).hasClass('mapsvg-popover') || $(event.target).closest('.mapsvg-popover').length)
                    return;
                event.preventDefault();
                var d = delta > 0 ? 1 : -1;
                var m = MapSVG.mouseCoords(event);
                m.x = m.x - $(_this.containers.svg).offset().left;
                m.y = m.y - $(_this.containers.svg).offset().top;
                var center = _this.convertPixelToSVG(new ScreenPoint(m.x, m.y));
                d > 0 ? _this.zoomIn(center) : _this.zoomOut(center);
                return false;
            });
        }
        _this.canZoom = true;
    }
    setControls(options) {
        var _this = this;
        options = options || {};
        $.extend(true, _this.options, { controls: options });
        _this.options.controls.zoom = MapSVG.parseBoolean(_this.options.controls.zoom);
        _this.options.controls.zoomReset = MapSVG.parseBoolean(_this.options.controls.zoomReset);
        _this.options.controls.userLocation = MapSVG.parseBoolean(_this.options.controls.userLocation);
        var loc = _this.options.controls.location || 'right';
        if (!_this.containers.controls) {
            var buttons = $('<div />').addClass('mapsvg-buttons');
            var zoomGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            var zoomIn = $('<div />').addClass('mapsvg-btn-map mapsvg-in');
            zoomIn.on('touchend click', function (e) {
                if (e.cancelable) {
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.zoomIn();
            });
            var zoomOut = $('<div />').addClass('mapsvg-btn-map mapsvg-out');
            zoomOut.on('touchend click', function (e) {
                if (e.cancelable) {
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.zoomOut();
            });
            zoomGroup.append(zoomIn).append(zoomOut);
            var location = $('<div />').addClass('mapsvg-btn-map mapsvg-btn-location');
            location.on('touchend click', function (e) {
                if (e.cancelable) {
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.showUserLocation(function (location) {
                    if (_this.options.scroll.on) {
                        _this.centerOn(location.marker);
                    }
                });
            });
            var userLocationIcon = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 447.342 447.342" style="enable-background:new 0 0 447.342 447.342;" xml:space="preserve"><path d="M443.537,3.805c-3.84-3.84-9.686-4.893-14.625-2.613L7.553,195.239c-4.827,2.215-7.807,7.153-7.535,12.459 c0.254,5.305,3.727,9.908,8.762,11.63l129.476,44.289c21.349,7.314,38.125,24.089,45.438,45.438l44.321,129.509 c1.72,5.018,6.325,8.491,11.63,8.762c5.306,0.271,10.244-2.725,12.458-7.535L446.15,18.429 C448.428,13.491,447.377,7.644,443.537,3.805z"/></svg>';
            location.html(userLocationIcon);
            var locationGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            locationGroup.append(location);
            var zoomResetIcon = '<svg height="14px" version="1.1" viewBox="0 0 14 14" width="14px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#000000" transform="translate(-215.000000, -257.000000)"><g id="fullscreen" transform="translate(215.000000, 257.000000)"><path d="M2,9 L0,9 L0,14 L5,14 L5,12 L2,12 L2,9 L2,9 Z M0,5 L2,5 L2,2 L5,2 L5,0 L0,0 L0,5 L0,5 Z M12,12 L9,12 L9,14 L14,14 L14,9 L12,9 L12,12 L12,12 Z M9,0 L9,2 L12,2 L12,5 L14,5 L14,0 L9,0 L9,0 Z" /></g></g></g></svg>';
            var zoomResetButton = $('<div />').html(zoomResetIcon).addClass('mapsvg-btn-map mapsvg-btn-zoom-reset');
            zoomResetButton.on('touchend click', function (e) {
                if (e.cancelable) {
                    e.preventDefault();
                }
                e.stopPropagation();
                _this.viewBoxReset(true);
            });
            var zoomResetGroup = $('<div />').addClass('mapsvg-btn-group').appendTo(buttons);
            zoomResetGroup.append(zoomResetButton);
            _this.containers.controls = buttons[0];
            _this.controls = {
                zoom: zoomGroup[0],
                userLocation: locationGroup[0],
                zoomReset: zoomResetGroup[0]
            };
            $(_this.containers.map).append($(_this.containers.controls));
        }
        $(_this.controls.zoom).toggle(_this.options.controls.zoom);
        $(_this.controls.userLocation).toggle(_this.options.controls.userLocation);
        $(_this.controls.zoomReset).toggle(_this.options.controls.zoomReset);
        $(_this.containers.controls).removeClass('left');
        $(_this.containers.controls).removeClass('right');
        loc == 'right' && $(_this.containers.controls).addClass('right')
            ||
                loc == 'left' && $(_this.containers.controls).addClass('left');
    }
    setZoomLevels() {
        var _this = this;
        _this.zoomLevels = {};
        var _scale = 1;
        for (var i = 0; i <= 20; i++) {
            _this.zoomLevels[i + ''] = {
                _scale: _scale,
                viewBox: new ViewBox(0, 0, _this._viewBox.width / _scale, _this._viewBox.height / _scale)
            };
            _scale = _scale * _this.options.zoom.delta;
        }
        _scale = 1;
        for (var i = 0; i >= -20; i--) {
            _this.zoomLevels[i + ''] = {
                _scale: _scale,
                viewBox: new ViewBox(0, 0, _this._viewBox.width / _scale, _this._viewBox.height / _scale)
            };
            _scale = _scale / _this.options.zoom.delta;
        }
    }
    setCursor(type) {
        var _this = this;
        type = type == 'pointer' ? 'pointer' : 'default';
        _this.options.cursor = type;
        if (type == 'pointer')
            $(_this.containers.map).addClass('mapsvg-cursor-pointer');
        else
            $(_this.containers.map).removeClass('mapsvg-cursor-pointer');
    }
    setMultiSelect(on, deselect) {
        var _this = this;
        _this.options.multiSelect = MapSVG.parseBoolean(on);
        if (deselect !== false)
            _this.deselectAllRegions();
    }
    setGauge(options) {
        var _this = this;
        options = options || _this.options.gauge;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, { gauge: options });
        var needsRedraw = false;
        if (!_this.containers.legend) {
            _this.containers.legend = {
                gradient: $('<td>&nbsp;</td>').addClass('mapsvg-gauge-gradient')[0],
                container: $('<div />').addClass('mapsvg-gauge').hide()[0],
                table: $('<table />')[0],
                labelLow: $('<td>' + _this.options.gauge.labels.low + '</td>')[0],
                labelHigh: $('<td>' + _this.options.gauge.labels.high + '</td>')[0]
            };
            _this.setGaugeGradientCSS();
            var tr = $('<tr />');
            tr.append(_this.containers.legend.labelLow);
            tr.append(_this.containers.legend.gradient);
            tr.append(_this.containers.legend.labelHigh);
            $(_this.containers.legend.table).append(tr);
            $(_this.containers.legend.container).append(_this.containers.legend.table);
            $(_this.containers.map).append(_this.containers.legend.container);
        }
        if (!_this.options.gauge.on && $(_this.containers.legend.container).is(":visible")) {
            $(_this.containers.legend.container).hide();
            needsRedraw = true;
        }
        else if (_this.options.gauge.on && !$(_this.containers.legend.container).is(":visible")) {
            $(_this.containers.legend.container).show();
            needsRedraw = true;
            _this.regionsRepository.events.on('change', function () {
                _this.redrawGauge();
            });
        }
        if (options.colors) {
            _this.options.gauge.colors.lowRGB = tinycolor(_this.options.gauge.colors.low).toRgb();
            _this.options.gauge.colors.highRGB = tinycolor(_this.options.gauge.colors.high).toRgb();
            _this.options.gauge.colors.diffRGB = {
                r: _this.options.gauge.colors.highRGB.r - _this.options.gauge.colors.lowRGB.r,
                g: _this.options.gauge.colors.highRGB.g - _this.options.gauge.colors.lowRGB.g,
                b: _this.options.gauge.colors.highRGB.b - _this.options.gauge.colors.lowRGB.b,
                a: _this.options.gauge.colors.highRGB.a - _this.options.gauge.colors.lowRGB.a
            };
            needsRedraw = true;
            _this.containers.legend && _this.setGaugeGradientCSS();
        }
        if (options.labels) {
            $(_this.containers.legend.labelLow).html(_this.options.gauge.labels.low);
            $(_this.containers.legend.labelHigh).html(_this.options.gauge.labels.high);
        }
        needsRedraw && _this.redrawGauge();
    }
    redrawGauge() {
        var _this = this;
        _this.updateGaugeMinMax();
        _this.regionsRedrawColors();
    }
    updateGaugeMinMax() {
        var _this = this;
        _this.options.gauge.min = 0;
        _this.options.gauge.max = null;
        var values = [];
        _this.regions.forEach(function (r) {
            var gauge = r.data && r.data[_this.options.regionChoroplethField];
            gauge != undefined && values.push(gauge);
        });
        if (values.length > 0) {
            _this.options.gauge.min = values.length == 1 ? 0 : Math.min.apply(null, values);
            _this.options.gauge.max = Math.max.apply(null, values);
            _this.options.gauge.maxAdjusted = _this.options.gauge.max - _this.options.gauge.min;
        }
    }
    setGaugeGradientCSS() {
        var _this = this;
        $(_this.containers.legend.gradient).css({
            'background': 'linear-gradient(to right,' + _this.options.gauge.colors.low + ' 1%,' + _this.options.gauge.colors.high + ' 100%)',
            'filter': 'progid:DXImageTransform.Microsoft.gradient( startColorstr="' + _this.options.gauge.colors.low + '", endColorstr="' + _this.options.gauge.colors.high + '",GradientType=1 )'
        });
    }
    setCss(css) {
        var _this = this;
        _this.options.css = css || _this.options.css.replace(/%id%/g, '' + this.id);
        _this.liveCss = _this.liveCss || $('<style></style>').appendTo('head')[0];
        $(_this.liveCss).html(_this.options.css);
    }
    setFilters(options) {
        var _this = this;
        options = options || _this.options.filters;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.hide != undefined && (options.hide = MapSVG.parseBoolean(options.hide));
        $.extend(true, _this.options, { filters: options });
        var scrollable = false;
        if (['leftSidebar', 'rightSidebar', 'header', 'footer', 'custom', 'mapContainer'].indexOf(_this.options.filters.location) === -1) {
            _this.options.filters.location = 'leftSidebar';
        }
        if (_this.options.filters.on) {
            if (_this.formBuilder) {
                _this.formBuilder.destroy();
            }
            if (!$(_this.containers.filters)) {
                _this.containers.filters = $('<div class="mapsvg-filters-wrap"></div>')[0];
            }
            $(_this.containers.filters).empty();
            $(_this.containers.filters).show();
            $(_this.containers.filters).css({
                'background-color': _this.options.colors.directorySearch,
            });
            if ($(_this.containers.filtersModal)) {
                $(_this.containers.filtersModal).css({ width: _this.options.filters.width });
            }
            if (_this.options.filters.location === 'custom') {
                $(_this.containers.filters).removeClass('mapsvg-filter-container-custom').addClass('mapsvg-filter-container-custom');
                if ($('#' + _this.options.filters.containerId).length) {
                    $('#' + _this.options.filters.containerId).append(_this.containers.filters);
                }
                else {
                    $(_this.containers.filters).hide();
                    console.error('MapSVG: filter container #' + _this.options.filters.containerId + ' does not exists');
                }
            }
            else {
                if (MapSVG.isPhone) {
                    $(_this.containers.header).append($(_this.containers.filters));
                    _this.setContainers({ header: { on: true } });
                }
                else {
                    var location = MapSVG.isPhone ? 'header' : _this.options.filters.location;
                    var $cont = '$' + location;
                    if (_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location) {
                        _this.controllers.directory.view.find('.mapsvg-directory-filter-wrap').append($(_this.containers.filters));
                        _this.controllers.directory.updateTopShift();
                    }
                    else {
                        _this[$cont].append($(_this.containers.filters));
                        _this.controllers.directory && _this.controllers.directory.updateTopShift();
                    }
                }
            }
            _this.loadFiltersController(_this.containers.filters, false);
            _this.updateFiltersState();
        }
        else {
            if ($(_this.containers.filters)) {
                $(_this.containers.filters).empty();
                $(_this.containers.filters).hide();
            }
        }
        if (_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location) {
            _this.controllers.directory.updateTopShift();
        }
    }
    updateFiltersState() {
        var _this = this;
        $(_this.containers.filterTags) && $(_this.containers.filterTags).empty();
        if ((_this.options.filters && _this.options.filters.on) || _this.objectsRepository.query.hasFilters()) {
            for (var field_name in _this.objectsRepository.query.filters) {
                var field_value = _this.objectsRepository.query.filters[field_name];
                var _field_name = field_name;
                var filterField = _this.filtersSchema.getField(_field_name);
                if (_this.options.filters.on && filterField) {
                    $(_this.containers.filters).find('select[data-parameter-name="' + _field_name + '"],radio[data-parameter-name="\'+_field_name+\'"]')
                        .data('ignoreSelect2Change', true)
                        .val(field_value)
                        .trigger('change');
                }
                else {
                    if (field_name == 'regions') {
                        _field_name = '';
                        field_value = _this.getRegion(field_value).title || field_value;
                    }
                    else {
                        _field_name = filterField && filterField.label;
                    }
                    if (field_name !== 'distance') {
                        if (!_this.containers.filterTags) {
                            _this.containers.filterTags = $('<div class="mapsvg-filter-tags"></div>')[0];
                            if ($(_this.containers.filters)) {
                            }
                            else {
                                if (_this.options.menu.on && _this.controllers.directory) {
                                    _this.controllers.directory.toolbarView.append(_this.containers.filterTags);
                                    _this.controllers.directory.updateTopShift();
                                }
                                else {
                                    $(_this.containers.map).append(_this.containers.filterTags);
                                    if (_this.options.zoom.buttons.on) {
                                        if (_this.options.layersControl.on) {
                                            if (_this.options.layersControl.position == 'top-left') {
                                                $(_this.containers.filterTags).css({
                                                    right: 0,
                                                    bottom: 0
                                                });
                                            }
                                            else {
                                                $(_this.containers.filterTags).css({
                                                    bottom: 0
                                                });
                                            }
                                        }
                                        else {
                                            if (_this.options.zoom.buttons.location == 'left') {
                                                $(_this.containers.filterTags).css({
                                                    right: 0
                                                });
                                            }
                                        }
                                    }
                                }
                            }
                            $(_this.containers.filterTags).on('click', '.mapsvg-filter-delete', function (e) {
                                var filterField = $(this).data('filter');
                                $(this).parent().remove();
                                _this.objectsRepository.query.removeFilter(filterField);
                                _this.deselectAllRegions();
                                _this.loadDataObjects();
                            });
                        }
                        $(_this.containers.filterTags).append('<div class="mapsvg-filter-tag">' + (_field_name ? _field_name + ': ' : '') + field_value + ' <span class="mapsvg-filter-delete" data-filter="' + field_name + '">×</span></div>');
                    }
                }
            }
        }
        else {
        }
    }
    setContainers(options) {
        var _this = this;
        if (!this.containersCreated) {
            this.containers.wrapAll = document.createElement('div');
            this.containers.wrapAll.classList.add('mapsvg-wrap-all');
            this.containers.wrapAll.id = 'mapsvg-map-' + this.id;
            this.containers.wrapAll.setAttribute('data-map-id', (this.id).toString());
            this.containers.wrap = document.createElement('div');
            this.containers.wrap.classList.add('mapsvg-wrap');
            this.containers.mapContainer = document.createElement('div');
            this.containers.mapContainer.classList.add('mapsvg-map-container');
            this.containers.leftSidebar = document.createElement('div');
            this.containers.leftSidebar.className = 'mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-left';
            this.containers.rightSidebar = document.createElement('div');
            this.containers.rightSidebar.className = "mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-right";
            this.containers.header = document.createElement('div');
            this.containers.header.className = "mapsvg-header mapsvg-top-container";
            this.containers.footer = document.createElement('div');
            this.containers.footer.className = "mapsvg-footer mapsvg-top-container";
            _this.containers.wrapAll = $('<div class="mapsvg-wrap-all"></div>').attr('id', 'mapsvg-map-' + this.id).attr('data-map-id', this.id)[0];
            _this.containers.wrap = $('<div class="mapsvg-wrap"></div>')[0];
            _this.containers.mapContainer = $('<div class="mapsvg-map-container"></div>')[0];
            _this.containers.leftSidebar = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-left"></div>')[0];
            _this.containers.rightSidebar = $('<div class="mapsvg-sidebar mapsvg-top-container mapsvg-sidebar-right"></div>')[0];
            _this.containers.header = $('<div class="mapsvg-header mapsvg-top-container"></div>')[0];
            _this.containers.footer = $('<div class="mapsvg-footer mapsvg-top-container"></div>')[0];
            $(_this.containers.wrapAll).insertBefore(_this.containers.map);
            $(_this.containers.wrapAll).append(_this.containers.header);
            $(_this.containers.wrapAll).append(_this.containers.wrap);
            $(_this.containers.wrapAll).append(_this.containers.footer);
            $(_this.containers.mapContainer).append(_this.containers.map);
            $(_this.containers.wrap).append(_this.containers.leftSidebar);
            $(_this.containers.wrap).append(_this.containers.mapContainer);
            $(_this.containers.wrap).append(_this.containers.rightSidebar);
            _this.containersCreated = true;
        }
        options = options || _this.options;
        for (var contName in options) {
            if (options[contName].on !== undefined) {
                options[contName].on = MapSVG.parseBoolean(options[contName].on);
            }
            if (options[contName].width) {
                if ((typeof options[contName].width != 'string') || options[contName].width.indexOf('px') === -1 && options[contName].width.indexOf('%') === -1 && options[contName].width !== 'auto') {
                    options[contName].width = options[contName].width + 'px';
                }
                _this.containers[contName].css({ 'flex-basis': options[contName].width });
            }
            if (options[contName].height) {
                if ((typeof options[contName].height != 'string') || options[contName].height.indexOf('px') === -1 && options[contName].height.indexOf('%') === -1 && options[contName].height !== 'auto') {
                    options[contName].height = options[contName].height + 'px';
                }
                _this.containers[contName].css({ 'flex-basis': options[contName].height, height: options[contName].height });
            }
            $.extend(true, _this.options, { containers: options });
            var on = _this.options.containers[contName].on;
            if (MapSVG.isPhone && _this.options.menu.hideOnMobile && _this.options.menu.location === contName && ['leftSidebar', 'rightSidebar'].indexOf(contName) !== -1) {
                on = false;
            }
            else if (MapSVG.isPhone && _this.options.menu.location === 'custom' && ['leftSidebar', 'rightSidebar'].indexOf(contName) !== -1) {
                on = false;
                $(_this.containers.wrapAll).addClass('mapsvg-hide-map-list-buttons');
            }
            else if (MapSVG.isPhone && !_this.options.menu.hideOnMobile && _this.options.menu.location === contName && ['leftSidebar', 'rightSidebar'].indexOf(contName) !== -1) {
                $(_this.containers.wrapAll).addClass('mapsvg-hide-map-list-buttons');
                $(_this.containers.wrapAll).addClass('mapsvg-directory-visible');
            }
            $(_this.containers[contName]).toggle(on);
        }
        _this.setDetailsView();
    }
    shouldBeScrollable(container) {
        var _this = this;
        switch (container) {
            case 'mapContainer':
            case 'leftSidebar':
            case 'rightSidebar':
                return true;
                break;
            case 'custom':
                return false;
                break;
            case 'header':
            case 'footer':
                if (_this.options.containers[container].height && _this.options.containers[container].height !== 'auto' && _this.options.containers[container].height !== '100%') {
                    return true;
                }
                else {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }
    setDirectory(options) {
        var _this = this;
        return _this.setMenu(options);
    }
    setMenu(options) {
        var _this = this;
        options = options || _this.options.menu;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        options.search != undefined && (options.search = MapSVG.parseBoolean(options.search));
        options.showMapOnClick != undefined && (options.showMapOnClick = MapSVG.parseBoolean(options.showMapOnClick));
        options.searchFallback != undefined && (options.searchFallback = MapSVG.parseBoolean(options.searchFallback));
        options.customContainer != undefined && (options.customContainer = MapSVG.parseBoolean(options.customContainer));
        $.extend(true, _this.options, { menu: options });
        _this.controllers = _this.controllers || {};
        if (!_this.containers.directory) {
            _this.containers.directory = $('<div class="mapsvg-directory"></div>')[0];
        }
        $(_this.containers.directory).toggleClass('flex', _this.shouldBeScrollable(_this.options.menu.location));
        if (_this.options.menu.on) {
            if (!_this.controllers.directory) {
                _this.controllers.directory = new DirectoryController({
                    container: _this.containers.directory,
                    template: _this.templates.directory,
                    mapsvg: _this,
                    repository: _this.options.menu.source === 'regions' ? _this.regionsRepository : _this.objectsRepository,
                    scrollable: _this.shouldBeScrollable(_this.options.menu.location),
                    events: {
                        'click': _this.events['click.directoryItem'],
                        'mouseover': _this.events['mouseover.directoryItem'],
                        'mouseout': _this.events['mouseout.directoryItem']
                    }
                });
            }
            else {
                _this.controllers.directory.database = _this.options.menu.source === 'regions' ? _this.regionsRepository : _this.objectsRepository;
                _this.controllers.directory.database.sortBy = _this.options.menu.sortBy;
                _this.controllers.directory.database.sortDir = _this.options.menu.sortDirection;
                if (options.filterout) {
                    var f = {};
                    f[_this.options.menu.filterout.field] = _this.options.menu.filterout.val;
                    _this.controllers.directory.database.query.setFilterOut(f);
                }
                _this.controllers.directory.scrollable = _this.shouldBeScrollable(_this.options.menu.location);
            }
            var $container;
            if (MapSVG.isPhone && _this.options.menu.hideOnMobile) {
                $container = $(_this.containers.leftSidebar);
            }
            else {
                $container = _this.options.menu.location !== 'custom' ? _this['$' + _this.options.menu.location] : $('#' + _this.options.menu.containerId);
            }
            $container.append(_this.containers.directory);
            if (_this.options.colors.directory) {
                $(_this.containers.directory).css({
                    'background-color': _this.options.colors.directory
                });
            }
            _this.setFilters();
            _this.setTemplates({ directoryItem: _this.options.templates.directoryItem });
            if ((_this.options.menu.source === 'regions' && _this.regionsRepository.loaded) || (_this.options.menu.source === 'database' && _this.objectsRepository.loaded)) {
                if (_this.editMode && (options.sortBy || options.sortDirection || options.filterout)) {
                    _this.controllers.directory.database.getAll();
                }
                _this.loadDirectory();
            }
        }
        else {
            _this.controllers.directory && _this.controllers.directory.destroy();
            _this.controllers.directory = null;
        }
    }
    setDatabase(options) {
        var _this = this;
        options = options || _this.options.database;
        if (options.pagination) {
            if (options.pagination.on != undefined) {
                options.pagination.on = MapSVG.parseBoolean(options.pagination.on);
            }
            if (options.pagination.perpage != undefined) {
                options.pagination.perpage = parseInt(options.pagination.perpage);
            }
        }
        $.extend(true, _this.options, { database: options });
        if (options.pagination) {
            if (options.pagination.on !== undefined || options.pagination.perpage) {
                var query = new Query({
                    perpage: _this.options.database.pagination.on ? _this.options.database.pagination.perpage : 0
                });
                _this.objectsRepository.find(query);
            }
            else {
                _this.setPagination();
            }
        }
    }
    setGoogleMaps(options) {
        var _this = this;
        options = options || _this.options.googleMaps;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        if (!_this.googleMaps) {
            _this.googleMaps = { loaded: false, initialized: false, map: null, overlay: null };
        }
        $.extend(true, _this.options, { googleMaps: options });
        if (_this.options.googleMaps.on) {
            $(_this.containers.map).toggleClass('mapsvg-with-google-map', true);
            if (!MapSVG.googleMapsApiLoaded) {
                _this.loadGoogleMapsAPI(function () {
                    _this.setGoogleMaps();
                }, function () {
                    _this.setGoogleMaps({ on: false });
                });
            }
            else {
                if (!_this.googleMaps.map) {
                    _this.containers.googleMaps = $('<div class="mapsvg-layer mapsvg-layer-gm" id="mapsvg-google-maps-' + _this.id + '"></div>').prependTo(_this.containers.map)[0];
                    $(_this.containers.googleMaps).css({
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        bottom: 0,
                        right: 0,
                        'z-index': '0'
                    });
                    _this.googleMaps.map = new google.maps.Map(_this.containers.googleMaps, {
                        mapTypeId: options.type,
                        fullscreenControl: false,
                        keyboardShortcuts: false,
                        mapTypeControl: false,
                        scaleControl: false,
                        scrollwheel: false,
                        streetViewControl: false,
                        zoomControl: false,
                        styles: options.styleJSON
                    });
                    var overlay;
                    USGSOverlay.prototype = new google.maps.OverlayView();
                    function USGSOverlay(bounds, map) {
                        this.bounds_ = bounds;
                        this.map_ = map;
                        this.setMap(map);
                        this.prevCoords = {
                            sw: { x: 0, y: 0 },
                            sw2: { x: 0, y: 0 },
                            ne: { x: 0, y: 0 },
                            ne2: { x: 0, y: 0 }
                        };
                    }
                    USGSOverlay.prototype.onAdd = function () {
                        var div = document.createElement('div');
                        div.style.borderStyle = 'none';
                        div.style.borderWidth = '0px';
                        div.style.position = 'absolute';
                        this.div_ = div;
                        var panes = this.getPanes();
                        panes.overlayLayer.appendChild(div);
                    };
                    USGSOverlay.prototype.draw = function (t) {
                        if (_this.isScrolling)
                            return;
                        var overlayProjection = this.getProjection();
                        if (!overlayProjection)
                            return;
                        var geoSW = this.bounds_.getSouthWest();
                        var geoNE = this.bounds_.getNorthEast();
                        var coords = {
                            sw: overlayProjection.fromLatLngToDivPixel(geoSW),
                            ne: overlayProjection.fromLatLngToDivPixel(geoNE),
                            sw2: overlayProjection.fromLatLngToContainerPixel(geoSW),
                            ne2: overlayProjection.fromLatLngToContainerPixel(geoNE)
                        };
                        var ww = overlayProjection.getWorldWidth();
                        if (this.prevCoords.sw) {
                            if (coords.ne.x < coords.sw.x) {
                                if (Math.abs(this.prevCoords.sw.x - coords.sw.x) > Math.abs(this.prevCoords.ne.x - coords.ne.x)) {
                                    coords.sw.x = coords.sw.x - ww;
                                }
                                else {
                                    coords.ne.x = coords.ne.x + ww;
                                }
                                if (Math.abs(this.prevCoords.sw2.x - coords.sw2.x) > Math.abs(this.prevCoords.ne2.x - coords.ne2.x)) {
                                    coords.sw2.x = coords.sw2.x - ww;
                                }
                                else {
                                    coords.ne2.x = coords.ne2.x + ww;
                                }
                            }
                        }
                        for (var i in this.prevCoords) { }
                        this.prevCoords = coords;
                        var scale = (coords.ne2.x - coords.sw2.x) / _this.svgDefault.viewBox.width;
                        var vb = new ViewBox(_this.svgDefault.viewBox.x - coords.sw2.x / scale, _this.svgDefault.viewBox.y - coords.ne2.y / scale, $(_this.containers.map).width() / scale, $(_this.containers.map).outerHeight() / scale);
                        _this.setViewBox(vb);
                    };
                    var southWest = new google.maps.LatLng(_this.geoViewBox.sw.lat, _this.geoViewBox.sw.lng);
                    var northEast = new google.maps.LatLng(_this.geoViewBox.ne.lat, _this.geoViewBox.ne.lng);
                    var bounds = new google.maps.LatLngBounds(southWest, northEast);
                    _this.googleMaps.overlay = new USGSOverlay(bounds, _this.googleMaps.map);
                    if (!_this.options.googleMaps.center || !_this.options.googleMaps.zoom) {
                        _this.googleMaps.map.fitBounds(bounds, 0);
                    }
                    else {
                        _this.googleMaps.map.setZoom(_this.options.googleMaps.zoom);
                        _this.googleMaps.map.setCenter(_this.options.googleMaps.center);
                    }
                    _this.googleMaps.initialized = true;
                    _this.googleMaps.map.addListener('idle', function () {
                        _this.isZooming = false;
                    });
                    google.maps.event.addListenerOnce(_this.googleMaps.map, 'idle', function () {
                        setTimeout(function () {
                            $(_this.containers.map).addClass('mapsvg-fade-in');
                            setTimeout(function () {
                                $(_this.containers.map).removeClass('mapsvg-google-map-loading');
                                $(_this.containers.map).removeClass('mapsvg-fade-in');
                                if (!_this.options.googleMaps.center || !_this.options.googleMaps.zoom) {
                                    _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
                                    _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
                                }
                                _this.zoomDelta = _this.options.googleMaps.zoom - _this.zoomLevel;
                                _this.events.trigger('googleMapsLoaded');
                            }, 300);
                        }, 1);
                    });
                }
                else {
                    $(_this.containers.map).toggleClass('mapsvg-with-google-map', true);
                    $(_this.containers.googleMaps) && $(_this.containers.googleMaps).show();
                    if (options.type) {
                        _this.googleMaps.map.setMapTypeId(options.type);
                    }
                }
            }
        }
        else {
            $(_this.containers.map).toggleClass('mapsvg-with-google-map', false);
            $(_this.containers.googleMaps) && $(_this.containers.googleMaps).hide();
            _this.googleMaps.initialized = false;
        }
    }
    loadGoogleMapsAPI(callback, fail) {
        var _this = this;
        if (window.google !== undefined && google.maps) {
            MapSVG.googleMapsApiLoaded = true;
        }
        if (MapSVG.googleMapsApiLoaded) {
            if (typeof callback == 'function') {
                callback();
            }
            return;
        }
        MapSVG.googleMapsLoadCallbacks = MapSVG.googleMapsLoadCallbacks || [];
        if (typeof callback == 'function') {
            MapSVG.googleMapsLoadCallbacks.push(callback);
        }
        if (MapSVG.googleMapsApiIsLoading) {
            return;
        }
        MapSVG.googleMapsApiIsLoading = true;
        window.gm_authFailure = function () {
            if (MapSVG.GoogleMapBadApiKey) {
                MapSVG.GoogleMapBadApiKey();
            }
            else {
                alert("Google maps API key is incorrect.");
            }
        };
        _this.googleMapsScript = document.createElement('script');
        _this.googleMapsScript.onload = function () {
            MapSVG.googleMapsApiLoaded = true;
            MapSVG.googleMapsLoadCallbacks.forEach(function (_callback) {
                if (typeof callback == 'function')
                    _callback();
            });
        };
        var gmLibraries = [];
        if (_this.options.googleMaps.drawingTools) {
            gmLibraries.push('drawing');
        }
        if (_this.options.googleMaps.geometry) {
            gmLibraries.push('geometry');
        }
        var libraries = '';
        if (gmLibraries.length > 0) {
            libraries = '&libraries=' + gmLibraries.join(',');
        }
        _this.googleMapsScript.src = 'https://maps.googleapis.com/maps/api/js?language=en&key=' + _this.options.googleMaps.apiKey + libraries;
        document.head.appendChild(_this.googleMapsScript);
    }
    loadDetailsView(obj) {
        var _this = this;
        _this.controllers.popover && _this.controllers.popover.close();
        if (_this.controllers.detailsView)
            _this.controllers.detailsView.destroy();
        _this.controllers.detailsView = new MapSVG.DetailsController({
            autoresize: MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom' ? false : _this.options.detailsView.autoresize,
            container: $(_this.containers.detailsView),
            template: obj instanceof MapSVG.Region ? _this.templates.detailsViewRegion : _this.templates.detailsView,
            mapsvg: _this,
            data: obj instanceof MapSVG.Region ? obj.forTemplate() : obj,
            modal: (MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom'),
            scrollable: (MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom') || _this.shouldBeScrollable(_this.options.detailsView.location),
            withToolbar: !(MapSVG.isPhone && _this.options.detailsView.mobileFullscreen && _this.options.detailsView.location !== 'custom') && _this.shouldBeScrollable(_this.options.detailsView.location),
            events: {
                'shown'(mapsvg) {
                    if (_this.events['shown.detailsView']) {
                        try {
                            _this.events['shown.detailsView'].forEach(function (handler) {
                                handler.call(this, _this);
                            });
                        }
                        catch (err) {
                            console.log(err);
                        }
                    }
                    _this.events.trigger('detailsShown');
                },
                'closed'(mapsvg) {
                    _this.deselectAllRegions();
                    _this.deselectAllMarkers();
                    _this.controllers && _this.controllers.directory && _this.controllers.directory.deselectItems();
                    if (_this.events['closed.detailsView']) {
                        try {
                            _this.events['closed.detailsView'].forEach(function (handler) {
                                handler.call(this, _this);
                            });
                        }
                        catch (err) {
                            console.log(err);
                        }
                    }
                    _this.events.trigger('detailsClosed');
                }
            }
        });
    }
    loadFiltersModal() {
        var _this = this;
        if (_this.options.filters.modalLocation != 'custom') {
            if (!_this.containers.filtersModal) {
                _this.containers.filtersModal = $('<div class="mapsvg-details-container mapsvg-filters-wrap"></div>')[0];
            }
            _this.setColors();
            $(_this.containers.filtersModal).css({ width: _this.options.filters.width });
            if (MapSVG.isPhone) {
                $('body').append($(_this.containers.filtersModal));
                $(_this.containers.filtersModal).css({ width: '' });
            }
            else {
                var $cont = '$' + _this.options.filters.modalLocation;
                _this[$cont].append($(_this.containers.filtersModal));
            }
        }
        else {
            _this.containers.filtersModal = $('#' + _this.options.filters.containerId)[0];
            $(_this.containers.filtersModal).css({ width: '' });
        }
        _this.loadFiltersController(_this.containers.filtersModal, true);
    }
    loadFiltersController(container, modal = false) {
        var _this = this;
        if (_this.filtersSchema.getFields().size === 0) {
            return;
        }
        let filtersInDirectory, filtersHide;
        if (MapSVG.isPhone) {
            filtersInDirectory = true;
            filtersHide = _this.options.filters.hideOnMobile;
        }
        else {
            filtersInDirectory = (_this.options.menu.on && _this.controllers.directory && _this.options.menu.location === _this.options.filters.location);
            filtersHide = _this.options.filters.hide;
        }
        var scrollable = modal || (!filtersInDirectory && (['leftSidebar', 'rightSidebar'].indexOf(_this.options.filters.location) !== -1));
        this.filtersRepository = _this.options.filters.source === 'regions' ? _this.regionsRepository : _this.objectsRepository;
        this.controllers.filters = new FiltersController({
            container: container,
            query: this.filtersRepository.query,
            mapsvg: _this,
            schema: _this.filtersSchema,
            template: Handlebars.compile('<div class="mapsvg-filters-container"></div>'),
            scrollable: scrollable,
            modal: modal,
            withToolbar: MapSVG.isPhone ? false : modal,
            width: $(container).hasClass('mapsvg-map-container') ? _this.options.filters.width : '100%',
            showButtonText: _this.options.filters.showButtonText,
            clearButton: _this.options.filters.clearButton,
            clearButtonText: _this.options.filters.clearButtonText,
            events: {
                'cleared.fields': () => {
                    _this.deselectAllRegions();
                },
                'changed.fields': () => {
                    _this.throttle(_this.filtersRepository.reload, 400, _this);
                },
                'shown': function (mapsvg) {
                },
                'closed': function (mapsvg) {
                },
                'loaded': () => {
                    _this.controllers.directory && _this.controllers.directory.updateTopShift();
                },
                'click.btn.showFilters': () => {
                    _this.loadFiltersModal();
                }
            }
        });
    }
    textSearch(text, fallback = false) {
        var query = new Query({
            filters: { "search": text },
            searchFallback: fallback
        });
        this.filtersRepository.find(query);
    }
    getRegion(id) {
        return this.regions.get(id);
    }
    getRegions() {
        return this.regions;
    }
    getMarker(id) {
        return this.markers.get(id);
    }
    checkId(id) {
        var _this = this;
        if (_this.getRegion(id))
            return { error: "This ID is already being used by a Region" };
        else if (_this.getMarker(id))
            return { error: "This ID is already being used by another Marker" };
        else
            return true;
    }
    regionsRedrawColors() {
        var _this = this;
        _this.regions.forEach(function (region) {
            region.setFill();
        });
    }
    destroy() {
        var _this = this;
        if (_this.controllers && _this.controllers.directory) {
            _this.controllers.directory.mobileButtons.remove();
        }
        $(_this.containers.map).empty().insertBefore($(_this.containers.wrapAll)).attr('style', '').removeClass('mapsvg mapsvg-responsive');
        _this.controllers.popover && _this.controllers.popover.close();
        if (_this.controllers.detailsView)
            _this.controllers.detailsView.destroy();
        $(_this.containers.wrapAll).remove();
        return _this;
    }
    getData() {
        return this;
    }
    mayBeFitMarkers() {
        var _this = this;
        if (!this.lastTimeFitWas) {
            this.lastTimeFitWas = Date.now() - 99999;
        }
        this.fitDelta = Date.now() - this.lastTimeFitWas;
        if (this.fitDelta > 1000 && !_this.firstDataLoad && !_this.fitOnDataLoadDone && _this.options.fitMarkers) {
            _this.fitMarkers();
            _this.fitOnDataLoadDone = true;
        }
        if (_this.firstDataLoad && _this.options.fitMarkersOnStart) {
            _this.firstDataLoad = false;
            if (_this.options.googleMaps.on && !_this.googleMaps.map) {
                _this.events.on('googleMapsLoaded', function () {
                    _this.fitMarkers();
                });
            }
            else {
                _this.fitMarkers();
            }
        }
        this.lastTimeFitWas = Date.now();
    }
    fitMarkers() {
        var _this = this;
        var dbObjects = _this.objectsRepository.getLoaded();
        if (!dbObjects || dbObjects.size === 0) {
            return;
        }
        if (_this.options.googleMaps.on && typeof google !== "undefined") {
            var lats = [];
            var lngs = [];
            if (dbObjects.size > 1) {
                dbObjects.forEach(function (object) {
                    if (object.location && object.location.geoPoint) {
                        lats.push(object.location.geoPoint.lat);
                        lngs.push(object.location.geoPoint.lng);
                    }
                });
                var minlat = Math.min.apply(null, lats), maxlat = Math.max.apply(null, lats);
                var minlng = Math.min.apply(null, lngs), maxlng = Math.max.apply(null, lngs);
                var bbox = new google.maps.LatLngBounds({ lat: minlat, lng: minlng }, { lat: maxlat, lng: maxlng });
                _this.googleMaps.map.fitBounds(bbox, 0);
            }
            else {
                if (dbObjects[0].location && dbObjects[0].location.lat && dbObjects[0].location.lng) {
                    var coords = { lat: dbObjects[0].location.lat, lng: dbObjects[0].location.lng };
                    if (_this.googleMaps.map) {
                        _this.googleMaps.map.setCenter(coords);
                        var max = _this.googleMaps.zoomLimit ? 17 : 20;
                        _this.googleMaps.map.setZoom(max);
                    }
                }
            }
        }
        else {
            if (_this.options.clustering.on) {
                let arr = [];
                _this.markersClusters.forEach(function (c) {
                    arr.push(c);
                });
                _this.markers.forEach(function (m) {
                    arr.push(m);
                });
                return _this.zoomTo(arr);
            }
            else {
                return _this.zoomTo(_this.markers);
            }
        }
    }
    showUserLocation(callback) {
        var _this = this;
        this.getUserLocation(function (latlng) {
            _this.userLocation = null;
            _this.userLocation = new MapSVG.Location({
                lat: latlng.lat,
                lng: latlng.lng,
                img: MapSVG.urls.root + '/markers/user-location.svg'
            });
            _this.userLocationMarker && _this.userLocationMarker.delete();
            _this.userLocationMarker = new MapSVG.Marker({
                location: _this.userLocation,
                mapsvg: _this,
                width: 15,
                height: 15
            });
            $(_this.userLocationMarker.element).addClass('mapsvg-user-location');
            _this.userLocationMarker.centered = true;
            $(_this.containers.scrollpane).append(_this.userLocationMarker.element);
            _this.userLocationMarker.adjustPosition();
            callback && callback(_this.userLocation);
        });
    }
    getUserLocation(callback) {
        var _this = this;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                callback && callback(pos);
            });
        }
        else {
            return false;
        }
    }
    getScale() {
        var _this = this;
        var scale2 = $(_this.containers.map).width() / _this.viewBox.width;
        return scale2 || 1;
    }
    updateSize() {
        var _this = this;
        _this.scale = _this.getScale();
        _this.controllers.popover && _this.controllers.popover.adjustPosition();
        _this.markersAdjustPosition();
        if (_this.options.labelsRegions.on) {
            _this.labelsRegionsAdjustPosition();
        }
        _this.mapAdjustStrokes();
    }
    getViewBox() {
        return this.viewBox;
    }
    viewBoxSetBySize(width, height) {
        var _this = this;
        width = parseFloat(width);
        height = parseFloat(height);
        _this.setSize(width, height);
        _this._viewBox = _this.viewBoxGetBySize(width, height);
        _this.setViewBox(_this._viewBox);
        $(window).trigger('resize');
        _this.setSize(width, height);
        _this.setZoomLevels();
        return _this.viewBox;
    }
    viewBoxGetBySize(width, height) {
        var _this = this;
        var new_ratio = width / height;
        var old_ratio = _this.svgDefault.viewBox.width / _this.svgDefault.viewBox.height;
        var vb = $.extend([], _this.svgDefault.viewBox);
        if (new_ratio != old_ratio) {
            if (new_ratio > old_ratio) {
                vb[2] = _this.svgDefault.viewBox.height * new_ratio;
                vb[0] = _this.svgDefault.viewBox.x - ((vb[2] - _this.svgDefault.viewBox.width) / 2);
            }
            else {
                vb[3] = _this.svgDefault.viewBox.width / new_ratio;
                vb[1] = _this.svgDefault.viewBox.y - ((vb[3] - _this.svgDefault.viewBox.height) / 2);
            }
        }
        return vb;
    }
    viewBoxReset(toInitial) {
        var _this = this;
        if (_this.options.googleMaps.on && _this.googleMaps.map) {
            if (!toInitial) {
                _this.options.googleMaps.center = null;
                _this.options.googleMaps.zoom = null;
            }
            if (!_this.options.googleMaps.center || !_this.options.googleMaps.zoom) {
                var southWest = new google.maps.LatLng(_this.geoViewBox.sw.lat, _this.geoViewBox.sw.lng);
                var northEast = new google.maps.LatLng(_this.geoViewBox.ne.lat, _this.geoViewBox.ne.lng);
                var bounds = new google.maps.LatLngBounds(southWest, northEast);
                _this.googleMaps.map.fitBounds(bounds, 0);
                _this.options.googleMaps.center = _this.googleMaps.map.getCenter().toJSON();
                _this.options.googleMaps.zoom = _this.googleMaps.map.getZoom();
            }
            else {
                _this.googleMaps.map.setZoom(_this.options.googleMaps.zoom);
                _this.googleMaps.map.setCenter(_this.options.googleMaps.center);
            }
        }
        else {
            if (toInitial) {
                var v = _this._viewBox || _this.svgDefault.viewBox;
                _this.zoomLevel = 0;
                _this._scale = 1;
                _this.setViewBox(v);
            }
            else {
                _this.setViewBox();
            }
        }
        return this.viewBox;
    }
    getGeoViewBox() {
        var _this = this;
        var v = _this.viewBox;
        var p1 = new SVGPoint(v[0], v[1]);
        var p2 = new SVGPoint(v[0] + v[2], v[1]);
        var p3 = new SVGPoint(v[0], v[1]);
        var p4 = new SVGPoint(v[0], v[1] + v[3]);
        var leftLon = _this.convertSVGToGeo(p1).lng;
        var rightLon = _this.convertSVGToGeo(p2).lng;
        var topLat = _this.convertSVGToGeo(p3).lat;
        var bottomLat = _this.convertSVGToGeo(p4).lat;
        return [leftLon, topLat, rightLon, bottomLat];
    }
    mapAdjustStrokes() {
        var _this = this;
        $(this.containers.svg).find('path, polygon, circle, ellipse, rect').each(function (index) {
            if ($(this).data('stroke-width')) {
                $(this).css('stroke-width', $(this).data('stroke-width') / _this.scale);
            }
        });
    }
    zoomIn(center) {
        var _this = this;
        if (_this.googleMaps.map) {
            if (!_this.isZooming) {
                var currentZoomInRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];
                var zoom = _this.googleMaps.map.getZoom();
                var max = _this.googleMaps.zoomLimit ? 17 : 20;
                var google_zoom_new = (zoom + 1) > max ? max : zoom + 1;
                var svg_zoom_new = google_zoom_new - _this.zoomDelta;
                var newZoomInInRange = svg_zoom_new >= _this.options.zoom.limit[0] && svg_zoom_new <= _this.options.zoom.limit[1];
                if (currentZoomInRange && !newZoomInInRange) {
                    return false;
                }
                _this.isZooming = true;
                _this.googleMaps.map.setZoom(google_zoom_new);
                if (center) {
                    var centerGeo = _this.convertSVGToGeo(center);
                    _this.googleMaps.map.setCenter(centerGeo);
                }
                _this.zoomLevel = svg_zoom_new;
            }
        }
        else if (_this.canZoom) {
            _this.canZoom = false;
            setTimeout(function () {
                _this.canZoom = true;
            }, 700);
            _this.zoom(1, center);
        }
    }
    zoomOut(center) {
        var _this = this;
        if (_this.googleMaps.map) {
            if (!_this.isZooming && _this.googleMaps.map.getZoom() - 1 >= _this.options.googleMaps.minZoom) {
                var currentZoomInRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];
                var zoom = _this.googleMaps.map.getZoom();
                var google_zoom_new = (zoom - 1) < 1 ? 1 : (zoom - 1);
                var svg_zoom_new = google_zoom_new - _this.zoomDelta;
                var newZoomInInRange = svg_zoom_new >= _this.options.zoom.limit[0] && svg_zoom_new <= _this.options.zoom.limit[1];
                if (currentZoomInRange && !newZoomInInRange) {
                    return false;
                }
                _this.isZooming = true;
                _this.googleMaps.map.setZoom(google_zoom_new);
                _this.zoomLevel = svg_zoom_new;
            }
        }
        else if (_this.canZoom) {
            _this.canZoom = false;
            setTimeout(function () {
                _this.canZoom = true;
            }, 700);
            _this.zoom(-1, center);
        }
    }
    touchZoomMove() {
    }
    touchZoomEnd() {
    }
    zoomTo(mapObjects, zoomToLevel) {
        var _this = this;
        if (typeof mapObjects == 'string') {
            mapObjects = _this.getRegion(mapObjects);
        }
        if (_this.googleMaps.map) {
            if (mapObjects instanceof MapSVG.Marker) {
                var geoPoint = _this.convertSVGToGeo(mapObjects.svgPoint);
                _this.googleMaps.map.setZoom(zoomToLevel || 1);
                _this.googleMaps.map.setCenter({ lat: geoPoint.lat, lng: geoPoint.lng });
                this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
            }
            else {
                if (mapObjects && mapObjects.length !== undefined) {
                    var rbounds = mapObjects[0].getGeoBounds();
                    var southWest = new google.maps.LatLng(rbounds.sw.lat, rbounds.sw.lng);
                    var northEast = new google.maps.LatLng(rbounds.ne.lat, rbounds.ne.lng);
                    var bounds = new google.maps.LatLngBounds(southWest, northEast);
                    for (var i = 1; i < mapObjects.length - 1; i++) {
                        var rbounds2 = mapObjects[i].getGeoBounds();
                        var southWest2 = new google.maps.LatLng(rbounds2.sw.lat, rbounds2.sw.lng);
                        var northEast2 = new google.maps.LatLng(rbounds2.ne.lat, rbounds2.ne.lng);
                        bounds.extend(southWest2);
                        bounds.extend(northEast2);
                    }
                }
                else {
                    var objectBounds = mapObjects.getGeoBounds();
                    var southWest = new google.maps.LatLng(objectBounds.sw.lat, objectBounds.sw.lng);
                    var northEast = new google.maps.LatLng(objectBounds.ne.lat, objectBounds.ne.lng);
                    var bounds = new google.maps.LatLngBounds(southWest, northEast);
                }
                _this.googleMaps.map.fitBounds(bounds, 0);
                if (_this.googleMaps.zoomLimit && (_this.googleMaps.map.getZoom() > 17)) {
                    _this.googleMaps.map.setZoom(17);
                }
                this.zoomLevel = this.googleMaps.map.getZoom() - this.zoomDelta;
            }
            return;
        }
        let bbox, viewBox, viewBoxPrev;
        if (mapObjects instanceof Marker || mapObjects instanceof MarkerCluster) {
            return _this.zoomToMarkerOrCluster(mapObjects, zoomToLevel);
        }
        if (typeof mapObjects == 'object' && mapObjects.length !== undefined) {
            var _bbox;
            if (mapObjects[0] instanceof Region) {
                bbox = mapObjects[0].getBBox();
                var xmin = [bbox.x];
                var ymin = [bbox.y];
                var w = (bbox.x + bbox.width);
                var xmax = [w];
                var h = (bbox.y + bbox.height);
                var ymax = [h];
                if (mapObjects.length > 1) {
                    for (var i = 1; i < mapObjects.length; i++) {
                        _bbox = mapObjects[i].getBBox();
                        xmin.push(_bbox.x);
                        ymin.push(_bbox.y);
                        var _w = _bbox.x + _bbox.width;
                        var _h = _bbox.y + _bbox.height;
                        xmax.push(_w);
                        ymax.push(_h);
                    }
                }
                var _xmin = Math.min.apply(Math, xmin);
                var _ymin = Math.min.apply(Math, ymin);
                var w = Math.max.apply(Math, _xmax) - _xmin;
                var h = Math.max.apply(Math, _ymax) - _ymin;
                bbox = new ViewBox(_xmin, _ymin, w, h);
            }
            else if (mapObjects[0] instanceof MapSVG.Marker || mapObjects[0] instanceof MapSVG.MarkersCluster) {
                var xs = [];
                var ys = [];
                if (mapObjects.length === 1) {
                    return _this.zoomToMarkerOrCluster(mapObjects[0]);
                }
                mapObjects.forEach(function (object) {
                    xs.push(object.x);
                    ys.push(object.y);
                });
                var minx = Math.min.apply(null, xs), maxx = Math.max.apply(null, xs);
                var miny = Math.min.apply(null, ys), maxy = Math.max.apply(null, ys);
                var padding = 10;
                var point1 = new ScreenPoint(padding, 0);
                var point2 = new ScreenPoint(0, 0);
                padding = _this.convertPixelToSVG(point1).x - _this.convertPixelToSVG(point2).x;
                var width = maxx - minx;
                var height = maxy - miny;
                bbox = new ViewBox(minx - padding, miny - padding, width + padding * 2, height + padding * 2);
            }
        }
        else {
            bbox = mapObjects.getBBox();
        }
        var searching = true;
        $.each(_this.zoomLevels, function (key, level) {
            if (searching && (viewBoxPrev && viewBoxPrev.x !== undefined)) {
                if ((viewBoxPrev.width > bbox.width && viewBoxPrev.height > bbox.height)
                    &&
                        (bbox.width > level.viewBox.width || bbox.height > level.viewBox.height)) {
                    _this.zoomLevel = zoomToLevel ? zoomToLevel : parseInt(key + '') - 1;
                    var vb = _this.zoomLevels[_this.zoomLevel].viewBox;
                    var newVb = new ViewBox(bbox.x - vb.width / 2 + bbox.width / 2, bbox.y - vb.height / 2 + bbox.height / 2, vb.width, vb.height);
                    _this.setViewBox();
                    _this._scale = _this.zoomLevels[_this.zoomLevel]._scale;
                    searching = false;
                }
            }
            viewBoxPrev = level && level.viewBox;
        });
    }
    zoomToMarkerOrCluster(mapObject, zoomToLevel) {
        var _this = this;
        _this.zoomLevel = zoomToLevel || 1;
        var vb = _this.zoomLevels[_this.zoomLevel].viewBox;
        var newViewBox = new ViewBox(mapObject.x - vb.width / 2, mapObject.y - vb.height / 2, vb.width, vb.height);
        _this.setViewBox(newViewBox);
        _this._scale = _this.zoomLevels[_this.zoomLevel]._scale;
        return;
    }
    centerOn(region, yShift) {
        var _this = this;
        if (_this.options.googleMaps.on) {
            yShift = yShift ? (yShift + 12) / _this.getScale() : 0;
            $(_this.containers.map).addClass('scrolling');
            var latLng = region.getCenterLatLng(yShift);
            _this.googleMaps.map.panTo(latLng);
            setTimeout(function () {
                $(_this.containers.map).removeClass('scrolling');
            }, 100);
        }
        else {
            yShift = yShift ? (yShift + 12) / _this.getScale() : 0;
            var bbox = region.getBBox();
            var vb = _this.viewBox;
            var newViewBox = new ViewBox(bbox.x - vb[2] / 2 + bbox.width / 2, bbox.y - vb[3] / 2 + bbox.height / 2 - yShift, vb[2], vb[3]);
            _this.setViewBox(newViewBox);
        }
    }
    zoom(delta, center, exact) {
        var _this = this;
        var vWidth = _this.viewBox.width;
        var vHeight = _this.viewBox.height;
        var newViewBox = new ViewBox(0, 0, 0, 0);
        var isInZoomRange = _this.zoomLevel >= _this.options.zoom.limit[0] && _this.zoomLevel <= _this.options.zoom.limit[1];
        if (!exact) {
            var d = delta > 0 ? 1 : -1;
            if (!_this.zoomLevels[_this.zoomLevel + d])
                return;
            _this._zoomLevel = _this.zoomLevel;
            _this._zoomLevel += d;
            if (isInZoomRange && (_this._zoomLevel > _this.options.zoom.limit[1] || _this._zoomLevel < _this.options.zoom.limit[0]))
                return false;
            _this.zoomLevel = _this._zoomLevel;
            var z = _this.zoomLevels[_this.zoomLevel];
            _this._scale = z._scale;
            newViewBox = z.viewBox;
        }
        else {
            newViewBox.width = _this._viewBox.width / exact;
            newViewBox.height = _this._viewBox.height / exact;
        }
        var shift = [];
        if (center) {
            var koef = d > 0 ? 0.5 : -1;
            shift = [((center.x - _this.viewBox.x) * koef), ((center.y - _this.viewBox.y) * koef)];
            newViewBox.x = _this.viewBox.x + shift[0];
            newViewBox.y = _this.viewBox.y + shift[1];
        }
        else {
            shift = [(vWidth - newViewBox.width) / 2, (vHeight - newViewBox.height) / 2];
            newViewBox.x = _this.viewBox.x + shift[0];
            newViewBox.y = _this.viewBox.y + shift[1];
        }
        if (_this.options.scroll.limit) {
            if (newViewBox.x < _this.svgDefault.viewBox.x)
                newViewBox.x = _this.svgDefault.viewBox.x;
            else if (newViewBox.x + newViewBox.width > _this.svgDefault.viewBox.x + _this.svgDefault.viewBox.width)
                newViewBox.x = _this.svgDefault.viewBox.x + _this.svgDefault.viewBox.width - newViewBox.width;
            if (newViewBox.y < _this.svgDefault.viewBox.y)
                newViewBox.y = _this.svgDefault.viewBox.y;
            else if (newViewBox.y + newViewBox.height > _this.svgDefault.viewBox.y + _this.svgDefault.viewBox.height)
                newViewBox.y = _this.svgDefault.viewBox.y + _this.svgDefault.viewBox.height - newViewBox.height;
        }
        _this.setViewBox(newViewBox);
    }
    markerDelete(marker) {
        var _this = this;
        if (_this.editingMarker && _this.editingMarker.id == marker.id) {
            _this.editingMarker = null;
            delete _this.editingMarker;
        }
        if (this.markers.has(marker.id)) {
            this.markers.get(marker.id).elem.remove();
            this.markers.delete(marker.id);
            marker = null;
        }
        if (_this.markers.size === 0)
            _this.options.markerLastID = 0;
    }
    markersClusterAdd(markersCluster) {
        var _this = this;
        _this.layers.markers.append(markersCluster.node);
        _this.markersClusters.set(markersCluster.id, markersCluster);
        markersCluster.adjustPosition();
    }
    markerAdd(marker) {
        var _this = this;
        $(marker.element).hide();
        marker.adjustPosition();
        _this.layers.markers.append(marker.element);
        _this.markers.set(marker.id, marker);
        marker.mapped = true;
        setTimeout(function () {
            $(marker.element).show();
        }, 100);
    }
    markerRemove(marker) {
        var _this = this;
        if (_this.editingMarker && _this.editingMarker.id == marker.id) {
            _this.editingMarker = null;
            delete _this.editingMarker;
        }
        if (this.markers.has(marker.id)) {
            this.markers.get(marker.id).elem.remove();
            this.markers.delete(marker.id);
            marker = null;
        }
        if (_this.markers.size === 0)
            _this.options.markerLastID = 0;
    }
    markerId() {
        var _this = this;
        _this.options.markerLastID = _this.options.markerLastID + 1;
        var id = 'marker_' + (_this.options.markerLastID);
        if (_this.getMarker(id))
            return _this.markerId();
        else
            return id;
    }
    labelsRegionsAdjustPosition() {
        var _this = this;
        var dx, dy;
        if (!$(_this.containers.map).is(":visible")) {
            return;
        }
        _this.regions.forEach(function (region) {
            if (!region.center) {
                region.center = region.getCenterSVG();
            }
            var pos = _this.convertSVGToPixel(region.center);
            if (region.textLabel)
                region.textLabel[0].style.transform = 'translate(-50%,-50%) translate(' + pos[0] + 'px,' + pos[1] + 'px)';
        });
    }
    markersAdjustPosition() {
        var _this = this;
        _this.markers.forEach(function (marker) {
            marker.adjustPosition();
        });
        _this.markersClusters.forEach(function (cluster) {
            cluster.adjustPosition();
        });
        if (_this.userLocationMarker) {
            _this.userLocationMarker.adjustPosition();
        }
    }
    markerMoveStart() {
    }
    markerMove(dx, dy) {
    }
    markerMoveEnd() {
    }
    setEditingMarker(marker) {
        var _this = this;
        _this.editingMarker = marker;
        if (!_this.editingMarker.mapped) {
            _this.editingMarker.needToRemove = true;
            _this.markerAdd(_this.editingMarker);
        }
    }
    unsetEditingMarker() {
        var _this = this;
        if (_this.editingMarker && _this.editingMarker.needToRemove) {
            _this.markerRemove(_this.editingMarker);
        }
        _this.editingMarker = null;
    }
    getEditingMarker() {
        var _this = this;
        return _this.editingMarker;
    }
    scrollStart(e, mapsvg) {
        var _this = this;
        if ($(e.target).hasClass('mapsvg-btn-map') || $(e.target).closest('.mapsvg-gauge').length)
            return false;
        if (_this.editMarkers.on && $(e.target).hasClass('mapsvg-marker'))
            return false;
        e.preventDefault();
        var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;
        _this.scrollStarted = true;
        if (e.type.indexOf('mouse') === 0) {
            $(document).on('mousemove.scroll.mapsvg', function (e) {
                _this.scrollMove(e);
            });
            if (_this.options.scroll.spacebar) {
                $(document).on('keyup.scroll.mapsvg', function (e) {
                    if (e.keyCode == 32) {
                        _this.scrollEnd(e, mapsvg);
                    }
                });
            }
            else {
                $(document).on('mouseup.scroll.mapsvg', function (e) {
                    _this.scrollEnd(e, mapsvg);
                });
            }
        }
    }
    scrollMove(e) {
        var _this = this;
        e.preventDefault();
        if (!_this.isScrolling) {
            _this.isScrolling = true;
            $(_this.containers.map).addClass('scrolling');
        }
        var ce = e.originalEvent && e.originalEvent.touches && e.originalEvent.touches[0] ? e.originalEvent.touches[0] : e;
        var scrolled = _this.panBy((_this.scroll.gx - ce.clientX), (_this.scroll.gy - ce.clientY));
        if (_this.googleMaps.map && (scrolled.x || scrolled.y)) {
            var point = _this.googleMaps.map.getCenter();
            var projection = _this.googleMaps.overlay.getProjection();
            var pixelpoint = projection.fromLatLngToDivPixel(point);
            pixelpoint.x += scrolled.x ? _this.scroll.gx - ce.clientX : 0;
            pixelpoint.y += scrolled.y ? _this.scroll.gy - ce.clientY : 0;
            point = projection.fromDivPixelToLatLng(pixelpoint);
            _this.googleMaps.map.setCenter(point);
        }
        _this.scroll.gx = ce.clientX;
        _this.scroll.gy = ce.clientY;
        _this.scroll.dx = (_this.scroll.x - ce.clientX);
        _this.scroll.dy = (_this.scroll.y - ce.clientY);
        var vx = _this.scroll.vxi + _this.scroll.dx / _this.scale;
        var vy = _this.scroll.vyi + _this.scroll.dy / _this.scale;
        if (_this.options.scroll.limit) {
            if (vx < _this.svgDefault.viewBox.x)
                vx = _this.svgDefault.viewBox.x;
            else if (_this.viewBox.width + vx > _this.svgDefault.viewBox.x + _this.svgDefault.viewBox.width)
                vx = (_this.svgDefault.viewBox.x + _this.svgDefault.viewBox.width - _this.viewBox.width);
            if (vy < _this.svgDefault.viewBox.y)
                vy = _this.svgDefault.viewBox.y;
            else if (_this.viewBox.height + vy > _this.svgDefault.viewBox.y + _this.svgDefault.viewBox.height)
                vy = (_this.svgDefault.viewBox.y + _this.svgDefault.viewBox.height - _this.viewBox.height);
        }
        _this.scroll.vx = vx;
        _this.scroll.vy = vy;
    }
    scrollEnd(e, mapsvg, noClick) {
        var _this = this;
        setTimeout(function () {
            _this.scrollStarted = false;
            _this.isScrolling = false;
        }, 100);
        _this.googleMaps && _this.googleMaps.overlay && _this.googleMaps.overlay.draw();
        $(_this.containers.map).removeClass('scrolling');
        $(document).off('keyup.scroll.mapsvg');
        $(document).off('mousemove.scroll.mapsvg');
        $(document).off('mouseup.scroll.mapsvg');
        if (noClick !== true && Math.abs(_this.scroll.dx) < 5 && Math.abs(_this.scroll.dy) < 5) {
            if (_this.editMarkers.on)
                _this.clickAddsMarker && _this.markerAddClickHandler(e);
            else if (_this.region_clicked)
                _this.regionClickHandler(e, _this.region_clicked);
        }
        _this.viewBox.x = _this.scroll.vx || _this.viewBox.x;
        _this.viewBox.y = _this.scroll.vy || _this.viewBox.y;
    }
    panBy(x, y) {
        var _this = this;
        var tx = _this.scroll.tx - x;
        var ty = _this.scroll.ty - y;
        var scrolled = { x: true, y: true };
        if (_this.options.scroll.limit) {
            var svg = $(_this.containers.svg)[0].getBoundingClientRect();
            var bounds = $(_this.containers.map)[0].getBoundingClientRect();
            if ((svg.left - x > bounds.left && x < 0) || (svg.right - x < bounds.right && x > 0)) {
                tx = _this.scroll.tx;
                scrolled.x = false;
            }
            if ((svg.top - y > bounds.top && y < 0) || (svg.bottom - y < bounds.bottom && y > 0)) {
                ty = _this.scroll.ty;
                scrolled.y = false;
            }
        }
        $(_this.containers.scrollpane).css({
            'transform': 'translate(' + tx + 'px,' + ty + 'px)'
        });
        _this.scroll.tx = tx;
        _this.scroll.ty = ty;
        return scrolled;
    }
    scrollRegionClickHandler(e, region) {
        this.region_clicked = region;
    }
    touchStart(_e, mapsvg) {
        var _this = this;
        _e.preventDefault();
        if (_this.scrollStarted) {
            _this.scrollEnd(_e, mapsvg, true);
        }
        var e = _e.originalEvent;
        if (_this.options.zoom.fingers && e.touches && e.touches.length == 2) {
            _this.touchZoomStart = true;
            _this.scaleDistStart = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
        }
        else if (e.touches && e.touches.length == 1) {
            _this.scrollStart(_e, mapsvg);
        }
        $(document).on('touchmove.scroll.mapsvg', function (e) {
            e.preventDefault();
            _this.touchMove(e, _this);
        }).on('touchend.scroll.mapsvg', function (e) {
            e.preventDefault();
            _this.touchEnd(e, _this);
        });
    }
    ;
    touchMove(_e, mapsvg) {
        var _this = this;
        _e.preventDefault();
        var e = _e.originalEvent;
        if (_this.options.zoom.fingers && e.touches && e.touches.length == 2) {
            if (!MapSVG.ios) {
                e.scale = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY) / _this.scaleDistStart;
            }
            if (e.scale != 1 && _this.canZoom) {
                var d = e.scale > 1 ? 1 : -1;
                var cx = e.touches[0].pageX >= e.touches[1].pageX ? e.touches[0].pageX - (e.touches[0].pageX - e.touches[1].pageX) / 2 - $(_this.containers.svg).offset().left : e.touches[1].pageX - (e.touches[1].pageX - e.touches[0].pageX) / 2 - $(_this.containers.svg).offset().left;
                var cy = e.touches[0].pageY >= e.touches[1].pageY ? e.touches[0].pageY - (e.touches[0].pageY - e.touches[1].pageY) - $(_this.containers.svg).offset().top : e.touches[1].pageY - (e.touches[1].pageY - e.touches[0].pageY) - $(_this.containers.svg).offset().top;
                var center = _this.convertPixelToSVG(new ScreenPoint(cx, cy));
                if (d > 0)
                    _this.zoomIn(center);
                else
                    _this.zoomOut(center);
            }
        }
        else if (e.touches && e.touches.length == 1) {
            _this.scrollMove(_e);
        }
    }
    ;
    touchEnd(_e, mapsvg) {
        var _this = this;
        _e.preventDefault();
        var e = _e.originalEvent;
        if (_this.touchZoomStart) {
            _this.touchZoomStart = false;
        }
        else if (_this.scrollStarted) {
            _this.scrollEnd(_e, mapsvg);
        }
        $(document).off('touchmove.scroll.mapsvg');
        $(document).off('touchend.scroll.mapsvg');
    }
    ;
    getSelected() {
        return this.selected_id;
    }
    ;
    selectRegion(id, skipDirectorySelection) {
        var _this = this;
        let region;
        if (typeof id == "string") {
            region = _this.getRegion(id);
        }
        else {
            region = id;
        }
        if (!region)
            return false;
        var ids;
        if (_this.options.multiSelect && !_this.editRegions.on) {
            if (region.selected) {
                _this.deselectRegion(region);
                if (!skipDirectorySelection && _this.options.menu.on) {
                    if (_this.options.menu.source == 'database') {
                        if (region.objects && region.objects.length) {
                            var ids = region.objects.map(function (obj) {
                                return obj.id.toString();
                            });
                        }
                    }
                    else {
                        var ids = [region.id];
                    }
                    _this.controllers.directory.deselectItems(ids);
                }
                return;
            }
        }
        else if (_this.selected_id.length > 0) {
            _this.deselectAllRegions();
            if (!skipDirectorySelection && _this.options.menu.on) {
                if (_this.options.menu.source == 'database') {
                    if (region.objects && region.objects.length) {
                        var ids = region.objects.map(function (obj) {
                            return obj.id.toString();
                        });
                    }
                }
                else {
                    var ids = [region.id];
                }
                _this.controllers.directory.deselectItems();
            }
        }
        _this.selected_id.push(region.id);
        region.select();
        var skip = _this.options.actions.region.click.filterDirectory;
        if (!skip && !skipDirectorySelection && _this.options.menu.on && _this.controllers && _this.controllers.directory) {
            if (_this.options.menu.source == 'database') {
                if (region.objects && region.objects.length) {
                    var ids = region.objects.map(function (obj) {
                        return obj.id.toString();
                    });
                }
                else {
                    var ids = [region.id];
                }
            }
            else {
                var ids = [region.id];
            }
            _this.controllers.directory.selectItems(ids);
        }
        if (_this.options.actions.region.click.addIdToUrl && !_this.options.actions.region.click.showAnotherMap) {
            window.location.hash = "/m/" + region.id;
        }
    }
    deselectAllRegions() {
        var _this = this;
        $.each(_this.selected_id, function (index, id) {
            _this.deselectRegion(_this.getRegion(id));
        });
    }
    deselectRegion(region) {
        var _this = this;
        if (!region)
            region = _this.getRegion(_this.selected_id[0]);
        if (region) {
            region.deselect();
            var i = $.inArray(region.id, _this.selected_id);
            _this.selected_id.splice(i, 1);
        }
        if (_this.options.actions.region.click.addIdToUrl) {
            if (window.location.hash.indexOf(region.id) !== -1) {
                history.replaceState(null, null, ' ');
            }
        }
    }
    highlightRegions(regions) {
        var _this = this;
        regions.forEach(function (region) {
            if (region && !region.selected && !region.disabled) {
                _this.highlightedRegions.push(region);
                region.highlight();
            }
        });
    }
    unhighlightRegions() {
        var _this = this;
        _this.highlightedRegions.forEach(function (region) {
            if (region && !region.selected && !region.disabled)
                region.unhighlight();
        });
        _this.highlightedRegions = [];
    }
    selectMarker(marker) {
        var _this = this;
        if (!(marker instanceof MapSVG.Marker))
            return false;
        _this.deselectAllMarkers();
        marker.select();
        _this.selected_marker = marker;
        $(_this.layers.markers).addClass('mapsvg-with-marker-active');
        if (_this.options.menu.on && _this.options.menu.source == 'database') {
            _this.controllers.directory.deselectItems();
            _this.controllers.directory.selectItems(marker.object.id);
        }
    }
    deselectAllMarkers() {
        var _this = this;
        _this.selected_marker && _this.selected_marker.deselect();
        $(_this.layers.markers).removeClass('mapsvg-with-marker-active');
    }
    deselectMarker(marker) {
        var _this = this;
        if (marker) {
            marker.deselect();
        }
    }
    highlightMarker(marker) {
        var _this = this;
        $(_this.layers.markers).addClass('mapsvg-with-marker-hover');
        marker.highlight();
        _this.highlighted_marker = marker;
    }
    unhighlightMarker() {
        var _this = this;
        $(_this.layers.markers).removeClass('mapsvg-with-marker-hover');
        _this.highlighted_marker && _this.highlighted_marker.unhighlight();
    }
    convertMouseToSVG(e) {
        var _this = this;
        var mc = MapSVG.mouseCoords(e);
        var x = mc.x - $(_this.containers.svg).offset().left;
        var y = mc.y - $(_this.containers.svg).offset().top;
        var screenPoint = new ScreenPoint(x, y);
        return _this.convertPixelToSVG(screenPoint);
    }
    convertSVGToPixel(svgPoint) {
        var _this = this;
        var scale = _this.getScale();
        var shiftX = 0, shiftY = 0;
        if (_this.options.googleMaps.on) {
            if ((_this.viewBox.x - _this.svgDefault.viewBox.x) > _this.svgDefault.viewBox.width) {
                var worldMapWidth = ((_this.svgDefault.viewBox.width / _this.mapLonDelta) * 360);
                shiftX = worldMapWidth * Math.floor((_this.viewBox.x - _this.svgDefault.viewBox.x) / _this.svgDefault.viewBox.width);
            }
        }
        let screenPoint = new ScreenPoint((svgPoint.x - _this.svgDefault.viewBox.x + shiftX) * scale, (svgPoint.y - _this.svgDefault.viewBox.y + shiftY) * scale);
        return screenPoint;
    }
    convertPixelToSVG(screenPoint) {
        var _this = this;
        var scale = _this.getScale();
        return new SVGPoint(screenPoint.x / scale + _this.svgDefault.viewBox.x, screenPoint.y / scale + _this.svgDefault.viewBox.y);
    }
    convertGeoToSVG(coords) {
        var _this = this;
        var x = (coords.lng - _this.geoViewBox.sw.lng) * (_this.svgDefault.viewBox.width / _this.mapLonDelta);
        var lat = coords.lat * 3.14159 / 180;
        var worldMapWidth = ((_this.svgDefault.viewBox.width / _this.mapLonDelta) * 360) / (2 * 3.14159);
        var mapOffsetY = (worldMapWidth / 2 * Math.log((1 + Math.sin(_this.mapLatBottomDegree)) / (1 - Math.sin(_this.mapLatBottomDegree))));
        var y = _this.svgDefault.viewBox.height - ((worldMapWidth / 2 * Math.log((1 + Math.sin(lat)) / (1 - Math.sin(lat)))) - mapOffsetY);
        x += _this.svgDefault.viewBox.x;
        y += _this.svgDefault.viewBox.y;
        return (new SVGPoint(x, y));
    }
    convertSVGToGeo(point) {
        var _this = this;
        let tx = point.x - _this.svgDefault.viewBox.x;
        let ty = point.y - _this.svgDefault.viewBox.y;
        var worldMapRadius = _this.svgDefault.viewBox.width / _this.mapLonDelta * 360 / (2 * Math.PI);
        var mapOffsetY = (worldMapRadius / 2 * Math.log((1 + Math.sin(_this.mapLatBottomDegree)) / (1 - Math.sin(_this.mapLatBottomDegree))));
        var equatorY = _this.svgDefault.viewBox.height + mapOffsetY;
        var a = (equatorY - ty) / worldMapRadius;
        var lat = 180 / Math.PI * (2 * Math.atan(Math.exp(a)) - Math.PI / 2);
        var lng = _this.geoViewBox.sw.lng + tx / _this.svgDefault.viewBox.width * _this.mapLonDelta;
        lat = parseFloat(lat.toFixed(6));
        lng = parseFloat(lng.toFixed(6));
        return (new GeoPoint(lat, lng));
    }
    pickGaugeColor(gaugeValue) {
        var _this = this;
        var w = (gaugeValue - _this.options.gauge.min) / _this.options.gauge.maxAdjusted;
        var rgba = {
            r: Math.round(_this.options.gauge.colors.diffRGB.r * w + _this.options.gauge.colors.lowRGB.r),
            g: Math.round(_this.options.gauge.colors.diffRGB.g * w + _this.options.gauge.colors.lowRGB.g),
            b: Math.round(_this.options.gauge.colors.diffRGB.b * w + _this.options.gauge.colors.lowRGB.b),
            a: Math.round(_this.options.gauge.colors.diffRGB.a * w + _this.options.gauge.colors.lowRGB.a)
        };
        return rgba;
    }
    isRegionDisabled(id, svgfill) {
        var _this = this;
        if (_this.options.regions[id] && (_this.options.regions[id].disabled || svgfill == 'none')) {
            return true;
        }
        else if ((_this.options.regions[id] == undefined || MapSVG.parseBoolean(_this.options.regions[id].disabled)) &&
            (_this.options.disableAll || svgfill == 'none' || id == 'labels' || id == 'Labels')) {
            return true;
        }
        else {
            return false;
        }
    }
    loadMap(id, container) {
        let mapsRepo = new MapsRepository();
        mapsRepo.findById(id).done((map) => {
            if ($(container).find('svg').length) {
                container.mapSvg().destroy();
            }
            container.mapSvg(map.options);
        });
    }
    regionClickHandler(e, region) {
        var _this = this;
        _this.region_clicked = null;
        var actions = _this.options.actions;
        if (_this.eventsPreventList['click'])
            return;
        if (_this.editRegions.on) {
            _this.selectRegion(region.id);
            _this.regionEditHandler.call(region);
            return;
        }
        if (region instanceof MarkerCluster) {
            _this.zoomTo(region.markers);
            return;
        }
        if (region instanceof Region) {
            _this.selectRegion(region.id);
            if (actions.region.click.zoom) {
                _this.zoomTo(region, actions.region.click.zoomToLevel);
            }
            if (actions.region.click.filterDirectory) {
                let query = new Query({ filters: { regions: region.id } });
                _this.objectsRepository.find(query).done(function () {
                    if (_this.controllers.popover) {
                        _this.controllers.popover.redraw(region.forTemplate());
                    }
                    if (_this.controllers.detailsView) {
                        _this.controllers.detailsView.redraw(region.forTemplate());
                    }
                });
                _this.updateFiltersState();
            }
            if (actions.region.click.showDetails) {
                _this.loadDetailsView(region);
            }
            if (actions.region.click.showPopover) {
                if (actions.region.click.zoom) {
                    setTimeout(function () {
                        _this.showPopover(region);
                    }, 400);
                }
                else {
                    _this.showPopover(region);
                }
            }
            else if (e && e.type.indexOf('touch') !== -1 && actions.region.touch.showPopover) {
                if (actions.region.click.zoom) {
                    setTimeout(function () {
                        _this.showPopover(region);
                    }, 400);
                }
                else {
                    _this.showPopover(region);
                }
            }
            if (actions.region.click.goToLink) {
                var linkParts = actions.region.click.linkField.split('.');
                var url;
                if (linkParts.length > 1) {
                    var obj = linkParts.shift();
                    var attr = '.' + linkParts.join('.');
                    if (obj == 'Region') {
                        if (region.data) {
                            try {
                                url = eval('region.data' + attr);
                            }
                            catch (err) {
                                console.log("No such field as region.data" + attr);
                            }
                        }
                    }
                    else {
                        if (region.objects && region.objects[0]) {
                            try {
                                url = eval('region.objects[0]' + attr);
                            }
                            catch (err) {
                                console.log("No such field as region.objects[0]" + attr);
                            }
                        }
                    }
                    if (url && !_this.disableLinks) {
                        if (_this.editMode) {
                            alert('Redirect: ' + url + '\nLinks are disabled in the preview.');
                            return true;
                        }
                        if (actions.region.click.newTab) {
                            var win = window.open(url, '_blank');
                            win.focus();
                        }
                        else {
                            window.location.href = url;
                        }
                    }
                }
            }
            if (actions.region.click.showAnotherMap) {
                if (_this.editMode) {
                    alert('"Show another map" action is disabled in the preview');
                    return true;
                }
                var linkParts = actions.region.click.showAnotherMapField.split('.');
                var url;
                if (linkParts.length > 1) {
                    var obj = linkParts.shift();
                    var attr = '.' + linkParts.join('.');
                    var map_id;
                    if (obj == 'Region') {
                        if (region.data)
                            map_id = eval('region.data' + attr);
                    }
                    else {
                        if (region.objects && region.objects[0])
                            map_id = eval('region.objects[0]' + attr);
                    }
                    if (map_id) {
                        var container = actions.region.click.showAnotherMapContainerId ? $('#' + actions.region.click.showAnotherMapContainerId)[0] : $(_this.containers.map)[0];
                        _this.loadMap(map_id, container);
                    }
                }
            }
            if (_this.events['click.region'])
                try {
                    _this.events['click.region'].call(region, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
        }
        else if (region instanceof Marker) {
            _this.selectMarker(region);
            var passingObject = region.object;
            if (actions.marker.click.zoom) {
                _this.zoomTo(region, actions.marker.click.zoomToLevel);
            }
            if (actions.marker.click.filterDirectory) {
                let query = new Query({ filters: { id: region.object.id } });
                _this.objectsRepository.find(query);
                _this.updateFiltersState();
            }
            if (actions.marker.click.showDetails)
                _this.loadDetailsView(passingObject);
            if (actions.marker.click.showPopover) {
                if (actions.marker.click.zoom) {
                    setTimeout(function () {
                        _this.showPopover(passingObject);
                    }, 500);
                }
                else {
                    _this.showPopover(passingObject);
                }
            }
            else if (e && e.type.indexOf('touch') !== -1 && actions.marker.touch.showPopover) {
                if (actions.marker.click.zoom) {
                    setTimeout(function () {
                        _this.showPopover(passingObject);
                    }, 500);
                }
                else {
                    _this.showPopover(passingObject);
                }
            }
            if (actions.marker.click.goToLink) {
                var linkParts = actions.marker.click.linkField.split('.');
                var url;
                if (linkParts.length > 1) {
                    var obj = linkParts.shift();
                    var attr = '.' + linkParts.join('.');
                    try {
                        url = eval('passingObject' + attr);
                    }
                    catch (err) {
                        console.log("MapSVG: No such field as passingObject" + attr);
                    }
                    if (url && !_this.disableLinks)
                        if (_this.editMode) {
                            alert('Redirect: ' + url + '\nLinks are disabled in the preview.');
                            return true;
                        }
                    if (actions.marker.click.newTab) {
                        var win = window.open(url, '_blank');
                        win.focus();
                    }
                    else {
                        window.location.href = url;
                    }
                }
            }
            if (_this.events['click.marker']) {
                try {
                    _this.events['click.marker'].call(region, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
            }
        }
    }
    fileExists(url) {
        var _this = this;
        if (url.substr(0, 4) == "data")
            return true;
        var http = new XMLHttpRequest();
        http.open('HEAD', url, false);
        http.send();
        return http.status != 404;
    }
    getStyle(elem, prop) {
        var _this = this;
        if (elem.currentStyle) {
            return elem.currentStyle.margin;
        }
        else if (window.getComputedStyle) {
            if (window.getComputedStyle.getPropertyValue) {
                return window.getComputedStyle(elem, null).getPropertyValue(prop);
            }
            else {
                return window.getComputedStyle(elem)[prop];
            }
        }
    }
    hideMarkersExceptOne(id) {
        var _this = this;
        _this.markers.forEach(function (m) {
            if (m.id != id) {
                m.hide();
            }
        });
        $(_this.containers.wrap).addClass('mapsvg-edit-marker-mode');
    }
    showMarkers() {
        var _this = this;
        _this.markers.forEach(function (m) {
            m.show();
        });
        $(_this.containers.wrap).removeClass('mapsvg-edit-marker-mode');
    }
    markerAddClickHandler(e) {
        var _this = this;
        if ($(e.target).hasClass('mapsvg-marker'))
            return false;
        var mc = MapSVG.mouseCoords(e);
        var x = mc.x - $(_this.containers.svg).offset().left;
        var y = mc.y - $(_this.containers.svg).offset().top;
        var screenPoint = new ScreenPoint(x, y);
        var svgPoint = _this.convertPixelToSVG(screenPoint);
        var geoPoint = _this.convertSVGToGeo(svgPoint);
        if (!$.isNumeric(x) || !$.isNumeric(y))
            return false;
        var location = new MapSVG.Location({
            x: svgPoint.x,
            y: svgPoint.y,
            lat: geoPoint.lat,
            lng: geoPoint.lng,
            img: _this.options.defaultMarkerImage
        });
        if (_this.editingMarker) {
            _this.editingMarker.setPoint(svgPoint);
            return;
        }
        var marker = new MapSVG.Marker({
            location: location,
            mapsvg: this
        });
        _this.markerAdd(marker);
        _this.markerEditHandler && _this.markerEditHandler.call(marker);
    }
    setDefaultMarkerImage(src) {
        var _this = this;
        _this.options.defaultMarkerImage = src;
    }
    setMarkerImagesDependency() {
        var _this = this;
        _this.locationField = _this.objectsRepository.schema.getFieldByType('location');
        if (_this.locationField.markersByFieldEnabled && _this.locationField.markerField && Object.values(_this.locationField.markersByField).length > 0) {
            this.setMarkersByField = true;
        }
        else {
            this.setMarkersByField = false;
        }
    }
    getMarkerImage(fieldValueOrObject, location) {
        var _this = this;
        var fieldValue;
        if (this.setMarkersByField) {
            if (typeof fieldValueOrObject === 'object') {
                fieldValue = fieldValueOrObject[_this.locationField.markerField];
            }
            else {
                fieldValue = fieldValueOrObject;
            }
            if (_this.locationField.markersByField[fieldValue]) {
                return _this.locationField.markersByField[fieldValue];
            }
        }
        return (location && location.img) ? location.img : (this.options.defaultMarkerImage ? this.options.defaultMarkerImage : MapSVG.urls.root + 'markers/_pin_default.png');
    }
    setMarkersEditMode(on, clickAddsMarker) {
        var _this = this;
        _this.editMarkers.on = MapSVG.parseBoolean(on);
        _this.clickAddsMarker = _this.editMarkers.on;
        _this.setEventHandlers();
    }
    setRegionsEditMode(on) {
        var _this = this;
        _this.editRegions.on = MapSVG.parseBoolean(on);
        _this.deselectAllRegions();
        _this.setEventHandlers();
    }
    setEditMode(on) {
        var _this = this;
        _this.editMode = on;
    }
    setDataEditMode(on) {
        var _this = this;
        _this.editData.on = MapSVG.parseBoolean(on);
        _this.deselectAllRegions();
        _this.setEventHandlers();
    }
    download() {
        var _this = this;
        var downloadForm;
        if ($('#mdownload').length === 1) {
            downloadForm = $('#mdownload');
        }
        else {
            downloadForm = $('<form id="mdownload" action="/wp-content/plugins/mapsvg-dev/download.php" method="POST"><input type="hidden" name="svg_file" value="0" /><input type="hidden" name="svg_title"></form>');
            downloadForm.appendTo('body');
        }
        downloadForm.find('input[name="svg_file"]').val($(_this.containers.svg).prop('outerHTML'));
        downloadForm.find('input[name="svg_title"]').val(_this.options.title);
        setTimeout(function () {
            jQuery('#mdownload').submit();
        }, 500);
    }
    showTooltip(html) {
        var _this = this;
        if (html.length) {
            $(_this.containers.tooltip).html(html);
            $(_this.containers.tooltip).addClass('mapsvg-tooltip-visible');
        }
    }
    popoverAdjustPosition() {
        var _this = this;
        if (!$(_this.containers.popover) || !$(_this.containers.popover).data('point'))
            return;
        var pos = _this.convertSVGToPixel($(_this.containers.popover).data('point'));
        $(_this.containers.popover)[0].style.transform = 'translateX(-50%) translate(' + pos[0] + 'px,' + pos[1] + 'px)';
    }
    showPopover(object) {
        var _this = this;
        var mapObject = object instanceof MapSVG.Region ? object : (object.location && object.location.marker && object.location.marker ? object.location.marker : null);
        if (!mapObject)
            return;
        var point;
        if (mapObject instanceof MapSVG.Marker) {
            point = { x: mapObject.x, y: mapObject.y };
        }
        else {
            point = mapObject.getCenterSVG();
        }
        _this.controllers.popover && _this.controllers.popover.destroy();
        _this.controllers.popover = new MapSVG.PopoverController({
            container: $(_this.containers.popover),
            point: point,
            yShift: mapObject instanceof MapSVG.Marker ? mapObject.height : 0,
            template: object instanceof MapSVG.Region ? _this.templates.popoverRegion : _this.templates.popoverMarker,
            mapsvg: _this,
            data: object instanceof MapSVG.Region ? object.forTemplate() : object,
            mapObject: mapObject,
            scrollable: true,
            withToolbar: MapSVG.isPhone && _this.options.popovers.mobileFullscreen ? false : true,
            events: {
                'shown'(mapsvg) {
                    if (_this.options.popovers.centerOn) {
                        var shift = this.container.height() / 2;
                        if (_this.options.popovers.centerOn && !(MapSVG.isPhone && _this.options.popovers.mobileFullscreen)) {
                            _this.centerOn(mapObject, shift);
                        }
                    }
                    try {
                        _this.events['shown.popover'] && _this.events['shown.popover'].call(this, _this);
                    }
                    catch (err) {
                        console.log(err);
                    }
                    _this.popoverShowingFor = mapObject;
                    _this.events.trigger('popoverShown');
                },
                'closed'(mapsvg) {
                    _this.options.popovers.resetViewboxOnClose && _this.viewBoxReset(true);
                    _this.popoverShowingFor = null;
                    try {
                        _this.events['closed.popover'] && _this.events['closed.popover'].call(this, mapsvg);
                    }
                    catch (err) {
                        console.log(err);
                    }
                    _this.events.trigger('popoverClosed');
                },
                'resize'() {
                    if (_this.options.popovers.centerOn) {
                        var shift = this.container.height() / 2;
                        if (_this.options.popovers.centerOn && !(MapSVG.isPhone && _this.options.popovers.mobileFullscreen)) {
                            _this.centerOn(mapObject, shift);
                        }
                    }
                }
            }
        });
    }
    hidePopover() {
        var _this = this;
        _this.controllers.popover && _this.controllers.popover.close();
    }
    hideTip() {
        var _this = this;
        $(_this.containers.tooltip).removeClass('mapsvg-tooltip-visible');
    }
    popoverOffHandler(e) {
        var _this = this;
        if (_this.isScrolling || $(e.target).closest('.mapsvg-popover').length || $(e.target).hasClass('mapsvg-btn-map'))
            return;
        this.controllers.popover && this.controllers.popover.close();
    }
    mouseOverHandler(e, object) {
        var _this = this;
        if (_this.eventsPreventList['mouseover']) {
            return;
        }
        if (_this.options.tooltips.on) {
            var name, data;
            if (object instanceof MapSVG.Region) {
                name = 'tooltipRegion';
                data = object.forTemplate();
            }
            if (object instanceof MapSVG.Marker) {
                name = 'tooltipMarker';
                data = object.object;
            }
            if (_this.popoverShowingFor !== object) {
                _this.showTooltip(_this.templates[name](data));
            }
        }
        let ids;
        if (_this.options.menu.on) {
            if (_this.options.menu.source == 'database') {
                if ((object instanceof MapSVG.Region) && object.objects.length) {
                    ids = object.objects.map(function (obj) {
                        return obj.id;
                    });
                }
                if (object instanceof MapSVG.Marker) {
                    ids = [object.object.id];
                }
            }
            else {
                if ((object instanceof MapSVG.Region)) {
                    ids = [object.id];
                }
                if (this instanceof MapSVG.Marker && object.object.regions && object.object.regions.length) {
                    ids = object.object.regions.map(function (obj) {
                        return obj.id;
                    });
                }
            }
            _this.controllers.directory.highlightItems(ids);
        }
        if (object instanceof MapSVG.Region) {
            if (!object.selected)
                object.highlight();
            if (_this.events['mouseover.region']) {
                try {
                    _this.events['mouseover.region'].call(object, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
            }
        }
        else {
            _this.highlightMarker(object);
            if (_this.events['mouseover.marker']) {
                try {
                    _this.events['mouseover.marker'].call(object, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
            }
        }
    }
    mouseOutHandler(e, object) {
        var _this = this;
        if (_this.eventsPreventList['mouseout']) {
            return;
        }
        if (_this.options.tooltips.on)
            _this.hideTip();
        if (object instanceof MapSVG.Region) {
            if (!object.selected)
                object.unhighlight();
            if (_this.events['mouseout.region']) {
                try {
                    _this.events['mouseout.region'].call(object, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
            }
        }
        else {
            _this.unhighlightMarker();
            if (_this.events['mouseout.marker']) {
                try {
                    _this.events['mouseout.marker'].call(object, e, _this);
                }
                catch (err) {
                    console.log(err);
                }
            }
        }
        if (_this.options.menu.on) {
            _this.controllers.directory.unhighlightItems();
        }
    }
    eventsPrevent(event) {
        var _this = this;
        _this.eventsPreventList[event] = true;
    }
    eventsRestore(event) {
        var _this = this;
        if (event) {
            _this.eventsPreventList[event] = false;
        }
        else {
            _this.eventsPreventList = {};
        }
    }
    setEventHandlers() {
        var _this = this;
        $(_this.containers.map).off('.common.mapsvg');
        $(_this.containers.scrollpane).off('.common.mapsvg');
        $(document).off('keydown.scroll.mapsvg');
        $(document).off('mousemove.scrollInit.mapsvg');
        $(document).off('mouseup.scrollInit.mapsvg');
        if (_this.editMarkers.on) {
            $(_this.containers.map).on('touchstart.common.mapsvg mousedown.common.mapsvg', '.mapsvg-marker', function (e) {
                e.originalEvent.preventDefault();
                var marker = _this.getMarker($(this).attr('id'));
                var startCoords = MapSVG.mouseCoords(e);
                marker.drag(startCoords, _this.scale, function () {
                    if (_this.mapIsGeo) {
                        var svgPoint = new SVGPoint(this.x + this.width / 2, this.y + (this.height - 1));
                        this.geoCoords = _this.convertSVGToGeo(svgPoint);
                    }
                    _this.markerEditHandler && _this.markerEditHandler.call(this, true);
                    this.events.trigger('change', this);
                }, function () {
                    _this.markerEditHandler && _this.markerEditHandler.call(this);
                    this.events.trigger('change', this);
                });
            });
        }
        if (!_this.editMarkers.on) {
            $(_this.containers.map).on('mouseover.common.mapsvg', '.mapsvg-region', function (e) {
                var id = $(this).attr('id');
                _this.mouseOverHandler.call(_this, e, _this.getRegion(id));
            }).on('mouseleave.common.mapsvg', '.mapsvg-region', function (e) {
                var id = $(this).attr('id');
                _this.mouseOutHandler.call(_this, e, _this.getRegion(id));
            });
        }
        if (!_this.editRegions.on) {
            $(_this.containers.map).on('mouseover.common.mapsvg', '.mapsvg-marker', function (e) {
                var id = $(this).attr('id');
                _this.mouseOverHandler.call(_this, e, _this.getMarker(id));
            }).on('mouseleave.common.mapsvg', '.mapsvg-marker', function (e) {
                var id = $(this).attr('id');
                _this.mouseOutHandler.call(_this, e, _this.getMarker(id));
            });
        }
        if (_this.options.scroll.spacebar) {
            $(document).on('keydown.scroll.mapsvg', function (e) {
                if (document.activeElement.tagName !== 'INPUT' && !_this.isScrolling && e.keyCode == 32) {
                    e.preventDefault();
                    $(_this.containers.map).addClass('mapsvg-scrollable');
                    $(document).on('mousemove.scrollInit.mapsvg', function (e) {
                        _this.isScrolling = true;
                        $(document).off('mousemove.scrollInit.mapsvg');
                        _this.scrollStart(e, _this);
                    }).on('keyup.scroll.mapsvg', function (e) {
                        if (e.keyCode == 32) {
                            $(document).off('mousemove.scrollInit.mapsvg');
                            $(_this.containers.map).removeClass('mapsvg-scrollable');
                        }
                    });
                }
            });
        }
        else if (!_this.options.scroll.on) {
            if (!_this.editMarkers.on) {
                $(_this.containers.map).on('touchstart.common.mapsvg', '.mapsvg-region', function (e) {
                    _this.scroll.touchScrollStart = $(window).scrollTop();
                });
                $(_this.containers.map).on('touchstart.common.mapsvg', '.mapsvg-marker', function (e) {
                    _this.scroll.touchScrollStart = $(window).scrollTop();
                });
                $(_this.containers.map).on('touchend.common.mapsvg mouseup.common.mapsvg', '.mapsvg-region', function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if (_this.scroll.touchScrollStart === undefined || _this.scroll.touchScrollStart === $(window).scrollTop()) {
                        _this.regionClickHandler.call(_this, e, _this.getRegion($(this).attr('id')));
                    }
                });
                $(_this.containers.map).on('touchend.common.mapsvg mouseup.common.mapsvg', '.mapsvg-marker', function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if (_this.scroll.touchScrollStart === undefined || _this.scroll.touchScrollStart === $(window).scrollTop()) {
                        _this.regionClickHandler.call(_this, e, _this.getMarker($(this).attr('id')));
                    }
                });
                $(_this.containers.map).on('touchend.common.mapsvg mouseup.common.mapsvg', '.mapsvg-marker-cluster', function (e) {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                    if (!_this.scroll.touchScrollStart || _this.scroll.touchScrollStart == $(window).scrollTop()) {
                        var cluster = $(this).data("cluster");
                        _this.zoomTo(cluster.markers);
                    }
                });
            }
            else {
                if (_this.clickAddsMarker)
                    $(_this.containers.map).on('touchend.common.mapsvg mouseup.common.mapsvg', function (e) {
                        if (e.cancelable) {
                            e.preventDefault();
                        }
                        _this.markerAddClickHandler(e);
                    });
            }
        }
        else {
            $(_this.containers.map).on('touchstart.common.mapsvg mousedown.common.mapsvg', function (e) {
                if ($(e.target).hasClass('mapsvg-popover') || $(e.target).closest('.mapsvg-popover').length) {
                    if ($(e.target).hasClass('mapsvg-popover-close')) {
                        if (e.type == 'touchstart') {
                            if (e.cancelable) {
                                e.preventDefault();
                            }
                        }
                    }
                    return;
                }
                if (e.type == 'touchstart') {
                    if (e.cancelable) {
                        e.preventDefault();
                    }
                }
                let obj;
                if (e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-region') != -1) {
                    obj = _this.getRegion($(e.target).attr('id'));
                    _this.scrollRegionClickHandler.call(_this, e, obj);
                }
                else if (e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker') != -1 && $(e.target).attr('class').indexOf('mapsvg-marker-cluster') === -1) {
                    if (_this.editMarkers.on) {
                        return;
                    }
                    obj = _this.getMarker($(e.target).attr('id'));
                    _this.scrollRegionClickHandler.call(_this, e, obj);
                }
                else if (e.target && $(e.target).attr('class') && $(e.target).attr('class').indexOf('mapsvg-marker-cluster') != -1) {
                    if (_this.editMarkers.on) {
                        return;
                    }
                    obj = ($(e.target).data('cluster'));
                    _this.scrollRegionClickHandler.call(_this, e, obj);
                }
                if (e.type == 'mousedown') {
                    _this.scrollStart(e, _this);
                }
                else {
                    _this.touchStart(e, _this);
                }
            });
        }
    }
    setLabelsRegions(options) {
        var _this = this;
        options = options || _this.options.labelsRegions;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, { labelsRegions: options });
        if (_this.options.labelsRegions.on) {
            _this.regions.forEach(function (region) {
                if (!region.textLabel) {
                    region.textLabel = jQuery('<div class="mapsvg-region-label" />')[0];
                    $(_this.containers.scrollpane).append(region.textLabel);
                }
                try {
                    $(region.textLabel).html(_this.templates.labelRegion(region.forTemplate()));
                }
                catch (err) {
                    console.error('MapSVG: Error in the "Region Label" template');
                }
            });
            _this.labelsRegionsAdjustPosition();
        }
        else {
            _this.regions.forEach(function (region) {
                if (region.textLabel) {
                    $(region.textLabel).remove();
                    region.textLabel = null;
                    delete region.textLabel;
                }
            });
        }
    }
    deleteLabelsMarkers() {
        var _this = this;
        _this.markers.forEach(function (marker) {
            if (marker.textLabel) {
                marker.textLabel.remove();
                marker.textLabel = null;
                delete marker.textLabel;
            }
        });
    }
    setLabelsMarkers(options) {
        var _this = this;
        options = options || _this.options.labelsMarkers;
        options.on != undefined && (options.on = MapSVG.parseBoolean(options.on));
        $.extend(true, _this.options, { labelsMarkers: options });
        if (_this.options.labelsMarkers.on) {
            _this.markers.forEach(function (marker) {
                if (!marker.textLabel) {
                    marker.textLabel = jQuery('<div class="mapsvg-marker-label" data-object-id="' + marker.object.id + '"/>')[0];
                    $(_this.containers.scrollpane).append(marker.textLabel);
                }
                try {
                    $(marker.textLabel).html(_this.templates.labelMarker(marker.object));
                }
                catch (err) {
                    console.error('MapSVG: Error in the "Marker Label" template');
                }
            });
            _this.markersAdjustPosition();
        }
        else {
            _this.deleteLabelsMarkers();
        }
    }
    addLayer(name) {
        var _this = this;
        _this.layers[name] = $('<div class="mapsvg-layer mapsvg-layer-' + name + '"></div>')[0];
        _this.containers.layers.appendChild(_this.layers[name]);
        return _this.layers[name];
    }
    getDb() {
        var _this = this;
        return this.objects;
    }
    getDbRegions() {
        var _this = this;
        return this.regions;
    }
    regionAdd(svgObject) {
        var _this = this;
        var region = new Region(svgObject, _this);
        region.setStatus(1);
        _this.regions.set(region.id, region);
        return region;
    }
    regionDelete(id) {
        if (this.regions.has(id)) {
            this.regions.get(id).elem.remove();
            this.regions.delete(id);
        }
        else if ($('#' + id).length) {
            $('#' + id).remove();
        }
    }
    reloadRegions() {
        var _this = this;
        _this.regions.clear();
        $(_this.containers.svg).find('.mapsvg-region').removeClass('mapsvg-region');
        $(_this.containers.svg).find('.mapsvg-region-disabled').removeClass('mapsvg-region-disabled');
        $(_this.containers.svg).find('path, polygon, circle, ellipse, rect').each(function (index) {
            var elem = this;
            if ($(elem).closest('defs').length)
                return;
            if (elem.getAttribute('id')) {
                if (!_this.options.regionPrefix || (_this.options.regionPrefix && elem.getAttribute('id').indexOf(_this.options.regionPrefix) === 0)) {
                    var region = new Region(elem, _this);
                    _this.regions.set(region.id, region);
                }
            }
        });
    }
    reloadRegionsFull() {
        var _this = this;
        var statuses = _this.regionsRepository.getSchema().getFieldByType('status');
        _this.regions.forEach(function (region) {
            let _region;
            _region = _this.regionsRepository.getLoaded().get(region.id);
            if (_region) {
                region.data = _region;
                if (statuses && _region.status !== undefined && _region.status !== null) {
                    region.setStatus(_region.status);
                }
            }
            else {
                if (_this.options.filters.filteredRegionsStatus || _this.options.filters.filteredRegionsStatus === 0) {
                    region.setStatus(_this.options.filters.filteredRegionsStatus);
                }
            }
        });
        _this.loadDirectory();
        _this.setGauge();
        _this.setLayersControl();
        _this.setGroups();
        if (_this.options.labelsRegions.on) {
            _this.setLabelsRegions();
        }
    }
    updateOutdatedOptions(options) {
        var _this = this;
        if (options.menu && (options.menu.position || options.menu.customContainer)) {
            if (options.menu.customContainer) {
                options.menu.location = 'custom';
            }
            else {
                options.menu.position = options.menu.position === 'left' ? 'left' : 'right';
                options.menu.location = options.menu.position === 'left' ? 'leftSidebar' : 'rightSidebar';
                if (!options.containers || !options.containers[options.menu.location]) {
                    options.containers = options.containers || {};
                    options.containers[options.menu.location] = { on: false, width: '200px' };
                }
                options.containers[options.menu.location].width = options.menu.width;
                if (MapSVG.parseBoolean(options.menu.on)) {
                    options.containers[options.menu.location].on = true;
                }
            }
            delete options.menu.position;
            delete options.menu.width;
            delete options.menu.customContainer;
        }
        if (options.detailsView && (options.detailsView.location === 'near' || options.detailsView.location === 'top')) {
            options.detailsView.location = 'mapContainer';
        }
        if (!options.controls) {
            options.controls = {};
            options.controls.zoom = options.zoom && options.zoom.on && options.zoom.buttons.location !== 'hide';
            options.controls.location = options.zoom && options.zoom.buttons.location !== 'hide' ? options.zoom.buttons.location : 'right';
        }
        if (options.colors && !options.colors.markers) {
            options.colors.markers = {
                base: { opacity: 100, saturation: 100 },
                hovered: { opacity: 100, saturation: 100 },
                unhovered: { opacity: 100, saturation: 100 },
                active: { opacity: 100, saturation: 100 },
                inactive: { opacity: 100, saturation: 100 }
            };
        }
        if (options.tooltipsMode) {
            options.tooltips.mode = options.tooltipsMode;
            delete options.tooltipsMode;
        }
        if (options.popover) {
            options.popovers = options.popover;
            delete options.popover;
        }
    }
    init() {
        var _this = this;
        if (this.options.source === '') {
            throw new Error('MapSVG: please provide SVG file source.');
            return false;
        }
        if (!('remove' in Element.prototype)) {
            Element.prototype.remove = function () {
                if (this.parentNode) {
                    this.parentNode.removeChild(this);
                }
            };
        }
        Math.hypot = Math.hypot || function () {
            var y = 0;
            var length = arguments.length;
            for (var i = 0; i < length; i++) {
                if (arguments[i] === Infinity || arguments[i] === -Infinity) {
                    return Infinity;
                }
                y += arguments[i] * arguments[i];
            }
            return Math.sqrt(y);
        };
        SVGElement.prototype.getTransformToElement = SVGElement.prototype.getTransformToElement || function (toElement) {
            return toElement.getScreenCTM().inverse().multiply(this.getScreenCTM());
        };
        this.setEvents(this.options.events);
        this.events.trigger('beforeLoad');
        this.setCss();
        this.containers.map.classList.add('mapsvg', 'no-transitions');
        this.containers.map.style.background = this.options.colors.background;
        this.setContainers(_this.options.containers);
        this.setColors();
        this.containers.loading = document.createElement('div');
        this.containers.loading.className = 'mapsvg-loading';
        this.containers.loading.innerHTML = this.options.loadingText;
        this.containers.map.appendChild(this.containers.loading);
        this.addLayer('markers');
        this.addLayer('popovers');
        $(_this.containers.loading).css({
            'margin-left'() {
                return -($(this).outerWidth(false) / 2) + 'px';
            },
            'margin-top'() {
                return -($(this).outerHeight(false) / 2) + 'px';
            }
        });
        if (_this.options.googleMaps.on) {
            $(_this.containers.map).addClass('mapsvg-google-map-loading');
        }
        if (_this.options.extension && $().mapSvg.extensions && $().mapSvg.extensions[_this.options.extension]) {
            var ext = $().mapSvg.extensions[_this.options.extension];
            ext && ext.common(_this);
        }
        $.ajax({ url: _this.options.source + '?v=' + _this.options.svgFileVersion }).fail(function (resp) {
            if (resp.status == 404) {
                alert('File not found: ' + _this.options.source + '\n\nIf you moved MapSVG from another server please read the following tutorial:\nhttps://mapsvg.com/tutorials/4.0.x/6/');
            }
            else {
                alert('Can\'t load SVG file. Please contact support.');
            }
        }).done(function (xmlData) {
            var svgTag = $(xmlData).find('svg');
            _this.containers.svg = svgTag[0];
            _this.svgDefault.viewBox = new ViewBox(svgTag.attr('viewBox').split(' '));
            if (svgTag.attr('width') && svgTag.attr('height')) {
                _this.svgDefault.width = parseFloat(svgTag.attr('width').replace(/px/g, ''));
                _this.svgDefault.height = parseFloat(svgTag.attr('height').replace(/px/g, ''));
                _this.svgDefault.viewBox = svgTag.attr('viewBox') ? new ViewBox(svgTag.attr('viewBox').split(' ')) : new ViewBox(0, 0, _this.svgDefault.width, _this.svgDefault.height);
            }
            else if (_this.svgDefault.viewBox) {
                _this.svgDefault.viewBox = new ViewBox(svgTag.attr('viewBox').split(' '));
                _this.svgDefault.width = _this.svgDefault.viewBox.width;
                _this.svgDefault.height = _this.svgDefault.viewBox.height;
            }
            else {
                alert('MapSVG needs width/height or viewBox parameter to be present in SVG file.');
                return false;
            }
            var geo = svgTag.attr("mapsvg:geoViewBox") || svgTag.attr("mapsvg:geoviewbox");
            if (geo) {
                let geoParts = geo.split(" ");
                if (geoParts.length == 4) {
                    _this.mapIsGeo = true;
                    _this.geoCoordinates = true;
                    let sw = new GeoPoint(parseFloat(geo[3]), parseFloat(geo[0]));
                    let ne = new GeoPoint(parseFloat(geo[1]), parseFloat(geo[2]));
                    _this.geoViewBox = new GeoViewBox(sw, ne);
                    _this.mapLonDelta = _this.geoViewBox.ne.lng - _this.geoViewBox.sw.lng;
                    _this.mapLatBottomDegree = _this.geoViewBox.sw.lat * 3.14159 / 180;
                }
            }
            if (this.options.viewBox && _this.options.viewBox.length == 4) {
                _this._viewBox = new ViewBox(_this.options.viewBox);
            }
            else {
                _this.viewBox = new ViewBox(_this.svgDefault.viewBox);
            }
            svgTag.attr('preserveAspectRatio', 'xMidYMid meet');
            svgTag.removeAttr('width');
            svgTag.removeAttr('height');
            _this.reloadRegions();
            $(_this.containers.scrollpane).append(svgTag);
            _this.setSize(_this.options.width, _this.options.height, _this.options.responsive);
            if (_this.options.disableAll) {
                _this.setDisableAll(true);
            }
            _this.setViewBox(_this._viewBox);
            _this.setResponsive(_this.options.responsive);
            _this.setScroll(_this.options.scroll, true);
            _this.setZoom(_this.options.zoom);
            _this.setControls(_this.options.controls);
            _this.setGoogleMaps();
            _this.setTooltips(_this.options.tooltips);
            _this.setPopovers(_this.options.popovers);
            if (_this.options.cursor)
                _this.setCursor(_this.options.cursor);
            _this.setTemplates(_this.options.templates);
            if (!_this.options.backend && _this.options.extension && $().mapSvg.extensions && $().mapSvg.extensions[_this.options.extension]) {
                var ext = $().mapSvg.extensions[_this.options.extension];
                ext && ext.frontend(_this);
            }
            _this.filtersSchema = new Schema({ fields: _this.options.filtersSchema });
            _this.objectsRepository = new MapSVG.DatabaseService({
                map_id: _this.id,
                perpage: _this.options.database.pagination.on ? _this.options.database.pagination.perpage : 0,
                sortBy: _this.options.menu.source == 'database' ? _this.options.menu.sortBy : 'id',
                sortDir: _this.options.menu.source == 'database' ? _this.options.menu.sortDirection : 'desc',
                table: 'database'
            }, _this);
            _this.objectsRepository.events.on('dataLoaded', function () {
                _this.fitOnDataLoadDone = false;
                _this.addLocations();
                _this.attachDataToRegions();
                _this.loadDirectory();
                if (_this.options.labelsMarkers.on) {
                    _this.setLabelsMarkers();
                }
                _this.events.trigger('databaseLoaded');
                _this.updateFiltersState();
            });
            _this.objectsRepository.events.on('schemaChange', function () {
                _this.objectsRepository.reload();
            });
            _this.objectsRepository.events.on('update', function (obj) {
                _this.attachDataToRegions(obj);
                if (_this.options.menu.on && _this.controllers.directory) {
                    _this.loadDirectory();
                }
            });
            _this.objectsRepository.events.on('create', function (obj) {
                _this.attachDataToRegions(obj);
            });
            _this.objectsRepository.events.on('delete', function (id) {
                _this.attachDataToRegions();
                if (_this.options.menu.on && _this.controllers.directory) {
                    _this.loadDirectory();
                }
            });
            _this.regionsRepository.events.on('dataLoaded', function () {
                _this.reloadRegionsFull();
                _this.loadDirectory();
                _this.events.trigger('regionsLoaded');
            });
            _this.setMenu();
            _this.setFilters();
            if (_this.options.menu.filterout.field) {
                var f = {};
                f[_this.options.menu.filterout.field] = _this.options.menu.filterout.val;
                if (_this.options.menu.source == 'regions') {
                }
                else {
                    _this.objectsRepository.query.setFilterOut(f);
                }
            }
            _this.setEventHandlers();
            if (!_this.id) {
                _this.final();
                return;
            }
            if (!_this.options.data_regions || !_this.options.data_db) {
                _this.regionsRepository.find().done(function (regions) {
                    if (_this.options.database.loadOnStart || _this.editMode) {
                        _this.objectsRepository.find().done(function (data) {
                            _this.final();
                        });
                    }
                    else {
                        _this.final();
                    }
                });
            }
            else {
                _this.regionsRepository.loadDataFromResponse(_this.options.data_regions);
                if (_this.editMode || _this.options.database.loadOnStart) {
                    _this.objectsRepository.loadDataFromResponse(_this.options.data_db);
                }
                delete _this.options.data_regions;
                delete _this.options.data_db;
            }
            _this.final();
        });
        return _this;
    }
    final() {
        var _this = this;
        if (_this.options.googleMaps.on && !_this.googleMaps.map) {
            _this.events.on('googleMapsLoaded', function () {
                _this.final();
            });
            return;
        }
        let match = RegExp('[?&]mapsvg_select=([^&]*)').exec(window.location.search);
        if (match) {
            var select = decodeURIComponent(match[1].replace(/\+/g, ' '));
            _this.selectRegion(select);
        }
        if (window.location.hash) {
            var query = window.location.hash.replace('#/m/', '');
            var region = _this.getRegion(query);
            if (region && _this.options.actions.map.afterLoad.selectRegion) {
                _this.regionClickHandler(null, region);
            }
            else {
            }
        }
        setTimeout(function () {
            _this.updateSize();
            setTimeout(function () {
                $(_this.containers.map).removeClass('no-transitions');
            }, 200);
        }, 100);
        _this.events.trigger('afterLoad');
        $(_this.containers.loading).hide();
        MapSVG.addInstance(_this);
    }
}
module.exports = Map;
//# sourceMappingURL=Map.js.map