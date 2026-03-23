<?php
session_start();

require_once 'init.php';

// Načtení parametru page z URL, výchozí hodnota je "home"
$page = $_GET["page"] ?? "home";

// Validace page parametru - jen povolené stránky
$allowed_pages = ['home', 'interests', 'skills'];
if (!in_array($page, $allowed_pages)) {
    $page = 'not_found';
}

// načtení profilu z databáze
$stmt = $db->query("SELECT * FROM profile WHERE id = 1");
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $profile ? $profile['name'] : 'Neznámý uživatel';

// načtení dovedností z databáze
$stmt = $db->query("SELECT * FROM skills ORDER BY name");
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// načtení zájmů z databáze
$stmt = $db->query("SELECT * FROM interests");
$interests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// načtení projektů z databáze
$stmt = $db->query("SELECT * FROM projects");
$real_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            try {
                $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
                $stmt->execute([$new]);
                $_SESSION['message'] = 'Zájem byl přidán.';
                $_SESSION['messageType'] = 'success';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento zájem už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při přidávání zájmu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=interests");
        exit;
    }
    // odstranění zájmu
    elseif (isset($_POST['remove_interest'])) {
        $id = (int)$_POST['remove_interest'];
        $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Zájem byl odstraněn.';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Zájem nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        }
        header("Location: index.php?page=interests");
        exit;
    }
    // zahájení editace zájmu
    elseif (isset($_POST['edit_interest'])) {
        $id = (int)$_POST['edit_interest'];
        $_SESSION['editing_interest'] = $id;
        header("Location: index.php?page=interests");
        exit;
    }
    // uložení editace zájmu
    elseif (isset($_POST['save_edit_interest'])) {
        $id = (int)$_POST['save_edit_interest'];
        $new_value = trim($_POST['edited_interest']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
                $stmt->execute([$new_value, $id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Zájem byl upraven.';
                    $_SESSION['messageType'] = 'success';
                    unset($_SESSION['editing_interest']);
                } else {
                    $_SESSION['message'] = 'Zájem nebyl nalezen.';
                    $_SESSION['messageType'] = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento zájem už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při úpravě zájmu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=interests");
        exit;
    }
    // zrušení editace zájmu
    elseif (isset($_POST['cancel_edit_interest'])) {
        unset($_SESSION['editing_interest']);
        header("Location: index.php?page=interests");
        exit;
    }
    // přidání projektu
    elseif (isset($_POST['new_project'])) {
        $new = trim($_POST['new_project']);
        if ($new === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO projects (name) VALUES (?)");
                $stmt->execute([$new]);
                $_SESSION['message'] = 'Projekt byl přidán.';
                $_SESSION['messageType'] = 'success';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento projekt už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při přidávání projektu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // odstranění projektu
    elseif (isset($_POST['remove_project'])) {
        $id = (int)$_POST['remove_project'];
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Projekt byl odstraněn.';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Projekt nebyl nalezen.';
            $_SESSION['messageType'] = 'error';
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // zahájení editace projektu
    elseif (isset($_POST['edit_project'])) {
        $id = (int)$_POST['edit_project'];
        $_SESSION['editing_project'] = $id;
        header("Location: index.php?page=skills");
        exit;
    }
    // uložení editace projektu
    elseif (isset($_POST['save_edit_project'])) {
        $id = (int)$_POST['save_edit_project'];
        $new_value = trim($_POST['edited_project']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("UPDATE projects SET name = ? WHERE id = ?");
                $stmt->execute([$new_value, $id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Projekt byl upraven.';
                    $_SESSION['messageType'] = 'success';
                    unset($_SESSION['editing_project']);
                } else {
                    $_SESSION['message'] = 'Projekt nebyl nalezen.';
                    $_SESSION['messageType'] = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tento projekt už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při úpravě projektu.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // zrušení editace projektu
    elseif (isset($_POST['cancel_edit_project'])) {
        unset($_SESSION['editing_project']);
        header("Location: index.php?page=skills");
        exit;
    }
    // přidání dovednosti
    elseif (isset($_POST['new_skill'])) {
        $new = trim($_POST['new_skill']);
        if ($new === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO skills (name) VALUES (?)");
                $stmt->execute([$new]);
                $_SESSION['message'] = 'Dovednost byla přidána.';
                $_SESSION['messageType'] = 'success';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tato dovednost už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při přidávání dovednosti.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // odstranění dovednosti
    elseif (isset($_POST['remove_skill'])) {
        $id = (int)$_POST['remove_skill'];
        $stmt = $db->prepare("DELETE FROM skills WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Dovednost byla odstraněna.';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Dovednost nebyla nalezena.';
            $_SESSION['messageType'] = 'error';
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // zahájení editace dovednosti
    elseif (isset($_POST['edit_skill'])) {
        $id = (int)$_POST['edit_skill'];
        $_SESSION['editing_skill'] = $id;
        header("Location: index.php?page=skills");
        exit;
    }
    // uložení editace dovednosti
    elseif (isset($_POST['save_edit_skill'])) {
        $id = (int)$_POST['save_edit_skill'];
        $new_value = trim($_POST['edited_skill']);
        if ($new_value === '') {
            $_SESSION['message'] = 'Pole nesmí být prázdné.';
            $_SESSION['messageType'] = 'error';
        } else {
            try {
                $stmt = $db->prepare("UPDATE skills SET name = ? WHERE id = ?");
                $stmt->execute([$new_value, $id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = 'Dovednost byla upravena.';
                    $_SESSION['messageType'] = 'success';
                    unset($_SESSION['editing_skill']);
                } else {
                    $_SESSION['message'] = 'Dovednost nebyla nalezena.';
                    $_SESSION['messageType'] = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // UNIQUE constraint failed
                    $_SESSION['message'] = 'Tato dovednost už existuje.';
                    $_SESSION['messageType'] = 'error';
                } else {
                    $_SESSION['message'] = 'Chyba při úpravě dovednosti.';
                    $_SESSION['messageType'] = 'error';
                }
            }
        }
        header("Location: index.php?page=skills");
        exit;
    }
    // zrušení editace dovednosti
    elseif (isset($_POST['cancel_edit_skill'])) {
        unset($_SESSION['editing_skill']);
        header("Location: index.php?page=skills");
        exit;
    }
}

$editing_interest = isset($_SESSION['editing_interest']) ? $_SESSION['editing_interest'] : null;
$editing_project = isset($_SESSION['editing_project']) ? $_SESSION['editing_project'] : null;
$editing_skill = isset($_SESSION['editing_skill']) ? $_SESSION['editing_skill'] : null;

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
    <nav>
      <a href="?page=home">Domů</a>
      <a href="?page=interests">Zájmy</a>
      <a href="?page=skills">Dovednosti</a>
    </nav>
  </header>
  <main>
    <?php
    // Načtení stránky podle parametru page
    if ($page === 'not_found') {
        require 'pages/not_found.php';
    } else {
        require "pages/{$page}.php";
    }
    ?>
  </main>
</body>
</html>
