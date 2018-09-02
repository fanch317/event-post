/* global ol, eventpost_params */
jQuery( window ).load(function() {

    if(typeof ol !== 'undefined'){
        ep_maps = [];
        ep_vectorSources = [];
        ep_pop_elements = [];
        ep_popups = [];
        ep_features = [];
        ep_icons = [];
        ep_proj_source = new ol.proj.Projection({code: 'EPSG:4326'});
        ep_proj_destination = new ol.proj.Projection({code: 'EPSG:900913'});
        ep_interactions = eventpost_params.map_interactions;
        ep_retina = (window.retina || window.devicePixelRatio > 1.5);

        function ep_is_retina(map_id){
            return (eventpost_params.maptiles[map_id]['urls_retina'] && ep_retina);
        }

        /* ------------------------------------------------------------------------------------------------------------------
         * Single Location link
         * show it in a tiny map instead of following the link
         */
        jQuery('a.event_link.gps').click(function () {
            if (jQuery(this).parent().data('latitude') !== undefined && jQuery(this).parent().data('longitude') !== undefined) {
                var lat = jQuery(this).parent().data('latitude');
                var lon = jQuery(this).parent().data('longitude');
                var marker = jQuery(this).parent().data('marker');
                var id = jQuery(this).parent().data('id');
                var map_id = 'event_map' + id;
                var position = new ol.proj.transform([lon, lat], ep_proj_source, ep_proj_destination);
                if (jQuery('#' + map_id).length === 0) {
                    jQuery(this).parent().append('<div id="' + map_id + '-wrap"><div id="' + map_id + '" class="event_map"></div></div>');
                    jQuery('#' + map_id+'-wrap').css({
                        height: '400px',
                        margin: 'auto',
                        clear:  'both'
                    }).animate({height: 'toggle'}, 1);

                    ep_vectorSources[map_id] = new ol.source.Vector();
                    ep_maps[map_id] = new ol.Map({
                        target: map_id,
                        layers: [
                            new ol.layer.Tile({
                                source: new ol.source.XYZ({
                                    urls: (ep_is_retina(eventpost_params.defaulttile) ? eventpost_params.maptiles[eventpost_params.defaulttile]['urls_retina'] : eventpost_params.maptiles[eventpost_params.defaulttile]['urls']),
                                    tilePixelRatio: (ep_is_retina(eventpost_params.defaulttile) ? 2 : 1),
                                    attribution: eventpost_params.maptiles[eventpost_params.defaulttile]['attribution']
                                })
                            }),
                            new ol.layer.Vector({
                                source: ep_vectorSources[map_id]
                            })
                        ],
                        view: new ol.View({
                            center: position,
                            zoom: eventpost_params.zoom
                        })
                    });

                    ep_maps[map_id].addControl(new ol.control.Zoom());
                    var ep_feature = new ol.Feature({
                        geometry: new ol.geom.Point(position)
                    });

                    if (ep_icons[marker] === undefined) {
                        ep_icons[marker] = new ol.style.Style({
                            image: new ol.style.Icon(({
                                anchor: [16, 32],
                                anchorXUnits: 'pixels',
                                anchorYUnits: 'pixels',
                                opacity: 1,
                                src: marker
                            }))
                        });
                    }
                    ep_feature.setStyle(ep_icons[marker]);
                    ep_vectorSources[map_id].addFeature(ep_feature);

                }
                jQuery('#' + map_id+'-wrap').animate({height: 'toggle'}, 1000, function () {
                    ep_maps[map_id].getView().setCenter(position);
                });
                return false;
            }
        });


        function ep_focus_feature(map_id, feature){
            jQuery(ep_pop_elements[map_id]).hide(0);
            view = ep_maps[map_id].getView();
            var geometry = feature.getGeometry();
            var coord = geometry.getCoordinates();
            var pan = view.animate({
                duration: 1000,
                center: coord
            });
            ep_popups[map_id].setPosition(coord);

            html_output = '<a href="' + feature.get('link') + '">' +
                    (feature.get('thumbnail')!==''&&feature.get('thumbnail')!==undefined?'<img src="'+feature.get('thumbnail')+'">':'')+
                    '<strong>' + feature.get('name') + '</strong><br>' +
                    '<time>' + feature.get('date') + '</time><br>' +
                    '<address>' + feature.get('address') + '</address>' +
                    (feature.get('desc')!==''&&feature.get('desc')!==undefined?'<p>'+feature.get('desc')+'</p>':'')+
                    '</a>';
            jQuery(ep_pop_elements[map_id]).delay(500).html(html_output).show(500);
        }

        /* ------------------------------------------------------------------------------------------------------------------
         * List of events
         * Making a big map with all available locations
         */
        // Parse all list wich have to be displayed as a map

        jQuery('.event_geolist').each(function () {
            var geo_id = jQuery(this).attr('id');
            var map_id = 'event_map_all' + geo_id;
            var mark_id = 'event_markersall' + geo_id;
            var width = jQuery(this).data('width');
            var height = jQuery(this).data('height');
            var zoom = jQuery(this).data('zoom');
            var maptile = jQuery(this).data('tile');
            if(maptile===''){
                maptile = eventpost_params.defaulttile;
            }
            var disabled_integrations = jQuery(this).data('disabled-interactions');

            // Add html elements for map and popup
            var map_elements = '<div id="' + map_id + '" class="event_map map"></div><div id="' + map_id + '-popup" class="event_map_popup"></div>';
            jQuery(this).addClass('event_geolist_parsed');
            var list_position = false;
            // Wrap items if needed
            if(jQuery(this).hasClass('has-list')){
                list_position = jQuery(this).data('list');
                jQuery('.event_item', jQuery(this)).wrapAll('<div class="eventpost-item-list">');
                jQuery('.eventpost-item-list a', jQuery(this)).click(function(e){
                    e.preventDefault();
                    feature_selector = geo_id+'-'+jQuery(this).parents('.event_item').first().data('id')
                    console.log(feature_selector);
                    if(ep_features[feature_selector] !== 'undefined'){
                        ep_focus_feature(map_id, ep_features[feature_selector]);
                    }
                    return false;
                });
            }
            if(list_position === 'below' || list_position === 'right'){
                jQuery(this).prepend(map_elements);
            }
            else{
                jQuery(this).append(map_elements);
            }

            css = {
                margin: 'auto',
                clear: 'both'
            };
            if (width !== 'auto')
                css.width = width;
            if (height !== 'auto')
                css.height = height;

            if(list_position === 'left' || list_position === 'right'){
                css.width = '70%';
            }
            if(list_position === 'above' || list_position === 'below'){
                jQuery(this).css({height: 'auto'});
            }
            jQuery('#' + map_id).css(css);

            // Create a layer for markers
            ep_vectorSources[map_id] = new ol.source.Vector();

            // Create a popup object
            ep_pop_elements[map_id] = document.getElementById(map_id + '-popup');
            ep_popups[map_id] = new ol.Overlay({
                element: ep_pop_elements[map_id],
                positioning: 'bottom-center',
                stopEvent: false
            });

            // Initialize map
            map_settings = {
                target: map_id,
                layers: [
                    new ol.layer.Tile({
                        source: new ol.source.XYZ({
                            urls: (ep_is_retina(maptile) ? eventpost_params.maptiles[maptile]['urls_retina'] : eventpost_params.maptiles[maptile]['urls']),
                            tilePixelRatio: (ep_is_retina(maptile) ? 2 : 1),
                            attribution: eventpost_params.maptiles[maptile]['attribution']
                        })
                    }),
                    new ol.layer.Vector({
                        source: ep_vectorSources[map_id]
                    })
                ],
                view: new ol.View({
                    center: [0, 0],
                    zoom: isNaN(zoom) ? 12 : zoom,
                    maxZoom: 18
                }),
                overlays: [ep_popups[map_id]]
            };

            ep_maps[map_id] = new ol.Map(map_settings);
            ep_maps[map_id].addControl(new ol.control.ZoomSlider());

            //Add action for each markers
            ep_maps[map_id].on('click', function (evt) {
                var feature = ep_maps[map_id].forEachFeatureAtPixel(evt.pixel,
                        function (feature, layer) {
                            return feature;
                        });
                if (feature) {
                    ep_focus_feature(map_id, feature);
                } else {
                    jQuery(ep_pop_elements[map_id]).hide(200);
                }
            });

            var feature_offset = 0;
            // Parse all items to create markers and put them on the map
            jQuery(this).find('address').each(function () {
                feature_offset++;
                var lat = parseFloat(jQuery(this).data('latitude'));
                var lon = parseFloat(jQuery(this).data('longitude'));
                jQuery(this).parents('.event_item').first().data('id', feature_offset);
                if (lat !== undefined && lon !== undefined) {
                    var item = jQuery(this).parent().parent();
                    var marker = jQuery(this).data('marker');
                    var id = jQuery(this).data('id');
                    coords = new ol.proj.transform([lon, lat], ep_proj_source, ep_proj_destination);

                    obj={
                        geometry: new ol.geom.Point(coords),
                        name: item.find('h5').text(),
                        address: jQuery(this).html(),
                        date: item.find('time').text(),
                        link: item.find('a').attr('href'),
                        desc: item.find('.event_exerpt').html()
                    };
                    if(item.find('img').length>0){
                        obj.thumbnail=item.find('img').attr('src');
                    }
                    var feature_id = geo_id+'-'+feature_offset;
                    ep_features[feature_id] = new ol.Feature(obj);


                    if (ep_icons[marker] === undefined) {
                        ep_icons[marker] = new ol.style.Style({
                            image: new ol.style.Icon(({
                                anchor: [16, 32],
                                anchorXUnits: 'pixels',
                                anchorYUnits: 'pixels',
                                opacity: 1,
                                src: marker
                            }))
                        });
                    }
                    ep_features[feature_id].setStyle(ep_icons[marker]);
                    ep_vectorSources[map_id].addFeature(ep_features[feature_id]);
                }
            });

            //Center the map to show all markers
            ep_maps[map_id].getView().fit(ep_vectorSources[map_id].getExtent(), ep_maps[map_id].getSize());
            if(!isNaN(zoom) && zoom !== '' && zoom>0){
                ep_maps[map_id].getView().setZoom(zoom);
            }

            m_i=0;
            var int_key;
            for(int_key in ep_interactions){
                if(disabled_integrations.indexOf(int_key+',')>-1){
                    ep_maps[map_id].getInteractions().getArray()[m_i].setActive(false);
                }
                m_i++;
            }


        });
    }
    else{
        jQuery('.event_geolist').hide();
    }


    /* ------------------------------------------------------------------------------------------------------------------
     * Calendar Widget
     */
    function eventpost_cal_links() {
        jQuery('.eventpost_calendar table td h4').each(function(){
            event_datas = jQuery(this).next('.event_data');
            jQuery(this).css({
                borderColor: event_datas.css('border-left-color'),
                color: event_datas.css('border-left-color'),
                backgroundColor: event_datas.css('background-color')
            });
        });
        jQuery('.eventpost_cal_bt').click(function () {
            var calcont = jQuery(this).parents('.eventpost_calendar');
            jQuery.get(
                    eventpost_params.ajaxurl,
                    {
                        action: 'EventPostCalendar',
                        date: jQuery(this).data('date'),
                        cat: calcont.data('cat'),
                        mf: calcont.data('mf'),
                        dp: calcont.data('dp'),
                        color: calcont.data('color'),
                        display_title: calcont.data('title'),
                        thumbnail: calcont.data('thumbnail')
                    },
                    function (data) {
                        calcont.html(data);
                        eventpost_cal_links();
                    });
                    }
                );
        jQuery('.eventpost_cal_link').click(function () {
            var calcont = jQuery(this).parents('.eventpost_calendar');
            jQuery('.eventpost_cal_list', calcont).fadeOut(function () {
                jQuery(this).remove();
            });
            jQuery.get(
                    eventpost_params.ajaxurl,
                    {
                        action: 'EventPostCalendarDate',
                        date: jQuery(this).data('date'),
                        cat: calcont.data('cat'),
                        mf: calcont.data('mf'),
                        dp: calcont.data('dp'),
                        color: calcont.data('color'),
                        display_title: calcont.data('title'),
                        thumbnail: calcont.data('thumbnail')
                    },
                    function (data) {
                        calcont.append('<div class="eventpost_cal_list"><button class="eventpost_cal_close">x</button>' + data + '</div>');
                        calcont.find('.eventpost_cal_list').hide(1).fadeIn(500);
                        calcont.find('.eventpost_cal_close').click(function () {
                            jQuery(this).parent().hide(500).remove();
                    });
            });
        });

    }
    jQuery('.eventpost_calendar').each(function () {
        var calcont = jQuery(this);
        calcont.html('<img src="' + eventpost_params.imgpath + 'cal-loader.gif" class="eventpost_cal_loader"/>');
        jQuery.get(
                eventpost_params.ajaxurl,
                {
                    action: 'EventPostCalendar',
                    date: jQuery(this).data('date'),
                    cat: jQuery(this).data('cat'),
                    mf: jQuery(this).data('mf'),
                    dp: jQuery(this).data('dp'),
                    color: jQuery(this).data('color'),
                    display_title: jQuery(this).data('title'),
                    thumbnail: jQuery(this).data('thumbnail')
                },
                function (data) {
                    calcont.html(data);
                    eventpost_cal_links();
                });
    });

});
