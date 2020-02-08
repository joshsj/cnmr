<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/styles/main.css">
  <title><?= (isset($title) ? "$title :: " : "") . "CNMR" ?></title>
</head>

<body>
  <?= $content; // template content
  ?>
</body>

</html>