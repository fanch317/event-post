<?php
$EventPostWeather = new EventPostWeather();

/**
 * Provides weather support thanks to OpenWeatherMap
 * http://openweathermap.org
 *
 * License: Creative Commons (cc-by-sa)
 * http://creativecommons.org/licenses/by-sa/2.0/.
 *
 *
 * Get an API key
 * http://openweathermap.org/appid#get
 *
 */
class EventPostWeather {

    var $META_WEATHER;

    var $api_key;
    var $units;
    var $unit_names;
    var $theme;

    function __construct() {
        // Hook into the plugin

        add_action('eventpost_getsettings_action', array(&$this, 'get_settings'), 1, 2);
        add_action('eventpost_settings_form', array(&$this, 'settings_form'));
        add_action('evenpost_init', array(&$this, 'init'));
    }

    /**
     * PHP4 constructor
     */
    function EventPostWeather() {
        $this->__construct();
    }

    /**
     * Only for localization
     *
     * available values:
     *
     * - clear sky
     * - few clouds
     * - scattered clouds
     * - broken clouds
     * - shower rain
     * - rain
     * - thunderstorm
     * - snow
     * - mist
     */
    function localize(){
        __('clear sky', 'event-post');
        __('few clouds', 'event-post');
        __('scattered clouds', 'event-post');
        __('broken clouds', 'event-post');
        __('shower rain', 'event-post');
        __('rain', 'event-post');
        __('thunderstorm', 'event-post');
        __('snow', 'event-post');
        __('mist', 'event-post');

    }

    /**
     *
     * @param array reference &$ep_settings
     * @param boolean reference &$reg_settings
     */
    function get_settings(&$ep_settings, &$reg_settings) {
        if (!isset($ep_settings['weather_enabled'])) {
            $ep_settings['weather_enabled'] = false;
            $reg_settings = true;
        }
        if (!isset($ep_settings['weather_api_key'])) {
            $ep_settings['weather_api_key'] = '';
            $reg_settings = true;
        }
        if (!isset($ep_settings['weather_units'])) {
            $ep_settings['weather_units'] = 'standard';
            $reg_settings = true;
        }
    }

