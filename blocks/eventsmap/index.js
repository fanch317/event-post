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

var epmap_blk_attr = {
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
    excerpt: {type: "string"},
    // Map
    zoom: {type:'string'},
    tile: {type:'string'},
    width: {type:'string'},
    height: {type:'string'},
    list: {type:'string'},
}
var interaction, InteractionsMap, funcinteraction, tiles, _tile;
for(interaction in eventpost_gut_params.map_interactions){
    epmap_blk_attr['disable_'+interaction.toLowerCase()] = {type:'string'};
}
tiles = [{ label: __('Tile', 'event-post'), value: "" }];

for(_tile in eventpost_gut_params.maptiles){
    tiles.push( { label: eventpost_gut_params.maptiles[_tile]['name'], value: _tile });
}
registerBlockType("eventpost/map", {
  title: __("Events Map", 'event-post'),
  icon: "location-alt",
  category: "widgets",
  attributes: epmap_blk_attr,


  edit({ className, attributes, setAttributes, isSelected, id }) {
	const {
		nb,
		future,
		past,
		cat,
		tag,
		orderby,
		order,
		title,
		before_title,
		after_title,
		style,
		thumbnail,
		thumbnail_size,
		excerpt,
        zoom,
        tile,
        width,
        height,
        list,
        disable_dragrotate,
        disable_doubleclickzoom,
        disable_dragpan,
        disable_pinchrotate,
        disable_pinchzoom,
        disable_Keyboardpan,
        disable_Keyboardzoom,
        disable_mousewheelzoom,
        disable_dragzoom,
	 } = attributes;

	const setNb = nb => setAttributes({ nb });
	const setFuture = future => setAttributes({ future });
	const setPast = past => setAttributes({ past });
	const setCat = cat => setAttributes({ cat });
	const setTag = tag => setAttributes({ tag });
	const setOrderby = orderby => setAttributes({ orderby });
	const setOrder = order => setAttributes({ order });
	const setTitle = title => setAttributes({ title });
	const setBeforeTitle = before_title => setAttributes({ before_title });
	const setAfterTitle = after_title => setAttributes({ after_title });
	const setStyle = style => setAttributes({ style });
	const setThumbnail = thumbnail => setAttributes({ thumbnail });
	const setThumbnailSize = thumbnail_size => setAttributes({ thumbnail_size });
	const setExcerpt  = excerpt => setAttributes({ excerpt });
	const setzoom  = zoom => setAttributes({ zoom });
	const setTile  = tile => setAttributes({ tile });
	const setWidth  = width => setAttributes({ width });
	const setHeight = height => setAttributes({ height });
	const setList = list => setAttributes({ list });
    const setDisable_dragrotate = disable_dragrotate => setAttributes({ disable_dragrotate });
    const setDisable_doubleclickzoom = disable_doubleclickzoom => setAttributes({ disable_doubleclickzoom });
    const setDisable_dragpan = disable_dragpan => setAttributes({ disable_dragpan });
    const setDisable_pinchrotate = disable_pinchrotate => setAttributes({ disable_pinchrotate });
    const setDisable_pinchzoom = disable_pinchzoom => setAttributes({ disable_pinchzoom });
    const setDisable_keyboardpan = disable_Keyboardpan => setAttributes({ disable_Keyboardpan });
    const setDisable_keyboardzoom = disable_Keyboardzoom => setAttributes({ disable_Keyboardzoom });
    const setDisable_mousewheelzoom = disable_mousewheelzoom => setAttributes({ disable_mousewheelzoom });
    const setDisable_dragzoom = disable_dragzoom => setAttributes({ disable_dragzoom });

    InteractionsMap = Object.entries(eventpost_gut_params.map_interactions);
    return (
		<Fragment>
		  <div className={"wp-block-eventpost-map"} id={"preview-"+id}>
			{__('Events Map Preview', 'event-post')}
		  </div>

		  {isSelected && (
			<InspectorControls>

			  <TextControl
				value={title}
				onChange={setTitle}
				className="title"
				placeholder={__('Title', 'event-post')}
			  />
			  <h4>{__('Filters', 'event-post')}</h4>
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
				  { label: __('Event date', 'event-post'), value: "date" },
				]}
				onChange={setOrderby}
			  />
			  <SelectControl
				label={__('Order:', 'event-post')}
				value={order}
				options={[
				  { label: '', value: "" },
				  { label: __('ASC', 'event-post'), value: "ASC" },
				  { label: __('DESC', 'event-post'), value: "DESC" },
				]}
				onChange={setOrder}
			  />

			  <p>{__('Wrap title, if not empty', 'event-post')}</p>
			  <TextControl
				value={before_title}
				onChange={setBeforeTitle}
				className="before_title"
				placeholder={__('Before Title', 'event-post')}
			  />
			  <TextControl
				value={after_title}
				onChange={setAfterTitle}
				className="after_title"
				placeholder={__('After Title', 'event-post')}
			  />
			  <TextControl
				label={__('CSS Style')}
				value={style}
				onChange={setStyle}
				className="style"
				placeholder={__('Style attribute')}
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

               <h4>{__('Map', 'event-post')}</h4>
 			  <RangeControl
 				label={__('Zoom', 'event-post')}
 				value={zoom}
 				onChange={setzoom}
 				className="zoom"
 				min={ 0 }
 				max={ 16 }
                initialPosition={ 0 }
   				help={__('0 fits to all events', 'event-post')}
 			  />
              <TextControl
				value={width}
				onChange={setWidth}
				className="width"
				placeholder={__('Width', 'event-post')}
			  />
              <TextControl
				value={height}
				onChange={setHeight}
				className="height"
				placeholder={__('Height', 'event-post')}
			  />
              <SelectControl
				value={tile}
				options={tiles}
				onChange={setTile}
			  />
              <SelectControl
				label={__('List', 'event-post')}
				value={list}
				options={[
				  { label: __('No list', 'event-post'), value: "0" },
				  { label: __('Left', 'event-post'), value: "left" },
				  { label: __('Right', 'event-post'), value: "right" },
				  { label: __('Above', 'event-post'), value: "above" },
				  { label: __('Below', 'event-post'), value: "below" },
				]}
				onChange={setList}
			  />

              <h4>{__('Disable Interactions:', 'event-post')}</h4>
              {InteractionsMap.map(
                  (interaction, i) =>
                    (eval('funcinteraction = setDisable_'+interaction[0].toLowerCase()) &&
                    <CheckboxControl
                        label={interaction[1]}
                        checked={attributes['disable_'+interaction[0].toLowerCase()]}
                        onChange={funcinteraction}
                    />)

                  )
              }

			</InspectorControls>
		  )}
		</Fragment>
	);
  },

  save() {
	return null;
  }
});
