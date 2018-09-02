var ep_map, ep_can_drag_map, ep_view, ep_vectorSource, ep_feature, ep_proj_source, ep_proj_destination, marker, ep_marker, lat, lon, map_id, ep_geoloasked = false, ep_geolocalized = false, ep_currentloc = [0, 0];

// DATE

/**
 *
 * @param {type} str
 * @returns {unresolved}
 */
function eventpost_numdate(str) {
    var r = new RegExp("[-: T/]", "g");
    if (str.replace) {
        str = str.replace(r, '');
    }
    return parseInt(str);
}
/**
 *
 * @param {type} field
 * @returns {unresolved|String}
 */
function eventpost_getdate(field) {
    d = jQuery('#' + field + '_date').val();
    return d ? eventpost_numdate(d.substring(0, 16) + ':00') : '';
}
/**
 *
 * @param {type} field
 * @returns {jQuery}
 */
function eventpost_getdate_sql(field) {
    return jQuery('#' + field + '_date').val();
}
/**
 *
 * @returns {undefined}
 */
function eventpost_concat_time() {
    jQuery('.eventpost-datepicker-simple-wrap').each(function () {
        d = jQuery(this).find('.eventpost-datepicker-simple-date').val() + ' '
                + jQuery(this).find('.eventpost-datepicker-simple-hour').val() + ':'
                + jQuery(this).find('.eventpost-datepicker-simple-time').val() + ':00';
        jQuery(this).find('.eventpost-datepicker-simple').val(d);
    });
    jQuery('.eventpost-datepicker-native-wrap').each(function () {
        year = parseInt(jQuery(this).find('.eventpost-datepicker-native-year').val());
        
        month = parseInt(jQuery(this).find('.eventpost-datepicker-native-month').val());
        if(month<10){
            month='0'+month;
        }
        day = parseInt(jQuery(this).find('.eventpost-datepicker-native-day').val());
        if(day<10){
            day='0'+day;
        }
        hour = parseInt(jQuery(this).find('.eventpost-datepicker-native-hour').val());
        if(hour<10){
            hour='0'+hour;
        }
        minute = parseInt(jQuery(this).find('.eventpost-datepicker-native-minute').val());
        if(minute<10){
            minute='0'+minute;
        }
        d = year + '-'
                + month + '-'
                + day + ' '
                + (isNaN(hour)?'00':hour) + ':'
                + (isNaN(minute)?'00':minute) + ':00';
        jQuery(this).find('.eventpost-datepicker-native').val(d);
    });
}


function eventpost_set_allday(){
    set = jQuery('#event-post-date-all-day').is(':checked');
    if(set===true){
        jQuery('.eventpost-datepicker-native-hour, .eventpost-datepicker-native-minute, .eventpost-datepicker-simple-hour, .eventpost-datepicker-simple-time').val('00').trigger('change').parent().hide(300);
    }
    else{
        jQuery('.eventpost-datepicker-native-hour, .eventpost-datepicker-native-minute, .eventpost-datepicker-simple-hour, .eventpost-datepicker-simple-time').parent().show(300);
    }
}
/**
 *
 * @returns {undefined}
 */
function eventpost_chkdate() {
    //console.log('change date');
    var date_start = eventpost_getdate(eventpost.META_START);
    var date_end = eventpost_getdate(eventpost.META_END);
    //console.log(date_start + ' ' + date_end);
    if (date_end === '' || date_start > date_end) {
        jQuery('#' + eventpost.META_END + '_date').val(jQuery('#' + eventpost.META_START + '_date').val());
        jQuery('#' + eventpost.META_END + '_date').parents('label').find('.human_date').html(jQuery('#' + eventpost.META_START + '_date').parents('label').find('.human_date').html());
        jQuery('#' + eventpost.META_END + '_date').parents('label').effect( "shake" );
        
        fields = [
            'simple-date',
            'simple-hour',
            'simple-time',
            'native-year',
            'native-month',
            'native-day',
            'native-hour',
            'native-minute',
        ]
        for(f in fields){
            fname = fields[f];
            jQuery('.eventpost-datepicker-'+fname, jQuery('#' + eventpost.META_END + '_date').parents('label')).val(jQuery('.eventpost-datepicker-'+fname, jQuery('#' + eventpost.META_START + '_date').parents('label')).val());
        }
        jQuery(".eventpost-datepicker-control", jQuery('#' + eventpost.META_END + '_date').parents('label')).last().trigger('change');
        date_end = date_start;
    }
    //console.log(date_start);
    // UI
    if (date_start === 0) {
        jQuery('.event-post-event_begin_date-remove').hide();
    } else {
        jQuery('.event-post-event_begin_date-remove').show();
    }
    if (date_end === 0) {
        jQuery('.event-post-event_end_date-remove').hide();
    } else {
        jQuery('.event-post-event_end_date-remove').show();
    }
}

