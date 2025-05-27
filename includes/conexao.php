<?php
$host = 'localhost';
$db   = 'controle_financeiro';
$user = 'root';
$pass = ''; // ajuste conforme seu ambiente

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  die("Erro na conexão: " . $e->getMessage());
}
