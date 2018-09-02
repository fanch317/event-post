<form class="eventpost-search-form" id="eventpost-search-form-<?php echo $list_id; ?>">
    <input type="hidden" name="evenpost_search" value="<?php echo $list_id; ?>">
    <?php if($params['q']) : ?>
    <div class="eventpost-search-group">
        <label for="eventpost-search-label-<?php echo $list_id; ?>">
            <?php _e('Name or keywords', 'event-post'); ?>
        </label>
        <input id="eventpost-search-label-<?php echo $list_id; ?>" type="search" name="q" value="<?php echo esc_attr($q); ?>"/>
    </div>
    <?php endif; ?>

    <?php if($params['dates']) : ?>
    <div class="eventpost-search-group">
        <label for="eventpost-search-from-<?php echo $list_id; ?>">
            <?php _e('Between:', 'event-post'); ?>
        </label>
        <input id="eventpost-search-from-<?php echo $list_id; ?>" type="text" name="from" value="<?php echo esc_attr($from); ?>" class="eventpost-datepicker-simple"/>

        <label for="eventpost-search-to-<?php echo $list_id; ?>">
            <?php _e('to:', 'event-post'); ?>
        </label>
        <input id="eventpost-search-from-<?php echo $list_id; ?>" type="text" name="to" value="<?php echo esc_attr($to); ?>" class="eventpost-datepicker-simple"/>
    </div>
    <?php endif; ?>

    <?php if($params['tax']) : ?>
    <label for="eventpost-search-tax-<?php echo $list_id; ?>">
        <?php _e('In:', 'event-post'); ?>
    </label>
    <select id="eventpost-search-tax-<?php echo $list_id; ?>" name="tax">
        <option value=""></option>
    </select>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit">
	<?php _e('Find events', 'event-post'); ?>
    </button>
</form>