// MAP

/**
 *
 * @param {type} location
 * @returns {undefined}
 */
function eventpost_getlocation(location) {
    ep_currentloc = [location.coords.latitude, location.coords.longitude];
}
/**
 *
 * @returns {Array|ep_currentloc}
 */
function eventpost_location() {
    if ("geolocation" in navigator) {
        if (ep_geoloasked === false) {
            ep_geoloasked = true;
            navigator.geolocation.getCurrentPosition(eventpost_getlocation);
            var watchID = navigator.geolocation.watchPosition(function (position) {
                if (ep_geolocalized === false) {
                    ep_geolocalized = true;
                    eventpost_getlocation(position);
                    eventpost_apply('', ep_currentloc[0], ep_currentloc[1]);
                }
            });
        } else if (ep_geolocalized === true) {
            eventpost_apply('', ep_currentloc[0], ep_currentloc[1]);
        }
    }
    if (ep_currentloc !== 'no') {
        return ep_currentloc;
    } else {
        return [lat, lon];
    }
}
/**
 *
 * @param {type} addr
 * @param {type} lat
 * @param {type} lon
 * @returns {undefined}
 */
function eventpost_apply(addr, lat, lon) {
    if (jQuery('#geo_address').val() === '' && addr !== '') {
        jQuery('#geo_address').val(addr);
    }
    jQuery('#geo_latitude').val(lat);
    jQuery('#geo_longitude').val(lon).trigger('change');
    eventpost_search_form();
}
/**
 *
 * @returns {undefined}
 */
function eventpost_edit() {
    sctype = jQuery('#ep_sce_type').val();
    if (sctype === 'list') {
        jQuery('#ep_sce_maponly').hide();
        jQuery('#ep_sce_listonly').show();
    }
    if (sctype === 'map') {
        jQuery('#ep_sce_maponly').show();
        jQuery('#ep_sce_listonly').hide();
    }
    var ep_sce = '[events_' + sctype;
    jQuery('#event_post_sc_edit input,#event_post_sc_edit select').each(function () {
        if (!jQuery(this).hasClass('event_post_sc_no_use')) {
            var pc = jQuery(this).parent().parent().parent().attr('class');
            var att = jQuery(this).data('att');
            var val = jQuery(this).val();
            if (att !== null && val !== '' && (pc === 'all' || pc === sctype)) {
                ep_sce += ' ' + att + '="' + val + '"';
            }
        }
    });
    ep_sce += ']';
    jQuery('#ep_sce_shortcode').html(ep_sce);
}
/**
 *
 * @param {type} str
 * @returns {undefined}
 */
function eventpost_insertcontent(str) {
    if (IEbof) {
        switchEditors.go('content', 'html');
        document.post.content.value += str;
        switchEditors.go('content', 'tinymce');
    } else {
        document.post.content.value += str;
        if (document.all) {
            value = str;
            document.getElementById('content_ifr').name = 'content_ifr';
            var ec_sel = document.getElementById('content_ifr').document.selection;
            if (tinyMCE.activeEditor.selection) {
                tinyMCE.activeEditor.selection.setContent(str);
            } else if (tinyMCE.activeEditor) {
                tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, str);
            } else if (ec_sel) {
                var ec_rng = ec_sel.createRange();
                ec_rng.pasteHTML(value);
            } else {
            }
        } else {
            document.getElementById('content_ifr').name = 'content_ifr';
            if (document.content_ifr) {
                document.content_ifr.document.execCommand('insertHTML', false, str);
            } else if (document.getElementById('content_ifr').contentDocument) {
                document.getElementById('content_ifr').contentDocument.execCommand('insertHTML', false, str);
            } else if (tinyMCE.activeEditor.selection) {
                tinyMCE.activeEditor.selection.setContent(str);
            } else {
                tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, str);
            }
        }
    }
}
/**
 *
 * @returns {undefined}
 */
