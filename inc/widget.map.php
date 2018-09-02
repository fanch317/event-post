<?php

/**
 * The Map widget
 * Displays a map of geolocalized posts
 *
 */
class EventPost_Map extends WP_Widget {
    var $defaults;
   function __construct() {
        global $EventPost;
        parent::__construct(false, __( 'Events Map', 'event-post' ),array('description'=>__( 'Map of events posts', 'event-post' )));
        $this->defaults = array(
            'numberposts'   => '',
            'widgettitle'   => '',
            'cat'           => '',
            'tag'           => '',
            'height'        => '',
            'width'         => '',
            'future'        => 0,
            'past'          => 0,
            'thumbnail'     => 0,
            'thumbnail_size' => '',
            'excerpt'       => 0,
            'tile'          => $EventPost->settings['tile'],
            'zoom'         => '',
        );
          // UI options
        foreach($EventPost->map_interactions as $int_key=>$int_name){
            $this->defaults[$int_key]=true;
        }
   }
   public function EventPost_Map(){
       $this->__construct();
   }
   function widget($args, $local_instance) {
       global $EventPost;
       extract( $args );
       $instance = wp_parse_args((array) $local_instance, $this->defaults);

        global $EventPost;
        $events = $EventPost->get_events(
                array(
                    'nb' => $instance['numberposts'],
                    'future' => $instance['future'],
                    'past' => $instance['past'],
                    'geo' => 1,
                    'cat' => $instance['cat'],
                    'tag' => $instance['tag']
                )
        );
        if (sizeof($events) > 0) {
            echo $args['before_widget'];
            if (!empty($instance['widgettitle'])) {
                echo $args['before_title'];
                echo $instance['widgettitle'];
                echo $args['after_title'];
            }
            $atts = array(
                'events' => $events,
                'width' => $instance['width'],
                'height' => $instance['height'],
                'zoom' => $instance['zoom'],
                'geo' => 1,
                'class' => 'eventpost_widget',
                'thumbnail' => $instance['thumbnail'],
                'thumbnail_size' => $instance['thumbnail_size'],
                'excerpt' => $instance['excerpt'],
                'tile' => $instance['tile']
            );
            foreach($EventPost->map_interactions as $int_key=>$int_name){
                $atts[$int_key]=$instance[$int_key];
            }
            echo $EventPost->list_events($atts, 'event_geolist', 'widget');
            echo $args['after_widget'];
        }
    }
   /**
    *
    * @global object $EventPost
    * @param array $new_instance
    * @param array $old_instance
    * @return array
    */
   function update($new_instance, $old_instance) {
       global $EventPost;
       foreach($EventPost->map_interactions as $int_key=>$int_name){
            if(!isset($new_instance[$int_key])){
                $new_instance[$int_key]=false;
            }
        }
       return $new_instance;
   }
   /**
    *
    * @global object $EventPost
    * @param array $instance
    */
   function form($local_instance) {
 	global $EventPost;
	$instance = wp_parse_args( (array) $local_instance, $this->defaults );

        $cats = get_categories();
        $tags = get_tags();
        $thumbnail_sizes = $EventPost->get_thumbnail_sizes();
       ?>
       <input type="hidden" id="<?php echo $this->get_field_id('widgettitle'); ?>-title" value="<?php echo $instance['widgettitle']; ?>">
       <p>
       <label for="<?php echo $this->get_field_id('widgettitle'); ?>"><?php _e('Title','event-post'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('widgettitle'); ?>" name="<?php echo $this->get_field_name('widgettitle'); ?>" type="text" value="<?php echo $instance['widgettitle']; ?>" />
       </label>
       </p>

       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of posts','event-post'); ?>
       <input id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>" type="number" value="<?php echo $instance['numberposts']; ?>" />
       </label> <?php _e('(-1 is no limit)','event-post'); ?>
       </p>


       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('future'); ?>">
       <input id="<?php echo $this->get_field_id('future'); ?>" name="<?php echo $this->get_field_name('future'); ?>" type="checkbox" value="1" <?php checked($instance['future'], true , true); ?> />
       <?php _e('Display future events','event-post'); ?>
       </label>
       </p>
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('past'); ?>">
       <input id="<?php echo $this->get_field_id('past'); ?>" name="<?php echo $this->get_field_name('past'); ?>" type="checkbox" value="1" <?php checked($instance['past'], true , true); ?> />
       <?php _e('Display past events','event-post'); ?>
       </label>
       </p>

       <p>
       	<label for="<?php echo $this->get_field_id('cat'); ?>">
            <span class="dashicons dashicons-category"></span>
                <?php _e('Only in :','event-post'); ?>
       	<select  class="widefat" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>">
       		<option value=''><?php _e('All categories','event-post') ?></option>
       <?php
	   	$cats = get_categories();
		foreach($cats as $_cat){ ?>
                <option value="<?=$_cat->slug?>" <?php selected($instance['cat'], $_cat->slug , true); ?>><?=$_cat->cat_name?></option>
       <?php  }  ?>
       </select>
       </label>
       </p>

       <p>
       	<label for="<?php echo $this->get_field_id('tag'); ?>">
            <span class="dashicons dashicons-tag"></span>
            <?php _e('Only in :','event-post'); ?>
       	<select  class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>">
       		<option value=''><?php _e('All tags','event-post') ?></option>
       <?php
	   	$tags = get_tags();
		foreach($tags as $_tag){?>
                <option value="<?=$_tag->slug?>" <?php selected($instance['tag'], $_tag->slug , true); ?>><?=$_tag->name?></option>
       <?php  }  ?>
       </select>
       </label>
       </p>

       <hr>
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width','event-post'); ?>
       <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
       </label> (px, %)
       </p>
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height','event-post'); ?>
       <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" />
       </label> (px, %)
       </p>
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('zoom'); ?>"><?php _e('Force zoom','event-post'); ?>
       <input id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name('zoom'); ?>" value="<?php echo $instance['zoom']; ?>" type="number" min="3" max="20" size="2"/>
       </label>
       </p>

       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('thumbnail'); ?>">
       <input id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>" type="checkbox" value="1" <?php checked($instance['thumbnail'], true , true); ?>/>
       <?php _e('Show thumbnails','event-post'); ?>
       </label>
       </p>
       <p>
       	<label for="<?php echo $this->get_field_id('thumbnail_size'); ?>">
            <?php _e('Thumbnail size:','event-post'); ?>
       	<select  class="widefat" id="<?php echo $this->get_field_id('thumbnail_size'); ?>" name="<?php echo $this->get_field_name('thumbnail_size'); ?>">
       		<option value=''></option>
       <?php foreach($thumbnail_sizes as $size){?>
       	<option value="<?php echo $size; ?>" <?php selected($size, $instance['thumbnail_size'], true); ?>><?php echo $size; ?></option>
       <?php  }  ?>
       </select>
       </label>
       </p>
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('excerpt'); ?>">
       <input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" value="1" <?php checked($instance['excerpt'], true , true); ?> />
       <?php _e('Show excerpt','event-post'); ?>
       </label>
       </p>

       <p style="margin-top:10px;">
        <label for="<?php echo $this->get_field_id('tile'); ?>">
            <?php _e('Map background', 'event-post'); ?>
                        <select id="<?php echo $this->get_field_id('tile'); ?>"  name="<?php echo $this->get_field_name('tile'); ?>">
                                    <?php
                                    foreach ($EventPost->maps as $id => $map):
                                        ?>
                                <option value="<?php
                                            if ($EventPost->settings['tile'] != $map['id']) {
                                                echo $map['id'];
                                            }
                                            ?>" <?php selected($instance['tile'], $id, true);?>>
            <?php echo $map['name']; ?><?php
            if ($EventPost->settings['tile'] == $map['id']) {
                echo' (default)';
            }
            ?>
                                </option>
        <?php endforeach; ?>
                        </select>
        </label>
        </p>
        <hr>
        <?php foreach($EventPost->map_interactions as $int_key=>$int_name): ?>
        <p>
            <label for="<?php echo $this->get_field_id($int_key); ?>">
            <input id="<?php echo $this->get_field_id($int_key); ?>" name="<?php echo $this->get_field_name($int_key); ?>" type="checkbox" value="1" <?php checked($instance[$int_key], true , true); ?>/>
            <?php printf(__('Activate %s interaction','event-post'), $int_name); ?>
            </label>
        </p>
       <?php  endforeach; ?>
       <?php
   }

}