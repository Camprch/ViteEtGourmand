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
            <nav class="footer-actions" aria-label="Pied de page">
                <a class="footer-btn" href="index.php?page=rgpd">RGPD</a>
                <a class="footer-btn" href="index.php?page=cgv">CGV</a>
                <a class="footer-btn" href="index.php?page=mentions_legales">Mentions légales</a>
                <details class="footer-hours-toggle">
                    <summary class="footer-btn">Horaires</summary>
                    <div class="footer-hours-panel" role="dialog" aria-label="Horaires">
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
                    </div>
                </details>
            </nav>

            <p class="footer-brand">© Vite & Gourmand</p>
        </div>
    </footer>
</body>
</html>
