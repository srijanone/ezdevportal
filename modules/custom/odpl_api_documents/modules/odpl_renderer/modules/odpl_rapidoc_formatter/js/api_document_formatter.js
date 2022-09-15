/**
 * @file
 * API Formatter select javascript behaviours
 */
(function ($, Drupal) {
  Drupal.behaviors.apiFormatterBehavior = {
    attach: function (context, settings) {
      let currLoc = $(location).attr('href');
      let checkHash = currLoc.split("#");
      if (checkHash[1] == null) {
        $('.opblock-summary-path').first().trigger('click');
      }
    }
  };
})(jQuery, Drupal);
