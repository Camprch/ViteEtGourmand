<?php
declare(strict_types=1);

// Contrôleur de gestion des commandes utilisateur.

// - form(int $menuId)         : Affiche le formulaire de commande pour un menu
// - store()                   : Traite la soumission et la validation d'une commande
// - mesCommandes()            : Affiche la liste des commandes de l'utilisateur connecté
// - detail(int $id)           : Affiche le détail d'une commande
// - annulerCommande()         : Permet à l'utilisateur d'annuler une commande en attente

require_once __DIR__ . '/../model/MenuModel.php';
require_once __DIR__ . '/../model/CommandeModel.php';

// Contrôleur de gestion des commandes : formulaire, validation, affichage, annulation
class CommandeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche le formulaire de commande pour un menu donné (utilisateur connecté)
    public function form(int $menuId): void
        // Vérifie que l'utilisateur est connecté
    {
        Auth::requireLogin();

        $menuModel = new MenuModel($this->pdo);
        $menu = $menuModel->findById($menuId);

        if (!$menu) {
            echo "Menu introuvable.";
            return;
        }

        // plus tard : vérifier authentification
        require __DIR__ . '/../../views/commande/form.php';
    }

    public function store(): void
    {
    // Traite la soumission du formulaire de commande
        // Vérifie la méthode, la sécurité CSRF et l'authentification
        // Vérifie que la date et l'heure de prestation sont valides et dans les horaires d'ouverture
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Méthode invalide.";
        return;
    }

    Csrf::check();
    Auth::requireLogin();

    require_once __DIR__ . '/../model/HoraireModel.php';

    $datePrest = $_POST['date_prestation'] ?? '';
    $heurePrest = $_POST['heure_prestation'] ?? '';

    $jour = fr_jour_depuis_date((string)$datePrest);
    if ($jour === null) {
        echo "<h2>Date de prestation invalide</h2>";
        return;
    }

    $minutesPrest = hhmm_to_minutes((string)$heurePrest);
    if ($minutesPrest === null) {
        echo "<h2>Heure de prestation invalide</h2>";
        return;
    }

    $horaireModel = new HoraireModel($this->pdo);
    $h = $horaireModel->findByJour($jour);

    if (!$h || !empty($h['ferme'])) {
        echo "<h2>Le restaurant est fermé le $jour</h2>";
        return;
    }

    $openMin = !empty($h['heure_ouverture'])
        ? hhmm_to_minutes($h['heure_ouverture'])
        : null;

    $closeMin = !empty($h['heure_fermeture'])
        ? hhmm_to_minutes($h['heure_fermeture'])
        : null;

    if ($openMin === null || $closeMin === null || $openMin >= $closeMin) {
        echo "<h2>Horaires non configurés correctement pour $jour</h2>";
        return;
    }

    if ($minutesPrest < $openMin || $minutesPrest > $closeMin) {
        echo "<h2>Heure de prestation hors horaires ($h[heure_ouverture] – $h[heure_fermeture])</h2>";
        return;
    }

    // 1. Récupération des données
    $idMenu          = (int)($_POST['id_menu'] ?? 0);
    $nbPersonnes     = (int)($_POST['nb_personnes'] ?? 0);
    $datePrestation  = trim($_POST['date_prestation'] ?? '');
    $heurePrestation = trim($_POST['heure_prestation'] ?? '');
    $adresse         = trim($_POST['adresse_prestation'] ?? '');
    $ville           = trim($_POST['ville'] ?? '');
    $codePostal      = trim($_POST['code_postal'] ?? '');
    $distanceRaw = str_replace(',', '.', (string)($_POST['distance_km'] ?? '0'));
    $distanceKm = (float)$distanceRaw;

    $erreurs = [];

    // 2. Validation basique des champs
    if ($idMenu <= 0) $erreurs[] = "Menu invalide.";
    if ($nbPersonnes <= 0) $erreurs[] = "Nombre de personnes invalide.";
    if ($datePrestation === '') $erreurs[] = "Date requise.";
    if ($heurePrestation === '') $erreurs[] = "Heure requise.";
    if ($adresse === '') $erreurs[] = "Adresse requise.";
    if ($ville === '') $erreurs[] = "Ville requise.";
    if ($codePostal === '') $erreurs[] = "Code postal requis.";
    if ($distanceKm < 0) $erreurs[] = "La distance ne peut pas être négative.";

    // 3. Charger le menu pour vérifier les règles métier
    $menuModel = new MenuModel($this->pdo);
    $menu = $menuModel->findById($idMenu);

    if (!$menu) {
        $erreurs[] = "Menu introuvable.";
    }

    if ($menu && $menu['stock'] !== null && (int)$menu['stock'] <= 0) {
        $erreurs[] = "Ce menu est indisponible (rupture de stock).";
    }

    // 4. Vérifier le minimum de personnes (règle métier)
    if ($menu && $nbPersonnes < (int)$menu['personnes_min']) {
        $erreurs[] = "Vous devez commander au minimum " . (int)$menu['personnes_min'] . " personnes pour ce menu.";
    }

    if (!empty($erreurs)) {
        echo "<h2>Erreur lors de la commande :</h2>";
        echo "<ul>";
        foreach ($erreurs as $e) {
            echo "<li>" . htmlspecialchars($e) . "</li>";
        }
        echo "</ul>";
        echo '<a href="javascript:history.back()">Retour</a>';
        return;
    }

    // 5. Calcul du prix du menu (sans réduction ni livraison pour l’instant)
    $prixParPersonne = (float)$menu['prix_par_personne'];
    $prixMenuTotal   = $nbPersonnes * $prixParPersonne;

    // --- Réduction de 10 % ---
    $reduction = 0.0;
    $min = (int)$menu['personnes_min'];

    if ($nbPersonnes >= $min + 5) {
    $reduction = $prixMenuTotal * 0.10; // 10 %
    } 

    // --- Total provisoire (avant livraison) ---
    $prixApresReduction = $prixMenuTotal - $reduction;

    // --- Frais de livraison ---
    // Si la ville est Bordeaux (insensible à la casse) => 0 €
    // Sinon => 5 € + 0,59 €/km
    $villeNormalisee = strtolower(trim($ville));

    if ($villeNormalisee === 'bordeaux') {
        $fraisLivraison = 0.0;
    } else {
        $fraisLivraison = 5.0 + (0.59 * max(0, $distanceKm));
    }

    // --- Total final ---
        // Création de la commande et ajout de l'historique de statut
    $prixTotal = $prixApresReduction + $fraisLivraison;

    $commandeModel = new CommandeModel($this->pdo);

    $user = Auth::user();
    $idUser = (int)$user['id'];

    $commandeId = $commandeModel->create([
        'id_user'            => $idUser,
        'id_menu'            => $idMenu,
        'date_prestation'    => $datePrestation,
        'heure_prestation'   => $heurePrestation,
        'adresse_prestation' => $adresse,
        'ville'              => $ville,
        'code_postal'        => $codePostal,
        'nb_personnes'       => $nbPersonnes,
        'prix_menu_total'    => $prixMenuTotal,
        'reduction_appliquee'=> $reduction,
        'frais_livraison'    => $fraisLivraison,
        'prix_total'         => $prixTotal,

    ]);

    if ($commandeId <= 0) {
        error_log("Commande non créée, email non envoyé");
        echo "<h2>Erreur lors de la création de la commande</h2>";
        return;
    }

    $commandeModel->addStatutHistorique($commandeId, 'EN_ATTENTE');

    // Envoi de l'email de confirmation
    require_once __DIR__ . '/../service/MailerService.php';

    try {
        $user = Auth::user();

        $toEmail = $user['email'];
        $toName  = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        if ($toName === '') {
            $toName = $toEmail;
        }

        $mailer = new MailerService();

        $html = "<p>Merci pour votre commande !</p>
                <p>Votre commande <strong>#$commandeId</strong> a bien été enregistrée.</p>
                <p>Menu : " . htmlspecialchars((string)$menu['titre']) . "</p>
                <p>Date : " . htmlspecialchars($datePrestation) . " à " . htmlspecialchars($heurePrestation) . "</p>
                <p>Total : " . number_format($prixTotal, 2, ',', ' ') . " €</p>";

        $text = "Merci pour votre commande !\n"
            . "Commande #$commandeId enregistrée.\n"
            . "Menu : " . (string)$menu['titre'] . "\n"
            . "Date : $datePrestation $heurePrestation\n"
            . "Total : " . number_format($prixTotal, 2, ',', ' ') . " €\n";

        $ok = $mailer->send(
            $toEmail,
            $toName,
            "Confirmation de votre commande #$commandeId",
            $html,
            $text
        );

        if (!$ok) {
            error_log("Email commande non envoyé (commandeId=$commandeId)");
        }
    } catch (Throwable $e) {
        error_log("Mailer exception (commandeId=$commandeId): " . $e->getMessage());
    }

    // Notification employé d'une nouvelle commande
    $notifyEmail = getenv('MAIL_NOTIFY_EMAIL') ?: '';
    if ($notifyEmail !== '') {
        try {
            $notifyName = getenv('MAIL_NOTIFY_NAME') ?: 'Equipe Vite Gourmand';
            $mailer = new MailerService();

            $html = "<p>Nouvelle commande reçue.</p>
                    <p><strong>Commande #$commandeId</strong></p>
                    <p>Menu : " . htmlspecialchars((string)$menu['titre']) . "</p>
                    <p>Date : " . htmlspecialchars($datePrestation) . " à " . htmlspecialchars($heurePrestation) . "</p>
                    <p>Ville : " . htmlspecialchars($ville) . "</p>
                    <p>Total : " . number_format($prixTotal, 2, ',', ' ') . " €</p>";

            $text = "Nouvelle commande #$commandeId\n"
                . "Menu : " . (string)$menu['titre'] . "\n"
                . "Date : $datePrestation $heurePrestation\n"
                . "Ville : $ville\n"
                . "Total : " . number_format($prixTotal, 2, ',', ' ') . " €\n";

            $mailer->send($notifyEmail, $notifyName, "Nouvelle commande #$commandeId", $html, $text);
        } catch (Throwable $e) {
            error_log("Email notif commande non envoyé: " . $e->getMessage());
        }
    }

    require __DIR__ . '/../../views/commande/recap.php';
    }

    public function mesCommandes(): void
    {
    // Affiche la liste des commandes de l'utilisateur connecté
    Auth::requireLogin();
    $userId = (int)Auth::user()['id'];

    $commandeModel = new CommandeModel($this->pdo);
    $commandes = $commandeModel->findByUserId($userId);

    require __DIR__ . '/../../views/commande/mes_commandes.php';
    }

    public function detail(int $id): void
    {
    // Affiche le détail d'une commande pour l'utilisateur connecté
    Auth::requireLogin();

    if ($id <= 0) {
        echo "Commande introuvable.";
        return;
    }

    $userId = (int)Auth::user()['id'];

    $commandeModel = new CommandeModel($this->pdo);
    $commande = $commandeModel->findByIdForUser($id, $userId);
    $historiqueStatuts = $commandeModel->getStatutHistorique($id);

    if (!$commande) {
        echo "Commande introuvable.";
        return;
    }

    require __DIR__ . '/../../views/commande/detail.php';
    }

    public function annulerCommande(): void
    {
    // Permet à l'utilisateur d'annuler une commande si elle est encore en attente

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Méthode invalide.";
        return;
    }

    Csrf::check();
    Auth::requireLogin();

    $commandeId = (int)($_POST['id_commande'] ?? 0);
    $userId = (int)Auth::user()['id'];

    if ($commandeId <= 0) {
        echo "Commande invalide.";
        return;
    }

    $commandeModel = new CommandeModel($this->pdo);
    $commande = $commandeModel->findByIdForUser($commandeId, $userId);

    if (!$commande) {
        echo "Commande introuvable.";
        return;
    }

    if ($commande['statut_courant'] !== 'EN_ATTENTE') {
        echo "<h2>Impossible d'annuler cette commande.</h2>";
        echo "<p>Elle a déjà été traitée.</p>";
        echo '<a href="index.php?page=mes_commandes">Retour</a>';
        return;
    }

    // Mise à jour du statut
    $commandeModel->updateStatus($commandeId, 'ANNULEE');
    $commandeModel->addStatutHistorique($commandeId, 'ANNULEE', null, 'Annulation par le client');

    echo "<h2>Commande annulée 👍</h2>";
    echo '<a href="index.php?page=mes_commandes">Retour à mes commandes</a>';
    }

    
}
