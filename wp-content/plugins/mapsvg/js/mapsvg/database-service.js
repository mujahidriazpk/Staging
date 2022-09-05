(function( $ ) {
 /**
 * Database class that gets and stores data from MySQL tables (Regions, DB Objects) connected to the current map.
 * two instances of this class are available in MapSVG.Map instance via .getDatabase() and .getDatabaseRegions().
 * @param {object} options
 * @param {MapSVG.Map} mapsvg
 * @constructor
 *
 * @example
 * var database = mapsvg.getDatabase();
 * var objects = database.getLoaded();
 *
 * var regionsDatabase = mapsvg.getDatabaseRegions();
 * var regionsData = regionsDatabase.getLoaded();
 */
MapSVG.DatabaseService = function(options, mapsvg){
    var _this = this;
    this.mapsvg         = mapsvg;
    this.map_id         = options.map_id;
    this.table          = options.table;
    this.perpage        = parseInt(options.perpage) || 0;
    this.sortBy         = options.sortBy  || null;
    this.sortDir        = options.sortDir || null;
    this.type           = options.type    || 'mysql'; // mysql / local
    this.loaded         = false;
    if(this.type == 'local')
        this.dbObject = options.dbObject;
    this.server = this.type == 'mysql' ? new MapSVG.ServerMySQL({map_id: this.map_id, table: this.table, perpage: this.perpage, mapsvg: this.mapsvg}) : new MapSVG.ServerLocal({storage : this.dbObject});
    this.lastChangeTime = Date.now();
    this.cols           = [];
    this.schemaDict     = {};
    this.index          = {};
    this.page           = 1;
    this.query          = new MapSVG.DatabaseQuery();
    this.query.set({
       sort: [{field: this.sortBy, order: this.sortDir}],
       perpage: this.perpage
    });
    this.events         = {
        'create': [],
        'update': [],
        'change': [],
        'schemaChange': [],
        'dataLoaded': [],
        'beforeLoad': []
    }; // array of callbacks
    this.schema         = new MapSVG.DatabaseSchema(options, mapsvg);
    this.schema.on('change',function(){
        _this.getAll();
        _this.trigger('schemaChange');
    });

};

MapSVG.DatabaseService.setQuery = function(params){
};
MapSVG.DatabaseService.prototype.setPerpage = function(perpage) {
    this.server.perpage = perpage;
};

MapSVG.DatabaseService.prototype.lastChangeTime = function() {
    return this.lastChangeTime;
};

MapSVG.DatabaseService.prototype.onFirstPage = function() {
    return this.query.page === 1;
};
MapSVG.DatabaseService.prototype.onLastPage = function() {
    return this.server.hasMoreRecords === false;
};

MapSVG.DatabaseService.prototype.import = function(data, convertLatlngToAddress){
    var _this = this;

    var locationField = _this.getSchemaFieldByType('location');
    var language = 'en';
    if(locationField && locationField.language){
        language = locationField.language;
    }

    data = this.formatCSV(data);
    return this.server.import(data, language, convertLatlngToAddress).done(function(){
        _this.getAll();
    });
};
MapSVG.DatabaseService.prototype.create = function(obj){
    var _this = this;
    return this.server.create(obj).done(function(){
        _this.trigger('create', obj);
        _this.trigger('change', obj);
    });
};
MapSVG.DatabaseService.prototype.update = function(obj){
    var _this = this;
    return this.server.update(obj).done(function(){
        _this.trigger('update', obj);
        _this.trigger('change', obj);
    });
};
MapSVG.DatabaseService.prototype.delete = function(id){
    var _this = this;
    return this.server.delete(id).done(function(){
        _this.trigger('delete', id);
        _this.trigger('change', id);
    });
};
MapSVG.DatabaseService.prototype.clear = function(id){
    var _this = this;
    return this.server.clear().done(function(){
        _this.getAll();
        _this.trigger('delete');
        _this.trigger('change');
    });
};
/**
 * Returns one of the loaded objects by ID.
 * Objects are usually loaded with pagination and this method
 * doesn't return an object if it's outside of the currently loaded page.
 * @param {string} id
 * @returns {object}
 * @example
 * var obejct = mapsvg.getDatabase().getLoadedObject(2);
 */
MapSVG.DatabaseService.prototype.getLoadedObject = function(id){
    var _this = this;
    return this.server.getLoadedObject(id);
};

/**
 * Loads objects from remote server, from the table connected to current MapSVG map.
 * This method triggers reloading markers on the map and reloading items in the directory.
 * You can add a custom event handler using MapSVG "dataLoaded" event.
 * @param params
 * @returns {Deferred}
 * @example
 * mapsvg.getDatabase().getAll({
 *   page: 1,
 *   perpage: 30,
 *   sortBy: 'id',
 *   sortDir: 'DESC',
 *   filters: {regions: "US-TX"}
 * }).done(function(objects){
 *   // do something
 * });
 */
MapSVG.DatabaseService.prototype.getAll = function(params){
    var _this = this;

    _this.trigger('beforeLoad', params);

    if(this.mapsvg.options.database.noFiltersNoLoad && Object.keys(_this.query.filters).length === 0){
        defer = $.Deferred();
        var obj = this.rows;
        defer.promise();
        defer.resolve(obj);
        _this.server.fill({objects: []});
        _this.trigger('dataLoaded');
        return defer;
    }

    if(typeof params == 'object' && Object.keys(params).length && !params.page)
        _this.query.page = 1;

    _this.query.set(params);

    this.page = params && params.page? parseInt(params.page) : this.page;

    var data = _this.query.get();

    if(!this.schema.loaded()){
        data.with_schema = true;
    }
    if(this.mapsvg.editMode && typeof data.filterout !== 'undefined'){
        delete data.filterout;
    }
    return this.server.get(data).done(function(_data){
        if(!_this.schema.loaded() && _data.schema){
            _this.schema.set(_data.schema);
        }
        _this.loaded = true;
        _this.trigger('dataLoaded');
    });
};

/**
 * Fill local database storage with preloaded data
 * @param {Object} data
 */
MapSVG.DatabaseService.prototype.fill = function(data){
    var _this = this;
    this.server.fill(data);
    if(!_this.schema.loaded() && data.schema){
        _this.schema.set(data.schema);
    }
    _this.loaded = true;
    _this.trigger('dataLoaded');
};


/**
 * Returns all loaded objects
 * @returns {Array}
 */
MapSVG.DatabaseService.prototype.getLoaded = function(){
    return this.server.rows;
};

MapSVG.DatabaseService.prototype.formatData = function(data){
    var _this = this;
    return data; //JSON.stringify(data);
};

MapSVG.DatabaseService.prototype.formatCSV = function(data){

    var _this = this;
    var newdata = [];

    data.forEach(function(object, index){
       var newObject = {};
       for(var key in object){
           var field = _this.schema.getField(key);
           if(key === 'post'){
               field = {type: "post"}
           }
           if(field !== undefined ){
               switch (field.type){
                   case "region":
                       newObject[key] = object[key].split(',')
                           .map(function(regionId){
                               return regionId.trim();
                           }).filter(function(rId) {
                               return  (_this.mapsvg.getRegion(rId) !== undefined || _this.mapsvg.regions.find(function(item){ return item.title === rId }) !== undefined);
                           }).map(function(rId){
                               var r = _this.mapsvg.getRegion(rId);
                               if(typeof r === 'undefined'){
                                   r = _this.mapsvg.regions.find(function(item){ return item.title === rId });
                               }
                               return {id: r.id, title: r.title}
                           });
                       break;
                   case "location":
                       var latLngRegex = /^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/g;
                       if(object[key].match(latLngRegex)){
                           var coords = object[key].split(',').map(function(n){ return parseFloat(n); });
                           if(coords.length == 2 && (coords[0] > -90 && coords[0] < 90) && (coords[1] > -180 && coords[1] < 180)){
                               newObject[key] = {lat: coords[0], lng: coords[1]};
                           } else {
                               newObject[key] = '';
                           }
                       } else if(object[key]){
                           newObject[key] = {address: object[key]};
                       }

                       if(typeof newObject[key] == 'object'){
                           newObject[key].img = _this.mapsvg.getData().options.defaultMarkerImage;
                       }

                       break;
                   case "select":
                       var field = _this.schema.getField(key);
                       if(field.multiselect){
                           var labels = _this.schema.getField(key).options.map(function(f){
                              return f.label;
                           });
                           newObject[key] = object[key].split(',')
                               .map(function(label){
                                   return label.trim();
                               }).filter(function(label) {
                                   return  labels.indexOf(label) !== -1;
                               }).map(function(label){
                                   return _this.schema.getField(key).options.filter(function(option){
                                       return option.label == label;
                                   })[0];
                               });
                       } else {
                           newObject[key] = object[key];
                       }
                       break;
                   case "radio":
                   case "text":
                   case "textarea":
                   case "status":
                   default:
                       newObject[key] = object[key];
                       break;
               }
           }
       }
       data[index] = newObject;
    });

    return data;
};

MapSVG.DatabaseService.prototype.on = function(event, callback){
    this.lastChangeTime = Date.now();
    if(!this.events[event])
        this.events[event] = [];
    this.events[event].push(callback);
};
MapSVG.DatabaseService.prototype.trigger = function(event, params){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback(params);
        });
};
MapSVG.DatabaseService.prototype.onSchemaChange = function(callback){
    this.onSchemaChangeCallbacks.push(callback);
};
/**
 * Returns table schema
 * @param options
 * @returns {object}
 */
