import {
  registerBlockType,
  RichText, // RichText is for Formatted content Editable areas (its value is an array)
  PlainText, // PlainText is for Formatted content Editable areas (its value is a string)
} from "@wordpress/blocks";

// Just a way to wrap elements without producing any wrapper markup
import {
	InspectorControls
} from "@wordpress/editor";

// Just a way to wrap elements without producing any wrapper markup
import {
	Fragment,
	RawHTML
} from "@wordpress/element";

// Components contain several reusable React components
import {
	CheckboxControl,
	RangeControl,
	SelectControl,
	TextControl
} from "@wordpress/components";

const { __ } = wp.i18n

import "./style.scss";

registerBlockType("eventpost/calendar", {
  title: __("Events Calendar", 'event-post'),
  icon: "calendar",
  category: "common",
  attributes: {
      date: {type: 'string'},
      color: {type: 'number'},
      display_title: {type: 'number'},
      mondayfirst: {type: 'number'},
      choose: {type: 'number'},
  },

  edit({ className, attributes, setAttributes, isSelected, id }) {
	const {
		date,
		color,
		display_title,
		mondayfirst,
		choose,
	 } = attributes;

	const setdate = date => setAttributes({ date });
	const setcolor = color => setAttributes({ color });
	const setdisplay_title = display_title => setAttributes({ display_title });
	const setmondayfirst = mondayfirst => setAttributes({ mondayfirst });
	const setchoose = choose => setAttributes({ choose });

    function eventpost_shortcode_preview(props){
        var data, unlinked;
		data = jQuery.extend({}, props);
		data.calendar_type = 'event_calendar';
        if(data.mondayfirst){
			data.mf = data.mondayfirst;
		}
		if(data.choose){
			data.dp = data.choose;
		}
		wp.ajax.post( 'EventPostCalendar', data)
		.done( function( response ) {
			jQuery('#preview-'+id).html(response) ;
		} )
		.fail( function(response) {
			unlinked = jQuery('<div>'+response+'</div>');
			jQuery('a', unlinked).removeAttr('href');
			jQuery('#preview-'+id).html(unlinked.html()) ;
		} );
	}

	return (
		<Fragment>
		  <div className={"wp-block-eventpost-calendar"} id={"preview-"+id}>
			{__('Events Calendar Preview', 'event-post')}
            {eventpost_shortcode_preview(attributes)}
		  </div>

		  {isSelected && (
			<InspectorControls>

			  <TextControl
				value={date}
				onChange={setdate}
				className="date"
				placeholder={__('Date', 'event-post')}
                help={__('Default date (YYYY-m)', 'event-post')}
			  />
			  <CheckboxControl
				label={__('Colored days', 'event-post')}
				checked={color}
				onChange={setcolor}
			  />
			  <CheckboxControl
				label={__('Display title', 'event-post')}
				checked={display_title}
				onChange={setdisplay_title}
			  />
			  <SelectControl
				label={__('Weeks start on', 'event-post')}
				value={mondayfirst}
				options={[
				  { label: __('Sunday', 'event-post'), value: "0" },
				  { label: __('Monday', 'event-post'), value: "1" },
				]}
				onChange={setmondayfirst}
			  />
			  <CheckboxControl
				label={__('Date picker', 'event-post')}
				checked={choose}
				onChange={setchoose}
			  />

			</InspectorControls>
		  )}
		</Fragment>
	);
  },

  save() {
	return null;
  }
});
