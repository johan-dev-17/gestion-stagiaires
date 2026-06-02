<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/xml_handler.php';

function ensureUsersFile() {
    $file = DATA_PATH . 'users.xml';
    if (!file_exists($file)) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><users/>');
        $defaults = [
            ['id' => 1, 'username' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'nom' => 'Administrateur'],
            ['id' => 2, 'username' => 'rh', 'password' => password_hash('rh123', PASSWORD_DEFAULT), 'role' => 'rh', 'nom' => 'Service RH'],
            ['id' => 3, 'username' => 'encadrant', 'password' => password_hash('enc123', PASSWORD_DEFAULT), 'role' => 'encadrant', 'nom' => 'Encadrant Démo'],
        ];
        foreach ($defaults as $u) {
            $user = $xml->addChild('user');
            foreach ($u as $k => $v) $user->addChild($k, htmlspecialchars((string)$v, ENT_XML1, 'UTF-8'));
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($file);
    }
}

function authenticate($username, $password) {
    ensureUsersFile();
    $xml = simplexml_load_file(DATA_PATH . 'users.xml');
    foreach ($xml->user as $user) {
        if ((string)$user->username === $username && password_verify($password, (string)$user->password)) {
            return [
                'id' => (string)$user->id,
                'username' => (string)$user->username,
                'role' => (string)$user->role,
                'nom' => (string)$user->nom,
            ];
        }
    }
    return null;
}

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: /index.php');
        exit;
    }
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function hasRole($roles) {
    $user = currentUser();
    if (!$user) return false;
    if (is_string($roles)) $roles = [$roles];
    return in_array($user['role'], $roles);
}
