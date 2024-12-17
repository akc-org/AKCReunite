<?php
?>
<div id="wpgae-modal-content">
  <div id="wpgae-modal-cancel"><a href>Cancel</a></div>
  <h2>Sorry to see you go. How can we do better?</h2>
  <form id="wpgae-feedback-form" class="wpgae-feedback-modal">
    <div>
      <div>
        <div class="inside">
          <fieldset>
            <div class="wpgae_event_form_fields">
              <input name="feedback" value="I went with a different plugin" type="radio" />
              <label for="feedback">I went with a different plugin.</label>
            </div>
            <div class="wpgae_event_form_fields">
              <input name="feedback" value="No support for my use case" type="radio" />
              <label for="feedback">No support for my use case.</label>
            </div>
            <div class="wpgae_event_form_fields">
              <input name="feedback" value="I'm just doing maintenance work" type="radio" />
              <label for="feedback">I'm just doing maintenance work</label>
            </div>
            <div class="wpgae_event_form_fields">
              <input name="feedback" value="I couldn't get the plugin to work" type="radio" />
              <label for="feedback">I couldn't get the plugin to work</label>
            </div>
            <div class="wpgae_event_form_fields">
              <input name="feedback" value="I need your help with" type="radio" checked />
              <label for="feedback">I need your help with:</label>
            </div>
            <div class="wpgae_event_form_fields">
              <textarea name="helpme-text" rows="2" cols="40" style="margin-left: 22px; resize: none;"/></textarea>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
    <div id="wpgae_form_buttons">
      <?php wp_nonce_field("wpflow_add_event", "ajax_nonce"); ?>
      <input type="hidden" name="action" value="wpflow_deactivate_event">
      <input type="submit" class="button button-large button-primary delete" value="Send Feedback and Deactivate">
      <a id="wpgae-just-deactivate" class="button button-secondary button-large" href="#" rel="modal:close">Just Deactivate</a>
    </div>
  </form>
</div>


<style>
  #wpgae-modal-cancel {
    right: 15px;
    position: absolute;
  }
  #wpgae_form_buttons {
    margin-top: 20px;
  }
  #wpgae-feedback-form {
    margin-left: 10px;
  }

  #wpgae-modal-content h2 {
    margin-left: 10px;
  }

  #wpgae-modal-content {
    background-color: white;
    border-radius: 10px;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    box-shadow: 0 0 20px 0 #222;
    -webkit-box-shadow: 0 0 20px 0 #222;
    -moz-box-shadow: 0 0 20px 0 #222;
    display: none;
    height: 300px;
    left: 50%;
    margin: -120px 0 0 -160px;
    padding: 10px;
    position: fixed;
    top: 40%;
    width: 550px;
    z-index: 1000;
    line-height: 22px;
    vertical-align: middle;
    font-size: 14px;
  }

  #wpgae-modal-content.active {
    display: block;
  }​
</style>

<script>jQuery(function(){


  });</script>