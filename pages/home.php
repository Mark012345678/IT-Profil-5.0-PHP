<?php
// pages/home.php - Domovská stránka s profilem a dovednostmi
?>

<section>
  <h2>Profil</h2>
  <p>Jméno: <strong><?php echo htmlspecialchars($name); ?></strong></p>
</section>

<section>
  <h2>Dovednosti</h2>
  <?php if (!empty($skills)): ?>
    <ul>
      <?php foreach ($skills as $skill): ?>
        <li><?php echo htmlspecialchars($skill['name']); ?></li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Žádné dovednosti nebyly uvedeny.</p>
  <?php endif; ?>
</section>

<section>
  <h2>Projekty</h2>
  <?php if (!empty($real_projects)): ?>
    <ul>
      <?php foreach ($real_projects as $project): ?>
        <li><?php echo htmlspecialchars($project['name']); ?></li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>Žádné projekty nebyly uvedeny.</p>
  <?php endif; ?>
</section>