MapSVG.DatabaseService.prototype.getSchema = function(options){
    return this.schema.get(options);
};
MapSVG.DatabaseService.prototype.getSchemaField = function(field){
    return this.schema.getField(field);
};
MapSVG.DatabaseService.prototype.getSchemaFieldByType = function(type){
    return this.schema.getFieldByType(type);
};
MapSVG.DatabaseService.prototype.setSchema = function(options){
    return this.schema.set(options);
};
MapSVG.DatabaseService.prototype.loadSchema = function(options){
    return this.schema.load(options);
};
MapSVG.DatabaseService.prototype.saveSchema = function(options){
    return this.schema.save(options);
};
MapSVG.DatabaseService.prototype.getColumns = function(options){
    return this.schema.getColumns(options);
};



/* SCHEMA */
MapSVG.DatabaseSchema = function(options, mapsvg){
    this.mapsvg         = mapsvg;
    this.map_id         = options.map_id;
    this.table          = options.table;
    this.lastChangeTime = Date.now();
    this.cols           = [];
    this.schema         = [];
    this.schemaDict     = {};
    this.events         = {
        'create': [],
        'update': [],
        'change': []
    }; // array of callbacks
};

MapSVG.DatabaseSchema.prototype.loaded = function(options){
    return this.schema.length!==0;
};
MapSVG.DatabaseSchema.prototype.set = function(options){
    var _this = this;

    if(options){
        _this.schema = options.map(function(field){
            field.visible = MapSVG.parseBoolean(field.visible);
            if(field.type == 'region'){
                field.options = [];
                field.optionsDict = {};
                _this.mapsvg.regions.forEach(function(region){
                    field.options.push({id: region.id, title: region.title});
                    field.optionsDict[region.id] = region.title ? region.title : region.id;
                });
            }
            _this.schemaDict[field.name] = field;
            return field;
        });
    }

};
MapSVG.DatabaseSchema.prototype.save = function(fields){
    var _this = this;
    this.set(fields);
    for(var i in this.schema){
        if(!this.schema[i])
            this.schema.splice(i,1);
    }
    fields = JSON.stringify(fields);
    fields = fields.replace(/select/g,"!mapsvg-encoded-slct");
    fields = fields.replace(/table/g,"!mapsvg-encoded-tbl");
    fields = fields.replace(/database/g,"!mapsvg-encoded-db");
    fields = fields.replace(/varchar/g,"!mapsvg-encoded-vc");

    return $.post(ajaxurl, {action: 'mapsvg_save_schema', schema: fields, map_id: this.map_id, table: this.table, _wpnonce: MapSVG.nonce}).done(function(){
        _this.trigger('change');
    });
};
MapSVG.DatabaseSchema.prototype.get = function(options){
    return this.schema;
};
MapSVG.DatabaseSchema.prototype.getField = function(field){

    if(field=='id'){
        return {name: 'id', visible: true, type: 'id'};
    }

    return this.schemaDict[field];
};
MapSVG.DatabaseSchema.prototype.getFieldByType = function(type){
    var f = null;
    this.schema.forEach(function(field){
       if(field.type === type)
           f = field;
    });
    return f;
};
MapSVG.DatabaseSchema.prototype.load = function(options){
    var _this = this;
    return $.get(ajaxurl, {action: 'mapsvg_get_schema', map_id: this.map_id, table: this.table}, null, 'json')
        .done(function(schema){
            _this.set(schema);
        });
};
MapSVG.DatabaseSchema.prototype.getColumns = function (filters) {

    filters = filters || {};

    var _this = this;
    var columns = this.get().slice(0); // clone array
    if(this.table == 'regions')
        columns.unshift({name: 'title', visible: true, type: 'title'}); // add Title column to the beginning
    var needAddId = true;
    columns.forEach(function(col){
        if(col.name == 'id'){
            needAddId = false;
        }
    });
    if(needAddId)
        columns.unshift({name: 'id', visible: true, type: 'id'}); // add ID column to the beginning
    var needfilters = Object.keys(filters).length !== 0;
    var results = [];

    if(needfilters){
        var filterpass;
        columns.forEach(function(obj){
            filterpass = true;
            for(var param in filters) {
                filterpass = (obj[param] == filters[param]);
            }
            filterpass && results.push(obj);
        });
    } else {
        results = columns;
    }

    return results;
};
MapSVG.DatabaseSchema.prototype.on = function(event, callback){
    this.lastChangeTime = Date.now();
    if(!this.events[event])
        this.events = {};
    this.events[event].push(callback);
};
MapSVG.DatabaseSchema.prototype.trigger = function(event){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback();
        });
};

