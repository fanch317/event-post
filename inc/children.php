<?php
$EventPostChild = new EventPostChild();

class EventPostChild {

    var $POST_TYPE;

    function __construct() {

        $this->POST_TYPE = 'eventpost';

        // Hook into the plugin

        add_action('eventpost_getsettings_action', array(&$this, 'get_settings'), 1, 2);
        add_action('eventpost_settings_form', array(&$this, 'settings_form'));
        add_action('evenpost_init', array(&$this, 'init'));
        add_action('wp_loaded', array(&$this, 'add_post_type'));
    }

    /**
     * PHP4 constructor
     */
    function EventPostChild() {
        $this->__construct();
    }


    /**
     *
     * @param object $EP
     * @return void
     */
    function init($EP) {
        // Ensure EventPostChild is required.
        if (!$EP->settings['children_enabled']) {
            return;
        }

        add_filter('eventpost_retreive', array(&$this, 'retreive'));
        add_filter('eventpost_get_post_types', array(&$this, 'get_post_types'));
        add_filter('eventpost_contentbar', array(&$this, 'display_single'), 3, 2);

        add_action('eventpost_add_custom_box', array(&$this, 'add_custom_box'));
        add_filter('eventpost_add_custom_box_position', array(&$this, 'add_custom_box_position'), 1, 2);
        add_action('admin_notices', array(&$this, 'notice'));
        add_action('save_post', array(&$this, 'save_postdata'), 100);
        add_action('edit_form_top', array(&$this, 'edit_form_top'), 1);

	add_action('admin_post_EventPostAddChild', array(&$this, 'add_child_admin_post'));
	add_action('admin_post_EventPostDeleteChild', array(&$this, 'delete_child_admin_post'));

        add_action('wp_ajax_EventPostAddChild', array(&$this, 'add_child_ajax'));
        add_action('wp_ajax_EventPostDeleteChild', array(&$this, 'delete_child_ajax'));

        add_filter('eventpost_columns_head', array(&$this, 'columns_head'));
        add_action('eventpost_columns_content', array(&$this, 'columns_content'), 10, 2);
    }

    /**
     *
     */
    function add_post_type(){
        register_post_type(
            $this->POST_TYPE,
            array(
                'label' => __("Event post",'event-post'),
                'description' => 'Child of event posts',
                'public' => false,
                'publicly_queryable'=>true,
                'show_ui' => true,
                'show_in_menu' => false,
                'capability_type' => 'post',
                'hierarchical' => false,
                'rewrite' => array('slug' => ''),
                'taxonomies'=>get_taxonomies(),
                'query_var' => true,
                'has_archive' => false,
                'supports' => array('page_attributes', 'author'),
                'labels' => array (
                    'name' => __("Event post",'event-post'),
                    'singular_name' => __("Event post",'event-post'),
                    'menu_name' => __("Event post",'event-post'),
                    'add_new' => __('add','event-post'),
                    'add_new_item' => __('Add event child','event-post'),
                    'edit' => __('Edit','event-post'),
                    'edit_item' => __('Edit event child','event-post'),
                    'new_item' => __('New','event-post'),
                    'view' => __('View','event-post'),
                    'view_item' => __('View  event child','event-post'),
                    'search_items' => __('Search event children','event-post'),
                    'not_found' => __('No  event child Found','event-post'),
                    'parent' => __(' Event post Parent','event-post'),
                    )
                )
            );

    }

    /**
     *
     * @param array reference &$ep_settings
     * @param boolean reference &$reg_settings
     */
    function get_settings(&$ep_settings, &$reg_settings) {
        if (!isset($ep_settings['children_enabled'])) {
            $ep_settings['children_enabled'] = false;
            $reg_settings = true;
        }
        elseif($ep_settings['children_enabled']==true && !in_array($this->POST_TYPE, $ep_settings['posttypes'])){
            array_push($ep_settings['posttypes'], $this->POST_TYPE);
        }
        if (!isset($ep_settings['children_sync_tax'])) {
            $ep_settings['children_sync_tax'] = false;
            $reg_settings = true;
        }
    }


    /**
     *
     * @global \EventPost $EventPost
     * @param string $post_type
     */
    function add_custom_box($post_type){
        global $EventPost;
        if($post_type==$this->POST_TYPE){
            return;
        }
        add_meta_box('event_post_children', __('Children events', 'event-post'), array(&$this, 'inner_custom_box_children'), $post_type, $EventPost->settings['adminpos'], 'core');
    }

    function add_custom_box_position($position, $post_type){
        if($post_type==$this->POST_TYPE){
            $position = 'advanced';
        }
        return $position;
    }

    /**
     * Remove self post type form global list
     * @param array $post_types
     * @return array
     */
    function get_post_types($post_types=array()){
        unset($post_types[$this->POST_TYPE]);
        return $post_types;
    }

    /**
     *
     * @param int $child_id
     * @return bool
     */
    function _delete_child($child_id){
        return wp_delete_post( $child_id, true );
    }

