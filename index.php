<?php
require_once __DIR__ . '/includes/auth.php';
ensureUsersFile();
$handler = new XMLHandler('users.xml', 'users', 'user');
$users = $handler->all();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = authenticate($username, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants invalides';
    }
}

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <div class="logo-circle">GS</div>
                <h1>Gestion des Stagiaires</h1>
                <p>Connectez-vous à votre espace</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label>Identifiant</label>
                    <select name="username" required autofocus>
                        <option value="">-- Sélectionnez un compte --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= htmlspecialchars($u['username']) ?>">
                                <?= htmlspecialchars($u['nom'] . ' (' . ucfirst($u['role']) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </form>


        </div>
    </div>
</body>
</html>
