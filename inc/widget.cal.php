<?php

/**
 *  The calendar widget
 */
class EventPost_Cal extends WP_Widget {
    var $defaults;
   function __construct() {
  	  parent::__construct(false, __( 'Events Calendar', 'event-post' ),array('description'=>__( 'Calendar presentation of events posts', 'event-post' )));
           $this->defaults = array(
            'title' => '',
            'cat'   => '',
            'date' => date('Y-n'),
            'mf' => 0,
            'choose' => 1,
            'colored' => 1,
            'display_title' => 0,
            'thumbnail' => 0,
            'thumbnail_size' => '',
        );
   }
   public function eventpostcal_widget(){
       $this->__construct();
   }
   function widget($args, $local_instance) {
        extract( $args );
        $instance = wp_parse_args((array) $local_instance, $this->defaults);

        $date = !empty($instance['date'])?date('Y-n',strtotime(esc_attr($instance['date']))):date('Y-n');

	global $EventPost;
	$events = $EventPost->get_events(
	    array(
		    'nb'=>-1,
		    'future'=>1,
		    'past'=>1,
		    'geo'=>0,
		    'cat'=>$instance['cat']
	    )
	);
	if(count($events)==0){
            return;
        }
        $EventPost->load_scripts();
        echo $args['before_widget'];
        if(!empty($instance['title'])){
                echo $args['before_title'];
                echo $instance['title'];
                echo $args['after_title'];
        }
        echo '<div class="eventpost_calendar" data-cat="'.$instance['cat'].'" data-date="'.$date.'" data-mf="'.$instance['mf'].'" data-dp="'.$instance['choose'].'" data-color="'.$instance['colored'].'" data-title="'.$instance['display_title'].'" data-thumbnail="'.($instance['thumbnail']?$instance['thumbnail_size']:'').'"></div>';
        echo $args['after_widget'];
   }

   function update($new_instance, $old_instance) {
       return $new_instance;
   }

   function form($local_instance) {
        global $EventPost;
	$instance = wp_parse_args( (array) $local_instance, $this->defaults );

        $cats = get_categories();
        $thumbnail_sizes = $EventPost->get_thumbnail_sizes();
       ?>
       <input type="hidden" id="<?php echo $this->get_field_id('title'); ?>-title" value="<?php echo $instance['title']; ?>">
       <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','event-post'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
       </label>
       </p>

       <p>
       	<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Only in :','event-post'); ?>
       	<select  id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>">
       		<option value=''><?php _e('All','event-post') ?></option>
       <?php foreach($cats as $cat){ ?>
       	<option value="<?php echo $cat->slug; ?>" <?php selected($cat->slug, $instance['cat'], true); ?>><?php echo $cat->cat_name; ?></option>
       <?php  }  ?>
       </select>
       </label>
       </p>

        <p>
       <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date','event-post'); ?>
       <select id="<?php echo $this->get_field_id('date'); ?>"  name="<?php echo $this->get_field_name('date'); ?>">
       	<option value='' <?php selected($instance['date'], '', true); ?>><?php _e('Current','event-post'); ?></option>
       	<option value='-1 Month' <?php selected($instance['date'], '-1 Month', true); ?>><?php _e('-1 Month','event-post'); ?></option>
       	<option value='-2 Months' <?php selected($instance['date'], '-2 Months', true); ?>><?php _e('-2 Months','event-post'); ?></option>
       	<option value='+1 Month' <?php selected($instance['date'],'+1 Month', true); ?>><?php _e('+1 Month','event-post'); ?></option>
       	<option value='+2 Months' <?php selected($instance['date'], '+2 Months', true); ?>><?php _e('+2 Months','event-post'); ?></option>
       </select>
       </label>
       </p>

       <p>
       <label for="<?php echo $this->get_field_id('mf'); ?>"><?php _e('Weeks start on','event-post'); ?>
       <select id="<?php echo $this->get_field_id('mf'); ?>" name="<?php echo $this->get_field_name('mf'); ?>">
       	<option value="0" <?php selected($instance['mf'], 0, true); ?>><?php _e('Sunday','event-post'); ?></option>
       	<option value="1" <?php selected($instance['mf'], 1, true); ?>><?php _e('Monday','event-post'); ?></option>
       </select>
       </label>
       </p>

       <p>
       <label for="<?php echo $this->get_field_id('choose'); ?>"><?php _e('Date picker','event-post'); ?>
       <select id="<?php echo $this->get_field_id('choose'); ?>" name="<?php echo $this->get_field_name('choose'); ?>">
       	<option value="0" <?php selected($instance['choose'], 0, true); ?>><?php _e('No','event-post'); ?></option>
       	<option value="1" <?php selected($instance['choose'], 1, true); ?>><?php _e('Yes','event-post'); ?></option>
       </select>
       </label>
       </p>

       <p>
       <label for="<?php echo $this->get_field_id('colored'); ?>"><?php _e('Colored days','event-post'); ?>
       <select id="<?php echo $this->get_field_id('colored'); ?>" name="<?php echo $this->get_field_name('colored'); ?>">
       	<option value="0" <?php selected($instance['colored'], 0, true); ?>><?php _e('No','event-post'); ?></option>
       	<option value="1" <?php selected($instance['colored'], 1, true); ?>><?php _e('Yes','event-post'); ?></option>
       </select>
       </label>
       </p>

       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('display_title'); ?>">
       <input id="<?php echo $this->get_field_id('display_title'); ?>" name="<?php echo $this->get_field_name('display_title'); ?>" type="checkbox" value="1" <?php checked($instance['display_title'], true , true); ?>/>
       <?php _e('Display title','event-post'); ?>
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
       <?php
   }

}