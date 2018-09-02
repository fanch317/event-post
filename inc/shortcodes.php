<?php

/**
 * Implements all shortcodes features
 */
class EventPost_Shortcodes{
    public $EP;

    function __construct() {
        //Shortcodes
        add_action('init', array(&$this,'init'));
        add_shortcode('events_list', array(&$this, 'shortcode_list'));
        add_shortcode('events_map', array(&$this, 'shortcode_map'));
        add_shortcode('events_cal', array(&$this, 'shortcode_cal'));
        add_shortcode('event_details', array(&$this, 'shortcode_single'));
        add_shortcode('event_term', array(&$this, 'shortcode_term'));
        add_shortcode('event_cat', array(&$this, 'shortcode_cat'));
        add_shortcode('event_search', array(&$this, 'shortcode_search'));
        // Workaround for ACF select2 bug
        // see: https://github.com/wp-shortcake/shortcake/issues/660
        add_filter( 'acf/settings/select2_version', array(&$this, 'return4'));
    }

    /**
     * Call functions when WP is ready
     */
    public function init(){
        global $EventPost;
        $this->EP = $EventPost;
        $this->shortcode_ui();
    }

    public function return4(){
        return 4;
    }

    /**
     * shortcode_single
     * @param array $atts
     * @filter : eventpost_params
     * @return string
     */
    public function shortcode_single($atts){
	extract(shortcode_atts(apply_filters('eventpost_params', array(
            'attribute' => '',
                        ), 'shortcode_single'), $atts));
	$event = $this->EP->retreive();
	switch($attribute){
	    case 'start':
		return $this->EP->human_date($event->time_start);
	    case 'end':
		return $this->EP->human_date($event->time_end);
	    case 'address':
		return $event->address;
	    case 'location':
		return $this->EP->get_singleloc($event, '', 'single');
	    case 'date':
		return $this->EP->get_singledate($event, '', 'single');
	    default:
		return $this->EP->get_single($event, '', 'single');
	}
    }

    /**
     *
     * @param array $atts
     * @return string
     */
    public function shortcode_term($atts){
        extract(shortcode_atts(apply_filters('eventpost_params', array(
            'tax' => null,
            'term' => null,
            'post_type' => null,
                        ), 'shortcode_term'), $atts));
        if(false !== $the_term = $this->EP->retreive_term($term, $tax, $post_type)){
             return $this->EP->delta_date($the_term->time_start, $the_term->time_end);
        }
    }
    public function shortcode_cat($_atts){
        $atts = shortcode_atts(array(
            'cat' => null,
        ), $_atts);
        $atts['tax']='category';
        $atts['post_type']='post';
        $atts['term']=$atts['cat'];
        unset($atts['cat']);
        return $this->shortcode_term($atts);
    }

