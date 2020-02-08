<?php foreach ($films as $film) : ?>
  <section>
    <h1><?= $film->title ?></h1>
    <p><?= $film->description ?></p>
    <p><b>Certificate </b><?= $film->certificate ?></p>
    <p><b>Released </b><?= $film->released ?></p>
    <p><b>Runtime </b><?= $film->runtime ?> minutes</p>
    <p><b>Director </b><?= $film->director ?></p>
    <p><b>Genres </b><?= implode(", ", $film->genres) ?></p>
  </section>
<?php endforeach ?>