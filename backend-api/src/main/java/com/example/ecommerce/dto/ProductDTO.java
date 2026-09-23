package com.example.ecommerce.dto;

import com.fasterxml.jackson.annotation.JsonProperty;
import java.math.BigDecimal;
import java.util.List;

public class ProductDTO {
    private Long id;
    private String name;
    private String description;
    private BigDecimal price;
    private Integer stock;
    private String category;
    private String image;

    private boolean alert;
    @JsonProperty("auth_required")
    private boolean authRequired;
    
    private String promo;
    private boolean maintenance;
    
    @JsonProperty("force_refresh")
    private boolean forceRefresh;
    
    private List<Badge> badges;
    private Availability availability;
    
    @JsonProperty("display_price")
    private DisplayPrice displayPrice;

    // Getters and Setters

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public BigDecimal getPrice() { return price; }
    public void setPrice(BigDecimal price) { this.price = price; }

    public Integer getStock() { return stock; }
    public void setStock(Integer stock) { this. stock = stock; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public String getImage() { return image; }
    public void setImage(String image) { this.image = image; }

    public boolean isAlert() { return alert; }
    public void setAlert(boolean alert) { this.alert = alert; }

    public boolean isAuthRequired() { return authRequired; }
    public void setAuthRequired(boolean authRequired) { this.authRequired = authRequired; }

    public String getPromo() { return promo; }
    public void setPromo(String promo) { this.promo = promo; }

    public boolean isMaintenance() { return maintenance; }
    public void setMaintenance(boolean maintenance) { this.maintenance = maintenance; }

    public boolean isForceRefresh() { return forceRefresh; }
    public void setForceRefresh(boolean forceRefresh) { this.forceRefresh = forceRefresh; }

    public List<Badge> getBadges() { return badges; }
    public void setBadges(List<Badge> badges) { this.badges = badges; }

    public Availability getAvailability() { return availability; }
    public void setAvailability(Availability availability) { this.availability = availability; }

    public DisplayPrice getDisplayPrice() { return displayPrice; }
    public void setDisplayPrice(DisplayPrice displayPrice) { this.displayPrice = displayPrice; }

    // Nested classes
    public static class Badge {
        private String text;
        private String color;
        private String icon;

        public Badge(String text, String color, String icon) {
            this.text = text;
            this.color = color;
            this.icon = icon;
        }

        public String getText() { return text; }
        public String getColor() { return color; }
        public String getIcon() { return icon; }
    }

    public static class Availability {
        private String status;
        private String message;
        private boolean available;

        public Availability(String status, String message, boolean available) {
            this.status = status;
            this.message = message;
            this.available = available;
        }

        public String getStatus() { return status; }
        public String getMessage() { return message; }
        public boolean isAvailable() { return available; }
    }

    public static class DisplayPrice {
        private BigDecimal original;
        
        @JsonProperty("final")
        private BigDecimal finalPrice;
        
        private Integer discount;
        
        @JsonProperty("has_discount")
        private boolean hasDiscount;
        
        private BigDecimal savings;

        public BigDecimal getOriginal() { return original; }
        public void setOriginal(BigDecimal original) { this.original = original; }

        public BigDecimal getFinalPrice() { return finalPrice; }
        public void setFinalPrice(BigDecimal finalPrice) { this.finalPrice = finalPrice; }

        public Integer getDiscount() { return discount; }
        public void setDiscount(Integer discount) { this.discount = discount; }

        public boolean isHasDiscount() { return hasDiscount; }
        public void setHasDiscount(boolean hasDiscount) { this.hasDiscount = hasDiscount; }

        public BigDecimal getSavings() { return savings; }
        public void setSavings(BigDecimal savings) { this.savings = savings; }
    }
}

