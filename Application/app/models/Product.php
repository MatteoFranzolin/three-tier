<?php

require_once __DIR__ . "/connection/Database.php";

class Product
{
    private $id, $nome, $marca, $prezzo;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getPrezzo()
    {
        return $this->prezzo;
    }

    public function setPrezzo($prezzo)
    {
        $this->prezzo = $prezzo;
    }

    public function getType()
    {
        return "products";
    }

    public static function Create($params)
    {
        /*$duplicate = self::CheckDuplicates($params['marca'], $params['nome']);
        if ($duplicate) {
            return false;
        }*/
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("insert into matteo_franzolin_ecommerce.products (marca, nome, prezzo) values (:marca, :nome, :prezzo)");
        $stmt->bindParam(":marca", $params['marca']);
        $stmt->bindParam(":nome", $params['nome']);
        $stmt->bindParam(":prezzo", $params['prezzo']);
        if (!$stmt->execute()) {
            return false;
        }
        return self::getLastInsert();
    }

    private static function CheckDuplicates($marca, $nome)
    {
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("select * from matteo_franzolin_ecommerce.products where marca=:marca and nome=:nome");
        $stmt->bindParam(":marca", $marca);
        $stmt->bindParam(":nome", $nome);
        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->fetchObject("Product");
    }

    public static function getLastInsert()
    {
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("select * from matteo_franzolin_ecommerce.products order by id desc limit 1");
        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->fetchObject("Product");
    }

    public static function FindById($id)
    {
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("select * from matteo_franzolin_ecommerce.products where id= :id");
        $stmt->bindParam(":id", $id);
        if (!$stmt->execute()) {
            return false;
        }
        return $stmt->fetchObject('Product');
    }

    public static function FetchAll()
    {
        $pdo = self::connectToDatabase();
        $stmt = "select * from products";
        return $pdo->query($stmt)->fetchAll(PDO::FETCH_CLASS, 'Product');
    }

    public function edit($params)
    {
        $id = $this->getId();
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("update matteo_franzolin_ecommerce.products set marca=:marca,nome=:nome,prezzo=:prezzo where id=:id");
        $stmt->bindParam(":marca", $params['marca']);
        $stmt->bindParam(":nome", $params['nome']);
        $stmt->bindParam(":prezzo", $params['prezzo']);
        $stmt->bindParam(":id", $id);
        if (!$stmt->execute()) {
            return false;
        }
        return Product::FindById($id);
    }

    public function delete()
    {
        $id = $this->getId();
        $pdo = self::connectToDatabase();
        $stmt = $pdo->prepare("delete from matteo_franzolin_ecommerce.products where id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    private static function connectToDatabase()
    {

        //$database = new Database("192.168.2.200", "matteo_franzolin", "transliterates.paganism.OfficeMax."); // A SCUOLA
        $database = new Database("127.0.0.1", "root", "root");
        return $database->connect("matteo_franzolin_ecommerce");
    }
}