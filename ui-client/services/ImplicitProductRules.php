<?php

class ImplicitProductRules {
    
    public static function applyRules($product) {
        $product['alert'] = self::checkStockAlert($product);
        
        $product['auth_required'] = self::checkAuthRequired($product);
        
        $product['promo'] = self::detectPromo($product);
        
        $product['maintenance'] = self::checkMaintenance($product);
        
        $product['force_refresh'] = self::checkForceRefresh($product);
        
        $product['badges'] = self::generateBadges($product);
        
        $product['availability'] = self::checkAvailability($product);
        
        $product['display_price'] = self::calculateDisplayPrice($product);
        
        return $product;
    }

    // Apply rules to multiple products
    public static function applyRulesToList($products) {
        return array_map([self::class, 'applyRules'], $products);
    }

    //  Stock Alert - Notifikasi Stok Habis
    private static function checkStockAlert($product) {
        return isset($product['stock']) && $product['stock'] <= 0;
    }

    // Auth Required - Wajib Login untuk produk premium
    private static function checkAuthRequired($product) {
        return isset($product['price']) && $product['price'] > 1000000;
    }

    // Promo Detection - Deteksi promo berdasarkan nama/kategori
    private static function detectPromo($product) {
        $name = strtolower($product['name'] ?? '');
        $category = strtolower($product['category'] ?? '');
        
        if (strpos($name, 'flash') !== false || strpos($name, 'sale') !== false) {
            return 'FLASH_SALE_50';
        }
        
        if ($category === 'electronics') {
            return 'ELECTRONIC_20';
        }
        
        if ($category === 'fashion') {
            return 'FASHION_15';
        }
        
        if (date('N') >= 6) {
            return 'WEEKEND_25';
        }
        
        return null;
    }

    // Maintenance Mode - Per kategori maintenance
    private static function checkMaintenance($product) {
        $maintenanceCategories = ['furniture', 'automotive'];
        $category = strtolower($product['category'] ?? '');
        
        return in_array($category, $maintenanceCategories);
    }

    // Force Refresh - Untuk produk time-sensitive
    private static function checkForceRefresh($product) {
        $name = strtolower($product['name'] ?? '');
        
        if (strpos($name, 'flash') !== false) {
            return true;
        }

        if (isset($product['stock']) && $product['stock'] > 0 && $product['stock'] <= 5) {
            return true;
        }
        
        return false;
    }

    // Badge System
    private static function generateBadges($product) {
        $badges = [];
        
        if (self::checkStockAlert($product)) {
            $badges[] = [
                'text' => 'Stok Habis',
                'color' => 'red',
                'icon' => '🚫'
            ];
        } elseif (isset($product['stock']) && $product['stock'] <= 5) {
            $badges[] = [
                'text' => 'Stok Terbatas',
                'color' => 'orange',
                'icon' => '⚠️'
            ];
        }
        
        $promo = self::detectPromo($product);
        if ($promo) {
            $discount = self::extractDiscountFromPromo($promo);
            $badges[] = [
                'text' => "Promo $discount%",
                'color' => 'yellow',
                'icon' => '🏷️'
            ];
        }
        
        if (isset($product['id']) && $product['id'] <= 10) {
            $badges[] = [
                'text' => 'Baru',
                'color' => 'green',
                'icon' => '✨'
            ];
        }
        
        if (self::checkAuthRequired($product)) {
            $badges[] = [
                'text' => 'Premium',
                'color' => 'purple',
                'icon' => '👑'
            ];
        }
        
        return $badges;
    }

    // Availability Status
    private static function checkAvailability($product) {
        if (self::checkMaintenance($product)) {
            return [
                'status' => 'maintenance',
                'message' => 'Kategori sedang maintenance',
                'available' => false
            ];
        }
        
        if (self::checkStockAlert($product)) {
            return [
                'status' => 'out_of_stock',
                'message' => 'Stok habis',
                'available' => false
            ];
        }
        
        if (self::checkAuthRequired($product) && !isLoggedIn()) {
            return [
                'status' => 'login_required',
                'message' => 'Login untuk melihat harga',
                'available' => false
            ];
        }
        
        return [
            'status' => 'available',
            'message' => 'Tersedia',
            'available' => true
        ];
    }

    // Calculate Display Price with Promo
    private static function calculateDisplayPrice($product) {
        $originalPrice = $product['price'] ?? 0;
        $promo = self::detectPromo($product);
        
        if (!$promo) {
            return [
                'original' => $originalPrice,
                'final' => $originalPrice,
                'discount' => 0,
                'has_discount' => false
            ];
        }
        
        $discount = self::extractDiscountFromPromo($promo);
        $finalPrice = $originalPrice - ($originalPrice * ($discount / 100));
        
        return [
            'original' => $originalPrice,
            'final' => $finalPrice,
            'discount' => $discount,
            'has_discount' => true,
            'savings' => $originalPrice - $finalPrice
        ];
    }

    // HELPER METHODS

    private static function extractDiscountFromPromo($promo) {
        if (preg_match('/(\d+)/', $promo, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }
}