<?php
// views/partials/footer.php
$horaires = $GLOBALS['horaires'] ?? [];
?>
    </main>
    <footer>
        <section>
            <h4>Horaires</h4>
            <?php if (!empty($horaires)): ?>
                <ul>
                    <?php foreach ($horaires as $h): ?>
                        <li>
                            <strong><?= htmlspecialchars($h['jour']) ?> :</strong>
                            <?php if ((int)$h['ferme'] === 1): ?>
                                Fermé
                            <?php else: ?>
                                <?= substr((string)$h['heure_ouverture'], 0, 5) ?>
                                –
                                <?= substr((string)$h['heure_fermeture'], 0, 5) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Horaires non renseignés.</p>
            <?php endif; ?>
        </section>

        <p>© Vite & Gourmand</p>
        <p>
        <a href="index.php?page=mentions_legales">Mentions légales</a> |
        <a href="index.php?page=cgv">CGV</a>
    </p>
    </footer>
</body>
</html>