    /**
     * Shortcode to display a list of events
     *
        ### Query parameters

        - **nb=5** *(number of post, -1 is all, default: 5)*
        - **future=1** *(boolean, retreive, or not, events in the future, default = 1)*
        - **past=0** *(boolean, retreive, or not, events in the past, default = 0)*
        - **cat=''** *(string, select posts only from the selected category, default=null, for all categories)*
        - **tag=''** *(string, select posts only from the selected tag, default=null, for all tags)*
        - **geo=0** *(boolean, retreives or not, only events wich have geolocation informations, default=0)*
        - **order="ASC"** *(string (can be "ASC" or "DESC")*
        - **orderby="meta_value"** *(string (if set to "meta_value" events are sorted by event date, possible values are native posts fileds : "post_title","post_date" etc...)*

        ### Display parameters

        - **thumbnail=""** * (Bool, default:false, used to display posts thumbnails)*
        - **thumbnail_size=""** * (String, default:"thmbnail", can be set to any existing size : "medium","large","full" etc...)*
        - **excerpt=""** * (Bool, default:false, used to display posts excerpts)*
        - **style=""** * (String, add some inline CSS to the list wrapper)*
        - **type=div** *(string, possible values are : div, ul, ol default=div)*
        - **title=''** *(string, hidden if no events is found)*
        - **before_title="&lt;h3&gt;"** *(string (default &lt;h3&gt;)*
        - **after_title="&lt;/h3&gt;"** *(string (default &lt;/h3&gt;)*
        - **container_schema=""** *(string html schema to display list)*
        - **item_schema=""** *(string html schema to display item)*
     *
     * @param array $_atts
     * @filter eventpost_params
     * @return string
     */
    public function shortcode_list($_atts) {
        $atts = shortcode_atts(apply_filters('eventpost_params', array(
            // Filters
            'nb' => 0,
            'type' => 'div',
            'future' => true,
            'past' => false,
            'geo' => 0,
            'cat' => '',
            'tag' => '',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'title' => '',
            // Display
            'before_title' => '<h3>',
            'after_title' => '</h3>',
            'thumbnail' => '',
            'thumbnail_size' => '',
            'excerpt' => '',
            'width' => '',
            'height' => 'auto',
            'style' => '',
            'container_schema' => $this->EP->list_shema['container'],
            'item_schema' => $this->EP->list_shema['item'],
                        ), 'shortcode_list'), $_atts);

        if ($atts['container_schema'] != $this->EP->list_shema['container'])
            $atts['container_schema'] = html_entity_decode($atts['container_schema']);
        if ($atts['item_schema'] != $this->EP->list_shema['item'])
            $atts['item_schema'] = html_entity_decode($atts['item_schema']);
        return $this->EP->list_events($atts, 'event_list', 'shortcode');
    }