    /**
     *
     * @param type $ep_settings
     */
    function settings_form($ep_settings) {
        ?>
        <h2><?php _e('Weather', 'event-post'); ?></h2>
        <p class="description"><?php printf(__('Provided thanks to %s', 'event-post'), '<a href="http://openweathermap.org" target="_blank">openweathermap.org</a>'); ?></p>
        <table class="form-table" id="eventpost-settings-table-weather">
            <tbody>
                <tr>
                    <th>
                        <?php _e('Enable weather', 'event-post') ?>
                    </th>
                    <td>
                        <label for="weather_enabled">
                            <input type="checkbox" name="ep_settings[weather_enabled]" id="weather_enabled" <?php if ($ep_settings['weather_enabled'] == '1') {
                            echo'checked';
                        } ?> value="1">
        <?php _e('Enable weather feature', 'event-post') ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="weather_api_key">
        <?php _e('API key', 'event-post') ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="ep_settings[weather_api_key]" id="weather_api_key" value="<?php echo $ep_settings['weather_api_key']; ?>" size="40">
                        <p class="description"><?php printf(__('Get a free API key at: %s', 'event-post'), '<a href="http://openweathermap.org/appid#get" target="_blank">openweathermap.org/appid#get</a>'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="weather_units">
                            <?php _e('Units', 'event-post') ?>
                        </label>
                    </th>
                    <td>
                        <select name="ep_settings[weather_units]" id="weather_units">
                            <option value="standard" <?php selected($ep_settings['weather_units'], 'standard', true);?>>
                                <?php _e('Standard (Fahrenheit)', 'event-post') ?>
                            </option>
                            <option value="metric" <?php selected($ep_settings['weather_units'], 'metric', true);?>>
                                <?php _e('Metric (Celsius)', 'event-post') ?>
                            </option>
                            <option value="imperial" <?php selected($ep_settings['weather_units'], 'imperial', true);?>>
                                <?php _e('Imperial (Kelvin)', 'event-post') ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table><!-- #eventpost-settings-table-weather -->
        <?php
    }

    /**
     *
     * @param object $EP
     * @return void
     */
    function init($EP) {
        // Ensure OpenWeatherMap is required and available.
        if (!$EP->settings['weather_enabled'] || !$EP->settings['weather_api_key']) {
            return;
        }

        $this->META_WEATHER = 'event_weather';
        $this->api_key = $EP->settings['weather_api_key'];
        $this->units = $EP->settings['weather_units'];
        $this->theme = 'default';
        $this->unit_names = array('standard'=>'F', 'metric'=>'C', 'imperial'=>'K');

        // Alter objects
        add_filter('eventpost_params', array(&$this, 'params'));
        add_filter('eventpost_retreive', array(&$this, 'retreive'));

        // Alter schema
        add_filter('eventpost_item_scheme_entities', array(&$this, 'scheme_entities'));
        add_filter('eventpost_item_scheme_values', array(&$this, 'scheme_values'), 1, 2);
        add_filter('eventpost_default_list_shema', array(&$this, 'default_shema'));
        add_filter('eventpost_get_single', array(&$this, 'get_single'), 1, 2);


        add_action('eventpost_custom_box_date', array(&$this, 'get_weather'));


    }

    /**
     *
     * @param array $params
     * @return array
     */
    function params($params = array()) {
        $params['weather'] = true;
        return $params;
    }

    /**
     *
     * @param WP_Post $event
     * @return WP_Post
     */
    function retreive($event) {
        $event->weather = get_post_meta($event->ID, $this->META_WEATHER, true);
        return $event;
    }

    /**
     *
     * @param array $attr
     * @return array
     */
    function scheme_entities($attr = array()) {
        array_push($attr, '%event_weather%');
        return $attr;
    }

    /**
     *
     * @param array $values
     * @return array
     */
    function scheme_values($values = array(), $post = null) {
        array_push($values, $this->get_weather($post));
        return $values;
    }

    /**
     *
     * @param array $schema
     * @return array
     */
    function default_shema($schema) {
        $schema['item'] = str_replace('</%child%>', "%event_weather%\n</%child%>", $schema['item']);
        return $schema;
    }

    function get_single($event_datas, $post = null) {
        return $event_datas . $this->get_weather($post);
    }

    /**
     * From here, methods intends to get datas
     */

    function get_weather_icons($weather){
        $string = '';
        foreach((array) $weather as $item){
            $text = ucfirst(__(strtolower($item->description), 'event-post'));
            $string.='<span class="eventpost-weather-'.str_replace('-', '', strtolower($item->description)).' eventpost-weather-'.$item->icon.'">'
                    . '<img src="'. plugins_url('../img/weather/'.$this->theme.'/'.$item->icon.'.png', __FILE__).'" alt="'.sprintf('%s icon', $text).'">'
                    . '<em class="eventpost-weather-text">'.$text.'</em>'
                    . '</span>';
        }
        return $string;
    }
    /**
     *
     * @param object $item
     * @return string
     */
    function get_weather_item($item){
        $string = '';
        $string.= '<div class="eventpost-weather-item">'
                            .'<span class="eventpost-weather-date">'.date_i18n(get_option('date_format').' '.get_option('time_format'), $item->dt).'</span> '
                            .'<span class="eventpost-weather-temp">'.round($item->main->temp).' &deg'.$this->unit_names[$this->units].'</span> '
                            .'<span class="eventpost-weather-list">'.$this->get_weather_icons($item->weather).'</span>'
                            .'</div>';
        return $string;
    }

    /**
     *
     * @param type $post
     * @return string
     */
    function get_weather($post = null, $echo=false) {
        global $EventPost;
        $event = $EventPost->retreive($post);
        if(false === $weather = $this->get_weather_datas($event)){
            return '';
        }
        if(false == $weather['data']){
            return '';
        }

        $string='';

        switch ($weather['type']){
            case 'current':
                $string.=$this->get_weather_item($weather['data']);
                break;
            case 'history':
                if(!isset($weather['data']->list) || !is_array($weather['data']->list)){
                    break;
                }
                foreach($weather['data']->list as $day){
                    if($day->dt >= $event->time_start && $day->dt <= $event->time_end){
                        $string.=$this->get_weather_item($day);
                    }
                }
                break;
            case 'forecast':
                if(!is_array($weather['data']->list)){
                    break;
                }
                foreach($weather['data']->list as $day){
                    if($day->dt >= $event->time_start && $day->dt <= $event->time_end){
                        $string.=$this->get_weather_item($day);
                    }
                }
                break;
        }
        if($echo){
            echo $string;
        }
        return $string;
    }

    /**
     *
     * @param type $event
     * @return type
     */
    function get_weather_datas($event){
        if (!$event->lat || !$event->long || !is_numeric($event->time_start) || !is_numeric($event->time_end)) {
            return false;
        }

        $now = current_time('timestamp');
        $local_weather = $event->weather;



        // Datas are allready stored, we probably won't get better ones.
        if(is_array($local_weather) && $local_weather['data'] && ($local_weather['type']=='current' || $local_weather['type']=='history')){
            return $local_weather;
        }

        // Finally, we have to fetch datas...
        $weather = array('type'=>false, 'data'=>false);

        // For Current and history results, we definitly store datas
        if ( $event->time_start<= $now && $event->time_end>=$now) {
            $weather = array('type'=>'current', 'data'=>$this->get_current($event), 'fetched'=>time());
            update_post_meta($event->ID, $this->META_WEATHER, $weather);
        }
        elseif ( $event->time_end<$now) {// History
            $weather = array('type'=>'history', 'data'=>$this->get_history($event), 'fetched'=>time());
            update_post_meta($event->ID, $this->META_WEATHER, $weather);
        }
        else {
            // Forecast datas are only stored in cache for 24 hours
            $transient_name = 'eventpost_weather_' . $event->ID;
            $weather = get_transient($transient_name);
            if (false === $weather || empty($weather)) {
                $weather = array('type'=>'forecast', 'data'=>$this->get_forecast($event), 'fetched'=>time());
                set_transient($transient_name, $weather, 1 * DAY_IN_SECONDS);
            }
        }
        return $weather;
    }


    /**
     * Generates the URL to call the API
     * @param type $method
     * @param type $params
     * @return type
     */
    function get_url($method = 'weather', $params = array()) {
        $params['APPID']= $this->api_key;
        $params['units']= $this->units;
        return 'http://api.openweathermap.org/data/2.5/' . $method . '?' . http_build_query($params);
    }

    /**
     * ### current
     * http://api.openweathermap.org/data/2.5/weather?lat={lat}&lon={lon}&APPID=XXXX
     *
     * return:
     *
        {"coord":{"lon":139,"lat":35},
        "sys":{"country":"JP","sunrise":1369769524,"sunset":1369821049},
        "weather":[{"id":804,"main":"clouds","description":"overcast clouds","icon":"04n"}],
        "main":{"temp":289.5,"humidity":89,"pressure":1013,"temp_min":287.04,"temp_max":292.04},
        "wind":{"speed":7.31,"deg":187.002},
        "rain":{"3h":0},
        "clouds":{"all":92},
        "dt":1369824698,
        "id":1851632,
        "name":"Shuzenji",
        "cod":200}
     *
     * @param type $event
     * @return object
     */
    function get_current($event){
        if (!$event->lat || !$event->long) {
            return;
        }
        return json_decode(wp_remote_retrieve_body(
                wp_remote_get(
                        $this-> get_url('weather', array(
                            'lat' => $event->lat,
                            'lon' => $event->long,
                        ))
                )
        ));
    }

    /**
     * ### forecast
     * api.openweathermap.org/data/2.5/forecast?lat={lat}&lon={lon}&APPID=XXXX
     *
     * return:
     *
        {"city":{"id":1851632,"name":"Shuzenji",
        "coord":{"lon":138.933334,"lat":34.966671},
        "country":"JP",
        "cod":"200",
        "message":0.0045,
        "cnt":38,
        "list":[{
        "dt":1406106000,
        "main":{
        "temp":298.77,
        "temp_min":298.77,
        "temp_max":298.774,
        "pressure":1005.93,
        "sea_level":1018.18,
        "grnd_level":1005.93,
        "humidity":87
        "temp_kf":0.26},
        "weather":[{"id":804,"main":"Clouds","description":"overcast clouds","icon":"04d"}],
        "clouds":{"all":88},
        "wind":{"speed":5.71,"deg":229.501},
        "sys":{"pod":"d"},
        "dt_txt":"2014-07-23 09:00:00"}
        ]}
     *
     * @param type $event
     * @return type
     */
    function get_forecast($event){
        if (!$event->lat || !$event->long) {
            return;
        }
        return json_decode(wp_remote_retrieve_body(
                wp_remote_get(
                        $this-> get_url('forecast', array(
                            'lat' => $event->lat,
                            'lon' => $event->long,
                        ))
                )
        ));
    }

    /**
     * ### history
     * http://api.openweathermap.org/data/2.5/history/city?lat={lat}&lon={lon}&type=hour&start={start}&end={end}&APPID=XXXX
     *
     *   Parameters:
     *   lat, lon coordinates of the location of your interest
     *   type type of the call, keep this parameter in the API call as 'hour'
     *   start start date (unix time, UTC time zone), e.g. start=1369728000
     *   end end date (unix time, UTC time zone), e.g. end=1369789200
     *   cnt amount of returned data (one per hour, can be used instead of 'end') *
     *
     * return:
      {"message":"","cod":"200","type":"tick","station_id":39419,"cnt":30,
      "list":[
      {"dt":1345291920,
      "main":{"temp":291.55,"humidity":95,"pressure":1009.3},
      "wind":{"speed":0,"gust":0.3},
      "rain":{"1h":0.6,"today":2.7},
      "calc":{"dewpoint":17.6} }
      ]}
     *
     *
     * @param type $event
     * @return type
     */
    function get_history($event) {
        if (!$event->start || !$event->end || !$event->lat || !$event->long) {
            return;
        }
        $history = json_decode(wp_remote_retrieve_body(
                wp_remote_get(
                        $this-> get_url('history/city', array(
                            'lat' => $event->lat,
                            'lon' => $event->long,
                            'start' => $event->time_start,
                            'end' => $event->time_end,
                            'type' => 'hour',
                        ))
                )
        ));
        return $history ? $history : (object) array('result'=>false);
    }

}
