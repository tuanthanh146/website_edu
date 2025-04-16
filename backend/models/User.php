<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    password = :password,
                    role = 'user',
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id, name, password, role
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->password = $row['password'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    public function createPasswordReset($token, $expiry) {
        $query = "INSERT INTO password_resets
                SET
                    email = :email,
                    token = :token,
                    expiry = :expiry,
                    created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $token = htmlspecialchars(strip_tags($token));
        $expiry = htmlspecialchars(strip_tags($expiry));

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expiry", $expiry);

        return $stmt->execute();
    }

    public function verifyPasswordReset($token) {
        $query = "SELECT email, expiry
                FROM password_resets
                WHERE token = ?
                AND expiry > NOW()
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $token = htmlspecialchars(strip_tags($token));
        $stmt->bindParam(1, $token);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->email = $row['email'];
            return true;
        }
        return false;
    }

    public function updatePassword($password) {
        $query = "UPDATE " . $this->table_name . "
                SET
                    password = :password,
                    updated_at = NOW()
                WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password);

        if($stmt->execute()) {
            $this->deletePasswordReset();
            return true;
        }
        return false;
    }

    private function deletePasswordReset() {
        $query = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        return $stmt->execute();
    }
}
?> 