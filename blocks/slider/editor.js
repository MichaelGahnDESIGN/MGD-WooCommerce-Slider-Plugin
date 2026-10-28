/* Block Editor: Slider wählen (Liste kommt per wp_localize_script als MGDSliderList), ohne Build-Schritt */
(function (wp) {
  var el = wp.element.createElement;
  wp.blocks.registerBlockType('mgd/slider', {
    edit: function (props) {
      var list = (window.MGDSliderList || []);
      var opts = [{ label: '- Slider wählen -', value: 0 }].concat(list.map(function (s) { return { label: s.title, value: s.id }; }));
      return el('div', wp.blockEditor.useBlockProps({ className: 'mgd-slider-block-editor' }),
        el(wp.components.SelectControl, {
          label: 'MGD Slider', value: props.attributes.sliderId, options: opts,
          onChange: function (v) { props.setAttributes({ sliderId: parseInt(v, 10) || 0 }); }
        }));
    },
    save: function () { return null; }
  });
})(window.wp);
