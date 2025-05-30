// Review Form
jQuery(document).ready(function ($) {
  $("#rs-review-form").on("submit", function (e) {
    e.preventDefault();

    var formData = $(this).serializeArray();
    formData.push({ name: "action", value: "rs_submit_review" });
    formData.push({ name: "nonce", value: rsData.nonce });

    $.post(rsData.ajax_url, formData, function (response) {
      const msg = $("#rs-review-message");
      msg.html(response.data.message);
      msg.css("color", response.success ? "green" : "red");
      if (response.success) {
        $("#rs-review-form")[0].reset();
      }
    });
  });
});

// Star Rating
jQuery(document).ready(function ($) {
  $(".star-rating").each(function () {
    const $this = $(this);
    const $rating = $this.find("input[type='radio']");
    const $ratingText = $this.find(".rating-text");
    const max_stars = $this.find("input[type='radio']:first").val();
    $rating.on("change", function () {
      $ratingText.text($(this).val());
      // text based on percentage (poor, good, excellent)
      const percentage = ($ratingText.text() / max_stars) * 100;
      if (percentage < 50) {
        $ratingText.html(
          `<span class="poor">${$ratingText.text()}/${max_stars} Poor</span>`
        );
      } else if (percentage < 75) {
        $ratingText.html(
          `<span class="good">${$ratingText.text()}/${max_stars} Good</span>`
        );
      } else {
        $ratingText.html(
          `<span class="excellent">${$ratingText.text()}/${max_stars} Excellent</span>`
        );
      }
    });
  });
});

// Character Counter
jQuery(document).ready(function ($) {
  const $commentInput = $("#rs_comment");
  const $counter = $("#remaining_chars");
  const max = $commentInput.attr("maxlength");

  if ($commentInput.length && $counter.length) {
    $commentInput.on("input", function () {
      const remaining = max - $(this).val().length;
      $counter.text(remaining + " characters remaining");
    });
  }
});
