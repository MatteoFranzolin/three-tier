<?php

require_once __DIR__ . "/../models/Product.php";

class ProductController
{
    public function add($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($params['data']['attributes']['marca'], $params['data']['attributes']['nome'], $params['data']['attributes']['prezzo'])) {
            $params = $params['data']['attributes'];
            $product = Product::Create($params);
            if ($product) {
                $data = [
                    'type' => $product->getType(),
                    'id' => $product->getId(),
                    'attributes' => [
                        'marca' => $product->getMarca(),
                        'nome' => $product->getNome(),
                        'prezzo' => $product->getPrezzo()
                    ]
                ];

                $response = [
                    'data' => $data
                ];
                header('Location: /products/' . $product->getId());
                header('HTTP/1.1 201 CREATED');
                header('Content-Type: application/vnd.api+json');
                echo json_encode($response);
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(405);
        }
        exit();
    }

    public function view()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $products = Product::FetchAll();
            if ($products) {
                $data = [];
                foreach ($products as $product) {
                    $data[] = [
                        'type' => $product->getType(),
                        'id' => $product->getId(),
                        'attributes' => [
                            'marca' => $product->getMarca(),
                            'nome' => $product->getNome(),
                            'prezzo' => $product->getPrezzo()
                        ]
                    ];
                }

                $response = [
                    'data' => $data
                ];
                header('Location: /products');
                header('HTTP/1.1 200 OK');
                header('Content-Type: application/vnd.api+json');
                echo json_encode($response);
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(405);
        }
        exit();
    }

    public function viewProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $product = Product::FindById($id);
            if ($product) {
                $data = [
                    'type' => $product->getType(),
                    'id' => $product->getId(),
                    'attributes' => [
                        'marca' => $product->getMarca(),
                        'nome' => $product->getNome(),
                        'prezzo' => $product->getPrezzo()
                    ]
                ];

                $response = [
                    'data' => $data
                ];
                header('Location: /products/' . $id);
                header('HTTP/1.1 200 OK');
                header('Content-Type: application/vnd.api+json');
                echo json_encode($response);
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(405);
        }
        exit();
    }

    public function deleteProduct($id)
    {
        $product = Product::FindById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $product) {
            $product->delete();
            http_response_code(204);
        } else {
            http_response_code(404);
        }
    }

    public function updateProduct($id, $params)
    {
        $product = Product::FindById($id);
        if (!$product) {
            http_response_code(404);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && (isset($params['data']['attributes']['marca']) || isset($params['data']['attributes']['nome']) || isset($params['data']['attributes']['prezzo']))) {
            $params = $params['data']['attributes'];
            $product = $product->edit($params);
            $data = [
                'type' => $product->getType(),
                'id' => $product->getId(),
                'attributes' => [
                    'marca' => $product->getMarca(),
                    'nome' => $product->getNome(),
                    'prezzo' => $product->getPrezzo()
                ]
            ];

            $response = [
                'data' => $data
            ];
            header('Location: /products/' . $product->getId());
            header('HTTP/1.1 200 OK');
            header('Content-Type: application/vnd.api+json');
            echo json_encode($response);
        } else {
            http_response_code(405);
        }
        exit();
    }
}
