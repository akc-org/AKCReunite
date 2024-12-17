<?php
?>
<!-- Modal HTML embedded directly into document -->
<div id="wpgae-add-scroll-event" class="modal">
	<h2>Add Scroll Event</h2>
	<form id="wpgae-add-event" class="wpgae-event-form">
		<div>
			<div class="postbox wpga_meta">
				<div class="inside">
					<div class="wpgae_event_form_fields">
						<label for="_wpgae_scroll_selector_meta_key">Selector</label>
						<input name="_wpgae_scroll_selector_meta_key" required id="_wpgae_scroll_selector_meta_key" class="postbox" value="">
					</div>
					<div class="wpgae_event_form_fields">
						<label for="_wpgae_scroll_type_meta_key">Selector Type</label>
						<select name="_wpgae_scroll_type_meta_key" id="_wpgae_scroll_type_meta_key">
							<option value="id">id</option>
							<option value="class">class</option>
							<?php if (isset($wpgae_options['advanced']) && $wpgae_options["advanced"] == 1) {
              ?>
              <option value="advanced">advanced</option> ?>
              <?php } ?>
            </select>
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_category_meta_key">Event Category</label>
            <input name="_wpgae_scroll_category_meta_key" id="_wpgae_scroll_category_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_action_meta_key" >Event Action</label>
            <input name="_wpgae_scroll_action_meta_key"  id="_wpgae_scroll_action_meta_key"  class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_label_meta_key">Event Label</label>
            <input name="_wpgae_scroll_label_meta_key" id="_wpgae_scroll_label_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_value_meta_key">Event Value</label>
            <input name="_wpgae_scroll_value_meta_key" id="_wpgae_scroll_value_meta_key" class="postbox" type="number" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_bounce_meta_key">Non-Interaction</label>
            <select name="_wpgae_scroll_bounce_meta_key" id="_wpgae_scroll_bounce_meta_key">
              <option value="true" selected="true">true</option>
              <option value="false">false</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div id="wpgae_form_buttons">
      <?php wp_nonce_field("wpflow_add_event", "ajax_nonce"); ?>
      <input type="hidden" name="action" value="wpflow_add_event">
      <input type="hidden" name="wpgae_type" value="wpgae_scroll_event">
      <a class="button-secondary" href="#" rel="modal:close">Cancel</a>
      <input type="submit" class="button-primary" value="Add Scroll Event">
    </div>
  </form>
</div>


<!-- Modal HTML embedded directly into document -->
<div id="wpgae-edit-scroll-event" class="modal">
  <h2>Edit Scroll Event</h2>
  <form id="wpgae-edit-event" class="wpgae-event-form">
    <div>
      <div class="wpga_meta postbox ">
        <div class="inside">
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_selector_meta_key">Selector</label>
            <input name="_wpgae_scroll_selector_meta_key" required id="_wpgae_scroll_selector_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_type_meta_key">Selector Type</label>
            <select name="_wpgae_scroll_type_meta_key" id="_wpgae_scroll_type_meta_key">
              <option value="id">id</option>
              <option value="class">class</option>
              <?php if (isset($wpgae_options['advanced']) && $wpgae_options["advanced"] == 1) {
                ?>
                <option value="advanced">advanced</option>
              <?php } ?>
            </select>
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_category_meta_key">Event Category</label>
            <input name="_wpgae_scroll_category_meta_key" id="_wpgae_scroll_category_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_action_meta_key" >Event Action</label>
            <input name="_wpgae_scroll_action_meta_key"  id="_wpgae_scroll_action_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_label_meta_key">Event Label</label>
            <input name="_wpgae_scroll_label_meta_key" id="_wpgae_scroll_label_meta_key" class="postbox" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_value_meta_key">Event Value</label>
            <input name="_wpgae_scroll_value_meta_key" id="_wpgae_scroll_value_meta_key" class="postbox" type="number" value="">
          </div>
          <div class="wpgae_event_form_fields">
            <label for="_wpgae_scroll_bounce_meta_key">Non-Interaction</label>
            <select name="_wpgae_scroll_bounce_meta_key" id="_wpgae_scroll_bounce_meta_key">
              <option value="true" selected="true">true</option>
              <option value="false">false</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div id="wpgae_form_buttons">
      <?php wp_nonce_field("wpflow_add_event", "ajax_nonce"); ?>
      <input type="hidden" name="action" value="wpflow_edit_event">
      <input type="hidden" name="event_id" id="event_id" value="">
      <a class="button-secondary" href="#" rel="modal:close">Cancel</a>
      <input type="submit" class="button-primary" value="Save Scroll Event">
    </div>
  </form>
</div>

<!-- Modal HTML embedded directly into document -->
<div id="wpgae-delete-scroll-event" class="modal">
  <h2>You are about to delete a Scroll Event</h2>
  <form id="wpgae-delete-event" class="wpgae-event-form">
    <div>
      <div class="wpga_meta postbox ">
        <div class="inside">
          <fieldset disabled>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_selector_meta_key">Selector</label>
              <input name="_wpgae_scroll_selector_meta_key" id="_wpgae_scroll_selector_meta_key" class="postbox" value="">
            </div>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_type_meta_key">Selector Type</label>
              <select name="_wpgae_scroll_type_meta_key" id="_wpgae_scroll_type_meta_key">
                <option value="id">id</option>
                <option value="class">class</option>
                <?php if (isset($wpgae_options['advanced']) && $wpgae_options["advanced"] == 1) {
                  ?>
                  <option value="advanced">advanced</option>
                <?php } ?>
              </select>
            </div>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_category_meta_key">Event Category</label>
              <input name="_wpgae_scroll_category_meta_key" id="_wpgae_scroll_category_meta_key" class="postbox" value="">
            </div>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_action_meta_key" >Event Action</label>
              <input name="_wpgae_scroll_action_meta_key"  id="_wpgae_scroll_action_meta_key" class="postbox" value="">
            </div>
            <div class="wpgae_event_form_fields">
              <label for="wpgae_event_label">Event Label</label>
              <input name="wpgae_event_label" id="_wpgae_scroll_label_meta_key" class="postbox" value="">
            </div>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_value_meta_key">Event Value</label>
              <input name="_wpgae_scroll_value_meta_key" id="_wpgae_click_value_meta_key" class="postbox" type="number" value="">
            </div>
            <div class="wpgae_event_form_fields">
              <label for="_wpgae_scroll_bounce_meta_key">Non-Interaction</label>
              <select name="_wpgae_scroll_bounce_meta_key" id="_wpgae_scroll_bounce_meta_key">
                <option value="true" selected="true">true</option>
                <option  value="false">false</option>
              </select>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    <div id="wpgae_form_buttons">
      <?php wp_nonce_field("wpflow_add_event", "ajax_nonce"); ?>
      <input type="hidden" name="action" value="wpflow_delete_event">
      <input type="hidden" name="event_id" id="event_id" value="">
      <input type="submit" class="button button-large butotn-secondary delete" value="Delete Scroll Event">
      <a class="button button-secondary button-large" href="#" rel="modal:close">Cancel</a>
    </div>
  </form>
</div>

