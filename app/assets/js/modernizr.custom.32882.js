/*!
 * Modernizr v3.12.0
 * https://modernizr.com/
 * MIT License
 */
(function(window, document, undefined){
  var Modernizr = {};

  // Test for input attributes
  Modernizr.input = (function(props) {
    var inputElem = document.createElement('input');
    var attrs = {};
    for (var i = 0, len = props.length; i < len; i++) {
      attrs[props[i]] = props[i] in inputElem;
    }
    // Special case for datalist
    attrs.list = !!(document.createElement('datalist') && window.HTMLDataListElement);
    return attrs;
  })([
    "autocomplete", "autofocus", "list", "placeholder", "max", "min",
    "multiple", "pattern", "required", "step"
  ]);

  // Add a test for input min/max support (for number fields)
  Modernizr.input.min = (function() {
    var input = document.createElement('input');
    input.setAttribute('type', 'number');
    input.setAttribute('min', '1');
    input.value = '0';
    return (input.value === '0');
  })();

  Modernizr.input.max = (function() {
    var input = document.createElement('input');
    input.setAttribute('type', 'number');
    input.setAttribute('max', '10');
    input.value = '11';
    return (input.value === '11');
  })();

  // Expose Modernizr globally
  window.Modernizr = Modernizr;
})(window, document);