document.addEventListener("DOMContentLoaded", function() {
  var selectWhen = document.getElementById("select-when");
  var selectWho = document.getElementById("select-who"); // when cinema chosen

  document
    .getElementById("select-where")
    .addEventListener("change", function(e) {
      selectWhen.style.display = ""; // reveal
      // clear current times except placeholder

      var screeningOpts = document.getElementById("screening-options");
      var options = screeningOpts.children; // leave 'choose time' placeholder

      for (var i = 1; i < options.length; ++i) {
        screeningOpts.removeChild(options[i]);
      } // get screenings for cinema

      var screenings = JSON.parse(
        e.target.selectedOptions[0].getAttribute("data-screenings")
      ); // add options

      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (
          var _iterator = screenings[Symbol.iterator](), _step;
          !(_iteratorNormalCompletion = (_step = _iterator.next()).done);
          _iteratorNormalCompletion = true
        ) {
          var screening = _step.value;
          // create elements
          var opt = document.createElement("option");
          opt.setAttribute("value", screening.id);
          opt.textContent = screening.start;
          screeningOpts.appendChild(opt);
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator.return != null) {
            _iterator.return();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }
    }); // when time chosen

  selectWhen.addEventListener("change", function() {
    selectWho.style.display = "";
  });
});
