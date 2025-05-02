<?php
namespace Data;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username OR email = :email');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $username, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);
        
        return $user ?: null;
    }

    public function create(array $userData): bool {
        try {
            $stmt = $this->db->prepare('
                INSERT INTO users (username, email, password, full_name)
                VALUES (:username, :email, :password, :full_name)
            ');

            $stmt->bindValue(':username', $userData['username'], SQLITE3_TEXT);
            $stmt->bindValue(':email', $userData['email'], SQLITE3_TEXT);
            $stmt->bindValue(':password', password_hash($userData['password'], PASSWORD_DEFAULT), SQLITE3_TEXT);
            $stmt->bindValue(':full_name', $userData['full_name'] ?? '', SQLITE3_TEXT);

            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            return false;
        }
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);
        
        return $user ?: null;
    }

    public function update(int $id, array $userData): bool {
        try {
            $fields = [];
            $values = [':id' => $id];

            foreach ($userData as $key => $value) {
                if ($key !== 'id' && $key !== 'password') {
                    $fields[] = "$key = :$key";
                    $values[":$key"] = $value;
                }
            }

            if (isset($userData['password'])) {
                $fields[] = "password = :password";
                $values[':password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }

            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->db->prepare($sql);

            foreach ($values as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
            }

            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }
} 