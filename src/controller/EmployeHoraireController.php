<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/HoraireModel.php';
require_once __DIR__ . '/../security/Auth.php';
require_once __DIR__ . '/../security/Csrf.php';

class EmployeHoraireController
{
    private PDO $pdo;

    private const JOURS = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function requireEmployeOrAdmin(): void
    {
        Auth::requireRole(['EMPLOYE', 'ADMIN']);
    }

    public function index(): void
    {
        $this->requireEmployeOrAdmin();

        $model = new HoraireModel($this->pdo);
        $horaires = $model->findAllOrdered();

        require __DIR__ . '/../../views/employe/horaires.php';
    }

    public function update(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        Csrf::check();

        $model = new HoraireModel($this->pdo);

        foreach (self::JOURS as $jour) {
            $ferme = isset($_POST['ferme'][$jour]) ? 1 : 0;

            $ouverture = trim((string)($_POST['heure_ouverture'][$jour] ?? ''));
            $fermeture = trim((string)($_POST['heure_fermeture'][$jour] ?? ''));

            // validation simple HH:MM
            $isTime = fn(string $t) => preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $t) === 1;

            // et juste après validation, normalise :
            $ouverture = $ouverture !== '' ? substr($ouverture, 0, 5) : $ouverture;
            $fermeture = $fermeture !== '' ? substr($fermeture, 0, 5) : $fermeture;

            if ($ferme === 1) {
                $ouverture = null;
                $fermeture = null;
            } else {
                if ($ouverture === '' || $fermeture === '' || !$isTime($ouverture) || !$isTime($fermeture)) {
                    // MVP : stop dès erreur
                    echo "<h2>Erreur horaires</h2>";
                    echo "<p>Heures invalides pour <strong>" . htmlspecialchars($jour) . "</strong> (format attendu HH:MM).</p>";
                    echo '<p><a href="javascript:history.back()">Retour</a></p>';
                    return;
                }
            }

            $model->updateJour($jour, $ouverture, $fermeture, $ferme);
        }

        header('Location: index.php?page=employe_horaires&ok=1');
        exit;
    }
}
