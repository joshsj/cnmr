<?php foreach ($cinemas as $c) : ?>
  <section>
    <h2><?= (isset($c->area) ? $c->area . ", " : "") . $c->city ?></h2>

    <p><b>Facilites</b></p>
    <p><?= $c->shop ? "Shop" : "None" ?></p>

    <p><b>Find Us</b></p>
    <p><?= $c->area ?></p>
    <p><?= $c->address ?></p>
    <p><?= $c->city ?></p>
    <p><?= $c->postcode ?></p>
  </section>
<?php endforeach ?>