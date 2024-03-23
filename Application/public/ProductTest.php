<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../app/models/Product.php";

final class ProductTest extends TestCase
{
    private $params = ["nome" => "nome", "marca" => "marca", "prezzo" => 1];

    public function testCreateMethod()
    {
        $product = Product::Create($this->params);

        $this->assertSame($this->params["nome"], $product->getNome());
        $this->assertSame($this->params["marca"], $product->getMarca());
        $this->assertEquals($this->params["prezzo"], $product->getPrezzo());
    }

    public function testGetLastInsertMethod()
    {
        Product::Create($this->params);
        $product = Product::getLastInsert();

        $this->assertSame($this->params["nome"], $product->getNome());
        $this->assertSame($this->params["marca"], $product->getMarca());
        $this->assertEquals($this->params["prezzo"], $product->getPrezzo());
    }

    public function testGetMethods()
    {
        $product = Product::Create($this->params);

        $this->assertSame(Product::getLastInsert()->getId(), $product->getId());
        $this->assertSame("nome", $product->getNome());
        $this->assertSame("marca", $product->getMarca());
        $this->assertEquals(1, $product->getPrezzo());
    }

    public function testSetMethods()
    {
        $product = Product::Create($this->params);

        $product->setId(2);
        $product->setNome("nome_modificato");
        $product->setMarca("marca_modificata");
        $product->setPrezzo(2);

        $this->assertSame(2, $product->getId());
        $this->assertSame("nome_modificato", $product->getNome());
        $this->assertSame("marca_modificata", $product->getMarca());
        $this->assertSame(2, $product->getPrezzo());
    }

    public function testFindByIdMethod()
    {
        $product = Product::Create($this->params);
        $product_found = Product::FindById($product->getId());

        $this->assertSame($this->params["nome"], $product_found->getNome());
        $this->assertSame($this->params["marca"], $product_found->getMarca());
        $this->assertEquals($this->params["prezzo"], $product_found->getPrezzo());
        $this->assertEquals($product->getId(), $product_found->getId());
    }

    public function testFetchAllMethod()
    {
        $product = Product::Create($this->params);
        $products_found = Product::FetchAll();

        $this->assertIsArray($products_found);
        $this->assertArrayHasKey(0, $products_found);
        $this->assertSame($this->params["nome"], end($products_found)->getNome());
        $this->assertSame($this->params["marca"], end($products_found)->getMarca());
        $this->assertEquals($this->params["prezzo"], end($products_found)->getPrezzo());
        $this->assertEquals($product->getId(), end($products_found)->getId());
    }

    public function testEditMethod()
    {
        $product = Product::Create($this->params);
        $edit_params = ["nome" => "nome_modificato", "marca" => "marca_modificata", "prezzo" => 2];
        $edited_product = $product->edit($edit_params);

        $this->assertSame($edit_params["nome"], $edited_product->getNome());
        $this->assertSame($edit_params["marca"], $edited_product->getMarca());
        $this->assertEquals($edit_params["prezzo"], $edited_product->getPrezzo());
        $this->assertEquals($product->getId(), $edited_product->getId());
    }

    public function testDeleteMethod()
    {
        $product = Product::Create($this->params);
        $product->delete();
        $products_found = Product::FetchAll();

        $this->assertNotEquals($product->getId(), end($products_found)->getId());
    }
}