<?php foreach ($films as $film) : ?>
  <section>
    <h1><?= $film->title ?></h1>
    <p><?= $film->description ?></p>
    <p><b><a href="/films/<?= $film->id ?>">See more</a></b></p>
  </section>
<?php endforeach ?>