    /**
     *
     * @global $EventPost
     * @param int $child_id
     */
    function _sync_child($child_id){
        global $EventPost;
        if (!$EventPost->settings['children_sync_tax']) {
            return;
        }
        $child = get_post($child_id);
        $taxonomies = get_object_taxonomies(get_post($child->post_parent));
        foreach($taxonomies as $taxonomy){
            wp_set_object_terms($child_id, wp_get_object_terms( $child->post_parent, $taxonomy, array('fields'=>'ids') ), $taxonomy);
        }
    }

    /**
     * @global type $EventPost
     * @param int $post_id
     * @return (int|WP_Error) The post ID on success. The value 0 or WP_Error on failure.
     */
    function _add_child($post_id){
        global $EventPost;
        if(!is_numeric($post_id)){
            return false;
        }

        $id = wp_insert_post(array(
            'post_type'=>$this->POST_TYPE,
            'post_parent'=>$post_id,
            'post_status'=>'publish',
            'post_mime_type'=>'text/calendar',
        ));

        if(!is_numeric($id)){
            return false;
        }
        if ($EventPost->settings['children_sync_tax']) {
            $this->_sync_child($id);
        }
        return  $id;
    }

    /**
     *
     * @global \EventPost $EventPost
     * @param int $post_id
     * @return array
     */
    function get($post_id){
        global $EventPost;
        $param = array(
          'post_type'=>$this->POST_TYPE,
          'post_parent'=>$post_id,
          'post_status'=>array('publish', 'draft'),
          'posts_per_page'=>-1
        );

        $children=array();
        $query = new WP_Query($param);
        foreach($query->posts as $post){
            array_push($children, $EventPost->retreive($post) );
        }
        return $children;
    }

