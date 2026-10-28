/* Slider Backend: Slides hinzufügen/entfernen, Mediathek, Artikelsuche (Name oder Artikelnummer) */
jQuery(function ($) {
  var $box = $('#mgd-slides');
  $('#mgd-add-slide').on('click', function () { $box.append($('#mgd-slide-tpl').html()); });
  $box.on('click', '.mgd-del', function () { $(this).closest('.mgd-slide-row').remove(); });
  $box.on('click', '.mgd-pick', function () {
    var $row = $(this).closest('.mgd-slide-row');
    var frame = wp.media({ library: { type: 'image' }, multiple: false });
    frame.on('select', function () {
      var a = frame.state().get('selection').first().toJSON();
      $row.find('input[name="mgd_slides[image][]"]').val(a.url);
      if (!$row.find('input[name="mgd_slides[alt][]"]').val()) { $row.find('input[name="mgd_slides[alt][]"]').val(a.alt || ''); }
    });
    frame.open();
  });

  // Artikelsuche
  var t;
  $('#mgd-product-search').on('input', function () {
    var q = this.value, $hits = $('#mgd-product-hits');
    clearTimeout(t);
    if (q.length < 2) { $hits.empty(); return; }
    t = setTimeout(function () {
      $.getJSON(MGDSliderAdmin.ajax, { action: 'mgd_slider_product_search', nonce: MGDSliderAdmin.nonce, q: q }, function (r) {
        $hits.empty();
        (r.data || []).forEach(function (p) {
          $('<button type="button" class="button button-small" style="margin:2px">')
            .text(p.name + (p.sku ? ' (' + p.sku + ')' : '') + ' +')
            .on('click', function () {
              var $f = $('#mgd-products'), cur = $f.val().split(/[\s,;]+/).filter(Boolean);
              if (cur.indexOf(p.id) < 0) { cur.push(p.id); }
              $f.val(cur.join(', '));
            }).appendTo($hits);
        });
      });
    }, 250);
  });
});
