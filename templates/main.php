<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/styles/main.css">
  <title><?= (isset($title) ? "$title :: " : "") . "CNMR" ?></title>
</head>

<body>
  <div id="header-container">
    <header>
      <h1><a href="/home">CNMR</a></h1>
      <nav>
        <ul>
          <li>
            <h2><a>About</a></h2>
          </li>
          <li>
            <h2><a href="/films">Films</a></h2>
          </li>
          <li>
            <h2><a href="/cinemas">Cinemas</a></h2>
          </li>
        </ul>
      </nav>
    </header>
  </div>

  <main>
    <?= $content ?>
  </main>
</body>

</html>