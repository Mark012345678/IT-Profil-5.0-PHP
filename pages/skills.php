<?php
// pages/skills.php - Dovednosti a projekty
?>

<section>
  <h2>Dovednosti</h2>
  
  <!-- zobrazení dovedností -->
  <?php if (!empty($skills)): ?>
    <ul>
      <?php foreach ($skills as $skill): ?>
        <li>
          <?php echo htmlspecialchars($skill['name']); ?>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="edit_skill" value="<?php echo $skill['id']; ?>">
            <button type="submit">Upravit</button>
          </form>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="remove_skill" value="<?php echo $skill['id']; ?>">
            <button type="submit" onclick="return confirm('Opravdu chcete odstranit tuto dovednost?')">Smazat</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Žádné dovednosti nebyly uvedeny.</p>
  <?php endif; ?>

  <!-- zpráva o výsledku formuláře -->
  <?php if (!empty($message)): ?>
    <p class="<?php echo htmlspecialchars($messageType); ?>">
      <?php echo htmlspecialchars($message); ?>
    </p>
  <?php endif; ?>

  <!-- formulář pro editaci dovednosti, pokud je aktivní -->
  <?php if ($editing_skill !== null): ?>
    <?php
    // Najdeme dovednost podle id
    $editing_item = null;
    foreach ($skills as $skill) {
      if ($skill['id'] == $editing_skill) {
        $editing_item = $skill;
        break;
      }
    }
    ?>
    <?php if ($editing_item): ?>
      <h3>Upravit dovednost</h3>
      <form method="POST">
        <input type="text" name="edited_skill" value="<?php echo htmlspecialchars($editing_item['name']); ?>" required>
        <input type="hidden" name="save_edit_skill" value="<?php echo $editing_item['id']; ?>">
        <button type="submit">Uložit změny</button>
        <button type="submit" name="cancel_edit_skill">Zrušit</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <!-- formulář pro novou dovednost -->
  <h3>Přidat novou dovednost</h3>
  <form method="POST">
    <input type="text" name="new_skill" required>
    <button type="submit">Přidat dovednost</button>
  </form>
</section>

<section>
  <h2>Projekty</h2>
  <?php if (!empty($real_projects)): ?>
    <ul>
      <?php foreach ($real_projects as $project): ?>
        <li>
          <?php echo htmlspecialchars($project['name']); ?>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="edit_project" value="<?php echo $project['id']; ?>">
            <button type="submit">Upravit</button>
          </form>
          <form method="POST" style="display: inline;">
            <input type="hidden" name="remove_project" value="<?php echo $project['id']; ?>">
            <button type="submit" onclick="return confirm('Opravdu chcete odstranit tento projekt?')">Smazat</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Žádné projekty nebyly uvedeny.</p>
  <?php endif; ?>

  <!-- zpráva o výsledku formuláře -->
  <?php if (!empty($message)): ?>
    <p class="<?php echo htmlspecialchars($messageType); ?>">
      <?php echo htmlspecialchars($message); ?>
    </p>
  <?php endif; ?>

  <!-- formulář pro editaci projektu, pokud je aktivní -->
  <?php if ($editing_project !== null): ?>
    <?php
    // Najdeme projekt podle id
    $editing_proj = null;
    foreach ($real_projects as $project) {
      if ($project['id'] == $editing_project) {
        $editing_proj = $project;
        break;
      }
    }
    ?>
    <?php if ($editing_proj): ?>
      <h3>Upravit projekt</h3>
      <form method="POST">
        <input type="text" name="edited_project" value="<?php echo htmlspecialchars($editing_proj['name']); ?>" required>
        <input type="hidden" name="save_edit_project" value="<?php echo $editing_proj['id']; ?>">
        <button type="submit">Uložit změny</button>
        <button type="submit" name="cancel_edit_project">Zrušit</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <!-- formulář pro nový projekt -->
  <h3>Přidat nový projekt</h3>
  <form method="POST">
    <input type="text" name="new_project" required>
    <button type="submit">Přidat projekt</button>
  </form>
</section>
