<?php

// Vue : Allergenes - Liste de tous les allergènes

// Utilisé par : EmployeAllergeneController::index()

require __DIR__ . '/../partials/header.php';
?>

<section class="page-head">
    <div>
        <p class="eyebrow">Allergènes</p>
        <h1>Référentiel des allergènes</h1>
    </div>
</section>

<section class="toolbar">
    <a class="btn" href="index.php?page=employe_allergene_create">+ Créer un allergène</a>
</section>

<?php if (isset($_GET['created'])): ?><section class="notice">✅ Créé</section><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><section class="notice">✅ Modifié</section><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><section class="notice">✅ Supprimé</section><?php endif; ?>
<?php if (isset($_GET['delete_error'])): ?><section class="notice">❌ Impossible : allergène utilisé par un plat.</section><?php endif; ?>

<section>
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($allergenes as $a): ?>
        <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars($a['nom']) ?></td>
            <td>
                <!-- Lien pour éditer l'allergène -->
                <a class="btn btn-ghost btn-sm" href="index.php?page=employe_allergene_edit&id=<?= (int)$a['id'] ?>">Éditer</a>

                <!-- Formulaire pour supprimer l'allergène (avec confirmation JS) -->
                <form method="post" action="index.php?page=employe_allergene_delete" class="action-row" onsubmit="return confirm('Supprimer ?');">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button class="btn btn-ghost btn-sm" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
