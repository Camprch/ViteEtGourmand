<?php
declare(strict_types=1);

// Activer les sessions
session_start();

// Afficher les erreurs en dev
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Connexion BDD
require_once __DIR__ . '/../src/config/db.php';

// Controllers
require_once __DIR__ . '/../src/controller/HomeController.php';

// Router ultra simple basé sur ?page=
$page = $_GET['page'] ?? 'home';

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

    default:
    $controller = new HomeController($pdo);
    $controller->index();
    break;
}
