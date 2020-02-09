<section>
  <h1><?= $film->title . ", " . explode("-", $film->released)[0] ?></h1>
  <p><b>Certificate: </b><?= $film->certificate ?></p>
  <p><b>Released: </b><?= $film->released ?></p>
  <p><b>Runtime: </b><?= $film->runtime ?> minutes</p>
  <p><b>Director: </b><?= $film->director ?></p>
  <p><b>Genres: </b><?= implode(", ", $film->genres) ?></p>
  <p><b>Description: </b><?= $film->description ?></p>
</section>