<?php

// Contrôleur pour la gestion des commandes.

// - index()         : Affiche la liste des commandes à traiter
// - updateStatut()  : Met à jour le statut d'une commande
// - annuler()       : Annule une commande avec motif

declare(strict_types=1);

require_once __DIR__ . '/../model/CommandeModel.php';
require_once __DIR__ . '/../security/Auth.php';
require_once __DIR__ . '/../security/Csrf.php';

class EmployeCommandeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie le rôle et retourne l'utilisateur courant
    private function currentUser(): array
    {
        Auth::requireRole(['EMPLOYE', 'ADMIN']);
        return Auth::user();
    }

    // Affiche la liste des commandes à traiter
    public function index(): void
    {
        $this->currentUser();

        $statut = isset($_GET['statut']) && $_GET['statut'] !== '' ? (string)$_GET['statut'] : null;

        $commandeModel = new CommandeModel($this->pdo);
        $commandes = $commandeModel->findAllForEmploye($statut);

        require __DIR__ . '/../../views/employe/commandes.php';
    }

    // Met à jour le statut d'une commande (et ajoute à l'historique)
    public function updateStatut(): void
{
    $user = $this->currentUser();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }

    Csrf::check();

    $commandeId = (int)($_POST['id'] ?? 0);
    $newStatut = (string)($_POST['statut'] ?? '');

    $allowed = [
        'EN_ATTENTE',
        'ACCEPTEE',
        'EN_PREPARATION',
        'EN_LIVRAISON',
        'LIVREE',
        'ATTENTE_RETOUR_MATERIEL',
        'TERMINEE',
        'ANNULEE',
    ];

    if ($commandeId <= 0 || !in_array($newStatut, $allowed, true)) {
        echo "Données invalides.";
        return;
    }

    $commandeModel = new CommandeModel($this->pdo);

    // Mise à jour statut courant + historique
    $commandeModel->updateStatus($commandeId, $newStatut);
    $commandeModel->addStatutHistorique($commandeId, $newStatut, (int)$user['id']);

    // TODO email client si ATTENTE_RETOUR_MATERIEL (on le fait après)
    header('Location: index.php?page=employe_commandes');
    exit;
    }

    // Annule une commande avec motif et mode de contact
    public function annuler(): void
    {
    $user = $this->currentUser();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }

    Csrf::check();

    $commandeId = (int)($_POST['id'] ?? 0);
    $modeContact = trim($_POST['mode_contact'] ?? '');
    $motif = trim($_POST['motif'] ?? '');

    if ($commandeId <= 0 || $modeContact === '' || $motif === '') {
        echo "Données invalides.";
        return;
    }

    $newStatut = 'ANNULEE';

    $commandeModel = new CommandeModel($this->pdo);

    $commentaire = "ANNULATION - Mode contact: {$modeContact} - Motif: {$motif}";
    $commandeModel->updateStatus($commandeId, $newStatut);
    $commandeModel->addStatutHistorique(
    $commandeId,
    $newStatut,
    (int)$user['id'],
    $commentaire
);

    // On stocke le commentaire via une entrée d'historique (mais ton modèle ne le prend pas encore)
    // => Étape suivante : on ajoute le champ commentaire dans addStatutHistorique()

    header('Location: index.php?page=employe_commandes');
    exit;
    }
}
