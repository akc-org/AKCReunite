/*global
 $, wpflow_ajax, jslint, alert
 */
var gaeAjax = (function ($) {
  $(document).ready(function ($) {
    // Handle twitter bootstrap modals
    if (typeof $.fn.modal.noConflict !== "undefined") {
      var bootstrapModal = $.fn.modal.noConflict();
    }

    // Form Submit
    $(".wpgae-event-form").on("submit", submitEventForm);
    // Populate and Show the edit event modal
    $(".ga_main .edit a").on("click", openAndPoplulateEventModal);
    // Populate and Show the Delete event modal
    $(".ga_main .delete a").on("click", openAndPoplulateEventModal);
    $("#ga-save-view").on("click", saveGAView);
    $(".btn_signout_ga").on("click", signOutFromGoogleAnalytics);

    $("#wpgae-feedback-form").on("submit", function (e) {
      e.preventDefault(); // avoid to execute the actual submit of the form.
      var form = $(this);

      $.ajax({
        type: "POST",
        url: wpflow_ajax.ajax_url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
          window.location = $("#wpgae-just-deactivate").attr("href");
        },
      });
    });
  });

  function openAndPoplulateEventModal(e) {
    e.preventDefault();
    var id_post = $(this).attr("id");
    var modalId = "#" + $(this).data("action");
    $.ajax({
      type: "POST",
      url: wpflow_ajax.ajax_url,
      data: {
        post_id: id_post,
        action: "wpflow_get_event_json",
      },
      success: function (result) {
        $(modalId).modal();
        populateMetaEditForm(modalId, result.meta);
        $(modalId + " #event_id").val(id_post);
      },
      error: function () {
        alert("Error updating event");
      },
    });
  }

  function saveGAView(e) {
    var nonce = $(this).data("nonce");
    $.ajax({
      type: "post",
      url: wpflow_ajax.ajax_url,
      data: {
        viewId: $(this).val(),
        action: "wpflow_save_view",
        ajax_nonce: nonce,
      },
      success: function (data) {
        window.location.reload();
      },
    });
  }

  function signOutFromGoogleAnalytics(e) {
    e.preventDefault();
    var nonce = $(this).data("delete-nonce");
    $.ajax({
      type: "post",
      url: wpflow_ajax.ajax_url,
      data: {
        viewId: $(this).val(),
        action: "wpflow_ga_disconnect",
        ajax_nonce: nonce,
      },
      success: function (data) {
        var redirect_url = btoa(
          window.location.protocol +
            "//" +
            window.location.host +
            window.location.pathname +
            window.location.search
        );
        var accessToken = btoa(sessionStorage.getItem("gaAccessToken"));
        sessionStorage.removeItem("gaAccessToken");
        sessionStorage.removeItem("gaTokenCreated");
        // var encodedRedirectUrl = encodeURIComponent(redirect_url);
        // var encodedAccessToken = encodeURIComponent(accessToken);
        window.location =
          "https://auth.wpflow.com/ga/sign_out/?redirectUrl=" +
          redirect_url +
          "&accessToken=" +
          accessToken;
      },
    });
  }

  function submitEventForm(e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
      type: "post",
      url: wpflow_ajax.ajax_url,
      data: form.serialize(),
      success: function (data) {
        window.location.reload();
      },
    });
  }

  function populateMetaEditForm(modal, meta) {
    if (typeof meta !== "undefined") {
      for (var input in meta) {
        if (meta.hasOwnProperty(input)) {
          if ($(modal + " #" + input).is(":checkbox")) {
            if (meta[input][0] === "true") {
              $(modal + " #" + input).attr("checked", true);
            } else {
              $(modal + " #" + input).removeAttr("checked", false);
            }
          } else {
            $(modal + " #" + input).val(meta[input][0]);
          }
        }
      }
    }
  }
})(jQuery);