    /**
     *
     * @global type $EventPost
     * @param int $post_id
     * @return void
     */
    public function save_postdata($post_id) {
        global $EventPost;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return;
	}
        if (!$EventPost->settings['children_sync_tax']) {
            return;
        }
        $children = $this->get($post_id);
        if(count($children)){
            foreach($children as $child){
                $this->_sync_child($child->ID);
            }
        }
    }

    /**
     *
     * @param \WP_Post $event
     */
    function retreive($event){
        global $EventPost;
        if($event->post_type == $this->POST_TYPE && $event->post_parent){
            $parent = $EventPost->retreive($event->post_parent);
            $event->root_ID = $event->post_parent;
            $event->post_title = $parent->post_title;
            $event->permalink = $parent->permalink;
            $event->post_content = $parent->post_content;
            $event->post_excerpt = $parent->post_excerpt;
        }
        return $event;
    }


    /* -------------------- VIEWS ----------------------- */

    /**
     *
     */
    function notice(){
        if(false === $notice = filter_input(INPUT_GET, 'eventpost_child_notice')){
            return;
        }
        $notices = array(
            'add_failed'=>array('warning', __( 'An error occured while creating a child event...', 'event-post' )),
            'delete_success'=>array('success', __( 'Child event has been deleted', 'event-post' )),
            'delete_failed'=>array('warning', __( 'An error occured while deleting a child event...', 'event-post' ))
        );

        if(!isset($notices[$notice])){
            return;
        }

        ?>
        <div class="notice-<?php echo $notices[$notice][0]; ?>"><p><?php echo $notices[$notice][1]; ?></p></div>
        <?php
    }

    function check_admin_legitimity(){
        if (!wp_verify_nonce(filter_input(INPUT_GET, 'eventpost_children_nonce', FILTER_SANITIZE_STRING), 'eventpost_children_nonce') ){
            wp_die(__('Invalid link', 'event-post'));
	}
    }
    /**
     *
     * @return void
     */
    function add_child_admin_post(){
        $this->check_admin_legitimity();

        if(false === $post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT)){
            wp_die(__('No post ID given...', 'event-post'));
        }

        if(false !== $child_id = $this->_add_child($post_id)){
            wp_redirect(admin_url('post.php?post='.$child_id.'&action=edit&post_type=eventpost'));
        }
        else{
            wp_redirect(admin_url('post.php?post='.$post_id.'&action=edit&eventpost_child_notice=add_failed'));
        }
        exit;
    }
    function add_child_ajax(){
        if(false === $post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT)){
            wp_die(__('No post ID given...', 'event-post'));
        }

        if(false !== $child_id = $this->_add_child($post_id)){
            wp_send_json(array(
               'success'=>true,
               'child_id'=>$child_id,
               'edit_url'=>admin_url('post.php?post='.$child_id.'&action=edit')
            ));
        }
        else{
            wp_send_json(array(
               'success'=>false
            ));
        }
        exit;
    }

    function delete_child_admin_post(){
        $this->check_admin_legitimity();
        if(false === $post_id = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT)){
            wp_die(__('No post ID given...', 'event-post'));
        }
        if(false === $child_id = filter_input(INPUT_GET, 'child_id', FILTER_SANITIZE_NUMBER_INT)){
            wp_die(__('No child ID given...', 'event-post'));
        }

        if(false !== $this->_delete_child($child_id)){
            wp_redirect(admin_url('post.php?post='.$post_id.'&action=edit&eventpost_child_notice=delete_success'));
        }
        else{
            wp_redirect(admin_url('post.php?post='.$post_id.'&action=edit&eventpost_child_notice=delete_failed'));
        }
        exit;
    }

    /**
     *
     * @param type $ep_settings
     */
    function settings_form($ep_settings) {
        ?>
        <h2><?php _e('Multi dates', 'event-post'); ?></h2>
        <table class="form-table" id="eventpost-settings-table-children">
            <tbody>
                <tr>
                    <th>
                        <?php _e('Enable children events', 'event-post') ?>
                    </th>
                    <td>
                        <label for="children_enabled">
                            <input type="checkbox" name="ep_settings[children_enabled]" id="children_enabled" <?php if ($ep_settings['children_enabled'] == '1') {
                            echo'checked';
                        } ?> value="1">
        <?php _e('Allow event post to create children to events', 'event-post') ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php _e('Synchronize taxonomies', 'event-post') ?>
                    </th>
                    <td>
                        <label for="children_sync_tax">
                            <input type="checkbox" name="ep_settings[children_sync_tax]" id="children_sync_tax" <?php if ($ep_settings['children_sync_tax'] == '1') {
                            echo'checked';
                        } ?> value="1">
        <?php _e('Make children herit all taxonomies (categories, tags, custom taxonomies...) from the original event.', 'event-post') ?>
                        </label>
                    </td>
                </tr>

            </tbody>
        </table><!-- #eventpost-settings-table-children -->
        <?php
    }

    /**
     *
     * @param type $post
     */
    function edit_form_top($post){
        if($post->post_type==$this->POST_TYPE){
            $parent = get_post($post->post_parent);
            ?>
            <a href="<?php echo admin_url('post.php?post='.$parent->ID.'&action=edit'); ?>" class="button button-default">
                <i class="dashicons dashicons-arrow-left-alt"></i>
                <strong><?php printf(__('Back to %s', 'event-post'), esc_attr($parent->post_title)); ?></strong>
            </a>
            <?php
        }
    }

    /**
     * display the children custom box
     * @global \EventPost $EventPost
     */
    function inner_custom_box_children() {
        global $EventPost;
        wp_nonce_field('eventpost_children_nonce', 'eventpost_children_nonce');
        $post_id = get_the_ID();
        $children = $this->get($post_id);
        ?>
        <ul id="eventpost-children-list">
            <?php foreach ($children as $child): ?>
            <li>
                <?php echo $EventPost->get_singledate($child); ?>
                <?php echo $EventPost->get_singleloc($child); ?>
                <a class="button button-default eventpost-children-edit" href="<?php echo admin_url('post.php?post='.$child->ID.'&action=edit'); ?>" title="<?php _e('Edit child event', 'event-post'); ?>">
                    <i class="dashicons dashicons-edit"></i>
                </a>
                <a class="button button-default eventpost-children-delete" href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=EventPostDeleteChild&post_id='.$post_id.'&child_id='.$child->ID), 'eventpost_children_nonce', 'eventpost_children_nonce'); ?>" title="<?php _e('Delete child event', 'event-post'); ?>">
                    <i class="dashicons dashicons-trash"></i>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <a class="button button-default eventpost-children-add" href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=EventPostAddChild&post_id='.$post_id), 'eventpost_children_nonce', 'eventpost_children_nonce'); ?>">
            <i class="dashicons dashicons-plus"></i>
            <?php _e('Add child event', 'event-post'); ?>
        </a>
        <?php
    }

    /**
     * alters columns
     * @param array $defaults
     * @return array
     */
    public function columns_head($defaults) {
        $defaults['children_events'] = __('Children events', 'event-post');
        return $defaults;
    }

    /**
     * echoes content of a row in a given column
     * @param string $column_name
     * @param int $post_id
     */
    public function columns_content($column_name, $post_id) {
        if ($column_name == 'children_events') {
            $nb = count($this->get($post_id));
            echo $nb ? '<p align="center">'.$nb.'</p>' : '';
        }
    }


    /* -------------------------------------- FRONT -------------------------------*/

    function display_single($eventbar, $event){
        remove_filter('eventpost_contentbar', array(&$this, 'display_single'), 3, 2);
        global $EventPost;
        $children = $this->get($event->ID);
        if(0 !== $count = count($children)){
            $eventbar.='<p class="eventpost-children-text-more">'.sprintf(_n('%d other date:', '%d other dates:', $count, 'event-post'), $count).'</p>';
            foreach($children as $child){
                $eventbar.=$EventPost->get_single($child, 'event_single', 'single');
            }
        }
        add_filter('eventpost_contentbar', array(&$this, 'display_single'), 3, 2);
        return $eventbar;
    }


}
