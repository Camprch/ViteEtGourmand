<?php
declare(strict_types=1);

// Contrôleur pour la gestion des menus

// - createForm()    : Affiche le formulaire de création de menu
// - store()         : Traite la création d'un menu
// - index()         : Affiche la liste des menus pour le backoffice
// - editForm()      : Affiche le formulaire d'édition d'un menu
// - update()        : Traite la modification d'un menu et la gestion des plats liés
// - toggleStock()   : Active/désactive le stock d'un menu
// - uploadImage()   : Gère l'upload d'une image pour un menu
// - deleteImage()   : Supprime une image d'un menu

require_once __DIR__ . '/../model/MenuModel.php';
require_once __DIR__ . '/../model/PlatModel.php';
require_once __DIR__ . '/../model/MenuImageModel.php';


class EmployeMenuController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie que l'utilisateur est employé ou admin
    private function requireEmployeOrAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;

        if (!$user || !in_array($user['role'], ['EMPLOYE', 'ADMIN'], true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    // Affiche le formulaire de création de menu
    public function createForm(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/menu_create.php';
    }

    // Traite la création d'un menu
    public function store(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        require_once __DIR__ . '/../security/Csrf.php';
        Csrf::check();

        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $theme = trim($_POST['theme'] ?? '');
        $regime = trim($_POST['regime'] ?? '');
        $prixRaw = str_replace(',', '.', (string)($_POST['prix_par_personne'] ?? '0'));
        $prix = (float)$prixRaw;
        $personnesMin = (int)($_POST['personnes_min'] ?? 0);
        $conditions = trim($_POST['conditions_particulieres'] ?? '');
        $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;

        $errors = [];
        if ($titre === '') $errors[] = "Titre obligatoire.";
        if ($description === '') $errors[] = "Description obligatoire.";
        if ($prix <= 0) $errors[] = "Prix par personne invalide.";
        if ($personnesMin <= 0) $errors[] = "Nombre minimum de personnes invalide.";
        if ($stock !== null && $stock < 0) $errors[] = "Stock invalide.";

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="javascript:history.back()">Retour</a></p>';
            return;
        }

        $menuModel = new MenuModel($this->pdo);
        $id = $menuModel->create([
            'titre' => $titre,
            'description' => $description,
            'theme' => $theme !== '' ? $theme : null,
            'prix_par_personne' => $prix,
            'personnes_min' => $personnesMin,
            'conditions_particulieres' => $conditions !== '' ? $conditions : null,
            'regime' => $regime !== '' ? $regime : null,
            'stock' => $stock,
        ]);

        echo "<h2>Menu créé ✅</h2>";
        echo "<p>ID : " . (int)$id . "</p>";
        echo "<p><a href='index.php?page=employe_menu_create'>Créer un autre menu</a></p>";
        echo "<p><a href='index.php?page=dashboard_employe'>Retour dashboard</a></p>";
    }

    // Affiche la liste des menus pour le backoffice
    public function index(): void
    {
        $this->requireEmployeOrAdmin();

        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findAllForBackoffice();

        require __DIR__ . '/../../views/employe/menu_index.php';
    }

    // Affiche le formulaire d'édition d'un menu (et ses plats/images)
    public function editForm(): void
    {
        $this->requireEmployeOrAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        $menuModel = new MenuModel($this->pdo);
        $menu = $menuModel->findById($id);

        $platModel = new PlatModel($this->pdo);
        $plats = $platModel->findAll(); // tous les plats pour la liste

        $menuPlats = $menuModel->getPlatsForMenu($id); // plats déjà liés

        $imageModel = new MenuImageModel($this->pdo);
        $images = $imageModel->findByMenu($id);

        // Map pour pré-cocher / pré-remplir ordre : [id_plat => ordre]
        $menuPlatMap = [];
        foreach ($menuPlats as $mp) {
            $menuPlatMap[(int)$mp['id_plat']] = $mp['ordre'] !== null ? (int)$mp['ordre'] : null;
        }

        if (!$menu) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(404, 'Menu introuvable', 'Le menu demandé n’existe pas.');
        }

        require __DIR__ . '/../../views/employe/menu_edit.php';
    }

    // Traite la modification d'un menu et la gestion des plats liés
    public function update(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        require_once __DIR__ . '/../security/Csrf.php';
        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $theme = trim($_POST['theme'] ?? '');
        $regime = trim($_POST['regime'] ?? '');
        $prixRaw = str_replace(',', '.', (string)($_POST['prix_par_personne'] ?? '0'));
        $prix = (float)$prixRaw;
        $personnesMin = (int)($_POST['personnes_min'] ?? 0);
        $conditions = trim($_POST['conditions_particulieres'] ?? '');
        $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;

        $errors = [];
        if ($titre === '') $errors[] = "Titre obligatoire.";
        if ($description === '') $errors[] = "Description obligatoire.";
        if ($prix <= 0) $errors[] = "Prix par personne invalide.";
        if ($personnesMin <= 0) $errors[] = "Nombre minimum de personnes invalide.";
        if ($stock !== null && $stock < 0) $errors[] = "Stock invalide.";

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo "<p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }

        $menuModel = new MenuModel($this->pdo);
        $ok = $menuModel->update($id, [
            'titre' => $titre,
            'description' => $description,
            'theme' => $theme !== '' ? $theme : null,
            'prix_par_personne' => $prix,
            'personnes_min' => $personnesMin,
            'conditions_particulieres' => $conditions !== '' ? $conditions : null,
            'regime' => $regime !== '' ? $regime : null,
            'stock' => $stock,
        ]);

        // --- Liaison plats ---
        $selected = $_POST['plats'] ?? [];          
        $orders = $_POST['plats_ordre'] ?? [];      

        $items = [];
        foreach ($selected as $platIdRaw) {
            $platId = (int)$platIdRaw;
            if ($platId <= 0) continue;

            $ordreRaw = $orders[$platId] ?? '';
            $ordreRaw = trim((string)$ordreRaw);

            $ordre = null;
            if ($ordreRaw !== '') {
                $o = (int)$ordreRaw;
                if ($o < 0) {
                    $o = 0;
                }
                $ordre = $o;
            }

            $items[] = ['id_plat' => $platId, 'ordre' => $ordre];
        }

        $menuModel->replacePlats($id, $items);

        header("Location: index.php?page=employe_menus&updated=" . ($ok ? "1" : "0"));
        exit;
    }

    // Active ou désactive le stock d'un menu (stock illimité ou 0)
    public function toggleStock(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        require_once __DIR__ . '/../security/Csrf.php';
        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        $current = $_POST['current_stock'] ?? null;

        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        // Règle simple :
        // - si stock est NULL ou >0 => désactiver => stock=0
        // - si stock=0 => réactiver => stock=NULL (illimité)
        $currentInt = ($current === '' || $current === null) ? null : (int)$current;
        $newStock = ($currentInt === 0) ? null : 0;

        $menuModel = new MenuModel($this->pdo);
        $menuModel->setStock($id, $newStock);

        header("Location: index.php?page=employe_menus");
        exit;
    }

    // Gère l'upload d'une image pour un menu
    public function uploadImage(): void
    {
        $this->requireEmployeOrAdmin();
        Csrf::check();

        $menuId = (int)($_POST['menu_id'] ?? 0);
        if ($menuId <= 0 || empty($_FILES['image'])) {
            http_response_code(400);
            echo "<h2>Données invalides</h2>";
            exit;
        }

        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo "<h2>Erreur upload</h2>";
            exit;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            echo "<h2>Image trop lourde (max 2 Mo)</h2>";
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!isset($allowed[$mime])) {
            echo "<h2>Format non autorisé</h2>";
            exit;
        }

        $baseName = bin2hex(random_bytes(16));
        $targetExt = $allowed[$mime];

        $uploadDir = realpath(__DIR__ . '/../../public') . '/uploads/menus';
        if ($uploadDir === false) {
            echo "<h2>Dossier public introuvable</h2>";
            exit;
        }

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                echo "<h2>Impossible de créer le dossier d'upload</h2>";
                exit;
            }
        }

        // Optimisation simple : redimensionnement + compression si GD dispo
        $chemin = $baseName . '.' . $targetExt;
        $dest = $uploadDir . '/' . $chemin;

        $optimized = false;
        if (function_exists('imagecreatetruecolor')) {
            $srcImg = null;
            if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
                $srcImg = @imagecreatefromjpeg($file['tmp_name']);
            } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
                $srcImg = @imagecreatefrompng($file['tmp_name']);
            } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                $srcImg = @imagecreatefromwebp($file['tmp_name']);
            }

            if ($srcImg) {
                $maxSize = 1600;
                $w = imagesx($srcImg);
                $h = imagesy($srcImg);
                $scale = min(1, $maxSize / max($w, $h));
                $newW = (int)floor($w * $scale);
                $newH = (int)floor($h * $scale);

                $dstImg = $srcImg;
                if ($scale < 1) {
                    $dstImg = imagecreatetruecolor($newW, $newH);
                    if ($mime === 'image/png' || $mime === 'image/webp') {
                        imagealphablending($dstImg, false);
                        imagesavealpha($dstImg, true);
                    }
                    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $w, $h);
                }

                if ($mime === 'image/jpeg' && function_exists('imagejpeg')) {
                    $optimized = imagejpeg($dstImg, $dest, 82);
                } elseif ($mime === 'image/png' && function_exists('imagepng')) {
                    $optimized = imagepng($dstImg, $dest, 6);
                } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
                    $optimized = imagewebp($dstImg, $dest, 82);
                }

                if ($dstImg !== $srcImg) {
                    imagedestroy($dstImg);
                }
                imagedestroy($srcImg);
            }
        }

        if (!$optimized) {
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                echo "<h2>Erreur sauvegarde fichier</h2>";
                exit;
            }
        }

        $alt = trim($_POST['alt'] ?? '');
        $isMain = isset($_POST['is_main']);

        $imageModel = new MenuImageModel($this->pdo);
        $imageModel->create($menuId, $chemin, $alt !== '' ? $alt : null, $isMain);

        header("Location: index.php?page=employe_menu_edit&id=$menuId");
        exit;
    }

    // Supprime une image d'un menu (et le fichier associé)
    public function deleteImage(): void
    {
        $this->requireEmployeOrAdmin();
        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        $menuId = (int)($_POST['menu_id'] ?? 0);

        if ($id <= 0 || $menuId <= 0) {
            http_response_code(400);
            exit;
        }

        $model = new MenuImageModel($this->pdo);
        $chemin = $model->delete($id);

        if ($chemin) {
            $path = __DIR__ . '/../../public/uploads/menus/' . $chemin;
            if (is_file($path)) {
                unlink($path);
            }
        }

        header("Location: index.php?page=employe_menu_edit&id=$menuId");
        exit;
    }

}
