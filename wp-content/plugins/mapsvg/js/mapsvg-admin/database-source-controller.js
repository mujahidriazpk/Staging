(function($, window){
    var MapSVGAdminDatabaseSourceController = function(container, admin, mapsvg){
        this.name = 'database-source';
        this.database = mapsvg.getDatabaseService();

        MapSVGAdminController.call(this, container, admin, mapsvg);
    };
    window.MapSVGAdminDatabaseSourceController = MapSVGAdminDatabaseSourceController;
    MapSVG.extend(MapSVGAdminDatabaseSourceController, window.MapSVGAdminController);


    MapSVGAdminDatabaseSourceController.prototype.setEventHandlers = function(){
        var _this = this;
        this.view.on('click','#tst', function(){
        });
    }

})(jQuery, window);