MapSVG.Server = function(params){
    this.index = {};
    this.hasMoreRecords = false;
};
MapSVG.Server.prototype.reindex = function(){
    var _this = this;
    this.index = {};
    this.rows && this.rows.forEach(function(obj, index) {
        _this.index[obj.id] = index;
    });
};
MapSVG.Server.prototype.getLoadedObject = function(id){
    var index = this.index[id];
    if(index != undefined) {
        return this.rows[index];
    }else{
        return null;
    }
};


MapSVG.ServerMySQL = function(params){
    this.map_id = params.map_id;
    this.table = params.table;
    this.perpage = params.perpage;
    this.rows = [];
    MapSVG.Server.call(this);
};
MapSVG.extend(MapSVG.ServerMySQL, MapSVG.Server);

MapSVG.ServerMySQL.prototype.formatData = function(_object){
    var object = {};
    for(var i in _object){
        if(_object[i] && (typeof _object[i] == 'object' || typeof _object[i] == 'function') && _object[i].getOptions!=undefined){
            object[i] = _object[i].getOptions();
        }else{
            if(_object[i] !== null && _object[i] !== undefined && !(typeof _object[i] == 'object' && _object[i].length===0)){
                object[i] = _object[i];
            }else{
                object[i] = '';
            }

        }
    }
    return JSON.stringify(object);
};

