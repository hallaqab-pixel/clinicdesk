<?php
function redirect(string $url): void {
    header('Location: ' . $url);
    exit();
}

function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string {
    return date('d/m/Y', strtotime($date));
}

function formatTime(string $time): string {
    return date('h:i A', strtotime($time));
}

function flashMessage(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isActivePage(string $page): string {
    return (($_GET['page'] ?? '') === $page) ? 'active' : '';
}