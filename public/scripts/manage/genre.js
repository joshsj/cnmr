document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("btnNew").addEventListener("click", function() {
    var newGenre = window.prompt("Enter a new genre name");
  });

  // add listener to each button
  var bcs = document.getElementsByClassName("btnChange");
  for (var i = 0; i < bcs.length; ++i) {
    var bc = bcs[i];

    bc.addEventListener("click", function() {
      var newName = window.prompt("Enter the new genre name");
    });
  }

  var bds = document.getElementsByClassName("btnDelete");
  for (var i = 0; i < bds.length; ++i) {
    var bd = bds[i];

    bd.addEventListener("click", function() {
      if (
        confirm(
          "Are you sure?\n" +
            "Deleting a genre will also delete it from all films"
        )
      ) {
      }
    });
  }
});
