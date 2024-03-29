<?php
class  Database{
  public $pdo;
  public function __construct() {
    if (!isset($this->pdo)) {
      try {
        $link = new PDO('mysql:host='.DB_HOST.'; dbname='.DB_NAME, DB_USER, DB_PASS);
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        //$link->exec("SET CHARACTER SET utf8");
        $this->pdo  =  $link;
      } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
      }
    }
  }
}
