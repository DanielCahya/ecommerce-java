package com.example.ecommerce.service;

import com.example.ecommerce.dto.ProductDTO;
import com.example.ecommerce.entity.Product;
import com.example.ecommerce.repository.ProductRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.cache.annotation.Cacheable;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.time.DayOfWeek;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
public class ProductService {

    @Autowired
    private ProductRepository productRepository;

    @Cacheable("products")
    public List<ProductDTO> getAllProducts() {
        return productRepository.findAll().stream()
                .map(this::applyRules)
                .collect(Collectors.toList());
    }

    public ProductDTO getProductById(Long id) {
        Optional<Product> productOpt = productRepository.findById(id);
        return productOpt.map(this::applyRules).orElse(null);
    }

    public ProductDTO applyRules(Product product) {
        ProductDTO dto = new ProductDTO();
        dto.setId(product.getId());
        dto.setName(product.getName());
        dto.setDescription(product.getDescription());
        dto.setPrice(product.getPrice());
        dto.setStock(product.getStock());
        dto.setCategory(product.getCategory());
        dto.setImage(product.getImage());

        dto.setAlert(checkStockAlert(product));
        dto.setAuthRequired(checkAuthRequired(product));
        dto.setPromo(detectPromo(product));
        dto.setMaintenance(checkMaintenance(product));
        dto.setForceRefresh(checkForceRefresh(product));
        dto.setBadges(generateBadges(product));
        dto.setAvailability(checkAvailability(product, dto.isAuthRequired()));
        dto.setDisplayPrice(calculateDisplayPrice(product, dto.getPromo()));

        return dto;
    }

    private boolean checkStockAlert(Product product) {
        return product.getStock() != null && product.getStock() <= 0;
    }

    private boolean checkAuthRequired(Product product) {
        return product.getPrice() != null && product.getPrice().compareTo(new BigDecimal("1000000")) > 0;
    }

    private String detectPromo(Product product) {
        String name = product.getName() != null ? product.getName().toLowerCase() : "";
        String category = product.getCategory() != null ? product.getCategory().toLowerCase() : "";

        if (name.contains("flash") || name.contains("sale")) {
            return "FLASH_SALE_50";
        }
        if ("electronics".equals(category)) {
            return "ELECTRONIC_20";
        }
        if ("fashion".equals(category)) {
            return "FASHION_15";
        }
        DayOfWeek dayOfWeek = LocalDate.now().getDayOfWeek();
        if (dayOfWeek == DayOfWeek.SATURDAY || dayOfWeek == DayOfWeek.SUNDAY) {
            return "WEEKEND_25";
        }
        return null;
    }

    private boolean checkMaintenance(Product product) {
        String category = product.getCategory() != null ? product.getCategory().toLowerCase() : "";
        return "furniture".equals(category) || "automotive".equals(category);
    }

    private boolean checkForceRefresh(Product product) {
        String name = product.getName() != null ? product.getName().toLowerCase() : "";
        if (name.contains("flash")) {
            return true;
        }
        return product.getStock() != null && product.getStock() > 0 && product.getStock() <= 5;
    }

    private List<ProductDTO.Badge> generateBadges(Product product) {
        List<ProductDTO.Badge> badges = new ArrayList<>();

        if (checkStockAlert(product)) {
            badges.add(new ProductDTO.Badge("Stok Habis", "red", "🚫"));
        } else if (product.getStock() != null && product.getStock() <= 5) {
            badges.add(new ProductDTO.Badge("Stok Terbatas", "orange", "⚠️"));
        }

        String promo = detectPromo(product);
        if (promo != null) {
            int discount = extractDiscountFromPromo(promo);
            badges.add(new ProductDTO.Badge("Promo " + discount + "%", "yellow", "🏷️"));
        }

        if (product.getId() != null && product.getId() <= 10) {
            badges.add(new ProductDTO.Badge("Baru", "green", "✨"));
        }

        if (checkAuthRequired(product)) {
            badges.add(new ProductDTO.Badge("Premium", "purple", "👑"));
        }

        return badges;
    }

    private ProductDTO.Availability checkAvailability(Product product, boolean authRequired) {
        if (checkMaintenance(product)) {
            return new ProductDTO.Availability("maintenance", "Kategori sedang maintenance", false);
        }
        if (checkStockAlert(product)) {
            return new ProductDTO.Availability("out_of_stock", "Stok habis", false);
        }
        // Auth requirement checking usually depends on current user context.
        // We will pass down 'login_required' state so frontend can show the button.
        // Even if the endpoint requires auth to purchase, listing it needs to state if it's available.
        if (authRequired) {
            // Wait, in PHP: checkAuthRequired && !isLoggedIn -> login_required.
            // Since this API might be called anonymously, we should always return login_required 
            // if it's premium, or check SecurityContext to see if user is logged in.
            // For now, let's check SecurityContext.
            org.springframework.security.core.Authentication auth = org.springframework.security.core.context.SecurityContextHolder.getContext().getAuthentication();
            boolean isLoggedIn = auth != null && auth.isAuthenticated() && !(auth instanceof org.springframework.security.authentication.AnonymousAuthenticationToken);
            if (!isLoggedIn) {
                return new ProductDTO.Availability("login_required", "Login untuk melihat harga", false);
            }
        }

        return new ProductDTO.Availability("available", "Tersedia", true);
    }

    private ProductDTO.DisplayPrice calculateDisplayPrice(Product product, String promo) {
        ProductDTO.DisplayPrice dp = new ProductDTO.DisplayPrice();
        BigDecimal originalPrice = product.getPrice() != null ? product.getPrice() : BigDecimal.ZERO;
        dp.setOriginal(originalPrice);

        if (promo == null) {
            dp.setFinalPrice(originalPrice);
            dp.setDiscount(0);
            dp.setHasDiscount(false);
            dp.setSavings(BigDecimal.ZERO);
            return dp;
        }

        int discount = extractDiscountFromPromo(promo);
        BigDecimal discountFactor = new BigDecimal(discount).divide(new BigDecimal(100));
        BigDecimal finalPrice = originalPrice.subtract(originalPrice.multiply(discountFactor));

        dp.setFinalPrice(finalPrice);
        dp.setDiscount(discount);
        dp.setHasDiscount(true);
        dp.setSavings(originalPrice.subtract(finalPrice));

        return dp;
    }

    private int extractDiscountFromPromo(String promo) {
        java.util.regex.Matcher matcher = java.util.regex.Pattern.compile("(\\d+)").matcher(promo);
        if (matcher.find()) {
            return Integer.parseInt(matcher.group(1));
        }
        return 0;
    }
}