MapSVG.ServerMySQL.prototype.actionData = function(action, data){
    data.action  = action;
    data.map_id  = this.map_id;
    data.table   =  this.table;
    data.perpage = this.perpage;
    data._wpnonce = MapSVG.nonce;
    return data;
};

MapSVG.ServerMySQL.prototype.fill = function(_data){
    var _this = this;
    if(_data.objects && _data.objects.length){
        _this.hasMoreRecords = this.perpage && (_data.objects.length > this.perpage) ? true : false;
        if(_this.hasMoreRecords){
            _data.objects.pop();
        }
        _this.rows = _data.objects;
    }else{
        _this.hasMoreRecords = false;
        _this.rows = [];
    }
    _this.reindex();
};

MapSVG.ServerMySQL.prototype.get = function(data){
    var _that = this;
    if(data.perpage !== undefined){
        this.perpage = data.perpage;
    }
    return $.getJSON(ajaxurl, this.actionData('mapsvg_data_get_all', data)).done(function(_data){
        _that.fill(_data);
    });
};
MapSVG.ServerMySQL.prototype.import = function(data, language, convertLatlngToAddress){
    var _this = this;

    var i,j,temparray,chunk = 50;
    var chunks = [];

    for (i=0,j=data.length; i<j; i+=chunk) {
        temparray = data.slice(i,i+chunk);
        chunks.push(temparray);
    }

    if(chunks.length > 0){

        var delay = 0;
        var delayPlus = chunks[0][0] && chunks[0][0].location ? 1000 : 0;

        defer = $.Deferred();
        defer.promise();

        _this.completeChunks = 0;

        chunks.forEach(function(chunk){
            delay += delayPlus;
            setTimeout(function(){
                var _data = JSON.stringify(chunk);
                $.post(ajaxurl, _this.actionData('mapsvg_data_import', {data: _data, language: language, convertLatlngToAddress: convertLatlngToAddress}), null, 'json').done(function(_data){

                    _this.completeChunk(chunks, defer);

                });
            }, delay);
        });
    }


    return defer;

};

