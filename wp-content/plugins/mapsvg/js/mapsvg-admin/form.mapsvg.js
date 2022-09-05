var scripts       = document.getElementsByTagName('script');
var myScript      = scripts[scripts.length - 1].src.split('/');
myScript.pop();
// var pluginJSURL   =  myScript.join('/')+'/';
// myScript.pop();
var pluginRootURL =  myScript.join('/')+'/';


(function($, MapSVG, window){

    MapSVG.parseBoolean = function (string) {
        switch (String(string).toLowerCase()) {
            case "on":
            case "true":
            case "1":
            case "yes":
            case "y":
                return true;
            case "off":
            case "false":
            case "0":
            case "no":
            case "n":
                return false;
            default:
                return undefined;
        }
    };

    function extend(sub, base) {
        sub.prototype = Object.create(base.prototype);
        sub.prototype.constructor = sub;
    }

    function Form(options){
        this.title = options.title;
        this.fields = options.fields;
    }
    Form.prototype.inputToObject = function(formattedValue) {

        var obj = {};

        function add(obj, name, value){
            //if(!addEmpty && !value)
            //    return false;
            if(name.length == 1) {
                obj[name[0]] = value;
            }else{
                if(obj[name[0]] == null)
                    obj[name[0]] = {};
                add(obj[name[0]], name.slice(1), value);
            }
        }

        if($(this).attr('name') && !($(this).attr('type')=='radio' && !$(this).prop('checked'))){
            add(obj, $(this).attr('name').replace(/]/g, '').split('['), formattedValue);
        }

        return obj;
    };

    function FormElement(options, formBuilder){

        options = options || {};

        var _this = this;

        this.formBuilder = formBuilder;
        this.images      = [];
        this.type        = options.type;
        this.value       = options.value;
        this.searchable  = MapSVG.parseBoolean(options.searchable);

        this.databaseFields = this.formBuilder.mapsvg.database.getSchema().map(function(obj){
            if(obj.type =='text' || obj.type =='region' || obj.type =='textarea' || obj.type =='post' || obj.type =='select' || obj.type =='radio' || obj.type =='checkbox'){
                if(obj.type == 'post'){
                    return 'Object.post.post_title';
                }else{
                    return 'Object.'+obj.name;
                }
            }
        });
        this.databaseFieldsFilterableShort = this.formBuilder.mapsvg.database.getSchema().filter(function(obj){
            return (obj.type == 'select' || obj.type == 'radio' || obj.type == 'region');
        }).map(function(obj){
            return obj.name;
        });
        this.regionFields = this.formBuilder.mapsvg.regionsDatabase.getSchema().map(function(obj){
            if(obj.type =='status' || obj.type =='text' || obj.type =='textarea' || obj.type =='post' || obj.type =='select' || obj.type =='radio' || obj.type =='checkbox'){
                if(obj.type == 'post'){
                    return 'Region.post.post_title';
                }else{
                    return 'Region.'+obj.name;
                }
            }
        });

        this.db_type = 'varchar(255)';

        if(this.type == 'region') {
            this.options = this.formBuilder.getRegionsList();
            this.label = 'Regions';
            this.name = 'regions';
            this.db_type = 'text';
        }else if(this.type == 'text') {
            this.searchType = options.searchType || 'fulltext';
        }else if(this.type == 'textarea') {
            this.autobr = options.autobr;
            this.html = options.html;
            this.db_type = 'text';
        }else if(this.type == 'modal'){
            this.showButtonText = options.showButtonText;
        }else if(this.type == 'location'){
            this.location = this.value;
            this.label = this.label || (options.label === undefined ? 'Location' : options.label);
            this.name = 'location';
            this.db_type = 'text';
            this.languages = [{"value":"sq","label":"Albanian"},{"value":"ar","label":"Arabic"},{"value":"eu","label":"Basque"},{"value":"be","label":"Belarusian"},{"value":"bg","label":"Bulgarian"},{"value":"my","label":"Burmese"},{"value":"bn","label":"Bengali"},{"value":"ca","label":"Catalan"},{"value":"zh-cn","label":"Chinese (simplified)"},{"value":"zh-tw","label":"Chinese (traditional)"},{"value":"hr","label":"Croatian"},{"value":"cs","label":"Czech"},{"value":"da","label":"Danish"},{"value":"nl","label":"Dutch"},{"value":"en","label":"English"},{"value":"en-au","label":"English (australian)"},{"value":"en-gb","label":"English (great Britain)"},{"value":"fa","label":"Farsi"},{"value":"fi","label":"Finnish"},{"value":"fil","label":"Filipino"},{"value":"fr","label":"French"},{"value":"gl","label":"Galician"},{"value":"de","label":"German"},{"value":"el","label":"Greek"},{"value":"gu","label":"Gujarati"},{"value":"iw","label":"Hebrew"},{"value":"hi","label":"Hindi"},{"value":"hu","label":"Hungarian"},{"value":"id","label":"Indonesian"},{"value":"it","label":"Italian"},{"value":"ja","label":"Japanese"},{"value":"kn","label":"Kannada"},{"value":"kk","label":"Kazakh"},{"value":"ko","label":"Korean"},{"value":"ky","label":"Kyrgyz"},{"value":"lt","label":"Lithuanian"},{"value":"lv","label":"Latvian"},{"value":"mk","label":"Macedonian"},{"value":"ml","label":"Malayalam"},{"value":"mr","label":"Marathi"},{"value":"no","label":"Norwegian"},{"value":"pl","label":"Polish"},{"value":"pt","label":"Portuguese"},{"value":"pt-br","label":"Portuguese (brazil)"},{"value":"pt-pt","label":"Portuguese (portugal)"},{"value":"pa","label":"Punjabi"},{"value":"ro","label":"Romanian"},{"value":"ru","label":"Russian"},{"value":"sr","label":"Serbian"},{"value":"sk","label":"Slovak"},{"value":"sl","label":"Slovenian"},{"value":"es","label":"Spanish"},{"value":"sv","label":"Swedish"},{"value":"tl","label":"Tagalog"},{"value":"ta","label":"Tamil"},{"value":"te","label":"Telugu"},{"value":"th","label":"Thai"},{"value":"tr","label":"Turkish"},{"value":"uk","label":"Ukrainian"},{"value":"uz","label":"Uzbek"},{"value":"vi","label":"Vietnamese"}];
            this.language = options.language;
            this.markerImages = MapSVG.markerImages;
            this.markersByField = options.markersByField;
            this.markerField = options.markerField;
            this.markersByFieldEnabled = MapSVG.parseBoolean(options.markersByFieldEnabled);
        }else if(this.type == 'search'){
            // this.options = this.formBuilder.editMarkersgetMarkersList();
            this.searchFallback = MapSVG.parseBoolean(options.searchFallback);
            this.placeholder = options.placeholder || 'Search';
            this.noResultsText = options.noResultsText || 'No results found';
            this.width = _this.formBuilder.filtersHide && !_this.formBuilder.modal ? null : (options.width || '100%');
        }else if(this.type == 'distance'){
            // this.options = this.formBuilder.getMarkersList();
            this.label       = this.label || (options.label === undefined ? 'Search radius' : options.label);
            this.distanceControl = options.distanceControl || 'select';
            this.distanceUnits = options.distanceUnits || 'km';
            this.distanceUnitsLabel = options.distanceUnitsLabel || 'km';
            this.fromLabel = options.fromLabel || 'from';
            this.placeholder = options.placeholder;
            this.userLocationButton = options.userLocationButton || false;
            this.type = options.type;
            this.addressField = options.addressField || true;
            this.addressFieldPlaceholder = options.addressFieldPlaceholder || 'Address';
            this.languages = [{"value":"sq","label":"Albanian"},{"value":"ar","label":"Arabic"},{"value":"eu","label":"Basque"},{"value":"be","label":"Belarusian"},{"value":"bg","label":"Bulgarian"},{"value":"my","label":"Burmese"},{"value":"bn","label":"Bengali"},{"value":"ca","label":"Catalan"},{"value":"zh-cn","label":"Chinese (simplified)"},{"value":"zh-tw","label":"Chinese (traditional)"},{"value":"hr","label":"Croatian"},{"value":"cs","label":"Czech"},{"value":"da","label":"Danish"},{"value":"nl","label":"Dutch"},{"value":"en","label":"English"},{"value":"en-au","label":"English (australian)"},{"value":"en-gb","label":"English (great Britain)"},{"value":"fa","label":"Farsi"},{"value":"fi","label":"Finnish"},{"value":"fil","label":"Filipino"},{"value":"fr","label":"French"},{"value":"gl","label":"Galician"},{"value":"de","label":"German"},{"value":"el","label":"Greek"},{"value":"gu","label":"Gujarati"},{"value":"iw","label":"Hebrew"},{"value":"hi","label":"Hindi"},{"value":"hu","label":"Hungarian"},{"value":"id","label":"Indonesian"},{"value":"it","label":"Italian"},{"value":"ja","label":"Japanese"},{"value":"kn","label":"Kannada"},{"value":"kk","label":"Kazakh"},{"value":"ko","label":"Korean"},{"value":"ky","label":"Kyrgyz"},{"value":"lt","label":"Lithuanian"},{"value":"lv","label":"Latvian"},{"value":"mk","label":"Macedonian"},{"value":"ml","label":"Malayalam"},{"value":"mr","label":"Marathi"},{"value":"no","label":"Norwegian"},{"value":"pl","label":"Polish"},{"value":"pt","label":"Portuguese"},{"value":"pt-br","label":"Portuguese (brazil)"},{"value":"pt-pt","label":"Portuguese (portugal)"},{"value":"pa","label":"Punjabi"},{"value":"ro","label":"Romanian"},{"value":"ru","label":"Russian"},{"value":"sr","label":"Serbian"},{"value":"sk","label":"Slovak"},{"value":"sl","label":"Slovenian"},{"value":"es","label":"Spanish"},{"value":"sv","label":"Swedish"},{"value":"tl","label":"Tagalog"},{"value":"ta","label":"Tamil"},{"value":"te","label":"Telugu"},{"value":"th","label":"Thai"},{"value":"tr","label":"Turkish"},{"value":"uk","label":"Ukrainian"},{"value":"uz","label":"Uzbek"},{"value":"vi","label":"Vietnamese"}];
            this.countries = MapSVG.countries;
            this.country = options.country;
            this.language = options.language;
            this.searchByZip = options.searchByZip;
            this.zipLength = options.zipLength || 5;
            this.userLocationButton = MapSVG.parseBoolean(options.userLocationButton);
            this.options = options.options || [
                {value: '10',  default: true},
                {value: '30',  default: false},
                {value: '50',  default: false},
                {value: '100',  default: false}
            ];

            var selected = false;
            if(_this.value){
                this.options.forEach(function(option){
                    if(option.value === _this.value.length){
                        option.selected = true;
                        selected = true;
                    }
                });
            }
            if(!selected){
                this.options.forEach(function(option){
                    if(option.default){
                        option.selected = true;
                    }
                });
            }


            // this.name = 'marker';
            // this.db_type = 'text';
            // this.isLink = options.isLink!==undefined ? MapSVG.parseBoolean(options.isLink) : false;
            // this.urlField = options.urlField || null;
        }else if(this.type == 'post'){
            if(_this.formBuilder.admin) this.post_types = this.formBuilder.admin.getPostTypes();
            this.post_type = options.post_type || this.post_types[0];
            this.add_fields = MapSVG.parseBoolean(options.add_fields);
            this.db_type = 'int(11)';
            this.name = 'post_id';
            this.post_id = options.post_id;
            this.post = options.post;
        }else if(this.type == 'checkbox'){
            this.db_type = 'tinyint(1)';
            this.checkboxLabel = options.checkboxLabel;
            this.checkboxValue = options.checkboxValue;
        }else if(this.type == 'checkboxes'){
            this.db_type = 'text';
            this.checkboxLabel = options.checkboxLabel;
            this.options = options.options || [
                {label: 'Option one', name: 'option_one', value: 1},
                {label: 'Option two', name: 'option_two', value: 2}
            ];
            this.optionsDict = options.optionsDict || {};
            if(!this.optionsDict){
                this.options.forEach(function(o){
                    _this.optionsDict[o.value] = o.label;
                });
            }
        }else if(this.type == 'radio' || this.type == 'select'){
            this.options = options.options || [
                {label: 'Option one', name: 'option_one', value: 1},
                {label: 'Option two', name: 'option_two', value: 2}
            ];
            if(this.type=='select'){
                this.multiselect = options.multiselect;
                this.optionsGrouped = options.optionsGrouped;
                this.db_type = this.multiselect ? 'text' : 'varchar(255)';
            }
            this.optionsDict = options.optionsDict || {};
            if(!this.optionsDict){
                this.options.forEach(function(o){
                    _this.optionsDict[o.value] = o.label;
                });
            }
        }else if(this.type=='image') {
            this.button_text = options.button_text || 'Browse...';
            this.db_type = 'text';
            this.label       = options.label || 'Images';
            this.name        = options.name || 'images';
        }else if(this.type == 'status'){
            this.label = options.label || 'Status';

            this.name = 'status';
            this.options = options.options || [
                {label: 'Enabled', value: 1, color: '', disabled: false},
                {label: 'Disabled', value: 0,  color: '', disabled: true}
            ];
        }else if(this.type == 'date'){
            if(_this.formBuilder.admin)
                this.languages = ['en-GB','ar','az','bg','bs','ca','cs','cy','da','de','el','es','et','eu','fa','fi','fo','fr','gl','he','hr','hu','hy','id','is','it','ja','ka','kh','kk','kr','lt','lv','mk','ms','nb','nl','nl-BE','no','pl','pt-BR','pt','ro','rs','rs-latin','ru','sk','sl','sq','sr','sr-latin','sv','sw','th','tr','uk','vi','zh-CN','zh-TW'];
            this.db_type = 'varchar(50)';
            this.language = options.language;
        }

        this.label       = this.label || (options.label === undefined ? 'Label' : options.label);
        this.name        = this.name  || options.name  || 'label';


        this.help = options.help || '';
        this.placeholder = options.placeholder;

        var t = this.type;

        if(t === 'marker' && _this.formBuilder.mapsvg.isGeo()){
            t = 'marker-geo';
        }
        if(t === 'location' && _this.formBuilder.mapsvg.isGeo()){
            t = 'location-geo';
        }

        if(this.formBuilder.filtersMode){
            this.parameterName = options.parameterName || '';
            this.parameterNameShort = this.parameterName.split('.')[1];
            this.placeholder = options.placeholder || '';
            this.templates = {
                result: Handlebars.compile($('#mapsvg-filters-tmpl-'+t+'-view').html())
            };
            this.views = {
                result: $(this.templates.result(this.get()))
            };
        }else{
            this.templates = {
                result: Handlebars.compile($('#mapsvg-data-tmpl-'+t+'-view').html())
            };
            this.views = {
                result: $(this.templates.result(this.get()))
            };
        }

        this.views.result.data('formElement',this);

        if($().mselect2){

            // if(!MapSVG.clickListener){
            //     MapSVG.clickListener = true;
            //     MapSVG.click = false;
            //     $(document).on('mousedown', function(){
            //         MapSVG.click = true;
            //     });
            //     $(document).on('keydown', function(){
            //         MapSVG.click = false;
            //     })
            // }

            if(this.type!=='distance'){
                var select2Options = {};
                if(this.formBuilder.filtersMode && this.type=='select'){
                    select2Options.placeholder = this.placeholder;
                    if(!this.multiselect){
                        select2Options.allowClear = true;
                    }
                }
                this.views.result.find('select').css({width: '100%', display: 'block'})
                    .mselect2(select2Options)
                    .on('select2:focus',function(){
                        $(this).mselect2('open');
                    });
                this.views.result.find('.select2-selection--multiple .select2-search__field').css('width', '100%');
            }else{
                this.views.result.find('select').mselect2().on('select2:focus',function(){
                    $(this).mselect2('open');
                });
            }
        }
        if($().colorpicker) {
            this.views.result.find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({'background-color': ''});
            });
            this.views.edit && this.views.edit.find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({'background-color': ''});
            });
        }

        // if(this.autobr){
        //     this.value = this.value.replace(/\n/g,'<br />');
        //     var updateTextarea = function(codemirror, changeobj){
        //         var css = codemirror.getValue();
        //         _this.admin.mapsvgCss = css;
        //         _this.admin.mapsvgCssChanged = true;
        //         _this.mapsvg.setCss(css);
        //     };
        //     this.editors.css.on('change',setCss);
        //
        //
        // }
        if(this.html){
            var txt = this.views.result.find('textarea')[0];
            this.editor = CodeMirror.fromTextArea(txt, {mode: {name: "handlebars", base: "text/html"}, matchBrackets: true, lineNumbers: true});
            if(_this.formBuilder.admin)
                this.editor.on('change', this.setTextareaValue);
        }

        if(this.type=='image'){
            this.images = this.value || [];
            this.redrawImages();
        }

        if(this.type=='marker' || this.type=='location'){
            this.templates.marker = Handlebars.compile($('#mapsvg-data-tmpl-marker').html());
            this.location && this.location.marker && _this.renderMarker();
        }
        this.setEventHandlers();
    }
    FormElement.prototype.setTextareaValue = function(codemirror, changeobj){
        var handler =  codemirror.getValue();
        var textarea = $(codemirror.getTextArea());
        textarea.val(handler).trigger('change');
    };


    FormElement.prototype.setEventHandlers = function() {
        var _this = this;

        if (this.formBuilder.editMode) {
            this.views.result.on('click', function () {
                _this.formBuilder.edit(_this);
            });
        } else  {
            if(this.type == 'image'){
                var imageDOM = _this.views.result.find('.mapsvg-data-images');

                // When a file is selected, grab the URL and set it as the text field's value
                this.formBuilder.mediaUploader.on('select', function() {
                    if(_this.formBuilder.mediaUploaderFor !== _this)
                        return false;
                    var attachments = _this.formBuilder.mediaUploader.state().get('selection').toJSON();
                    attachments.forEach(function(img){
                        var image = {sizes: {}};
                        for (var type in img.sizes){
                            image[type] = img.sizes[type].url.replace('http://','//').replace('https://','//');
                            image.sizes[type] = {width: img.sizes[type].width, height: img.sizes[type].height};
                        }
                        if(!image.thumbnail){
                            image.thumbnail = image.full;
                            image.sizes.thumbnail = {width: img.sizes.full.width, height: img.sizes.full.height};
                        }
                        if(!image.medium){
                            image.medium = image.full;
                            image.sizes.medium = {width: img.sizes.full.width, height: img.sizes.full.height};
                        }

                        // image.title = img.title;
                        image.caption = img.caption;
                        image.description = img.description;

                        _this.images.push(image);
                    });
                    _this.redrawImages();
                });
                _this.views.result.on('click','.mapsvg-upload-image', function(e) {
                    e.preventDefault();
                    // Open the uploader dialog
                    _this.formBuilder.mediaUploaderFor = _this;
                    _this.formBuilder.mediaUploader.open();
                });
                _this.views.result.on('click','.mapsvg-image-delete', function(e) {
                    e.preventDefault();
                    $(this).closest('.mapsvg-thumbnail-wrap').remove();
                    _this.images = [];
                    _this.views.result.find('img').each(function(i, image){
                        _this.images.push($(image).data('image'));
                    });
                    _this.views.result.find('input').val(JSON.stringify(_this.images));
                });

                _this.sortable = new Sortable(imageDOM[0], {
                    animation: 150,
                    onStart: function(){
                        _this.elements.containers.form_view.addClass('sorting');
                    },
                    onEnd: function(evt){
                        _this.images = [];
                        _this.views.result.find('img').each(function(i, image){
                            _this.images.push($(image).data('image'));
                        });
                        _this.views.result.find('input').val(JSON.stringify(_this.images));
                    }
                });

            }
            if(this.type == 'post'){

                _this.views.result.find(".mapsvg-find-post").mselect2({
                    placeholder: 'Search post by title',
                    allowClear: true,
                    ajax: {
                        url: ajaxurl+'?action=mapsvg_search_posts&post_type='+_this.post_type,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                query: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;

                            return {
                                results: data,
                                pagination: {
                                    more: false //(params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 1,
                    templateResult: formatRepo, // omitted for brevity, see the source of this page
                    templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
                }).on('select2:select',function(e){
                    _this.post = e.params.data;
                    _this.views.result.find(".mapsvg-post-id").text(_this.post.ID);
                    _this.views.result.find(".mapsvg-post-url").text(_this.post.url).attr('href', _this.post.url);
                    _this.views.result.find('input[name="post_id"]').val(_this.post.ID);
                }).on('change',function(e){
                    if(e.target.value===''){
                        _this.views.result.find(".mapsvg-post-id").text('');
                        _this.views.result.find(".mapsvg-post-url").text('');
                        _this.views.result.find('input[name="post_id"]').val('');
                    }
                });

                function formatRepo (repo) {
                    if (repo.loading) return repo.text;

                    var markup = "<div class='select2-result-repository clearfix'>" +
                        repo.post_title + "</div>";

                    return markup;
                }

                function formatRepoSelection (repo) {
                    return repo.post_title || repo.text;
                }

            }
            if(this.type === 'location'){
                // Google geocoding
                if(_this.formBuilder.mapsvg.isGeo()){

                    var locations = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('formatted_address'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: ajaxurl+'?action=mapsvg_geocoding&address=%QUERY%&language='+this.language,
                            wildcard: '%QUERY%',
                            transform: function(response) {
                                if(response.error_message){
                                    alert(response.error_message);
                                }
                                return response.results;
                            },
                            rateLimitWait: 600
                        }
                    });
                    var thContainer = this.views.result.find('.typeahead');
                    var tH = thContainer.typeahead({
                        minLength: 3
                    }, {
                        name: 'mapsvg-addresses',
                        display: 'formatted_address',
                        source: locations
                    });
                    thContainer.on('typeahead:select',function(ev,item){
                        _this.location && _this.location.marker && _this.deleteMarker();

                        var address = {};
                        address.formatted = item.formatted_address;
                        item.address_components.forEach(function(addr_item){
                            var type = addr_item.types[0];
                            address[type] = addr_item.long_name;
                            if(addr_item.short_name != addr_item.long_name){
                                address[type+'_short'] = addr_item.short_name;
                            }
                        });

                        var locationData = {
                            address: address,
                            lat: item.geometry.location.lat,
                            lng: item.geometry.location.lng,
                            img: _this.formBuilder.mapsvg.getMarkerImage(_this.formBuilder.getData())
                        };

                        _this.location = new MapSVG.Location(locationData, _this.formBuilder.mapsvg);

                        // _this.location

                        _this.formBuilder.location = this.location;

                        var marker = new MapSVG.Marker({
                            location: _this.location,
                            mapsvg: _this.formBuilder.mapsvg
                        });
                        _this.location.marker = marker;

                        // TODO just added
                        _this.formBuilder.mapsvg.markerAdd(_this.location.marker);

                        _this.formBuilder.mapsvg.setEditingMarker(marker);
                        _this.formBuilder.marker = marker.getOptions();
                        _this.renderMarker();

                        var select = _this.formBuilder.view.find('select[name="regions"]');
                        if(_this.formBuilder.mapsvg.getData().options.source.indexOf('/geo-calibrated/usa.svg')!==-1){
                            if(select.length !== 0 && _this.location.address.state_short){
                                select.val(['US-'+_this.location.address.state_short]);
                                select.trigger('change');
                            }
                        } else if(_this.formBuilder.mapsvg.getData().options.source.indexOf('/geo-calibrated/world.svg')!==-1){
                            if(select.length !== 0 && _this.location.address.country_short){
                                select.val([_this.location.address.country_short]);
                                select.trigger('change');
                            }
                        } else {
                            if(select.length !== 0 && _this.location.address.administrative_area_level_1){
                                var regionObject = _this.formBuilder.mapsvg.getData().regions.filter(function(region){
                                    return (
                                        region.title === _this.location.address.administrative_area_level_1
                                        ||
                                        region.title === _this.location.address.administrative_area_level_2
                                        ||
                                        region.id === _this.location.address.country_short+'-'+_this.location.address.administrative_area_level_1_short

                                    );
                                });
                                if(regionObject.length > 0){
                                    regionObject = regionObject[0];
                                    select.val([regionObject.id]);
                                    select.trigger('change');
                                }
                            }
                        }

                        thContainer.typeahead('val', '');
                    });
                }

                _this.views.result.on('click','.mapsvg-marker-image-btn-trigger',function(e){
                    $(this).toggleClass('active');
                    _this.toggleMarkerSelector.call(_this, $(this), e);
                });

                _this.views.result.on('click','.mapsvg-marker-delete',function(e){
                    e.preventDefault();
                    _this.deleteMarker();
                });
            }

            if(this.type === 'select' || this.type === 'radio'){
                var locationField = _this.formBuilder.mapsvg.database.getSchemaField('location');
                if(locationField && locationField.markersByFieldEnabled && locationField.markerField && this.name == locationField.markerField && Object.values(locationField.markersByField).length > 0){
                    _this.views.result.on('change','select',function(e){
                        var src = locationField.markersByField[$(this).val()];
                        if(src){
                            if(_this.formBuilder.marker){
                                var marker = _this.formBuilder.mapsvg.getMarker(_this.formBuilder.marker.id);
                                marker.setImage(src);
                                _this.views.result.closest('.mapsvg-form-builder').find('.mapsvg-marker-image-btn img').attr('src',src);
                            }
                            // _this.markerImageButton.attr('src',src);
                        }
                    });
                }
                // todo check if there's a location field
                // then check if there is a markerField && markersByField array
                // check if there a marker and set its src
                // also when marker is added check the same things and set marker src
            }
            if(this.type === 'distance'){
                // Google geocoding
                if(_this.formBuilder.mapsvg.isGeo()){
                    var locations = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('formatted_address'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: ajaxurl+'?action=mapsvg_geocoding&address='+(this.searchByZip===true?'zip%20':'')+'%QUERY%&language='+this.language+(this.country?'&country='+this.country:''),
                            wildcard: '%QUERY%',
                            transform: function(response) {
                                if(response.error_message){
                                    alert(response.error_message);
                                }
                                return response.results;
                            },
                            rateLimitWait: 600
                        }
                    });

                    var thContainer = this.views.result.find('.typeahead');

                    if(this.searchByZip){
                        // var tH = thContainer.typeahead({minLength: this.zipLength, autoselect: true}, {
                        //     name: 'mapsvg-addresses',
                        //     source: locations,
                        //     display: 'formatted_address',
                        // });
                        this.views.result.find('.mapsvg-distance-fields').addClass('search-by-zip');
                        // thContainer.on('typeahead:asyncreceive',function(ev,query,dataset){
                        //     console.log(ev,query,dataset);
                        // });
                        thContainer.on('change keyup', function(){
                            if($(this).val().length === parseInt(_this.zipLength)){
                                locations.search($(this).val(),null,function(data){
                                    if(data && data[0]){
                                        var latlng = data[0].geometry.location
                                        _this.formBuilder.view.find('[name="distanceLatLng"]').val(latlng.lat+','+latlng.lng).trigger('change');
                                    }
                                });
                            }
                        });


                    } else {
                        var tH = thContainer.typeahead({minLength: 3}, {
                            name: 'mapsvg-addresses',
                            display: 'formatted_address',
                            source: locations,
                            limit: 5
                        });
                        this.views.result.find('.mapsvg-distance-fields').removeClass('search-by-zip');
                    }

                    if(_this.userLocationButton){
                        var userLocationButton = this.views.result.find('.user-location-button');
                        userLocationButton.on('click',function(){
                            _this.formBuilder.mapsvg.showUserLocation(function(location){
                                locations.search(location.lat+','+location.lng,null,function(data){
                                    if(data && data[0]){
                                        thContainer.val(data[0].formatted_address);
                                    }else{
                                        thContainer.val(location.lat+','+location.lng);
                                    }
                                });
                                _this.formBuilder.view.find('[name="distanceLatLng"]').val(location.lat+','+location.lng).trigger('change');
                            });
                        });
                    }

                    thContainer.on('change keyup', function(){
                        if($(this).val()===''){
                            _this.formBuilder.view.find('[name="distanceLatLng"]').val('').trigger('change');
                        }
                    });
                    thContainer.on('typeahead:select',function(ev,item){
                        var address = {};
                        address.formatted = item.formatted_address;
                        var latlng = item.geometry.location;
                        _this.formBuilder.view.find('[name="distanceLatLng"]').val(latlng.lat+','+latlng.lng).trigger('change');
                        thContainer.blur();
                        //thContainer.val(item.address_components[0].long_name);
                    });

                }

                _this.views.result.on('click','.mapsvg-marker-image-btn-trigger',function(e){
                    $(this).toggleClass('active');
                    _this.toggleMarkerSelector.call(_this, $(this), e);
                });

                _this.views.result.on('click','.mapsvg-marker-delete',function(e){
                    e.preventDefault();
                    _this.deleteMarker();
                });
            }
        }
    };


    FormElement.prototype.toggleMarkerSelector = function(jQueryObj, e){
        e.preventDefault();
        var _this = this;
        if(_this.markerSelector && _this.markerSelector.is(':visible')){
            _this.markerSelector.hide();
            return;
        }
        if(_this.markerSelector && _this.markerSelector.not(':visible')){
            _this.markerSelector.show();
            return;
        }

        _this.markerImageButton = jQueryObj.find('img');
        var currentImage = _this.markerImageButton.attr('src');
        var images = MapSVG.markerImages.map(function(image){
            return '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose '+(currentImage==image.url?'active':'')+'"><img src="'+image.url+'" /></button>';
        });

        if(!_this.markerSelector){
            _this.markerSelector = _this.views.result.find('.mapsvg-marker-image-selector');
        }

        // delete previous marker image selector and reset events
        if(_this.markerSelector){
            _this.markerSelector.empty();
        }

        // create new markers image selector
        // _this.markerSelector = jQueryObj;
        _this.formBuilder.markerSelector = _this.markerSelector;

        // attach Marker object to the selector
        if(_this.formBuilder.marker){
            _this.markerSelector.data('marker', _this.formBuilder.marker);
        }else{
            _this.markerSelector.data('marker', null);
        }

        _this.markerSelector.html(images.join(''));

        _this.markerSelector.on('click', '.mapsvg-marker-image-btn-choose',function(e){
            e.preventDefault();
            var src = $(this).find('img').attr('src');

            if(_this.formBuilder.marker){
                var marker = _this.formBuilder.mapsvg.getMarker(_this.formBuilder.marker.id);
                // _this.formBuilder.marker.setImage(src);
                marker.setImage(src);
            }
            _this.markerSelector.hide();
            _this.views.result.find('.mapsvg-marker-image-btn-trigger').toggleClass('active',false);
            _this.markerImageButton.attr('src',src);

            // TODO вот зачем нужен defaultMarkerImage:
            // _this.formBuilder.mapsvg.setDefaultMarkerImage(src);
        });
    };

    FormElement.prototype.toggleMarkerSelectorInLocationEditor = function(jQueryObj, e){
        e.preventDefault();
        var _this = this;
        if(jQueryObj.data('markerSelector') && jQueryObj.data('markerSelector').is(':visible')){
            jQueryObj.data('markerSelector').hide();
            return;
        }
        if(jQueryObj.data('markerSelector') && jQueryObj.data('markerSelector').not(':visible')){
            jQueryObj.data('markerSelector').show();
            return;
        }

        var markerBtn = $(this).closest('td').find('.mapsvg-marker-image-btn-trigger');
        var currentImage = markerBtn.attr('src');
        var images = MapSVG.markerImages.map(function(image){
            return '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose '+(currentImage==image.url?'active':'')+'"><img src="'+image.url+'" /></button>';
        });

        if(!jQueryObj.data('markerSelector')){
            var ms = $('<div class="mapsvg-marker-image-selector"></div>');
            jQueryObj.closest('td').append(ms);
            jQueryObj.data('markerSelector', ms);
        } else {
            jQueryObj.data('markerSelector').empty();
        }

        jQueryObj.data('markerSelector').html(images.join(''));

        jQueryObj.data('markerSelector').on('click', '.mapsvg-marker-image-btn-choose',function(e){
            e.preventDefault();
            var src = $(this).find('img').attr('src');
            jQueryObj.data('markerSelector').hide();
            var td = $(this).closest('td');
            var fieldId = $(this).closest('tr').data('option-id');
            var btn = td.find('.mapsvg-marker-image-btn-trigger');
            btn.toggleClass('active',false);
            btn.find('img').attr('src', src);
            _this.setMarkerByField(fieldId, src);
        });
    };

    FormElement.prototype.setMarkerByField = function(fieldId, markerImg){
        this.markersByField = this.markersByField || {};
        this.markersByField[fieldId] = markerImg;
    };
    FormElement.prototype.deleteMarker = function(){
        var _this = this;

        this.formBuilder.backupData = this.formBuilder.backupData || {};
        this.formBuilder.backupData.location = this.location;
        this.formBuilder.backupData.marker = this.marker;
        this.location = null;
        this.marker = null;

        if(this.formBuilder.marker){
            this.formBuilder.mapsvg.getMarker(this.formBuilder.marker.id).delete();
            _this.formBuilder.mapsvg.editingMarker = null;
            // delete this.formBuilder.marker;
        }
        _this.views.result.find('.mapsvg-new-marker').hide();
        _this.views.result.find('.mapsvg-marker-id').attr('disabled','disabled');
    };
    FormElement.prototype.renderMarker = function(marker){
        var _this = this;
        if(!this.location && !(marker && marker.location)){
            return false;
        }
        if(marker && marker.location){
            this.location = marker.location;
        }
        // _this.views.result.find('.mapsvg-marker-hidden-input').val(JSON.stringify(this.marker));
        _this.views.result.find('.mapsvg-new-marker').show().html( this.templates.marker(this.location) );
        this.location.marker.onChange = function(){
            _this.renderMarker();
        };
    };
    FormElement.prototype.redrawImages = function(){
        var _this = this;
        var imageDOM = _this.views.result.find('.mapsvg-data-images');
        imageDOM.empty();
        this.images && this.images.forEach(function(image){
            var img = $('<img class="mapsvg-data-thumbnail" />').attr('src',image.thumbnail).data('image',image);
            var imgContainer = $('<div class="mapsvg-thumbnail-wrap"></div>').data('image',image);
            imgContainer.append(img);
            imgContainer.append('<i class="fa fa-times  mapsvg-image-delete"></i>');
            imageDOM.append(imgContainer);
        });
        _this.views.result.find('input').val(JSON.stringify(this.images));
    };

    FormElement.prototype.setEditorEventHandlers = function(){
        var _this = this;

        this.views.edit.on('click', 'button.mapsvg-remove', function(){
            _this.views.result.empty().remove();
            _this.views.edit.empty().remove();
            _this.formBuilder.delete(_this);
        }).on('keyup change paste','.mapsvg-edit-status-row input',function(){
            _this.mayBeAddStatusRow();
        }).on('keyup change paste','.mapsvg-edit-distance-row input',function(){
            _this.mayBeAddDistanceRow();
        });


        var imgSelector = $('#marker-file-uploader').closest('.form-group').find('.mapsvg-marker-image-selector');
        var uploadBtn = $('#marker-file-uploader').closest('.btn-file');
        this.views.edit.on('change', '#marker-file-uploader', function(){
            uploadBtn = $(this).closest('.btn-file')._button('loading');
            for(var i = 0; i < this.files.length; i++) {

                var data = new FormData();
                data.append('file', this.files[0]);
                data.append('action', 'mapsvg_marker_upload');
                data.append('_wpnonce', MapSVG.nonce);

                $.ajax({
                    url:  ajaxurl,
                    type: "POST",
                    data: data,
                    processData: false,
                    contentType: false
                }).done(function(resp){
                    resp = JSON.parse(resp);
                    if(resp.error){
                        alert(resp.error);
                    }else{
                        var newMarker = '<button class="btn btn-default mapsvg-marker-image-btn mapsvg-marker-image-btn-choose">'
                            +'<img src="'+resp.url+'" />'
                            +'</button>';
                        $(newMarker).appendTo(imgSelector);
                        MapSVG.markerImages.push(resp);
                    }
                }).always(function(){
                    uploadBtn._button('reset');
                });
            }
        });

        this.views.edit.on('change', 'select[name="markerField"]',function(){
            var fieldName = $(this).val();
            _this.fillMarkersByFieldOptions(fieldName);
        });
        _this.views.edit.on('click','.mapsvg-marker-image-btn-trigger',function(e){
            $(this).toggleClass('active');
            _this.toggleMarkerSelectorInLocationEditor.call(_this, $(this), e);
        });


        this.views.edit.on('click', '.mapsvg-filter-insert-options',function(){
            var objType = _this.parameterName.split('.')[0];
            var fieldName   = _this.parameterName.split('.')[1];
            var field;
            if(objType == 'Object'){
                field = _this.formBuilder.mapsvg.database.getSchemaField(fieldName);
            }else{
                if(fieldName == 'id'){
                    field = {options: _this.formBuilder.mapsvg.getData().regions.map(function(r){ return {label: r.id, value: r.id} })};
                }else if(fieldName == 'region_title'){
                    field = {options: _this.formBuilder.mapsvg.getData().regions.map(function(r){ return {label: r.title, value: r.title} })};
                } else {
                    field = _this.formBuilder.mapsvg.regionsDatabase.getSchemaField(fieldName);
                }
            }
            if(field && field.options){
                var options;
                if(fieldName == 'regions') {
                    if(field.options[0].title && field.options[0].title.length)
                        field.options.sort(function(a, b) {
                            if (a.title < b.title)
                                return -1;
                            if (a.title > b.title)
                                return 1;
                            return 0;
                        });
                    options = field.options.map(function(o){  return (o.title||o.id)+':'+o.id});
                }else{
                    options = field.options.map(function(o){  return o.label+':'+o.value});
                }
                $(this).closest('.form-group').find('textarea').val(options.join("\n")).trigger('change');
            }
        });

        this.views.edit.on('keyup change paste', 'input, textarea, select', function(){
            var prop = $(this).attr('name');

            var array = $(this).data('array');
            if(_this.type ==='status' && array) {
                var param = $(this).data('param');
                var index = $(this).closest('tr').index();
                _this.options[index] = _this.options[index] || {label: '', value: '', color: '', disabled: false};
                _this.options[index][param] = $(this).is(':checkbox') ? $(this).prop('checked') : $(this).val();
                _this.redraw();
            }else if(_this.type ==='distance' && array){
                var param = $(this).data('param');
                var index = $(this).closest('tr').index();
                if(!_this.options[index]){
                    _this.options[index] = {value: '',default: false};
                }
                if(param === 'default'){
                    _this.options.forEach(function(option, i){
                        option.default = false;
                    });
                    _this.options[index].default = $(this).prop('checked');
                } else{
                    _this.options[index].value = $(this).val();
                }
                _this.redraw();
            }else if(prop == 'label' || prop == 'name') {
                return false;
            }else{
                var value;
                value = ($(this).attr('type') == 'checkbox') ? $(this).prop('checked') : $(this).val();
                if($(this).attr('type') == 'radio'){
                    var name = $(this).attr('name');
                    value = $('input[name="'+name+'"]:checked').val();
                }
                _this.update(prop,value);
            }
        });
        this.views.edit.on('keyup change paste', 'input[name="label"]', function(){
            if(!_this.nameChanged){
                // _this.update('name',str);
                _this.label = $(this).val();
                if(_this.type != 'region' && _this.type != 'location'){
                    var str = $(this).val();
                    str = str.toLowerCase().replace(/ /g, '_').replace(/\W/g, '');
                    _this.views.edit.find('input[name="name"]').val(str);
                    _this.name = str;
                }
                _this.views.result.find('label').first().html(_this.label);
                if(!_this.formBuilder.filtersMode){
                    _this.views.result.find('label').first().append('<div class="field-name">'+_this.name+'</div>');
                }

            }
        });
        this.views.edit.on('keyup change paste', 'input[name="name"]', function(){
            if(this.value){
                if (this.value.match(/[^a-zA-Z0-9_]/g)) {
                    this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
                    $(this).trigger('change');
                }
                if (this.value[0].match(/[^a-zA-Z_]/g)) {
                    this.value = this.value[0].replace(/[^a-zA-Z_]/g, '')+this.value.slice(1);
                    $(this).trigger('change');
                }
            }
            if(_this.type != 'region')
                _this.name = this.value;
            _this.views.result.find('label').html(_this.label+'<div class="field-name">'+_this.name+'</div>');
            _this.nameChanged = true;
        });
    };
    FormElement.prototype.fillMarkersByFieldOptions = function(fieldName){

        var _this = this;

        var field = _this.formBuilder.mapsvg.database.getSchemaField(fieldName);
        if(field){
            var markerImg = _this.formBuilder.mapsvg.getData().options.defaultMarkerImage;

            var rows = field.options.map(function(option){
                var img = _this.markersByField && _this.markersByField[option.value] ? _this.markersByField[option.value] : markerImg;
                return '<tr data-option-id="'+option.value+'"><td>'+option.label+'</td><td><button class="btn btn-default mapsvg-marker-image-btn-trigger mapsvg-marker-image-btn"><img src="'+img+'" class="new-marker-img" style="margin-right: 4px;"/><span class="caret"></span></button></td></tr>';
            });
            $("#markers-by-field").empty().append(rows);
        }
    };
    FormElement.prototype.getEditor = function(){

        // if(!this.views.edit){
        if(!this.formBuilder.filtersMode){
            this.templates.edit = this.templates.edit || Handlebars.compile($('#mapsvg-data-tmpl-'+this.type+'-control').html());
        }else {
            this.templates.edit = this.templates.edit || Handlebars.compile($('#mapsvg-filters-tmpl-' + this.type + '-control').html());
        }
        this.views.edit = $(this.templates.edit(this.get()));
        // }
        return this.views.edit;
    };
    FormElement.prototype.destroyEditor = function(){
        // this.views.edit.find('select').mselect2('destroy');
        this.views.edit.empty().remove();
    };
    FormElement.prototype.initEditor = function(){
        var _this = this;
        this.views.edit.find('input').first().select();

        if($().mselect2){
            if(this.type !=='distance'){
                this.views.edit.find('select').css({width: '100%', display: 'block'}).mselect2();
            } else {
                this.views.edit.find('select').mselect2();
            }
        }
        if(this.type == 'status'){
            _this.views.edit.find('.cpicker').colorpicker().on('changeColor.colorpicker', function(event){
                var input = $(this).find('input');
                var index = $(this).closest('tr').index();
                if(input.val() == '')
                    $(this).find('i').css({'background-color': ''});
                _this.options[index] = _this.options[index] || {label: '',value: '', color: '', disabled: false};
                _this.options[index]['color'] = input.val();
            });
            _this.mayBeAddStatusRow();
        }
        if(this.type === 'distance'){
            _this.mayBeAddDistanceRow()
        }
        if(this.type === 'location'){
            _this.fillMarkersByFieldOptions(_this.markerField);
        }
        this.views.edit.find('.mapsvg-onoff').bootstrapToggle({
            onstyle: 'default',
            offstyle: 'default'
        });
        this.setEditorEventHandlers();
    };
    FormElement.prototype.mayBeAddStatusRow = function(){
        var _this = this;
        this.templates.editStatusRow = this.templates.editStatusRow || $($('#mapsvg-edit-status-row').html());
        // if there's something in the last status edit field, add +1 status row
        var z = _this.views.edit.find('.mapsvg-edit-status-label:last-child');
        if(z && z.last() && z.last().val() && z.last().val().trim().length){
            var newRow = this.templates.editStatusRow.clone();
            newRow.insertAfter(_this.views.edit.find('.mapsvg-edit-status-row:last-child'));
            newRow.find('.cpicker').colorpicker().on('changeColor.colorpicker', function(event){
                var input = $(this).find('input');
                var index = $(this).closest('tr').index();
                if(input.val() == '')
                    $(this).find('i').css({'background-color': ''});
                _this.options[index] = _this.options[index] || {label: '',value: '', color: '', disabled: false};
                _this.options[index]['color'] = input.val();
            });
        }
        var rows = _this.views.edit.find('.mapsvg-edit-status-row');
        var row1 = rows.eq( rows.length-2 );
        var row2 = rows.eq( rows.length-1 );

        if( row1.length && row2.length &&
            !(row1.find('input:eq(0)').val().trim() || row1.find('input:eq(1)').val().trim() || row1.find('input:eq(2)').val().trim())
            &&
            !(row2.find('input:eq(0)').val().trim() || row2.find('input:eq(1)').val().trim() || row2.find('input:eq(2)').val().trim())
        ){
            row2.remove();
        }
    };
    FormElement.prototype.mayBeAddDistanceRow = function() {
        var _this = this;
        this.templates.editDistanceRow = this.templates.editDistanceRow || $($('#mapsvg-edit-distance-row').html());
        // if there's something in the last status edit field, add +1 status row
        var z = _this.views.edit.find('.mapsvg-edit-distance-row:last-child input');
        if(z && z.last() && z.last().val() && z.last().val().trim().length){
            var newRow = this.templates.editDistanceRow.clone();
            newRow.insertAfter(_this.views.edit.find('.mapsvg-edit-distance-row:last-child'));
        }
        var rows = _this.views.edit.find('.mapsvg-edit-distance-row');
        var row1 = rows.eq( rows.length-2 );
        var row2 = rows.eq( rows.length-1 );

        if( row1.length && row2.length && !row1.find('input:eq(0)').val().trim() && !row2.find('input:eq(0)').val().trim()) {
            row2.remove();
        }
    };
    FormElement.prototype.getSchema = function() {

        var _this = this;

        var data = {
            type: this.type,
            db_type: this.db_type,
            label: this.label,
            name: this.name,
            value: this.value,
            searchable: MapSVG.parseBoolean(this.searchable)

        };

        if(this.type == 'select'){
            data.multiselect = MapSVG.parseBoolean(this.multiselect);
            if(data.multiselect)
                data.db_type = 'text';
            data.optionsGrouped = this.optionsGrouped;
        }

        if (this.options) {
            var opts = $.extend(true, {},{options: this.options});
            data.options = opts.options;
            data.optionsDict = {};
            if (data.type == 'region') {
                data.options.forEach(function (option) {
                    data.optionsDict[option.id] = option.title || option.id;
                });
            } else if (_this.type == 'status'){
                data.options.forEach(function (option, index) {
                    if(data.options[index].value===''){
                        data.options.splice(index,1);
                    }else{
                        data.options[index].disabled = MapSVG.parseBoolean(data.options[index].disabled);
                        data.optionsDict[option.value] = option;
                    }
                });
            } else if (_this.type == 'distance'){
                data.options.forEach(function (option, index) {
                    if(data.options[index].value===''){
                        data.options.splice(index,1);
                    }else{
                        data.options[index].default = MapSVG.parseBoolean(data.options[index].default);
                        // data.optionsDict[option.value] = option;
                    }
                });
            } else {
                data.options.forEach(function (option) {
                    data.optionsDict[option.value] = _this.type == 'status' ? option : option.label;
                });
            }
        }

        if (this.help) {
            data.help = this.help;
        }
        if (this.button_text) {
            data.button_text = this.button_text;
        }
        if (this.type == 'post') {
            data.post_type = this.post_type;
            data.add_fields = this.add_fields;
        }
        if (this.type == 'date') {
            data.language = this.language;
        }
        if (this.type == 'location') {
            data.language = this.language;
            data.markersByField = this.markersByField;
            data.markerField = this.markerField;
            data.markersByFieldEnabled = MapSVG.parseBoolean(this.markersByFieldEnabled);
        }
        if(this.type == 'textarea'){
            data.autobr = this.autobr;
            data.html = this.html;
        }
        if(this.type == 'text'){
            data.searchType = this.searchType;
        }
        if(this.type == 'modal') {
            data.showButtonText = this.showButtonText;
        }

        if(this.type == 'distance'){
            data.distanceControl = this.distanceControl;
            data.distanceUnits = this.distanceUnits;
            data.distanceUnitsLabel = this.distanceUnitsLabel;
            data.fromLabel = this.fromLabel;
            data.addressField = this.addressField;
            data.addressFieldPlaceholder = this.addressFieldPlaceholder;
            data.userLocationButton = this.userLocationButton;
            data.placeholder = this.placeholder;
            data.language = this.language;
            data.country = this.country;
            data.searchByZip = this.searchByZip;
            data.zipLength = this.zipLength;
            data.userLocationButton = MapSVG.parseBoolean(this.userLocationButton);
            if(data.distanceControl === 'none'){
                data.distanceDefault = data.options.filter(function(o){ return o.default; })[0].value;
            }
        }
        if(this.type == 'search'){
            data.searchFallback = MapSVG.parseBoolean(this.searchFallback);
            data.placeholder = this.placeholder;
            data.noResultsText = this.noResultsText;
            data.width = this.width;
        }

        if(this.checkboxLabel){
            data.checkboxLabel = this.checkboxLabel;
        }
        if(this.checkboxValue){
            data.checkboxValue = this.checkboxValue;
        }
        if(this.formBuilder.filtersMode){
            data.parameterName = this.parameterName;
            data.parameterNameShort = this.parameterName.split('.')[1];
            data.placeholder = this.placeholder;
        }
        data.visible = this.visible === undefined ? true : this.visible;
        return data;
    };
    FormElement.prototype.get = function(){

        var data = this.getSchema();
        // Add namespace to names
        data._name = data.name;
        if(this.formBuilder.namespace){
            data.name = this.name.split('[')[0];
            var suffix = this.name.split('[')[1] || '';
            if(suffix)
                suffix = '['+suffix;
            data.name = this.formBuilder.namespace+'['+data.name+']'+suffix
        }
        if(this.type == 'post'){
            if(this.formBuilder.admin)
                data.post_types = this.formBuilder.admin.getPostTypes();
            data.post_type = this.post_type;
            data.post = this.post;
            data.add_fields = this.add_fields || 0;
        }
        if(this.type == 'date'){
            if(this.formBuilder.admin)
                data.languages = this.languages;
            data.language = this.language;
        }
        if(this.type == 'distance'){
            if(this.formBuilder.admin) {
                data.languages = this.languages;
                data.countries= this.countries;
            }
            data.language = this.language;
            data.country = this.country;
            data.searchByZip = this.searchByZip;
            data.zipLength = this.zipLength;
            data.userLocationButton = MapSVG.parseBoolean(this.userLocationButton);
        }

        if(this.type == 'location'){
            if(this.formBuilder.admin){
                data.languages = this.languages;
                data.markerImages = MapSVG.markerImages;
                data.markersByField = this.markersByField;
                data.markerField = this.markerField;
                data.markersByFieldEnabled = MapSVG.parseBoolean(this.markersByFieldEnabled);
                var _this = this;
                data.markerImages.forEach(function(m){
                    if(m.url === _this.formBuilder.mapsvg.getData().options.defaultMarkerImage){
                        m.default = true;
                    } else {
                        m.default = false;
                    }
                })

            }
            data.language = this.language;

            if(this.location){
                data.location = this.location;
                if(this.location.marker){
                    data.location.img = (this.location.marker.src.indexOf(MapSVG.urls.uploads)===0?'uploads/':'') + (this.location.marker.src.split('/').pop());
                }
            }
        }
        if(this.type == 'textarea') {
            data.html = this.html;
        }
        data.databaseFields = this.databaseFields;
        data.databaseFieldsFilterableShort = this.databaseFieldsFilterableShort;
        data.regionFields = this.regionFields;
        data.placeholder = this.placeholder;
        return data;
    };
    FormElement.prototype.update = function(prop, value){
        var _this = this;
        if(prop=='options'){
            var options = [];
            value = value.split("\n").forEach(function(row){
                row = row.trim().split(':');
                if(_this.type=='checkbox' && row.length == 3){
                    options.push({
                        label: row[0],
                        name: row[1],
                        value: row[2]
                    });
                }else if((_this.type=='radio' || _this.type=='select'|| _this.type=='checkboxes') && row.length==2){
                    options.push({
                        label: row[0],
                        value: row[1]
                    });
                }
            });
            this.options = options;
        }else{
            this[prop] = value;
        }
        if(prop == 'parameterName'){
            this.views.edit.find('.mapsvg-filter-param-name').text(value);
        }
        this.redraw();
    };
    FormElement.prototype.redraw = function(){
        var _this = this;
        var newView = $(this.templates.result(this.get()));
        this.views.result.html(newView.html());
        if($().mselect2){
            if(this.type!=='distance'){
                this.views.result.find('select').css({width: '100%', display: 'block'})
                    .mselect2()
                    .on('select2:focus',function(){
                        $(this).mselect2('open');
                    });
            }else{
                this.views.result.find('select').mselect2().on('select2:focus',function(){
                    $(this).mselect2('open');
                });
            }
        }
        if($().colorpicker) {
            this.views.edit && this.views.edit.find('.cpicker').colorpicker().on('changeColor.colorpicker', function (event) {
                var input = $(this).find('input');
                if (input.val() == '')
                    $(this).find('i').css({'background-color': ''});
            });
        }

    };

    var FormBuilder = function(options) {

        // schema, editMode, mapsvg, mediaUploader, data, admin, namespace

        var _this = this;

        // options
        this.container     = options.container;
        this.namespace     = options.namespace;
        this.mediaUploader = options.mediaUploader;
        this.schema        = options.schema || [];
        this.editMode      = options.editMode == undefined ? false : options.editMode;
        this.filtersMode   = options.filtersMode == undefined ? false : options.filtersMode;
        this.filtersHide   = options.filtersHide == undefined ? false : options.filtersHide;
        this.modal         = options.modal == undefined ? false : options.modal;
        this.admin         = options.admin;
        this.mapsvg        = options.mapsvg;
        this.data          = options.data || {};
        this.eventHandlers = options.events;
        this.template      = 'form-builder';
        this.closeOnSave   = options.closeOnSave !== true ? false : true;
        this.newRecord     = options.newRecord !== true ? false : true;
        // this.id            = this.data.id || null;


        this.types         = options.types || ['text', 'textarea', 'checkbox', 'radio', 'select', 'image', 'region', 'location', 'post', 'date'];

        this.templates = {};
        this.elements = {};
        this.view = $('<div />').addClass('mapsvg-form-builder');
        if(this.editMode)
            this.view.addClass('full-flex');
        // this.eventHandlers = {};

        this.formControls = [];

        if(!MapSVG.templatesLoaded[this.template]){
            $.get(MapSVG.urls.templates + _this.template+'.html?'+Math.random(), function (data) {
                $(data).appendTo('body');
                MapSVG.templatesLoaded[_this.template] = true;
                Handlebars.registerPartial('dataMarkerPartial', $('#mapsvg-data-tmpl-marker').html());
                if(_this.editMode){
                    Handlebars.registerPartial('markerByFieldPartial', $('#mapsvg-markers-by-field-tmpl-partial').html());
                }
                _this.init();
            });
        }else{
            this.init();
        }
    };

    FormBuilder.prototype.init = function(){

        var _this = this;
        MapSVG.formBuilder = this;

        if(_this.editMode){
            var templateUI = Handlebars.compile($('#mapsvg-form-editor-tmpl-ui').html());
            _this.view.append( templateUI({types: this.types}) );
            _this.view.addClass('edit');
        }else{
            var form = $('<div class="mapsvg-data-form-view"></div>');
            _this.view.append(form);
            if(!this.filtersMode){
                form.addClass('form-horizontal');
            }
        }

        _this.elements = {
            buttons: {
                text: _this.view.find('#mapsvg-data-btn-text'),
                textarea: _this.view.find('#mapsvg-data-btn-textarea'),
                checkbox: _this.view.find('#mapsvg-data-btn-checkbox'),
                radio: _this.view.find('#mapsvg-data-btn-radio'),
                select: _this.view.find('#mapsvg-data-btn-select'),
                image: _this.view.find('#mapsvg-data-btn-image'),
                region: _this.view.find('#mapsvg-data-btn-region'),
                marker: _this.view.find('#mapsvg-data-btn-marker'),
                saveSchema: _this.view.find('#mapsvg-data-btn-save-schema')
            },
            containers: {
                buttons_add: _this.view.find('#mapsvg-data-buttons-add'),
                form_view: _this.view.find('.mapsvg-data-form-view'),
                form_edit: _this.view.find('#mapsvg-data-form-edit')
            }
        };

        _this.redraw();
    };

    FormBuilder.prototype.viewDidLoad = function(){
        _this.formControls.forEach(function(control) {
            if(control.html){
                var txt = control.views.result.find('textarea')[0];
                control.htmlEditor = CodeMirror.fromTextArea(txt, {mode: {name: "handlebars", base: "text/html"}, matchBrackets: true, lineNumbers: true});
            }
        });
    };

    FormBuilder.prototype.setEventHandlers = function(){

        var _this = this;

        $(window).off('keydown.form.mapsvg').on('keydown.form.mapsvg', function(e) {
            if(MapSVG.formBuilder){
                if((e.metaKey||e.ctrlKey) && e.keyCode == 13)
                    MapSVG.formBuilder.save();
                else if(e.keyCode == 27)
                    MapSVG.formBuilder.close();
            }
        });


        if(this.editMode){
            this.view.on('click','.mapsvg-marker-image-selector button',function(e){
                e.preventDefault();
                var src = $(this).find('img').attr('src');
                $(this).parent().find('button').removeClass('active');
                $(this).addClass('active');
                _this.mapsvg.setDefaultMarkerImage(src);
            });
            this.view.on('click','#mapsvg-data-buttons-add button',function(e){
                e.preventDefault();
                var type = $(this).data('create');
                _this.add({type: type});
            });
            this.view.on('click','#mapsvg-data-btn-save-schema', function(e){
                e.preventDefault();
                var fields = _this.getSchema();

                var counts = {};
                _this.formControls.forEach(function(elem) { counts[elem.name] = (counts[elem.name] || 0) +1; });

                _this.elements.containers.form_view.find('.form-group').removeClass('has-error');
                var errors = [];

                var reservedFields = ['id','lat','lon','lng','location','location_lat','location_lon','location_lng','location_address','location_img','marker','marker_id','regions','region_id','post_id', 'post','post_title','post_url', 'keywords','status'];
                var reservedFieldsToTypes = {'regions':'region','status':'status','post_id':'post','marker':'marker','location':'location'};

                var errUnique, errEmpty;

                _this.formControls.forEach(function(formElement, index){
                    var err = false;

                    // If that's not Form Builder for Filters (when there is no "name" parameter)
                    // we should check if names are non-empty and unique
                    if(!_this.filtersMode){
                        if(counts[formElement.name] > 1){
                            if(!errUnique){
                                errUnique = 'Field names should be unique';
                                errors.push(errUnique);
                                err = true;
                            }
                        }else if(formElement.name.length===0){
                            if(!errEmpty){
                                errEmpty = 'Field name can\'t be empty';
                                errors.push(errEmpty);
                                err = true;
                            }
                        }else if(reservedFields.indexOf(formElement.name)!=-1){
                            // if reserved field name is for proper type of object then it's OK
                            if(!reservedFieldsToTypes[formElement.name] || (reservedFieldsToTypes[formElement.name] && reservedFieldsToTypes[formElement.name]!=formElement.type)){
                                var msg = 'Field name "'+formElement.name+'" is reserved, please set another name';
                                errors.push(msg);
                                err = true;
                            }
                        }
                    }


                    if(formElement.options && formElement.type != 'region' && formElement.type != 'marker'){
                        var vals = _.pluck(formElement.options, 'value');
                        if(vals.length != _.uniq(vals).length){
                            errors.push('Check "Options" list - values should not repeat');
                            err = true;
                        }

                    }

                    err && formElement.views.result.addClass('has-error');
                });

                if(errors.length == 0)
                    _this.eventHandlers.saveSchema && _this.eventHandlers.saveSchema(fields);
                else
                    $.growl.error({title: "Errors", message: errors.join('<br />')});

            });
            setTimeout(function(){
                var el = _this.elements.containers.form_view[0];
                _this.sortable = new Sortable(el, {
                    animation: 150,
                    onStart: function(){
                        _this.elements.containers.form_view.addClass('sorting');
                    },
                    onEnd: function(){
                        setTimeout(function(){
                            _this.elements.containers.form_view.removeClass('sorting');
                            _this.formControls = [];
                            $(el).find('.form-group').each(function(index, elem){
                                _this.formControls.push($(elem).data('formElement'));
                            });
                        },500);
                    }
                });
            },1000);
        }else{
            // Save
            _this.view.on('click','button.btn-save',function(e){
                e.preventDefault();
                _this.save();
            });
            // Close
            _this.view.on('click','button.btn-close',function(e){
                e.preventDefault();
                _this.close();
            });
        }
        new MapSVG.ResizeSensor(this.view[0], function(){
            _this.scrollApi && _this.scrollApi.reinitialise();
        });


    };
    FormBuilder.prototype.save = function(){

        var _this = this;

        if(_this.marker){
            // var m = _this.mapsvg.getEditingMarker();
            _this.marker.onChange = null;
            // _this.marker = m.getOptions();
            _this.mapsvg.unsetEditingMarker();
        }
        var data = _this.getData();
        _this.saved = true;

        _this.eventHandlers.save && _this.eventHandlers.save.call(_this, data);
    };
    FormBuilder.prototype.getData = function(){

        var _this = this;
        var data = _this.toJSON(true);

        _this.formControls.forEach(function(control){

            // Get Image field data
            if(control.type == 'image'){
                data[control.name] = [];
                if(control.images && control.images.length && control.images[0]!=null){
                    var newList = [];
                    control.views.result.find('.mapsvg-thumbnail-wrap').each(function(index, el){
                        var imageData = $(el).data('image');
                        newList.push(imageData);
                    });
                    control.images = newList;
                    data[control.name] = data[control.name].concat(control.images);
                }
            }

            // if(control.type == 'marker'){
            //     if(_this.marker)
            //         data[control.name] = _this.marker;
            //     else
            //         data[control.name] = '';
            // }

            if(control.type == 'location'){
                if(control.location){
                    // data[control.name] = $.extend(true, {},control.location);
                    data[control.name] = control.location; //$.extend(true, {},control.location);
                    // delete data[control.name].marker;
                } else {
                    data[control.name] = '';
                }
            }
            if(control.type == 'post'){
                data.post = control.post;
            }
            if(control.type == 'region') {
                if (data.regions && typeof data.regions == 'object' && data.regions.length) {
                    data.regions = data.regions.map(function (region_id) {
                        return {id: region_id, title: _this.mapsvg.getRegion(region_id).title}
                    });
                } else {
                    data.regions = '';
                }
            }
            if(control.type == 'select' && control.multiselect && data[control.name] && data[control.name].length) {
                data[control.name] = data[control.name].map(function(value){
                    return {value: value, label: control.optionsDict[value]};
                });
            }
        });

        if(_this.data.id != undefined){
            data.id = _this.data.id;
        }

        delete data.marker_id;

        return data;
    };
    FormBuilder.prototype.getDataJSON = function(){
        var _this = this;
        var data = _this.toJSON(true);
        _this.formControls.forEach(function(control){
            if(control.type == 'image'){
                data[control.name] = [];
                if(control.images && control.images.length && control.images[0]!=null){
                    var newList = [];
                    _this.view.find('.mapsvg-thumbnail-wrap').each(function(index, el){
                        var imageData = $(el).data('image');
                        newList.push(imageData);
                    });
                    control.images = newList;
                    data[control.name] = data[control.name].concat(control.images);
                }
            }
            // if(control.type == 'marker'){
            //     data[control.name] = control.marker.getOptions();
            // }
            if(control.type == 'location'){
                data[control.name] = control.location;
            }
        });

        // if(_this.marker){
        //     var new_id = _this.view.find('.mapsvg-marker-id').val();
        //     var check = {};
        //     if(_this.markerIdChanged)
        //         check = _this.mapsvg.checkId(new_id);
        //     if(check.error){
        //         $().message('Please change Marker ID.<br /> '+check.error+'.');
        //         _this.view.find('.mapsvg-marker-id').focus().select();
        //         return false;
        //     }else{
        //         _this.marker.setId(new_id);
        //         _this.mapsvg.updateMarkersDict();
        //     }
        // }
        if(_this.data.id != undefined){
            data.id = _this.data.id;
        }
        return data;
    };
    FormBuilder.prototype.redraw = function(formElement){
        var _this = this;

        delete _this.marker;

        _this.container.empty();
        _this.elements.containers.form_view.empty();
        _this.formControls = [];

        if(_this.data && _this.data.id){
            _this.add({type: 'id', label: 'ID', name: 'id', value: _this.data.id});
        }

        if(_this.data && _this.data._title){
            _this.add({type: 'title', label: 'Title', name: 'title', value: _this.data._title});
        }


        _this.schema && _this.schema.length && _this.schema.forEach(function(elem){

            if(_this.admin && _this.admin.isMetabox && elem.type == 'post'){

            }else{
                if(_this.filtersMode){
                    if(elem.type=='distance'){
                        elem.value = _this.data.distance ? _this.data.distance : elem.value!==undefined?elem.value:null ;
                    } else {
                        elem.value = _this.data[elem.parameterNameShort];
                    }
                } else {
                    elem.value = _this.data ? _this.data[elem.name] : elem.value!==undefined?elem.value:null ;
                }

                if(elem.type=='location' && !_this.editMode){

                    // add Marker Object into formElement
                    if(elem.value && elem.value.marker && elem.value.marker.id){
                        _this.marker = elem.value.marker.getOptions();
                        _this.mapsvg.setEditingMarker(elem.value.marker);
                    }
                    _this.admin && _this.admin.setMode && _this.admin.setMode('editMarkers');
                    _this.admin && _this.admin.enableMarkersMode(true);

                    _this.mapsvg.setMarkerEditHandler(function(){
                        _this.marker = this.getOptions();
                        _this.mapsvg.setEditingMarker(this);
                        var object = _this.getData();
                        var img = _this.mapsvg.getMarkerImage(object);
                        var marker = this;
                        marker.setImage(img);
                        // setTimeout(function(){
                        //     marker.setImage(img);
                        // },2000);
                        _this.locationFormElement && _this.locationFormElement.renderMarker(_this.mapsvg.getMarker(_this.marker.id));
                    });

                }else if(elem.type == 'post'){
                    elem.post = _this.data['post'];
                }

                if(_this.filtersMode){
                    if(!_this.filtersHide || (_this.filtersHide && (_this.modal && elem.type !== 'search') || (!_this.modal && elem.type === 'search'))) {
                        var formElement = _this.add(elem);
                    }
                } else {
                    var formElement = _this.add(elem);
                }
                if(elem.type=='location'){
                    _this.locationFormElement = formElement;
                }
            }
        });

        if(!_this.editMode){
            if(this.schema.length == 0){
                _this.add({type: 'empty'});
            }else{
                if(_this.admin && !_this.admin.isMetabox){
                    _this.add({type: 'save'});
                }
                // if(_this.filtersMode){
                //     _this.add({type: 'ok'});
                // }
            }
        }

        // If part of filters is hidden in a modal, and what is showing now is NOT a modal,
        // then add a "Show filters" button that opens a modal with remaining filers.
        if(_this.filtersMode && _this.filtersHide && !_this.modal){
            this.showFiltersButton = _this.add({type: 'modal', 'buttonText': _this.mapsvg.getData().options.filters.showButtonText});
        }

        if(!_this.editMode && !_this.filtersMode){

            var nano = $('<div class="nano"></div>');
            var nanoContent = $('<div class="nano-content"></div>');
            nano.append(nanoContent);
            nanoContent.html(this.view);
            _this.container.html(nano);
            nano.jScrollPane();
            _this.scrollApi = nano.data('jsp');
        }else{
            _this.container.html(this.view);
        }


        _this.eventHandlers.load && _this.eventHandlers.load(_this);

        if (!this.editMode && !_this.filtersMode)
            this.view.find('input:visible,textarea:visible').not('.tt-hint').first().focus();

        var cm  = this.container.find('.CodeMirror');

        cm.each(function(index, el){
            el && el.CodeMirror.refresh();
        });
        _this.setEventHandlers();
        _this.eventHandlers.init && _this.eventHandlers.init(_this.data);

    };
    FormBuilder.prototype.delete = function(formElement){
        var _this = this;
        _this.formControls.forEach(function(fc, index){
            if(fc === formElement){
                _this.formControls.splice(index,1);
                _this.structureChanged = true;
            }
        });
    };
    FormBuilder.prototype.add = function(params){
        var _this = this;

        if(['region','marker','post','status','distance','location','search'].indexOf(params.type)!=-1){
            var repeat = false;
            _this.formControls.forEach(function(control){
                if(control.type == params.type)
                    repeat = true;
            });
            if (repeat) {
                $.growl.error({title: 'Error', message: 'You can add only 1 "'+MapSVG.ucfirst(params.type)+'" field'});

                return;
            }
        }

        if(params.type == 'date'){
            MapSVG.datepicker = MapSVG.datepicker || {};
            // if(!MapSVG.datepicker[params.language]){
            //     $.get(mapsvg_paths.root+'js/datepicker-locales/bootstrap-datepicker.'+(params.language||'en-GB')+'.min.js', function(data){
            //        eval(data);
            //     });
            //     // var script = document.createElement('script');
            //     // script.src = mapsvg_paths.root+'js/datepicker-locales/bootstrap-datepicker.'+params.language+'.min.js';
            //     MapSVG.datepicker[params.language] = true;
            // }
        }
        var formElement = new FormElement(params, _this);
        _this.formControls.push(formElement);
        _this.elements.containers.form_view.append(formElement.views.result);
        if(this.editMode)
            this.edit(formElement);
        return formElement;
    };
    FormBuilder.prototype.edit = function(formElement){
        var _this = this;

        // destroy previous editor
        _this.currentlyEditing && _this.currentlyEditing.destroyEditor();

        // create new  editor
        _this.elements.containers.form_edit.append(formElement.getEditor());
        // setTimeout(function(){
        formElement.initEditor();
        _this.currentlyEditing = formElement;
        _this.elements.containers.form_view.find('.form-group.active').removeClass('active');
        formElement.views.result.addClass('active');
        // }, 500);
    };
    FormBuilder.prototype.get = function(){
        return this.formControls.map(function(c){
            return c.get();
        });
    };
    FormBuilder.prototype.getSchema = function(){
        return this.formControls.map(function(c){
            return c.getSchema();
        });
    };
    FormBuilder.prototype.close = function(){
        var _this = this;

        // $('body').off('keydown.mapsvg');

        if(!_this.saved){
            if(_this.data.id == undefined && _this.marker){
                var marker = _this.mapsvg.getMarker(_this.marker.id);
                marker.onChange = null;
                marker.delete();
                delete _this.marker;
            }

            if(this.backupData){
                if(this.backupData.location){
                    this.location = this.backupData.location;
                    _this.mapsvg.markerAdd(this.location.marker);
                    _this.mapsvg.setEditingMarker(this.location.marker);
                }
                // if(this.backupData.marker){
                //     this.marker = this.backupData.marker;
                //     var marker = new MapSVG.Marker({location: _this.location, mapsvg: _this.mapsvg});
                // }
            }

            if(_this.marker){

                var editingMarker = _this.mapsvg.getEditingMarker();

                if (editingMarker){
                    // editingMarker.update(_this.marker);
                    editingMarker.setImage(_this.marker.src);
                    editingMarker.setXy([_this.marker.x,_this.marker.y]);
                    _this.mapsvg.unsetEditingMarker();
                }
            }
        }

        _this.markerSelector && _this.markerSelector.popover('destroy');
        if($().mselect2){
            var sel = _this.view.find('.mapsvg-select2');
            if(sel.length){
                sel.mselect2('destroy');
            }
        }

        var cm = _this.view.find('.CodeMirror');
        if(cm.length){
            cm.empty().remove();
        }



        _this.admin && _this.admin.enableMarkersMode(false);
        MapSVG.formBuilder = null;
        _this.eventHandlers.close && _this.eventHandlers.close();

    };
    FormBuilder.prototype.destroy = function(){
        this.view.empty().remove();
        this.sortable = null;
    };

    FormBuilder.prototype.on = function(event, handler){
        this.eventHandlers[event] = handler;
    };
    FormBuilder.prototype.toJSON = function(addEmpty) {

        var obj = {};

        function add(obj, name, value){
            if(!addEmpty && !value)
                return false;
            if(name.length == 1) {
                obj[name[0]] = value;
            }else{
                if(obj[name[0]] == null){
                    if(name[1]===''){
                        obj[name[0]] = [];
                    }else{
                        obj[name[0]] = {};
                    }
                }

                if(obj[name[0]].length !== undefined){
                    obj[name[0]].push(value);
                } else {
                    add(obj[name[0]], name.slice(1), value);
                }

            }
        }

        this.elements.containers.form_view.find('input, textarea, select').each(function(){
            if( !$(this).data('skip')
                &&
                !$(this).prop('disabled')
                &&
                $(this).attr('name')
                &&
                !( !addEmpty && $(this).attr('type')=='checkbox' && $(this).attr('checked')==undefined)
                &&
                !( $(this).attr('type')=='radio' && !$(this).is(':checked')))
            {
                var value;
                if($(this).attr('type')=='checkbox'){
                    value = $(this).prop('checked');
                }else{
                    value = $(this).val();
                }
                add(obj, $(this).attr('name').replace(/]/g, '').split('['), value);
            }
        });

        return obj;
    };
    FormBuilder.prototype.getRegionsList = function(){
        return this.mapsvg.getData().regions.map(function(region){
            return {id: region.id, title: region.title};
        });
    };
    FormBuilder.prototype.getMarkersList = function(){
        return this.mapsvg.getData().markers.map(function(marker){
            return {id: marker.id};
        });
    };

    function FormElementEditor($Object){
        this.form = $Object;
    }

    // window.FormBuilder = FormBuilder;
    MapSVG.FormBuilder = FormBuilder;


})(jQuery, MapSVG, window);