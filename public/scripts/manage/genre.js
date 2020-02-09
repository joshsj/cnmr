// lord forgive the var, bless the IE9 support
document.addEventListener("DOMContentLoaded", function() {
  // add listener to each delete button
  var bds = document.getElementsByClassName("btnDelete");
  for (var i = 0; i < bds.length; ++i) {
    var bd = bds[i];

    bd.addEventListener("click", function(e) {
      if (
        confirm(
          "Are you sure?\n" +
            "Deleting a genre will also remove it from all films"
        )
      ) {
        // get genre ID
        var id = console.log(e.target.getAttribute("for"));
      }
    });
  }
});