function eventpost_search_location() {
    var addr = jQuery('#event_address_search_txt').attr('value');

    if (addr === '') {
        eventpost_search_form();
        jQuery('#eventaddress_result').append('<span class="alert">' + eventpost.empty_address + '</span>');
        return;
    }
    //console.log(addr);
    var data = {
        action: 'EventPostGetLatLong',
        q: addr
    };

    jQuery('#eventaddress_result').html(addr + '<br/><img src="' + eventpost.imgpath + 'loader.gif" alt="..."/>');

    jQuery.post(eventpost.ajaxurl, data, function (data) {
        eventpost_search_form();
        var html_ret = '';
        for (var lieu in data) {
            //console.log(data[lieu]);
            lieu = data[lieu];
            if (lieu.lat !== undefined && lieu.lon !== undefined && lieu.display_name !== undefined) {
                html_ret += '<p><a onclick=\'eventpost_apply("' + lieu.display_name.replace('\'', '&apos;').replace('"', '&quot;') + '","' + lieu.lat + '","' + lieu.lon + '")\'>';
                if (lieu.icon !== undefined) {
                    html_ret += '<img src="' + lieu.icon + '" alt="' + lieu.type + '"/>';
                }
                html_ret += lieu.display_name + '</a></p>';
            }
        }
        jQuery('#eventaddress_result').append(html_ret);
    }, 'json');
}

/**
 *
 * @returns {undefined}
 */
function eventpost_search_form() {
    jQuery('#eventaddress_result').html('<input type="search" id="event_address_search_txt" placeholder="' + eventpost.search + '"/><button id="event_address_search_bt" class="button"><span class="dashicons dashicons-search"></span><span class="screen-reader-text">ok</span></button>');
    jQuery('#event_address_search_txt').on("keydown", function (e) {
        var code = e.keyCode || e.which;

        if (code === 13) {
            // alert('enter pressed');
            e.preventDefault();
            eventpost_search_location();
            return false;
        }
    });
    jQuery('#event_address_search_bt').click(function () {
        eventpost_search_location();
    });
}
/**
 *
 * @returns {undefined}
 */
function ep_set_coord() {
    lat = parseFloat(jQuery('#geo_latitude').val());
    lon = parseFloat(jQuery('#geo_longitude').val());
    if (isNaN(lat)) {
        lat = 0;
    }
    if (isNaN(lon)) {
        lon = 0;
    }
    var position = new ol.proj.transform([lon, lat], ep_proj_source, ep_proj_destination);

    ep_map.getView().setCenter(position);

    if (lat === 0 && lon === 0) {
        ep_map.getView().setZoom(1);
    } else {
        ep_map.getView().setZoom(12);
    }
}

/**
 *
 * @param {type} can
 * @returns {undefined}
 */
function ep_set_can_drag(can) {
    if (can === true) {
        ep_can_drag_map = true;
        jQuery('.ep-block-draging').hide();
        jQuery('.ep-stop-draging').show();
    } else {
        ep_can_drag_map = false;
        jQuery('.ep-block-draging').show();
        jQuery('.ep-stop-draging').hide();
    }
}

// LOAD

