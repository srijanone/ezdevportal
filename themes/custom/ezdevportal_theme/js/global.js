/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {
  'use strict';
   Drupal.behaviors.filter_dropdown = {
    attach: function (context, settings) {
     
      jQuery('.field--name-field-filter-options .field__items').each(function () {
        var list = jQuery(this), select = jQuery(document.createElement('select')).insertBefore(jQuery(this).hide());
        jQuery('>.field__item a', this).each(function () {
            var target = jQuery(this).attr('target'),
            option = jQuery(document.createElement('option'))
                .appendTo(select)
                .val(this.href)
                .html(jQuery(this).html())
                .click(function () {
                    if(target === '_blank') {
                        window.open($(this).val());
                    }
                    else {
                        window.location.href = jQuery(this).val();
                    }
                });

                if(window.location.href == option.val()){
                  option.attr('selected','selected');
                }
        });
        list.remove();
        select.change(function () {
          window.location = $(this).find("option:selected").val();
          });
      });
    }
  };
  Drupal.behaviors.odp = {
    attach: function (context, settings) {
      jQuery('li.nav-link--search-page a').addClass('search');
      jQuery('.views-field-field-blog-tags  ul').removeClass('list-group');
      jQuery('.views-field-field-blog-tags  ul li').addClass("btn btn-outline-secondary ");
      jQuery('.views-field-field-blog-tags  ul li').removeClass("list-group-item");
      jQuery('#views-exposed-form-blog-block-all-blogs .form-select ul li').addClass('btn btn-outline-secondary'); 
      jQuery('.block-views-blockblog-block-all-blogs  > .content > .container').addClass('g-0');
      jQuery('.block-product-content-block  > .content > .container').addClass('g-0');
      jQuery('.view-forum .tags > div').removeClass('item-list');
      jQuery('.view-forum .tags > div > ul ').removeClass('list-group');
      jQuery('.view-forum .tags > div > ul > li').removeClass('list-group-item');
      jQuery('.block-views-blockforum-forum-topic-list > div > div').removeClass('container');
      jQuery('.block-extra-field-blocknodeforumlinks .statistics-counter').removeClass('nav-link');
      jQuery('.block-extra-field-blocknodeforumlinks .comment-add').addClass('button');
      jQuery('#views-exposed-form-media-library-page-1 .bef-links').removeClass('form-select');
      jQuery('.select-all .form-checkbox').addClass('form-check-input'); 
      jQuery('.apigee-entity--app  fieldset.app-credential').removeClass('items--inline');
      jQuery('.btn-subscribe').removeClass('button');
      jQuery('.webform-submission-subscribe-form .form-actions').removeClass('mb-3');
    }
  };
  Drupal.behaviors.odplite_developerSlickConfig = {
    attach: function (context, settings) {
      $(".homepage-slider").slick({
        dots: true,
        autoplay:true,
        infinite: false,
        speed: 1000,
        slidesToShow: 1,
        centerPadding: '460px',
        adaptiveHeight: true,
        prevArrow: '',
        nextArrow: '',
        focusOnSelect: true,
        responsive: [
          {
            breakpoint: 1600,
            settings: {
              centerPadding: '300px',
            }
          },
          {
            breakpoint: 960,
            settings: {
              adaptiveHeight: true,
              centerPadding: '100px',
            }
          },
        ]
      });

    }
  };

})(jQuery, Drupal);
