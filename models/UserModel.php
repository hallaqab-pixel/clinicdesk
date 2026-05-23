<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {

    public function findById(int $id): ?array {
        $result = $this->execute('SELECT * FROM users WHERE id = ?', 'i', [$id]);
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array {
        $result = $this->execute('SELECT * FROM users WHERE email = ?', 's', [$email]);
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)',
            'sssss',
            [$data['name'], $data['email'], $data['password'], $data['role'], $data['phone'] ?? null]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        return $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?',
            'sssi',
            [$data['name'], $data['phone'] ?? null, $data['avatar'] ?? null, $id]
        );
    }

    public function updatePassword(int $id, string $newHash): bool {
        return $this->execute(
            'UPDATE users SET password = ? WHERE id = ?',
            'si',
            [$newHash, $id]
        );
    }

    public function getAllPaginated(int $page, string $role = ''): array {
        $perPage = ITEMS_PER_PAGE;
        $offset  = ($page - 1) * $perPage;

        if ($role) {
            $result = $this->execute(
                'SELECT * FROM users WHERE role = ? LIMIT ? OFFSET ?',
                'sii',
                [$role, $perPage, $offset]
            );
        } else {
            $result = $this->execute(
                'SELECT * FROM users LIMIT ? OFFSET ?',
                'ii',
                [$perPage, $offset]
            );
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(string $role = ''): int {
        if ($role) {
            $result = $this->execute(
                'SELECT COUNT(*) as total FROM users WHERE role = ?',
                's',
                [$role]
            );
        } else {
            $result = $this->execute('SELECT COUNT(*) as total FROM users');
        }
        return (int) $result->fetch_assoc()['total'];
    }

    public function toggleActive(int $id): bool {
        return $this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i',
            [$id]
        );
    }
}