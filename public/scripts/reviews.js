document.addEventListener("DOMContentLoaded", function() {
  // get film id
  var tmdb_id = document.getElementById("film").getAttribute("data-tmdb-id");
  var req = new XMLHttpRequest();
  req.open("GET", "/api/reviews?id=" + tmdb_id, true);
  req.send();
  req.addEventListener("load", function() {
    // parse data
    var data = JSON.parse(req.responseText); // insert into page

    document.getElementById("score").textContent = data.score;
    document.getElementById("count").textContent = data.count; // remove style hiding element

    document.getElementById("reviews").style.display = null; // add link to tmdb reviews page

    document
      .getElementById("reviews-link")
      .setAttribute(
        "href",
        "https://www.themoviedb.org/movie/" + tmdb_id + "/reviews"
      );
  });
});
