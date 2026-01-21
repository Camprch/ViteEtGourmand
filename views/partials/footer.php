<?php

// Vue : pied de page

// Utilisé par : toutes les vues du site
$horaires = $GLOBALS['horaires'] ?? [];
?>
    </div>
    </main>
    <!-- Début du pied de page -->
    <footer class="site-footer">
        <div class="container footer-inner">
            <section>
            <h4>Horaires</h4>
            <?php if (!empty($horaires)): ?>
                <ul class="footer-hours">
                    <?php foreach ($horaires as $h): ?>
                        <li>
                            <span class="footer-hours-day"><?= htmlspecialchars($h['jour']) ?></span>
                            <span class="footer-hours-time">
                                <?php if ((int)$h['ferme'] === 1): ?>
                                    Fermé
                                <?php else: ?>
                                    <?= htmlspecialchars(substr((string)$h['heure_ouverture'], 0, 5)) ?>
                                    –
                                    <?= htmlspecialchars(substr((string)$h['heure_fermeture'], 0, 5)) ?>
                                <?php endif; ?>
                            </span>
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
                <a href="index.php?page=rgpd">RGPD</a> |
                <a href="index.php?page=cgv">CGV</a>
            </p>
        </div>
    </footer>
</body>
</html>
