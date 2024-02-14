<?php

class DB
{
    private $dbHost     = "127.0.0.1";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "phpmailer";
    private $conn;

    public function __construct()
    {
        if (!isset($this->conn)) {
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            if ($conn->connect_error) {
                die("Falha ao tentar acessar o banco de Dados: " . $conn->connect_error);
            } else {
                $this->conn = $conn;
            }
        }
    }

    public function is_table_empty()
    {
        $result = $this->conn->query("SELECT id FROM oauth_tokens WHERE provider = 'google'");
        if ($result->num_rows) {
            return false;
        }
        return true;
       
    }

    public function get_refresh_token()
    {
        $sql = $this->conn->query("SELECT provider_value FROM oauth_tokens WHERE provider = 'google'");

        if ($sql->num_rows >= 1) {
            $result = $sql->fetch_assoc();
            return $result['provider_value'];
        }
        return false;
    }

    public function update_refresh_token($token)
    {
        if ($this->is_table_empty()) {
            $sql = sprintf("INSERT INTO oauth_tokens(provider, provider_value) VALUES ('%s','%s')", 'google', $this->conn->real_escape_string($token));
            $this->conn->query($sql);
        } else {
            $sql = sprintf("UPDATE oauth_tokens SET provider_value = '%s' WHERE provider = '%s'", $this->conn->real_escape_string($token), 'google');
            $this->conn->query($sql);
        }
    }
}
