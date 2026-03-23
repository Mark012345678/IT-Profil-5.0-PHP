<?php
// pages/interests.php - Správa zájmů s CRUD operacemi
?>

<section>
  <h2>Zájmy</h2>
  
  <!-- zobrazení zájmů -->
  <?php if (!empty($interests)): ?>
    <ul>
      <?php foreach ($interests as $interest): ?>
        <li>
          <?php echo htmlspecialchars($interest['name']); ?>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="edit_interest" value="<?php echo $interest['id']; ?>">
            <button type="submit">Upravit</button>
          </form>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="remove_interest" value="<?php echo $interest['id']; ?>">
            <button type="submit" onclick="return confirm('Opravdu chcete odstranit tento zájem?')">Smazat</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Žádné zájmy nebyly uvedeny.</p>
  <?php endif; ?>

  <!-- zpráva o výsledku formuláře -->
  <?php if (!empty($message)): ?>
    <p class="<?php echo htmlspecialchars($messageType); ?>">
      <?php echo htmlspecialchars($message); ?>
    </p>
  <?php endif; ?>

  <!-- formulář pro editaci zájmu, pokud je aktivní -->
  <?php if ($editing_interest !== null): ?>
    <?php
    // Najdeme zájem podle id
    $editing_item = null;
    foreach ($interests as $interest) {
      if ($interest['id'] == $editing_interest) {
        $editing_item = $interest;
        break;
      }
    }
    ?>
    <?php if ($editing_item): ?>
      <h3>Upravit zájem</h3>
      <form method="POST">
        <input type="text" name="edited_interest" value="<?php echo htmlspecialchars($editing_item['name']); ?>" required>
        <input type="hidden" name="save_edit_interest" value="<?php echo $editing_item['id']; ?>">
        <button type="submit">Uložit změny</button>
        <button type="submit" name="cancel_edit_interest">Zrušit</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <!-- formulář pro nový zájem -->
  <h3>Přidat nový zájem</h3>
  <form method="POST">
    <input type="text" name="new_interest" required>
    <button type="submit">Přidat zájem</button>
  </form>
</section>