MapSVG.ServerMySQL.prototype.completeChunk = function(chunks, defer){
    var _this = this;
    _this.completeChunks++;
    if(_this.completeChunks === chunks.length){
        defer.resolve();
    }
};

MapSVG.ServerMySQL.prototype.create = function(data){
    var _this = this;
    this.rows.push(data);
    var newObj = $.extend({}, data);
    newObj.post && delete newObj.post;
    var _data = this.formatData(newObj);
    return $.post(ajaxurl, this.actionData('mapsvg_data_create', {data: _data}), null, 'json').done(function(resp){

    }).then(function(resp){
        data.id = resp.id;
        _this.reindex();
       return  data;
    });
};
MapSVG.ServerMySQL.prototype.update = function(data){
    var index = this.index[data.id];
    if(index!==undefined){
        $.extend(this.rows[index], data);
        var newObj = $.extend({}, data);
        newObj.post && delete newObj.post;
        var _data = this.formatData(newObj);
        return $.post(ajaxurl, this.actionData('mapsvg_data_update', {data: _data}), null, 'json');
    }
};
MapSVG.ServerMySQL.prototype.delete = function(id){
    var _this = this;
    var index = this.index[id];
    this.rows.splice(index,1);
    return $.post(ajaxurl, this.actionData('mapsvg_data_delete', {data: {id: id}}), null, 'json').done(function(){
        _this.reindex();
    });
};
MapSVG.ServerMySQL.prototype.clear = function(id){
    var _this = this;
    // var index = this.index[id];
    // this.rows.splice(index,1);
    return $.post(ajaxurl, this.actionData('mapsvg_data_clear', {data: {}}), null, 'json').done(function(){
        this.rows = [];
        _this.reindex();
    });
};

MapSVG.ServerLocal = function(params){
    this.rows = params.storage;
    MapSVG.Server.call(this, params);
    this.reindex();
};
MapSVG.extend(MapSVG.ServerLocal, MapSVG.Server);

