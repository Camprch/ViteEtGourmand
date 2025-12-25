<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Activer les sessions
session_start();

// Afficher les erreurs en dev
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Connexion BDD
require_once __DIR__ . '/../src/config/db.php';

// Sécurité et Authentification
require_once __DIR__ . '/../src/security/Csrf.php';
require_once __DIR__ . '/../src/security/Auth.php';

// Chargement global des horaires (footer)
require_once __DIR__ . '/../src/model/HoraireModel.php';
$horaireModel = new HoraireModel($pdo);
$horaires = $horaireModel->findAllOrdered();

// Controllers
require_once __DIR__ . '/../src/controller/HomeController.php';

// Router ultra simple basé sur ?page=
$page = $_GET['page'] ?? 'home';

// Guard simple basé sur le nom de page (évite les oublis dans les controllers)
if ($page === 'dashboard_admin' || str_starts_with($page, 'admin_')) {
    Auth::requireRole(['ADMIN']);
}
if ($page === 'dashboard_employe' || str_starts_with($page, 'employe_')) {
    Auth::requireRole(['EMPLOYE', 'ADMIN']);
}

switch ($page) {
    case 'home':
    $controller = new HomeController($pdo);
    $controller->index();
    break;

    case 'menus':
    require_once __DIR__ . '/../src/controller/MenuController.php';
    $controller = new MenuController($pdo);
    $controller->index();
    break;

    case 'menu':
    require_once __DIR__ . '/../src/controller/MenuController.php';
    $controller = new MenuController($pdo);
    $controller->show((int)($_GET['id'] ?? 0));
    break;

    case 'commande':
    require_once __DIR__ . '/../src/controller/CommandeController.php';
    $controller = new CommandeController($pdo);
    $controller->form((int)($_GET['menu_id'] ?? 0));
    break;

    case 'commande_traitement':
    require_once __DIR__ . '/../src/controller/CommandeController.php';
    $controller = new CommandeController($pdo);
    $controller->store();
    break;

    case 'register':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->showRegisterForm();
    break;

    case 'register_post':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->registerPost();
    break;

    case 'login':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->showLoginForm();
    break;

    case 'forgot_password':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->showForgotPasswordForm();
    break;

    case 'forgot_password_post':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->forgotPasswordPost();
    break;

    case 'reset_password':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->showResetPasswordForm();
    break;

    case 'reset_password_post':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->resetPasswordPost();
    break;

    case 'login_post':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->loginPost();
    break;

    case 'logout':
    require_once __DIR__ . '/../src/controller/AuthController.php';
    $controller = new AuthController($pdo);
    $controller->logout();
    break;

    case 'mes_commandes':
    require_once __DIR__ . '/../src/controller/CommandeController.php';
    $controller = new CommandeController($pdo);
    $controller->mesCommandes();
    break;

    case 'commande_detail':
    require_once __DIR__ . '/../src/controller/CommandeController.php';
    $controller = new CommandeController($pdo);
    $controller->detail((int)($_GET['id'] ?? 0));
    break;

    case 'annuler_commande':
    require_once __DIR__ . '/../src/controller/CommandeController.php';
    $controller = new CommandeController($pdo);
    $controller->annulerCommande();
    break;

    case 'avis_post':
    require_once __DIR__ . '/../src/controller/AvisController.php';
    $controller = new AvisController($pdo);
    $controller->store();
    break;

    case 'avis_a_valider':
    require_once __DIR__ . '/../src/controller/AvisController.php';
    $controller = new AvisController($pdo);
    $controller->pending();
    break;

    case 'avis_valider':
    require_once __DIR__ . '/../src/controller/AvisController.php';
    $controller = new AvisController($pdo);
    $controller->validate();
    break;

    case 'avis_refuser':
    require_once __DIR__ . '/../src/controller/AvisController.php';
    $controller = new AvisController($pdo);
    $controller->refuse();
    break;

    case 'menus_filter':
    require_once __DIR__ . '/../src/controller/MenuController.php';
    $controller = new MenuController($pdo);
    $controller->filterAjax();
    break;

    case 'contact':
    require_once __DIR__ . '/../src/controller/ContactController.php';
    $controller = new ContactController($pdo);
    $controller->showForm();
    break;

    case 'contact_post':
    require_once __DIR__ . '/../src/controller/ContactController.php';
    $controller = new ContactController($pdo);
    $controller->submit();
    break;

    case 'mentions_legales':
    require __DIR__ . '/../views/legal/mentions_legales.php';
    break;

    case 'cgv':
    require __DIR__ . '/../views/legal/cgv.php';
    break;

    case 'dashboard_employe':
    require_once __DIR__ . '/../src/controller/EmployeController.php';
    $controller = new EmployeController($pdo);
    $controller->dashboard();
    break;

    case 'dashboard_admin':
    require_once __DIR__ . '/../src/controller/AdminController.php';
    $controller = new AdminController($pdo);
    $controller->dashboard();
    break;

    case 'employe_commande_update_statut':
    require_once __DIR__ . '/../src/controller/EmployeCommandeController.php';
    $controller = new EmployeCommandeController($pdo);
    $controller->updateStatut();
    break;

    case 'employe_commande_annuler':
    require_once __DIR__ . '/../src/controller/EmployeCommandeController.php';
    $controller = new EmployeCommandeController($pdo);
    $controller->annuler();
    break;

    case 'employe_commandes':
    require_once __DIR__ . '/../src/controller/EmployeCommandeController.php';
    $controller = new EmployeCommandeController($pdo);
    $controller->index();
    break;

    case 'employe_horaires':
    require_once __DIR__ . '/../src/controller/EmployeHoraireController.php';
    $controller = new EmployeHoraireController($pdo);
    $controller->index();
    break;

    case 'employe_horaires_update':
    require_once __DIR__ . '/../src/controller/EmployeHoraireController.php';
    $controller = new EmployeHoraireController($pdo);
    $controller->update();
    break;

    case 'admin_employes':
    require_once __DIR__ . '/../src/controller/AdminEmployeController.php';
    $controller = new AdminEmployeController($pdo);
    $controller->index();
    break;

    case 'admin_employe_create':
    require_once __DIR__ . '/../src/controller/AdminEmployeController.php';
    $controller = new AdminEmployeController($pdo);
    $controller->create();
    break;

    case 'admin_employe_toggle':
    require_once __DIR__ . '/../src/controller/AdminEmployeController.php';
    $controller = new AdminEmployeController($pdo);
    $controller->toggleActif();
    break;

    case 'admin_stats':
    require_once __DIR__ . '/../src/controller/AdminStatsController.php';
    $controller = new AdminStatsController($pdo);
    $controller->index();
    break;

    case 'employe_menu_create':
    require_once __DIR__ . '/../src/controller/EmployeMenuController.php';
    $controller = new EmployeMenuController($pdo);
    $controller->createForm();
    break;

    case 'employe_menu_store':
    require_once __DIR__ . '/../src/controller/EmployeMenuController.php';
    $controller = new EmployeMenuController($pdo);
    $controller->store();
    break;

    default:
    $controller = new HomeController($pdo);
    $controller->index();
    break;
}
