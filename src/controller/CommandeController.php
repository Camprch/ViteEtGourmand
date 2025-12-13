<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';
require_once __DIR__ . '/../model/CommandeModel.php';

class CommandeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affichage du formulaire de commande
    public function form(int $menuId): void
    {
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Méthode invalide.";
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
    $distanceKm      = (float)($_POST['distance_km'] ?? 0);

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
    $prixTotal = $prixApresReduction + $fraisLivraison;

    $commandeModel = new CommandeModel($this->pdo);

    // On doit être connecté pour commander
    if (!isset($_SESSION['user'])) {
    echo "<h2>Vous devez être connecté pour passer une commande.</h2>";
    echo '<p><a href="index.php?page=login">Se connecter</a></p>';
    return;
    }

    $idUser = (int)$_SESSION['user']['id'];

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

    // 6. Affichage d'un récapitulatif simple
    echo "<h2>Récapitulatif de votre commande</h2>";
    echo "<p>Menu : <strong>" . htmlspecialchars($menu['titre']) . "</strong></p>";
    echo "<p>Nombre de personnes : " . (int)$nbPersonnes . "</p>";
    echo "<p>Prix par personne : " . number_format($prixParPersonne, 2, ',', ' ') . " €</p>";
    echo "<p>Total menus : <strong>" . number_format($prixMenuTotal, 2, ',', ' ') . " €</strong></p>";
    echo "<p>Réduction : " . number_format($reduction, 2, ',', ' ') . " €</p>";
    echo "<p>Frais de livraison : " . number_format($fraisLivraison, 2, ',', ' ') . " €</p>";
    echo "<p>Prix total : <strong>" . number_format($prixTotal, 2, ',', ' ') . " €</strong></p>";
    echo "<p>Commande n° " . (int)$commandeId . " enregistrée.</p>";
    echo "<hr>";
    echo "<p>Prestation le " . htmlspecialchars($datePrestation) .
         " à " . htmlspecialchars($heurePrestation) . "</p>";
    echo "<p>Adresse : " . htmlspecialchars($adresse) . ", " .
         htmlspecialchars($codePostal) . " " . htmlspecialchars($ville) . "</p>";

    echo '<p><a href="index.php?page=menus">← Retour aux menus</a></p>';
    }

    public function mesCommandes(): void
    {
    if (!isset($_SESSION['user'])) {
        echo "<h2>Vous devez être connecté pour voir vos commandes.</h2>";
        echo '<a href="index.php?page=login">Se connecter</a>';
        return;
    }

    $userId = (int)$_SESSION['user']['id'];

    $commandeModel = new CommandeModel($this->pdo);
    $commandes = $commandeModel->findByUserId($userId);

    require __DIR__ . '/../../views/commande/mes_commandes.php';
    }

    public function detail(int $id): void
    {
    if (!isset($_SESSION['user'])) {
        echo "<h2>Vous devez être connecté pour voir cette commande.</h2>";
        echo '<a href="index.php?page=login">Se connecter</a>';
        return;
    }

    if ($id <= 0) {
        echo "Commande introuvable.";
        return;
    }

    $userId = (int)$_SESSION['user']['id'];

    $commandeModel = new CommandeModel($this->pdo);
    $commande = $commandeModel->findByIdForUser($id, $userId);

    if (!$commande) {
        echo "Commande introuvable.";
        return;
    }

    require __DIR__ . '/../../views/commande/detail.php';
    }

    public function annulerCommande(): void
    {
    if (!isset($_SESSION['user'])) {
        echo "<h2>Vous devez être connecté pour annuler une commande.</h2>";
        echo '<a href="index.php?page=login">Se connecter</a>';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Méthode invalide.";
        return;
    }

    $commandeId = (int)($_POST['id_commande'] ?? 0);
    $userId = (int)$_SESSION['user']['id'];

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

    echo "<h2>Commande annulée 👍</h2>";
    echo '<a href="index.php?page=mes_commandes">Retour à mes commandes</a>';
    }

}