jQuery(window).load(function () {
    jQuery('#event_address_coords').hide();
    jQuery('#event_address_search').click(function () {
        if (jQuery('#event_address_coords').css('display') === 'block') {
            jQuery('#event_address_coords').hide(100);
        } else {
            jQuery('#event_address_coords').show(100);
            eventpost_search_form();

        }
    });
    jQuery('#event_address_unsearch').click(function () {
        jQuery('#event_address_coords').hide();
    });
    jQuery('#event_post_sc_edit input,#event_post_sc_edit select').change(function () {
        eventpost_edit();
    });
    jQuery('#ep_sce_submit').click(function () {
        eventpost_insertcontent(jQuery('#ep_sce_shortcode').html());
    });
    jQuery('#ep_sce_nball').click(function () {
        jQuery('#ep_sce_nb').val('-1');
        eventpost_edit();
    });
    jQuery('#ep_sce_shortcode').click(function () {
        jQuery(this).select();
    });


    eventpost_edit();
    /*
     * Hide icons
     */

    jQuery('#eventpost-color-dropdown').hide().before(' <a href="#" id="event-color-section-more-btn">' + eventpost.datepickeri18n.edit + '</a>');
    jQuery('#eventpost-color-preview, #event-color-section-more-btn').click(function (e) {
        e.preventDefault();
        jQuery('#eventpost-color-dropdown').toggle(300);
    });
    jQuery('#eventpost-color-dropdown input').on('change click', function(){
       jQuery('#eventpost-color-preview').attr('src', jQuery('#eventpost-color-preview').data('url')+jQuery(this).val()+'.png');
       jQuery('#eventpost-color-dropdown').hide(300);
    });
    /*
     * Date picker Dual
     */
    eventpost_chkdate();
    /*
     * Date picker Separate
     */
    jQuery(".eventpost-datepicker-simple").each(function () {
        current_date = jQuery(this).val();
        current_id = jQuery(this).attr('id');
        jQuery(this).wrap('<div class="eventpost-datepicker-simple-wrap eventpost-date-wrapper" id="eventpost-datepicker-simple-wrap-' + current_id + '" data-id="' + current_id + '">');
        jQuery(this).after('<input  class="eventpost-datepicker-control eventpost-datepicker-simple-date" value="' + current_date.substr(0, 10) + '" size="10" data-value="'+current_date.substr(0, 10)+'">'
                + '<label><span class="screen-reader-text">'+eventpost.datepickeri18n.hour+'</span><select class="eventpost-datepicker-control eventpost-datepicker-simple-hour" data-value="'+current_date.substr(11, 2)+'"></select></label>'
                + '<label><span class="screen-reader-text">'+eventpost.datepickeri18n.minute+'</span><select class="eventpost-datepicker-control eventpost-datepicker-simple-time" data-value="'+current_date.substr(14, 2)+'"></select></label>');
        jQuery(this).hide();
        for (h = 0; h < 24; h++) {
            h0 = h > 9 ? h : '0' + h;
            h0h = h0;
            if (eventpost.lang === 'en') {
                if (h <= 12) {
                    h0h = h + ' AM';
                } else {
                    h0h = (h - 12) + ' PM';
                }
            }
            jQuery("#eventpost-datepicker-simple-wrap-" + current_id + " .eventpost-datepicker-simple-hour").append('<option value="' + h0 + '"' + (h === parseInt(current_date.substr(11, 2)) ? ' selected' : '') + '>' + h0h + '</option>');
        }
        m_sel = false;
        for (m = 0; m < 60; m += 5) {
            m0 = m > 9 ? m : '0' + m;
            selected = '';
            if (!m_sel && Math.abs(m - parseInt(current_date.substr(14, 2))) <= 3) {
                selected = ' selected';
                m_sel = true;
            }
            jQuery("#eventpost-datepicker-simple-wrap-" + current_id + " .eventpost-datepicker-simple-time").append('<option value="' + m0 + '"' + selected + '>' + m0 + '</option>');
        }
    });
    if (jQuery.datepicker) {
        jQuery(".eventpost-datepicker-simple-date").wrap('<div class="eventpost-datepicker-simple-inputgroup">').datepicker({
            firstDay: 1,
            changeYear: true,
            changeMonth: true,
            showMonthAfterYear: true,
            yearRange: "c-5:+5",
            buttonText: eventpost.date_choose,
            showOn: "both",
            dateFormat: "yy-mm-dd",
            autoSize: true
        });
    }
    /**
     * Date picker native
     */
    jQuery(".eventpost-datepicker-native").each(function () {
        current_date = jQuery(this).val();
        current_id = jQuery(this).attr('id');
        jQuery(this).wrap('<div class="timestamp-wrap eventpost-timestamp-wrap eventpost-date-wrapper eventpost-datepicker-native-wrap" id="eventpost-datepicker-native-wrap-' + current_id + '" data-id="' + current_id + '">');
        dp_month='<label><span class="screen-reader-text">'+eventpost.datepickeri18n.month+'</span><select class="eventpost-datepicker-control eventpost-datepicker-native-month" id="eventpost-datepicker-native-' + current_id + '-month" data-value="'+current_date.substr(5, 2)+'"></select></label>';
        dp_day='<label><span class="screen-reader-text">'+eventpost.datepickeri18n.day+'</span><input type="text" class="eventpost-datepicker-control eventpost-datepicker-native-day" id="eventpost-datepicker-native-' + current_id + '-day" value="'+current_date.substr(8, 2)+'" data-value="'+current_date.substr(8, 2)+'" size="2" maxlength="2" autocomplete="off" /></label>';
        dp_year='<label><span class="screen-reader-text">'+eventpost.datepickeri18n.year+'</span><input type="text" class="eventpost-datepicker-control eventpost-datepicker-native-year" id="eventpost-datepicker-native-' + current_id + '-year" value="'+current_date.substr(0, 4)+'" data-value="'+current_date.substr(0, 4)+'" size="4" maxlength="4" autocomplete="off" /></label>';
        dp_hour='<label><span class="screen-reader-text">'+eventpost.datepickeri18n.hour+'</span><input type="text" class="eventpost-datepicker-control eventpost-datepicker-native-hour" id="eventpost-datepicker-native-' + current_id + '-hour" value="'+current_date.substr(11, 2)+'" data-value="'+current_date.substr(11, 2)+'" size="2" maxlength="2" autocomplete="off" /></label>';
        dp_minute='<label><span class="screen-reader-text">'+eventpost.datepickeri18n.minute+'</span><input type="text" class="eventpost-datepicker-control eventpost-datepicker-native-minute" id="eventpost-datepicker-native-' + current_id + '-minute" value="'+current_date.substr(14, 2)+'" data-value="'+current_date.substr(14, 2)+'" size="2" maxlength="2" autocomplete="off" /></label>';
        /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
        datepickerhtml=eventpost.datepickeri18n.order;
        datepickerhtml = datepickerhtml.replace('%1$s', dp_month);
        datepickerhtml = datepickerhtml.replace('%2$s', dp_day);
        datepickerhtml = datepickerhtml.replace('%3$s', dp_year);
        datepickerhtml = datepickerhtml.replace('%4$s', dp_hour);
        datepickerhtml = datepickerhtml.replace('%5$s', dp_minute);
        jQuery(this).after(datepickerhtml);
        for (m = 1; m < 12; m += 1) {
            m0 = m > 9 ? m : '0' + m;
            selected = '';
            if ( m0 === current_date.substr(5, 2)) {
                selected = ' selected';
            }
            jQuery('select', jQuery(this).parent()).append('<option value="' + m0 + '"' + selected + '>' + m0+'-'+eventpost.datepickeri18n.months[m] + '</option>');
        }
    }).hide();

    jQuery('.postbox .eventpost-datepicker-simple-wrap, .postbox .eventpost-datepicker-native-wrap').each(function(){
        jQuery(this).append('<p>\n\
<a href="#eventpost-edit_timestamp-' + current_id + '" class="save-timestamp hide-if-no-js button">'+eventpost.datepickeri18n.ok+'</a>\n\
<a href="#eventpost-edit_timestamp-' + current_id + '" class="cancel-timestamp hide-if-no-js button-cancel">'+eventpost.datepickeri18n.cancel+'</a>\n\
</p>').before('<a href="#'+jQuery(this).attr('id')+'" class="eventpost-datepicker-native-edit-link">'+eventpost.datepickeri18n.edit+'</a>');
    }).hide();
    jQuery('.eventpost-datepicker-simple-edit-link, .eventpost-datepicker-native-edit-link').click(function(e){
        e.preventDefault();
        jQuery(this).next('.eventpost-datepicker-simple-wrap, .eventpost-datepicker-native-wrap').toggle(300);
    });
    jQuery('.eventpost-datepicker-simple-wrap .save-timestamp, .eventpost-datepicker-native-wrap .save-timestamp').click(function(e){
        jQuery(this).parent().parent().toggle(300);
    });
    jQuery('.eventpost-datepicker-simple-wrap .cancel-timestamp, .eventpost-datepicker-native-wrap .cancel-timestamp').click(function(e){
        jQuery('.eventpost-datepicker-control', jQuery(this).parent().parent()).each(function(){
              jQuery(this).val(jQuery(this).data('value'));
        }).last().trigger('change');
        jQuery(this).parent().parent().toggle(300);
    });

    jQuery(".eventpost-datepicker-control").change(function () {
        eventpost_concat_time();
        eventpost_chkdate();
        var date_id = jQuery(this).parents('.eventpost-date-wrapper').data('id').replace('_date', '');
        var hd = jQuery('#' + date_id + '_date_human');
        if (jQuery(this).val() !== '') {
            jQuery.post(eventpost.ajaxurl, {action: 'EventPostHumanDate', date: eventpost_getdate_sql(date_id)}, function (data) {
                hd.html(data);
            });
        }
    });

    jQuery('#event-post-date-all-day').on('change click', function(){
        eventpost_set_allday();
    });
    eventpost_set_allday();


    /**
     * Map preview
     */
    if (typeof ol !== 'undefined') {
        ep_map = undefined;
        ep_vectorSource = undefined;
        ep_feature = undefined;
        ep_proj_source = new ol.proj.Projection({code: 'EPSG:4326'});
        ep_proj_destination = new ol.proj.Projection({code: 'EPSG:900913'});
        marker = '';
        ep_marker = undefined;
        lat = 0;
        lon = 0;
        map_id = 'event-post-map-preview';
    }
    jQuery('#' + map_id).each(function () {
        marker = jQuery(this).data('marker');

        jQuery(this).css({
            "width": "100%",
            "height": "200px",
            "position": "relative",
            "cursor": "move"
        });
        var position = new ol.proj.transform([lon, lat], ep_proj_source, ep_proj_destination);
        ep_vectorSource = new ol.source.Vector();
        ep_view = new ol.View({
            center: position,
            zoom: 12
        });
        ep_map = new ol.Map({
            target: map_id,
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.XYZ({
                        urls: eventpost_params.maptiles[eventpost_params.defaulttile]['urls']
                    })
                }),
                new ol.layer.Vector({
                    source: ep_vectorSource
                })
            ],
            view: ep_view
        });
        ep_map.addControl(new ol.control.Zoom());

        jQuery(this).append('<img id="' + map_id + '-fixed-marker" src="' + marker + '" tabIndex="-1" draggable="false" alt="position">');
        jQuery(this).append('<div id="' + map_id + '-startdrag" class="ep-block-draging"><span>' + eventpost.start_drag + '</span></div>');
        jQuery(this).append('<a id="' + map_id + '-stopdrag" class="ep-stop-draging button button-default">' + eventpost.stop_drag + '</a>');
        jQuery(this).after('<p style="text-align:center;"><a class="button button-default" id="' + map_id + '-get-current-loc"><span class="dashicons dashicons-marker"></span> ' + eventpost.use_current_location + '</a></p>');

        jQuery('#' + map_id + '-fixed-marker').css({
            "left": jQuery(this).width() / 2 - 16,
            "top": 100 - 16,
        });
        jQuery('#' + map_id + '-get-current-loc').click(function () {
            eventpost_location();
        });
        jQuery('.ep-stop-draging').hide().click(function () {
            ep_set_can_drag(false);
        });
        jQuery('.ep-block-draging').click(function () {
            ep_set_can_drag(true);
        });

        ep_map.on("moveend", function () {
            if (ep_can_drag_map) {
                map_coord = ep_view.getCenter();
                local_coord = new ol.proj.transform(map_coord, ep_proj_destination, ep_proj_source);
                if (local_coord[0] === 0 && local_coord[1] === 0) {
                    local_coord = ['', ''];
                }
                jQuery('#geo_longitude').val(local_coord[0]);
                jQuery('#geo_latitude').val(local_coord[1]);
            }
        });

        ep_set_coord();
        jQuery('#geo_longitude, #geo_latitude').on('change blur', function () {
            ep_set_coord();
        });
    });
    jQuery('#event_post_date .event-color-section label').on('focus click', function () {
        jQuery('#' + map_id + '-fixed-marker').attr('src', jQuery('img', jQuery(this)).attr('src'));
    });
    /*
     * Widgets stylish with icons
     */
    if (jQuery('body').hasClass('widgets-php')) {
        jQuery('.widget').each(function () {
            wid = jQuery(this).attr('id');
            if (wid.indexOf('eventpostmap') > -1) {
                jQuery(this).addClass('eventpost_admin_widget eventpost_widget_map');
            } else if (wid.indexOf('eventpostcal') > -1) {
                jQuery(this).addClass('eventpost_admin_widget eventpost_widget_cal');
            } else if (wid.indexOf('eventpost') > -1) {
                jQuery(this).addClass('eventpost_admin_widget eventpost_widget_list');
            }
        });
    }
});