    /**
     * Shortcode to display a map of events
     * @param array $_atts
     * @filter eventpost_params
     * @return string
     */
    public function shortcode_map($_atts) {
        $ep_settings = $this->EP->settings;

        $defaults = array(
            // Display
            'width' => '',
            'height' => '',
            'tile' => $ep_settings['tile'],
            'title' => '',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
            'style' => '',
            'thumbnail' => '',
            'thumbnail_size' => '',
            'excerpt' => '',
            'zoom' => '',
            'list' => '0',
            // Filters
            'nb' => 0,
            'future' => true,
            'past' => false,
            'cat' => '',
            'tag' => '',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
            // UI options
        foreach($this->EP->map_interactions as $int_key=>$int_name){
            $defaults[$int_key]=true;
        }
            // - UI options
        foreach($this->EP->map_interactions as $int_key=>$int_name){
            $defaults['disable_'.strtolower($int_key)]=false;
        }

        $atts = shortcode_atts(apply_filters('eventpost_params', $defaults, 'shortcode_map'), $_atts);
            // UI options
        foreach($this->EP->map_interactions as $int_key=>$int_name){
            if($atts['disable_'.strtolower($int_key)]==true){
                $atts[$int_key]=false;
            }
            unset($atts['disable_'.strtolower($int_key)]);
        }
        $atts['geo'] = 1;
        $atts['type'] = 'div';
        return $this->EP->list_events($atts, 'event_geolist', 'shortcode'); //$nb,'div',$future,$past,1,'event_geolist');
    }

    /**
     * Shortcode to display a calendar of events
     * @param array $_atts
     * @filter eventpost_params
     * @return string
     */
    public function shortcode_cal($_atts) {
	$this->EP->load_scripts();
        $atts = shortcode_atts(apply_filters('eventpost_params', array(
            'date' => date('Y-n'),
            'cat' => '',
            'mondayfirst' => 0, //1 : weeks starts on monday
            'display_title' => 0,
            'datepicker' => 1,
            'thumbnail' => '',
                        ), 'shortcode_cal'), $_atts);
        extract($atts);
        return '<div class="eventpost_calendar" data-cat="' . $cat . '" data-date="' . $date . '" data-mf="' . $mondayfirst . '" data-dp="' . $datepicker . '" data-title="'. $display_title .'">' . $this->EP->calendar($atts) . '</div>';
    }

    /**
     *
     * @param type $_atts
     * @return type
     */
    public function shortcode_search($_atts){
        return $this->EP->search($_atts);
    }

    /**
     * set_shortcode_ui
     * needs Shortcake (shortcode UI) plugin
     * https://wordpress.org/plugins/shortcode-ui/
     */
    public function shortcode_ui(){
	if(!function_exists('shortcode_ui_register_for_shortcode')){
	    return;
	}
    // In gutenberg, prefers native integration
    if(function_exists('gutenberg_pre_init')){
        return;
    }
	$shortcodes_list_atts=array(
            'label' => __('Events list','event-post'),
            'listItemImage' => 'dashicons-calendar',
            'post_type'=>array('page','post'),
            'attrs' => array(
                0=>array(
                    'label'       => __('Max. number of events:','event-post'),
                    'attr'        => 'nb',
                    'type'        => 'number',
                    'description' => __('-1 is for: no limit','event-post')
                ),
                1=>array(
                    'label'       => __('Categories','event-post'),
                    'attr'        => 'cat',
                    'type'        => 'term_select',
                    'taxonomy'    => 'category',
                    'multiple'    => true,
                    'help'        => __('Categories, separated by comma', 'event-post'),
                ),
                2=>array(
                    'label'       => __('Tags','event-post'),
                    'attr'        => 'tag',
                    'type'        => 'term_select',
                    'taxonomy'    => 'post_tag',
                    'multiple'    => true,
                    'help'        => __('Tags, separated by comma', 'event-post'),
                ),
                3=>array(
                    'label' =>  __('Display upcoming events','event-post'),
                    'attr'  => 'future',
                    'type'  => 'select',
                    'options' => array(
                        '1' => __('Yes','event-post'),
                        '0' => __('No','event-post'),
                    ),
                ),
                4=>array(
                    'label' =>  __('Display past events','event-post'),
                    'attr'  => 'past',
                    'type'  => 'select',
                    'options' => array(
                        '1' => __('Yes','event-post'),
                        '0' => __('No','event-post'),
                    ),
                ),
                5=>array(
                    'label' =>  __('Only geotagged events:','event-post'),
                    'attr'  => 'geo',
                    'type'  => 'select',
                    'options' => array(
                        '1' => __('Yes','event-post'),
                        '0' => __('No','event-post'),
                    ),
                ),
                6=>array(
                    'label' =>  __('Thumbnail:','event-post'),
                    'attr'  => 'thumbnail',
                    'type'  => 'select',
                    'options' => array(
                        '1' => __('Yes','event-post'),
                        '0' => __('No','event-post'),
                    ),
                ),
                7=>array(
                    'label' =>  __('Thumbnail size:','event-post'),
                    'attr'  => 'thumbnail_size',
                    'type'  => 'select',
                    'options' => array(
                        'thumbnail' => __('Thumbnail'),
                        'medium' => __('Medium'),
                        'large' => __('Large'),
                    ),
                ),
                8=>array(
                    'label' =>  __('Order by:','event-post'),
                    'attr'  => 'orderby',
                    'type'  => 'select',
                    'options' => array(
                        'meta_value' => __('Date'),
                        'title' => __('Title'),
                    ),
                ),
                9=>array(
                    'label' =>  __('Order:','event-post'),
                    'attr'  => 'order',
                    'type'  => 'select',
                    'options' => array(
                        'ASC' => __('Asc.'),
                        'DESC' => __('Desc.'),
                    ),
                ),
            ),
        );
	/*
	 * Map
	 */
	$shortcodes_map_atts = $shortcodes_list_atts;
	unset($shortcodes_map_atts['attrs'][5]); // Remove geotagged attr
	unset($shortcodes_map_atts['attrs'][8]); // Remove orderby attr
	unset($shortcodes_map_atts['attrs'][9]); // Remove order attr
	array_unshift($shortcodes_map_atts['attrs'],
		array(
                    'label'       => __('Width','event-post'),
                    'attr'        => 'width',
                    'type'        => 'text',
                ),
		array(
                    'label'       => __('Height','event-post'),
                    'attr'        => 'height',
                    'type'        => 'text',
                ),
		array(
                    'label'       => __('Zoom','event-post'),
                    'attr'        => 'zoom',
                    'type'        => 'number',
                    'help'        => __('0 fits to all events', 'event-post')
                ),
		array(
                    'label'       => __('List','event-post'),
                    'attr'        => 'list',
                    'default'      => '0',
                    'type'        => 'select',
                    'options' => array(
                        '0' => __('No list','event-post'),
                        'left' => __('Left','event-post'),
                        'right' => __('Right','event-post'),
                        'above' => __('Above','event-post'),
                        'below' => __('Below','event-post'),
                    ),
                )
	);
	$shortcodes_map_atts['label']=__('Events map','event-post');
	$shortcodes_map_atts['listItemImage']='dashicons-location-alt';
        foreach($this->EP->map_interactions as $int_key=>$int_name){
            $shortcodes_map_atts['attrs'][]=array(
                'label' => sprintf(__('Disable %s interaction','event-post'), $int_name),
                'attr'  => 'disable_'.$int_key,
                'type'  => 'checkbox'
            );
        }
	shortcode_ui_register_for_shortcode('events_list', apply_filters('eventpost_shortcodeui_list',$shortcodes_list_atts));
	shortcode_ui_register_for_shortcode('events_map', apply_filters('eventpost_shortcodeui_map',$shortcodes_map_atts));

	/*
	 * Calendar
	 */
	$shortcodes_cal_atts=array(
            'label' => __('Events calendar','event-post'),
            'listItemImage' => 'dashicons-calendar-alt',
	    'post_type'=>array('page','post'),
            'attrs' => array(
                array(
                    'label'       => __('Default date','event-post'),
                    'attr'        => 'date',
                    'type'        => 'text',
		    'description' => date('Y-n')
                ),
                array(
                    'label'       => __('Categories','event-post'),
                    'attr'        => 'cat',
                    'type'        => 'text',
                ),
                array(
                    'label'       => __('Monday first','event-post'),
                    'attr'        => 'mondayfirst',
                    'type'        => 'checkbox',
                ),
                array(
                    'label'       => __('Date selector','event-post'),
                    'attr'        => 'choose',
                    'type'        => 'checkbox',
                )
	    )
	);
	shortcode_ui_register_for_shortcode('events_cal', apply_filters('eventpost_shortcodeui_cal',$shortcodes_cal_atts));
	/*
	 * Details
	 */
	$shortcodes_details_atts=array(
            'label' => __('Event details','event-post'),
            'listItemImage' => 'dashicons-clock',
            'attrs' => array(
                array(
                    'label' =>  __('Attribute','event-post'),
                    'attr'  => 'attribute',
                    'type'  => 'select',
		    'options' => array(
			'' => __('Full details','event-post'),
			'date' => __('Full date','event-post'),
			'start' => __('Begin date','event-post'),
			'end' => __('End date','event-post'),
			'address' => __('Address text','event-post'),
			'location' => __('Location','event-post'),
		    ),
                ),
	    )
	);
	shortcode_ui_register_for_shortcode('event_details', apply_filters('eventpost_shortcodeui_details',$shortcodes_details_atts));


	/*
	 * Search
	 */
	$shortcodes_search_atts=array(
            'label' => __('Events search form','event-post'),
            'listItemImage' => 'dashicons-search',
            'attrs' => array(
                array(
                    'label' =>  __('Keywords','event-post'),
                    'attr'  => 'q',
                    'type'  => 'select',
		    'options' => array(
			'1' => __('Yes','event-post'),
			'0' => __('No','event-post'),
		    ),
                ),
                array(
                    'label' =>  __('Dates','event-post'),
                    'attr'  => 'dates',
                    'type'  => 'select',
		    'options' => array(
			'1' => __('Yes','event-post'),
			'0' => __('No','event-post'),
		    ),

                ),
	    )
	);
	shortcode_ui_register_for_shortcode('event_search', apply_filters('eventpost_shortcodeui_search',$shortcodes_search_atts));

    }
}
