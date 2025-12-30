<?php
declare(strict_types=1);

// Contrôleur pour la gestion des commandes.

// - index()         : Affiche la liste des commandes à traiter
// - updateStatut()  : Met à jour le statut d'une commande
// - annuler()       : Annule une commande avec motif

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
    try {
        $commandeModel->changeStatutWithHistorique($commandeId, $newStatut, (int)$user['id'], null);
    } catch (Throwable $e) {
        error_log("Erreur changement statut: " . $e->getMessage());
        header('Location: index.php?page=employe_commandes&err=1');
        exit;
    }

    // Mongo: log stats au moment cohérent
    if ($newStatut === 'ACCEPTEE') {
        try {
            // Récupérer la commande
            $commande = $commandeModel->findByIdForEmploye($commandeId);

            // id_menu, prix_total
            if ($commande && !empty($commande['id_menu']) && isset($commande['prix_total'])) {

                // 2) Récupérer le menu pour avoir titre
                require_once __DIR__ . '/../model/MenuModel.php';
                $menuModel = new MenuModel($this->pdo);
                $menu = $menuModel->findById((int)$commande['id_menu']);

                if ($menu) {
                    // 3) Log Mongo
                    $envPath = __DIR__ . '/../../.env';
                    $env = file_exists($envPath) ? parse_ini_file($envPath) : [];
                    $mongoDsn = isset($env['MONGO_DSN']) ? trim((string)$env['MONGO_DSN'], "\"'") : null;
                    $mongoDb  = isset($env['MONGO_DB']) ? trim((string)$env['MONGO_DB'], "\"'") : 'vite_gourmand';

                    if ($mongoDsn) {
                        require_once __DIR__ . '/../model/StatsMongoLogger.php';
                        $logger = new StatsMongoLogger($mongoDsn, $mongoDb);
                        $logger->logCommandeAcceptee(
                            (int)$commandeId,
                            (int)$menu['id'],
                            (string)$menu['titre'],
                            (float)$commande['prix_total']
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            // On ne bloque pas le changement de statut si Mongo est KO
            error_log("Mongo log ACCEPTEE failed: " . $e->getMessage());
        }
    }

    // Email client si ATTENTE_RETOUR_MATERIEL (on le fait après)
    if (in_array($newStatut, ['ATTENTE_RETOUR_MATERIEL', 'TERMINEE'], true)) {
        require_once __DIR__ . '/../service/MailerService.php';

        $commande = $commandeModel->findByIdForEmploye($commandeId); // contient email client
        if ($commande && !empty($commande['email'])) {
            $mailer = new MailerService();
            $toEmail = $commande['email'];
            $toName  = trim(($commande['prenom'] ?? '') . ' ' . ($commande['nom'] ?? ''));
            if ($toName === '') $toName = $toEmail;

            if ($newStatut === 'ATTENTE_RETOUR_MATERIEL') {
                $subject = "Retour matériel – commande #$commandeId";
                $html = "<p>Bonjour,</p>
                        <p>Votre commande <strong>#$commandeId</strong> nécessite un retour de matériel.</p>
                        <p>Merci de procéder au retour sous <strong>10 jours ouvrés</strong>. Passé ce délai, une pénalité de <strong>600€</strong> pourra être appliquée.</p>";
                $mailer->send($toEmail, $toName, $subject, $html);
            }

            if ($newStatut === 'TERMINEE') {
                $subject = "Commande #$commandeId terminée – donnez votre avis";
                $html = "<p>Bonjour,</p>
                        <p>Votre commande <strong>#$commandeId</strong> est terminée.</p>
                        <p>Vous pouvez maintenant laisser un avis depuis votre espace client.</p>";
                $mailer->send($toEmail, $toName, $subject, $html);
            }
        }
    }

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

    // Mise à jour statut courant + historique

    $commentaire = "ANNULATION - Mode contact: {$modeContact} - Motif: {$motif}";
    try {
        $commandeModel->changeStatutWithHistorique(
            $commandeId,
            $newStatut,
            (int)$user['id'],
            $commentaire
        );
    } catch (Throwable $e) {
        error_log("Erreur annulation: " . $e->getMessage());
        header('Location: index.php?page=employe_commandes&err=1');
        exit;
    }

    // On stocke le commentaire via une entrée d'historique

    header('Location: index.php?page=employe_commandes');
    exit;
    }
}
