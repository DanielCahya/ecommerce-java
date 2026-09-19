<?php

require_once __DIR__ . '/ApiClient.php';

class ProductService {
    private $client;

    public function __construct() {
        $this->client = new ApiClient();
    }

    public function getAllProducts() {
        return $this->client->get('products');
    }

    public function getProductById($id) {
        return $this->client->get("products/$id");
    }

    public function searchProducts($keyword) {
        return $this->client->get("products/search/$keyword");
    }

    public function getProductsByCategory($category) {
        return $this->client->get("products/category/$category");
    }

    public function addProduct($productData) {
        return $this->client->post('products', $productData);
    }

    public function updateProduct($id, $productData) {
        return $this->client->put("products/$id", $productData);
    }

    public function deleteProduct($id) {
        return $this->client->delete("products/$id");
    }

    public function checkout($cartItems) {
        return $this->client->post('products/checkout', $cartItems);
    }
}