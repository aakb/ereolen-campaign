(function ($, Drupal) {
  Drupal.behaviors.ereolenCampaign = {
    attach: function (context, settings) {
      var iframe = $("#player");
      $(".js-open-frame").click(function () {
        var link_src = $(this).attr('data-src');
        iframe.attr("src", link_src);
        $(".js-frame").toggleClass('is-visible');
        ga('send', 'event', 'Audio', 'play', link_src);
      });

      $(".js-frame-close").click(function () {
        $(".js-frame").toggleClass('is-visible');
        iframe.attr("src", "");
      });
    }
  }
})(jQuery, Drupal);