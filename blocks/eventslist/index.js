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

registerBlockType("eventpost/list", {
  title: __("Events list", 'event-post'),
  icon: "calendar",
  category: "common",
  attributes: {
	  nb: {type: "number"},
	  future: {type: "number"},
	  past: {type: "number"},
	  cat: {type: "string"},
	  tag: {type: "string"},
	  geo: {type: "number"},
	  orderby: {type: "string"},
	  order: {type: "string"},
	  // Display
	  title: {type: "string"},
	  before_title: {type: "string"},
	  after_title: {type: "string"},
	  style: {type: "string"},
	  thumbnail: {type: "string"},
	  thumbnail_size: {type: "string"},
	  excerpt: {type: "string"}
  },

  edit({ className, attributes, setAttributes, isSelected, id }) {
	const {
		nb,
		future,
		past,
		cat,
		tag,
		geo,
		orderby,
		order,
		title,
		before_title,
		after_title,
		style,
		thumbnail,
		thumbnail_size,
		excerpt,
	 } = attributes;

	const setNb = nb => setAttributes({ nb });
	const setFuture = future => setAttributes({ future });
	const setPast = past => setAttributes({ past });
	const setCat = cat => setAttributes({ cat });
	const setTag = tag => setAttributes({ tag });
	const setGeo = geo => setAttributes({ geo });
	const setOrderby = orderby => setAttributes({ orderby });
	const setOrder = order => setAttributes({ order });
	const setTitle = title => setAttributes({ title });
	const setBeforeTitle = before_title => setAttributes({ before_title });
	const setAfterTitle = after_title => setAttributes({ after_title });
	const setStyle = style => setAttributes({ style });
	const setThumbnail = thumbnail => setAttributes({ thumbnail });
	const setThumbnailSize = thumbnail_size => setAttributes({ thumbnail_size });
	const setExcerpt  = excerpt => setAttributes({ excerpt });

    function eventpost_shortcode_preview(props){
        var data, unlinked;
		data = jQuery.extend({}, props);
		data.list_type = 'event_list';
		wp.ajax.post( 'EventPostList', data)
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
		  <div className={"wp-block-eventpost-list"} id={"preview-"+id}>
			{__('Events List Preview', 'event-post')}
            {eventpost_shortcode_preview(attributes)}
		  </div>

		  {isSelected && (
			<InspectorControls>

			  <TextControl
				value={title}
				onChange={setTitle}
				className="title"
				placeholder={__('Title', 'event-post')}
			  />
			  <h4>{__('Filters')}</h4>
			  <CheckboxControl
				label={__('Display upcoming events', 'event-post')}
				checked={future}
				onChange={setFuture}
			  />
			  <CheckboxControl
				label={__('Display past events', 'event-post')}
				checked={past}
				onChange={setPast}
			  />
			  <CheckboxControl
				label={__('Only geotagged events:', 'event-post')}
				value={geo}
				onChange={setGeo}
			  />
			  <RangeControl
				label={__('Max. number of events:', 'event-post')}
				value={nb}
				onChange={setNb}
				className="nb"
				min={ -1 }
				max={ 50 }
				help={__('-1 is for: no limit', 'event-post')}
			  />
			  <TextControl
				label={__('Categories', 'event-post')}
				value={cat}
				onChange={setCat}
				className="category"
				placeholder={__('Categories, separated by comma', 'event-post')}
			  />
			  <TextControl
				label={__('Tags', 'event-post')}
				value={tag}
				onChange={setTag}
				className="tag"
				placeholder={__('Tags, separated by comma', 'event-post')}
			  />

              <h4>{__('Display', 'event-post')}</h4>
			  <SelectControl
				label={__('Order by:', 'event-post')}
				value={orderby}
				options={[
				  { label: '', value: "" },
				  { label: __('Post title', 'event-post'), value: "title" },
				  { label: __('Event date', 'event-post'), value: "meta_value" },
				]}
				onChange={setOrderby}
			  />
			  <SelectControl
				label={__('Order:', 'event-post')}
				value={order}
				options={[
				  { label: '', value: "" },
				  { label: __('Ascendant', 'event-post'), value: "ASC" },
				  { label: __('Descendant', 'event-post'), value: "DESC" },
				]}
				onChange={setOrder}
			  />

			  <p>{__('Wrap title, if not empty', 'event-post')}</p>
			  <TextControl
				value={before_title}
				onChange={setBeforeTitle}
				className="before_title"
				placeholder={__('Before Title')}
			  />
			  <TextControl
				value={after_title}
				onChange={setAfterTitle}
				className="after_title"
				placeholder={__('After Title', 'event-post')}
			  />
			  <TextControl
				label={__('CSS Style', 'event-post')}
				value={style}
				onChange={setStyle}
				className="style"
				placeholder={__('Style attribute', 'event-post')}
			  />
			  <CheckboxControl
				label={__('Show excerpt', 'event-post')}
				checked={excerpt}
				onChange={setExcerpt}
			  />
			  <CheckboxControl
				label={__('Thumbnail', 'event-post')}
				checked={thumbnail}
				onChange={setThumbnail}
			  />
			  <SelectControl
				label={__('Thumbnail size:', 'event-post')}
				value={thumbnail_size}
				options={[
				  { label: '', value: "" },
				  { label: __('Thumbnail', 'event-post'), value: "thumbnail" },
				  { label: __('Medium', 'event-post'), value: "medium" },
				  { label: __('Large', 'event-post'), value: "large" },
				  { label: __('Full', 'event-post'), value: "full" },
				]}
				onChange={setThumbnailSize}
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
