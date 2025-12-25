<?php
// views/menu/index.php
$pageTitle = 'Nos menus - Vite & Gourmand';
require __DIR__ . '/../partials/header.php';
?>

<h2>Nos menus</h2>

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

<div id="menus-container">
    <?php
    // Affichage initial (sans filtres)
    require __DIR__ . '/_list_partial.php';
    ?>
</div>

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