MapSVG.ServerLocal.prototype.get = function(data){
    defer = $.Deferred();
    var obj = this.rows;
    defer.promise();
    defer.resolve(obj);
    return defer;
};
MapSVG.ServerLocal.prototype.create = function(data){
    if(data.id){
        var index = this.index[data.id];
        if(this.rows[index]){
            return this.update(data);
        }
    }else{
        data.id = this.getId();
    }

    this.rows.push(data);
    this.reindex();

    defer = $.Deferred();
    defer.promise();
    defer.resolve(data);

    return defer;
};
MapSVG.ServerLocal.prototype.update = function(data){
    defer = $.Deferred();
    var index = this.index[data.id];

    if(this.rows[index]){
        // $.extend(true, this.rows[index], data);
        for(var key in data){
            this.rows[index][key] = data[key];
        }
        this.rows[index] = data;
    } else {
        this.rows.push(data);
    }
    this.reindex();

    defer.promise();
    defer.resolve(data);
    return defer;
};
MapSVG.ServerLocal.prototype.delete = function(id){
    var index = this.index[id];
    this.rows.splice(index,1);
    this.reindex();
    defer = $.Deferred();
    defer.promise();
    defer.resolve(this.storage);
    return defer;
};
MapSVG.ServerLocal.prototype.getId = function(){
    var t = this.rows.map(function(obj){
        return obj.id;
    });
    if(!t.length)
        t = [0];
    return !t ? 1 : Math.max.apply(null, t)+1;
};

MapSVG.Filters = function(fields){
    this.schema = {};
    this.schemaDict = {};
    this.fields = {};
    this.events         = {
        'change': []
    }; // array of callbacks
    this.setSchema(fields);
};
MapSVG.Filters.prototype.set = function(fields){
    this.fields = fields;
};
MapSVG.Filters.prototype.save = function(fields){
    this.fields = fields;
};
MapSVG.Filters.prototype.reset = function(fields){
    this.fields = {};
};
MapSVG.Filters.prototype.get = function(fields){
    return this.fields;
};
MapSVG.Filters.prototype.getField = function(field){
    return this.schemaDict[field];
};

MapSVG.Filters.prototype.getSchema = function(fields){
    return this.schema;
};
MapSVG.Filters.prototype.setSchema = function(fields){
    var _this = this;
    if(fields)
        fields.forEach(function(field){
            var paramName = field.parameterName.split('.')[1];
            _this.schemaDict[paramName] = field;
        });
    this.schema = fields;
    return this.schema;
};
MapSVG.Filters.prototype.on = function(event, callback){
    if(!this.events[event])
        this.events[event] = [];
    this.events[event].push(callback);
};
MapSVG.Filters.prototype.trigger = function(event){
    var _this = this;
    if(this.events[event] && this.events[event].length)
        this.events[event].forEach(function(callback){
            callback && callback(_this);
        });
};


MapSVG.DatabaseQuery = function(options){
    options = options || {};
    this.sort           = options.sort   || {};
    this.sortBy         = options.sortBy || null;
    this.sortDir        = options.sortDir || null;
    this.page           = options.page || 1;
    this.perpage        = options.perpage || 0;
    this.search         = options.search;
    this.searchField    = options.searchField;
    this.filters        = options.filters || {};
    this.filterout      = options.filterout || {};
};
MapSVG.DatabaseQuery.prototype.set = function(fields){
    var _this = this;
    for(var key in fields){
        if(key == 'filters'){
            _this.setFilters(fields[key]);
        }else{
            _this[key] = fields[key];
        }
    }
};
MapSVG.DatabaseQuery.prototype.get = function(){
    return {
        search: this.search,
        searchField: this.searchField,
        searchFallback: this.searchFallback,
        filters: this.filters,
        filterout: this.filterout,
        page: this.page,
        sort: this.sort,
        perpage: this.perpage
    };
};

MapSVG.DatabaseQuery.prototype.setFilters = function(fields){
    var _this = this;
    for(var key in fields){
        if(fields[key] === null || fields[key] === "" || fields[key] === undefined){
            if(_this.filters[key]){
                delete _this.filters[key];
            }
        } else {
            _this.filters[key] = fields[key];
        }
    }
};
MapSVG.DatabaseQuery.prototype.setFilterOut = function(fields){
    var _this = this;
    for(var key in fields){
        _this.filterout[key] = fields[key];
    }
};
MapSVG.DatabaseQuery.prototype.resetFilters = function(fields){
    this.filters = {};
};
MapSVG.DatabaseQuery.prototype.setFilterField = function(field, value){
    this.filters[field] = value;
};

})( jQuery );