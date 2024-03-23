<?php

require_once __DIR__ . "/../app/models/Product.php";

$params = [
    "marca" => "Nike",
    "nome" => "Superstar",
    "prezzo" => 100
];

$product = Product::Create($params);
$product_found = Product::FindById($product->getId());

echo $product_found->getMarca() . "\n";
echo $product_found->getNome() . "\n";
echo $product_found->getPrezzo() . "\n";

$product->delete();

