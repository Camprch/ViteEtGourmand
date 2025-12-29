
<?php

// Vue : index des menus

// Utilisé par : route page=menus
$pageTitle = 'Nos menus - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>


<!-- Titre de la page -->
<h2>Nos menus</h2>


<!-- Formulaire de filtrage des menus -->
<form id="menu-filters">
    <label>
        Thème :
        <input type="text" name="theme">
    </label>

    <label>
        Régime :
        <input type="text" name="regime">
    </label>

    <label>
        Prix max :
        <input type="number" name="prix_max" step="0.01" min="0">
    </label>

    <label>
        Personnes minimum :
        <input type="number" name="personnes_min" min="1">
    </label>

    <button type="submit">Filtrer</button>
</form>


<hr>


<!-- Conteneur pour l'affichage dynamique des menus (filtrés ou non) -->
<div id="menus-container">
    <?php
    // Affichage initial (sans filtres)
    require __DIR__ . '/_list_partial.php';
    ?>
</div>


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
