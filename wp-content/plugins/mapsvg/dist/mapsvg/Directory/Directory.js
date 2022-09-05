import { Controller } from "../Core/Controller";
import { MapSVG } from "../Core/globals";
export class DirectoryController extends Controller {
    constructor(options) {
        super(options);
        this.repository = options.repository;
        this.noPadding = true;
        this.position = options.position;
        this.search = options.search;
    }
    getToolbarTemplate() {
        var t = '<div class="mapsvg-directory-search-wrap">';
        t += '<div class="mapsvg-directory-filter-wrap filter-wrap"></div>';
        t += '</div>';
        t += '</div>';
        return t;
    }
    ;
    viewDidLoad() {
        var _this = this;
        this.menuBtn = $('<div class="mapsvg-button-menu"><i class="mapsvg-icon-menu"></i> ' + this.mapsvg.options.mobileView.labelList + '</div>')[0];
        this.mapBtn = $('<div class="mapsvg-button-map"><i class="mapsvg-icon-map"></i> ' + this.mapsvg.options.mobileView.labelMap + '</div>')[0];
        if (MapSVG.isPhone && _this.mapsvg.options.menu.hideOnMobile) {
            if (this.mapsvg.options.menu.showFirst == 'map') {
                this.toggle(false);
            }
            else {
                this.toggle(true);
            }
        }
        this.mobileButtons = $('<div class="mapsvg-mobile-buttons"></div>')[0];
        this.mobileButtons.append(this.menuBtn, this.mapBtn);
        if (this.mapsvg.options.menu.on !== false) {
            this.mapsvg.containers.wrapAll.appendChild(this.mobileButtons);
        }
        this.events.trigger('shown', this.containers.view);
    }
    ;
    setEventHandlers() {
        var _this = this;
        $(window).on('resize', function () {
            _this.updateTopShift();
        });
        $(this.menuBtn).on('click', function () {
            _this.toggle(true);
        });
        $(this.mapBtn).on('click', function () {
            _this.toggle(false);
            _this.mapsvg.redraw();
        });
        $(this.containers.view).on('click.menu.mapsvg', '.mapsvg-directory-item', function (e) {
            e.preventDefault();
            var objID = $(this).data('object-id');
            var regions;
            var marker;
            var detailsViewObject;
            var eventObject;
            _this.deselectItems();
            _this.selectItems(objID);
            if (MapSVG.isPhone && _this.mapsvg.options.menu.showMapOnClick) {
                _this.toggle(false);
            }
            if (_this.mapsvg.options.menu.source == 'regions') {
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            }
            else {
                detailsViewObject = _this.objects.get(objID);
                eventObject = detailsViewObject;
                if (detailsViewObject.regions) {
                    regions = detailsViewObject.regions.map(function (region) {
                        return _this.mapsvg.getRegion(region.id);
                    }).filter(function (r) {
                        return r !== undefined;
                    });
                }
            }
            if (detailsViewObject.location && detailsViewObject.location.marker)
                marker = detailsViewObject.location.marker;
            if (_this.mapsvg.options.actions.directoryItem.click.showDetails) {
                _this.mapsvg.loadDetailsView(detailsViewObject);
            }
            if (regions && regions.length > 0) {
                if (_this.mapsvg.options.actions.directoryItem.click.zoom) {
                    _this.mapsvg.zoomTo(regions, _this.mapsvg.options.actions.directoryItem.click.zoomToLevel);
                }
                if (regions.length > 1) {
                    _this.mapsvg.setMultiSelect(true);
                }
                regions.forEach(function (region) {
                    var center = region.getCenter();
                    e.clientX = center[0];
                    e.clientY = center[1];
                    if (_this.mapsvg.options.actions.directoryItem.click.selectRegion) {
                        _this.mapsvg.selectRegion(region, true);
                    }
                    if (_this.mapsvg.options.actions.directoryItem.click.showRegionPopover) {
                        if (_this.mapsvg.options.actions.directoryItem.click.zoom) {
                            setTimeout(function () {
                                _this.mapsvg.showPopover(region);
                            }, 500);
                        }
                        else {
                            _this.mapsvg.showPopover(region);
                        }
                    }
                    if (_this.mapsvg.options.actions.directoryItem.click.fireRegionOnClick) {
                        _this.mapsvg.events.trigger('click.region', region, [region]);
                    }
                });
                if (regions.length > 1) {
                    _this.mapsvg.setMultiSelect(false, false);
                }
            }
            if (marker) {
                if (_this.mapsvg.options.actions.directoryItem.click.zoomToMarker) {
                    _this.mapsvg.zoomTo(marker, _this.mapsvg.options.actions.directoryItem.click.zoomToMarkerLevel);
                }
                if (_this.mapsvg.options.actions.directoryItem.click.showMarkerPopover) {
                    if (_this.mapsvg.options.actions.directoryItem.click.zoomToMarker) {
                        setTimeout(function () {
                            _this.mapsvg.showPopover(detailsViewObject);
                        }, 500);
                    }
                    else {
                        _this.mapsvg.showPopover(detailsViewObject);
                    }
                }
                if (_this.mapsvg.options.actions.directoryItem.click.fireMarkerOnClick) {
                    _this.mapsvg.events.trigger('click.marker', marker, [e, _this.mapsvg]);
                }
                _this.mapsvg.selectMarker(marker);
            }
            this.trigger('click', [e, eventObject, _this.mapsvg], $(this));
            var actions = _this.mapsvg.options.actions;
            if (actions.directoryItem.click.goToLink) {
                var linkParts = actions.directoryItem.click.linkField.split('.');
                var url;
                if (linkParts.length > 1) {
                    var obj = linkParts.shift();
                    var attr = '.' + linkParts.join('.');
                    if (obj == 'Region') {
                        if (regions[0] && regions[0].data)
                            url = eval('regions[0].data' + attr);
                    }
                    else {
                        if (detailsViewObject)
                            url = eval('detailsViewObject' + attr);
                    }
                    if (url) {
                        if (actions.directoryItem.click.newTab) {
                            var win = window.open(url, '_blank');
                            win.focus();
                        }
                        else {
                            window.location.href = url;
                        }
                    }
                }
            }
            if (actions.directoryItem.click.showAnotherMap) {
                if (_this.mapsvg.editMode) {
                    alert('"Show another map" action is disabled in the preview');
                    return true;
                }
                var linkParts2 = actions.directoryItem.click.showAnotherMapField.split('.');
                if (linkParts2.length > 1) {
                    var obj2 = linkParts2.shift();
                    var attr2 = '.' + linkParts2.join('.');
                    var map_id;
                    if (obj2 == 'Region') {
                        if (regions[0] && regions[0].data)
                            map_id = eval('regions[0].data' + attr2);
                    }
                    else {
                        if (detailsViewObject)
                            map_id = eval('detailsViewObject' + attr2);
                    }
                    if (map_id) {
                        var container = actions.directoryItem.click.showAnotherMapContainerId ? $('#' + actions.directoryItem.click.showAnotherMapContainerId)[0] : $(_this.mapsvg.containers.map)[0];
                        _this.mapsvg.loadMap(map_id, container);
                    }
                }
            }
        }).on('mouseover.menu.mapsvg', '.mapsvg-directory-item', function (e) {
            var objID = $(this).data('object-id');
            var regions;
            var detailsViewObject;
            var eventObject;
            var marker;
            if (_this.mapsvg.options.menu.source == 'regions') {
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            }
            else {
                detailsViewObject = _this.objects.get(objID);
                eventObject = detailsViewObject;
                if (detailsViewObject.regions) {
                    regions = detailsViewObject.regions.map(function (region) {
                        return _this.mapsvg.getRegion(region.id);
                    });
                }
                if (detailsViewObject.location) {
                    marker = detailsViewObject.location.marker;
                }
            }
            if (regions && regions.length) {
                _this.mapsvg.highlightRegions(regions);
            }
            if (marker) {
                _this.mapsvg.highlightMarker(marker);
                if (_this.mapsvg.options.actions.directoryItem.hover.centerOnMarker) {
                    _this.mapsvg.centerOn(marker);
                }
            }
            _this.events.trigger('mouseover', $(this), [e, eventObject, _this.mapsvg]);
        }).on('mouseout.menu.mapsvg', '.mapsvg-directory-item', function (e) {
            var objID = $(this).data('object-id');
            var regions;
            var detailsViewObject;
            var eventObject;
            var marker;
            if (_this.mapsvg.options.menu.source == 'regions') {
                regions = [_this.mapsvg.getRegion(objID)];
                eventObject = regions[0];
                detailsViewObject = regions[0];
            }
            else {
                detailsViewObject = _this.objects.get(objID);
                eventObject = detailsViewObject;
                if (detailsViewObject.regions) {
                    regions = detailsViewObject.regions.map(function (region) {
                        return _this.mapsvg.getRegion(region.id);
                    });
                }
                if (detailsViewObject.location) {
                    marker = detailsViewObject.location.marker;
                }
            }
            if (regions && regions.length) {
                _this.mapsvg.unhighlightRegions();
            }
            if (marker) {
                _this.mapsvg.unhighlightMarker();
            }
            _this.events.trigger('mouseout', $(this), [e, eventObject, _this.mapsvg]);
        });
        $(this.containers.contentView).on('click', '.mapsvg-category-item', function () {
            var panel = $(this).next('.mapsvg-category-block');
            if (panel[0].style.maxHeight || panel.hasClass('active')) {
                panel[0].style.maxHeight = null;
            }
            else {
                panel[0].style.maxHeight = panel[0].scrollHeight + "px";
            }
            if ($(this).hasClass('active')) {
                $(this).toggleClass('active', false);
                $(this).next('.mapsvg-category-block').addClass('collapsed').removeClass('active');
            }
            else {
                if (_this.mapsvg.options.menu.categories.collapseOther) {
                    $(this).parent().find('.mapsvg-category-item.active').removeClass('active');
                    $(this).parent().find('.mapsvg-category-block.active').removeClass('active').addClass('collapsed');
                }
                $(this).toggleClass('active', true);
                $(this).next('.mapsvg-category-block').removeClass('collapsed').addClass('active');
            }
            var panels = $('.mapsvg-category-block.collapsed');
            panels.each(function (i, panel) {
                panel.style.maxHeight = null;
            });
        });
    }
    highlightItems(ids) {
        var _this = this;
        if (typeof ids != 'object')
            ids = [ids];
        ids.forEach(function (id) {
            $(_this.containers.view).find('#mapsvg-directory-item-' + id).addClass('hover');
        });
    }
    unhighlightItems() {
        $(this.containers.view).find('.mapsvg-directory-item').removeClass('hover');
    }
    selectItems(ids) {
        var _this = this;
        if (typeof ids != 'object')
            ids = [ids];
        ids.forEach(function (id) {
            $(_this.containers.view).find('#mapsvg-directory-item-' + id).addClass('selected');
        });
        _this.scrollable && $(_this.containers.contentWrap).nanoScroller({ scrollTo: $('#mapsvg-directory-item-' + ids[0]) });
    }
    deselectItems() {
        $(this.containers.view).find('.mapsvg-directory-item').removeClass('selected');
    }
    removeItems(ids) {
        $(this.containers.view).find('#mapsvg-directory-item-' + ids).remove();
    }
    filterOut(items) {
        var _this = this;
        return items;
    }
    loadItemsToDirectory() {
        var items;
        var _this = this;
        if (!_this.repository.loaded)
            return false;
        if (_this.mapsvg.options.menu.categories && _this.mapsvg.options.menu.categories.on && _this.mapsvg.options.menu.categories.groupBy) {
            var categoryField = _this.mapsvg.options.menu.categories.groupBy;
            if (_this.repository.getSchema().getField(categoryField) === undefined || _this.repository.getSchema().getField(categoryField).options === undefined) {
                return false;
            }
            var categories = _this.repository.getSchema().getField(categoryField).options;
            let items = [];
            categories.forEach(function (category) {
                var dbItems = _this.repository.getLoaded();
                dbItems = _this.filterOut(dbItems);
                let itemArr;
                dbItems.forEach((item) => {
                    itemArr.push(item);
                });
                var catItems = itemArr.filter(function (object) {
                    if (categoryField === 'regions') {
                        var objectRegionIDs = object[categoryField].map(function (region) {
                            return region.id;
                        });
                        return objectRegionIDs.indexOf(category.id) !== -1;
                    }
                    else {
                        return parseInt(object[categoryField]) === parseInt(category.value);
                    }
                });
                category.counter = catItems.length;
                if (categoryField === 'regions') {
                    category.label = category.title;
                    category.value = category.id;
                }
                items.push({ category: category, items: catItems });
            });
            if (_this.mapsvg.options.menu.categories.hideEmpty) {
                items = items.filter(function (item) {
                    return item.category.counter > 0;
                });
            }
        }
        else {
            items = _this.repository.getLoaded();
        }
        try {
            $(this.containers.contentView).html(this.templates.main({ 'items': items }));
        }
        catch (err) {
            console.error('MapSVG: Error in the "Directory item" template');
            console.error(err);
        }
        if (items.size === 0) {
            $(this.containers.contentView).html('<div class="mapsvg-no-results">' + this.mapsvg.options.menu.noResultsText + '</div>');
        }
        if (_this.mapsvg.options.menu.categories.on) {
            if (_this.mapsvg.options.menu.categories.collapse && items.length > 1) {
                $(this.containers.contentView).find('.mapsvg-category-block').addClass('collapsed');
            }
            else if (_this.mapsvg.options.menu.categories.collapse && items.length === 1) {
                $(this.containers.contentView).find('.mapsvg-category-item').addClass('active');
                $(this.containers.contentView).find('.mapsvg-category-block').addClass('active');
                var panel = $(this.containers.contentView).find('.mapsvg-category-block')[0];
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
            else if (!_this.mapsvg.options.menu.categories.collapse) {
                $(this.containers.contentView).find('.mapsvg-category-item').addClass('active');
                $(this.containers.contentView).find('.mapsvg-category-block').addClass('active');
                var panels = $(this.containers.contentView).find('.mapsvg-category-block');
                panels.each(function (i, panel) {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                });
            }
        }
        this.updateTopShift();
        this.updateScroll();
    }
    toggle(on) {
        var _this = this;
        if (on) {
            $(this.containers.main).parent().show();
            $(_this.mapsvg.containers.mapContainer).hide();
            $(this.menuBtn).addClass('active');
            $(this.mapBtn).removeClass('active');
        }
        else {
            $(this.containers.main).parent().hide();
            $(_this.mapsvg.containers.mapContainer).show();
            $(this.menuBtn).removeClass('active');
            $(this.mapBtn).addClass('active');
        }
        if (!$(this.containers.main).parent().is(':visible')) {
            if (MapSVG.isPhone) {
                $(_this.mapsvg.containers.wrap).css('height', 'auto');
                _this.updateScroll();
            }
        }
        else {
            if (MapSVG.isPhone && $(this.containers.main).height() < parseInt(this.mapsvg.options.menu.minHeight)) {
                $(_this.mapsvg.containers.wrap).css('height', parseInt(this.mapsvg.options.menu.minHeight) + 'px');
                _this.updateScroll();
            }
        }
        this.updateTopShift();
    }
    addPagination(pager) {
        this.containers.contentView.append('<div class="mapsvg-pagination-container"></div>');
        $(this.containers.contentView).find('.mapsvg-pagination-container').html(pager);
    }
}
//# sourceMappingURL=Directory.js.map