<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/AvisModel.php';
require_once __DIR__ . '/../model/CommandeModel.php';

class AvisController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(): void
    {
        if (!isset($_SESSION['user'])) {
            echo "<h2>Vous devez être connecté pour laisser un avis.</h2>";
            echo '<a href="index.php?page=login">Se connecter</a>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        $userId     = (int)$_SESSION['user']['id'];
        $commandeId = (int)($_POST['id_commande'] ?? 0);
        $note       = (int)($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        $errors = [];

        if ($commandeId <= 0) $errors[] = "Commande invalide.";
        if ($note < 1 || $note > 5) $errors[] = "La note doit être entre 1 et 5.";
        if ($commentaire === '') $errors[] = "Le commentaire est obligatoire.";

        $commandeModel = new CommandeModel($this->pdo);
        $commande = $commandeModel->findByIdForUser($commandeId, $userId);

        if (!$commande) {
            $errors[] = "Commande introuvable.";
        } elseif ($commande['statut_courant'] !== 'TERMINEE') {
            $errors[] = "Vous ne pouvez laisser un avis que pour une commande terminée.";
        }

        $avisModel = new AvisModel($this->pdo);

        if ($commandeId > 0 && $avisModel->existsForCommande($commandeId)) {
            $errors[] = "Vous avez déjà laissé un avis pour cette commande.";
        }

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        $avisModel->create([
            'id_user'     => $userId,
            'id_commande' => $commandeId,
            'id_menu'     => (int)$commande['id_menu'],
            'note'        => $note,
            'commentaire' => $commentaire,
        ]);

        echo "<h2>Avis envoyé ✅</h2>";
        echo "<p>Merci ! Votre avis sera visible après validation.</p>";
        echo '<p><a href="index.php?page=commande_detail&id=' . $commandeId . '">Retour à la commande</a></p>';
    }
}
