<?php
session_start();

// načtení a dekódování JSON dat
$json = @file_get_contents('profile.json');
$data = $json ? json_decode($json, true) : [];
$name = isset($data['name']) ? $data['name'] : 'Neznámý uživatel';
$skills = isset($data['skills']) && is_array($data['skills']) ? $data['skills'] : [];
$projects = isset($data['projects']) && is_array($data['projects']) ? $data['projects'] : [];

// připravíme proměnné pro notifikace ze session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['messageType']) ? $_SESSION['messageType'] : '';
unset($_SESSION['message'], $_SESSION['messageType']);

// zpracování formulářů pro přidání, odstranění nebo editaci zájmu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // přidání zájmu
    if (isset($_POST['new_interest'])) {
        $new = trim($_POST['new_interest']);
        if ($new === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            // porovnáme bez velikosti písmen
            $lowered = strtolower($new);
            $existing = array_map('strtolower', $projects);
            if (in_array($lowered, $existing, true)) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                // přidáme a uložíme zpět do JSON
                $projects[] = $new;
                $data['projects'] = $projects;
                file_put_contents('profile.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $_SESSION['message'] = 'Zájem byl úspěšně přidán.';
                $_SESSION['messageType'] = 'success';
            }
        }
        header("Location: index.php");
        exit;
    }
    // odstranění zájmu
    elseif (isset($_POST['remove_interest'])) {
        $index = (int)$_POST['remove_interest'];
        if (!isset($projects[$index])) {
            $_SESSION['message'] = 'Tento zájem nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        } else {
            unset($projects[$index]);
            // reindex pole
            $projects = array_values($projects);
            $data['projects'] = $projects;
            file_put_contents('profile.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $_SESSION['message'] = 'Zájem byl úspěšně odstraněn.';
            $_SESSION['messageType'] = 'success';
        }
        header("Location: index.php");
        exit;
    }
    // zahájení editace
    elseif (isset($_POST['edit_interest'])) {
        $index = (int)$_POST['edit_interest'];
        if (isset($projects[$index])) {
            $_SESSION['editing'] = $index;
        }
        header("Location: index.php");
        exit;
    }
    // uložení editace
    elseif (isset($_POST['save_edit'])) {
        $index = (int)$_POST['save_edit'];
        $new_value = trim($_POST['edited_interest']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } elseif (!isset($projects[$index])) {
            $_SESSION['message'] = 'Tento zájem nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        } else {
            // kontrola duplicit, kromě sebe
            $lowered = strtolower($new_value);
            $existing = array_map('strtolower', $projects);
            unset($existing[$index]); // odstraníme sebe
            if (in_array($lowered, $existing, true)) {
                $_SESSION['message'] = 'Tento zájem už existuje.';
                $_SESSION['messageType'] = 'error';
            } else {
                $projects[$index] = $new_value;
                $data['projects'] = $projects;
                file_put_contents('profile.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $_SESSION['message'] = 'Zájem byl úspěšně upraven.';
                $_SESSION['messageType'] = 'success';
                unset($_SESSION['editing']);
            }
        }
        header("Location: index.php");
        exit;
    }
    // zrušení editace
    elseif (isset($_POST['cancel_edit'])) {
        unset($_SESSION['editing']);
        header("Location: index.php");
        exit;
    }
}

$editing = isset($_SESSION['editing']) ? $_SESSION['editing'] : null;

?>

?>
<!doctype html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>IT Profil - <?php echo htmlspecialchars($name); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1><?php echo htmlspecialchars($name); ?></h1>
  </header>
  <main>
    <section>
      <h2>Dovednosti</h2>
      <?php if (!empty($skills)): ?>
        <ul>
          <?php foreach ($skills as $skill): ?>
            <li><?php echo htmlspecialchars($skill); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné dovednosti nebyly uvedeny.</p>
      <?php endif; ?>
    </section>
    <section>
      <h2>Projekty / Zájmy</h2>
      <?php if (!empty($projects)): ?>
        <ul>
          <?php foreach ($projects as $index => $p): ?>
            <li>
              <?php echo htmlspecialchars($p); ?>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="edit_interest" value="<?php echo $index; ?>">
                <button type="submit">Upravit</button>
              </form>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="remove_interest" value="<?php echo $index; ?>">
                <button type="submit" onclick="return confirm('Opravdu chcete odstranit tento zájem?')">Smazat</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Žádné projekty nebo zájmy nebyly uvedeny.</p>
      <?php endif; ?>

      <!-- zpráva o výsledku formuláře -->
      <?php if (!empty($message)): ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <!-- formulář pro editaci, pokud je aktivní -->
      <?php if ($editing !== null && isset($projects[$editing])): ?>
        <h3>Upravit zájem</h3>
        <form method="POST">
          <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($projects[$editing]); ?>" required>
          <input type="hidden" name="save_edit" value="<?php echo $editing; ?>">
          <button type="submit">Uložit změny</button>
          <button type="submit" name="cancel_edit">Zrušit</button>
        </form>
      <?php endif; ?>

      <!-- formulář pro nový zájem -->
      <h3>Přidat nový zájem</h3>
      <form method="POST">
        <input type="text" name="new_interest" required>
        <button type="submit">Přidat zájem</button>
      </form>
    </section>
  </main>
</body>
</html>
