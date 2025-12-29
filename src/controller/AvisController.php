<?php
declare(strict_types=1);

// Contrôleur pour la gestion des avis clients.

// - Soumission d'un avis par un utilisateur
// - Validation ou refus d'un avis par un employé/admin
// - Affichage des avis en attente de validation
// Chaque méthode correspond à une action précise sur les avis.

require_once __DIR__ . '/../model/AvisModel.php';
require_once __DIR__ . '/../model/CommandeModel.php';
require_once __DIR__ . '/../security/Auth.php';
require_once __DIR__ . '/../security/Csrf.php';

class AvisController
{
    // Connexion PDO à la base de données
    private PDO $pdo;

    // Constructeur : injection de la connexion PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Soumission d'un avis par un utilisateur
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();
        Auth::requireLogin();

        // Récupération des données du formulaire
        $userId = (int)Auth::user()['id'];
        $commandeId = (int)($_POST['id_commande'] ?? 0);
        $note       = (int)($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        $errors = [];

        // Validation des champs
        if ($commandeId <= 0) $errors[] = "Commande invalide.";
        if ($note < 1 || $note > 5) $errors[] = "La note doit être entre 1 et 5.";
        if ($commentaire === '') $errors[] = "Le commentaire est obligatoire.";

        $commandeModel = new CommandeModel($this->pdo);
        $commande = $commandeModel->findByIdForUser($commandeId, $userId);

        // Vérifie que la commande existe et est terminée
        if (!$commande) {
            $errors[] = "Commande introuvable.";
        } elseif ($commande['statut_courant'] !== 'TERMINEE') {
            $errors[] = "Vous ne pouvez laisser un avis que pour une commande terminée.";
        }

        $avisModel = new AvisModel($this->pdo);

        // Vérifie qu'il n'y a pas déjà un avis pour cette commande
        if ($commandeId > 0 && $avisModel->existsForCommande($commandeId)) {
            $errors[] = "Vous avez déjà laissé un avis pour cette commande.";
        }

        // Affichage des erreurs éventuelles
        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        // Création de l'avis en base
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

    // Affiche les avis en attente de validation (employé/admin)
    public function pending(): void
    {
        Auth::requireRole(['EMPLOYE', 'ADMIN']);

        $avisModel = new AvisModel($this->pdo);
        $avis = $avisModel->getPendingAvis();

        require __DIR__ . '/../../views/avis/pending.php';
    }

    // Valide un avis (employé/admin)
    public function validate(): void
    {
        Auth::requireRole(['EMPLOYE', 'ADMIN']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        $avisId = (int)($_POST['avis_id'] ?? 0);
        if ($avisId <= 0) {
            echo "Avis invalide.";
            return;
        }

        $avisModel = new AvisModel($this->pdo);
        $avisModel->setValid($avisId);

        echo "<h2>Avis validé ✅</h2>";
        echo '<p><a href="index.php?page=avis_a_valider">Retour</a></p>';
    }

    // Refuse/supprime un avis (employé/admin)
    public function refuse(): void
    {
        Auth::requireRole(['EMPLOYE', 'ADMIN']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        $avisId = (int)($_POST['avis_id'] ?? 0);
        if ($avisId <= 0) {
            echo "Avis invalide.";
            return;
        }

        $avisModel = new AvisModel($this->pdo);
        $avisModel->delete($avisId);

        echo "<h2>Avis refusé ✅</h2>";
        echo '<p><a href="index.php?page=avis_a_valider">Retour</a></p>';
    }

}
