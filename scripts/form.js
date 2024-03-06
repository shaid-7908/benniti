function updateStepIndicators(currentStep) {
  $(".step-indicator").removeClass("active"); // Remove active class from all step indicators
  $(".step-number").removeClass("active"); // Remove active class from all step numbers
  $(".step-indicators")
    .find(".step-indicator:nth-child(" + currentStep + ")")
    .addClass("active"); // Add active class to current step indicator
    $(".step-indicator:nth-child(" + currentStep + ") .step-number").addClass("active");  // Add active class to corresponding step number

}
$(document).ready(function () {
  var form_count = 1,
    previous_form,
    next_form,
    total_forms;
  total_forms = $("fieldset").length;
  $(".next-form").click(function () {
    previous_form = $(this).parent();
    next_form = $(this).parent().next();
    next_form.show();
    previous_form.hide();
    setProgressBarValue(++form_count);
    updateStepIndicators(form_count);
    window.scrollTo({
      top: 0,
      behavior: "smooth", // Smooth scrolling
    });
  });
  $(".previous-form").click(function () {
    previous_form = $(this).parent();
    next_form = $(this).parent().prev();
    next_form.show();
    previous_form.hide();
    setProgressBarValue(--form_count);
    updateStepIndicators(form_count);
    window.scrollTo({
      top: 0,
      behavior: "smooth", // Smooth scrolling
    });
  });
  setProgressBarValue(form_count);
  function setProgressBarValue(value) {
    var percent = parseFloat(100 / total_forms) * value;
    percent = percent.toFixed();
    $(".progress-bar")
      .css("width", percent + "%");
  }
  // Handle form submit and validation
});
