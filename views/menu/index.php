<?php

// Vue : index des menus

// Utilisé par : route page=menus
$pageTitle = 'Nos menus - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>


<section class="page-head">
    <div>
        <p class="eyebrow">Menus maison</p>
        <h2>Nos menus</h2>
        <p class="muted">Affinez votre recherche par thème, régime ou budget.</p>
    </div>
</section>


<!-- Formulaire de filtrage des menus -->
<section class="filters">
    <form id="menu-filters" class="filters-grid">
        <label>
            Thème
            <input type="text" name="theme" placeholder="Ex : italien">
        </label>

        <label>
            Régime
            <input type="text" name="regime" placeholder="Ex : vegan">
        </label>

        <label>
            Prix max (€)
            <input type="number" name="prix_max" step="0.01" min="0" placeholder="25">
        </label>

        <label>
            Personnes minimum
            <input type="number" name="personnes_min" min="1" placeholder="8">
        </label>

        <div class="filters-actions">
            <button type="submit">Filtrer</button>
        </div>
    </form>
</section>


<!-- Conteneur pour l'affichage dynamique des menus (filtrés ou non) -->
<section>
<div id="menus-container">
    <?php
    // Affichage initial (sans filtres)
    require __DIR__ . '/_list_partial.php';
    ?>
</div>
</section>


<!-- Script JS pour la soumission AJAX du formulaire de filtres -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('menu-filters');
    const container = document.getElementById('menus-container');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(form));

        const response = await fetch('index.php?page=menus_filter&' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            container.innerHTML = "<p>Erreur lors du chargement des menus.</p>";
            return;
        }

        container.innerHTML = await response.text();
    });
});
</script>


<?php require __DIR__ . '/../partials/footer.php'; ?>
