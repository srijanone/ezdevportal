/**
 * @file
 * Voyager rendering.
 */

(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.nodeFieldDocument = {
    attach: function attach(context, settings) {
      let file_url = drupalSettings.ezdevportal_voyager.document;
      $.getJSON(file_url, function (data) {
        GraphQLVoyager.init(document.getElementById('voyager'), {
          introspection: data
        });
      }).fail(function () {
        $('#voyager').html('Something went wrong. Please check logs or validate the schema.');
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
