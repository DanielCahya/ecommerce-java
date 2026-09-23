package com.example.ecommerce.controller;

import com.example.ecommerce.dto.JwtResponse;
import com.example.ecommerce.dto.LoginRequest;
import com.example.ecommerce.security.JwtUtils;
import com.example.ecommerce.security.UserDetailsImpl;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/api/auth")
public class AuthController {

    @Autowired
    private AuthenticationManager authenticationManager;

    @Autowired
    private JwtUtils jwtUtils;

    @PostMapping("/login")
    public ResponseEntity<?> authenticateUser(@RequestBody LoginRequest loginRequest) {
        Authentication authentication = authenticationManager.authenticate(
                new UsernamePasswordAuthenticationToken(loginRequest.getUsername(), loginRequest.getPassword()));

        SecurityContextHolder.getContext().setAuthentication(authentication);
        String jwt = jwtUtils.generateJwtToken(authentication);

        UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();

        JwtResponse jwtResponse = new JwtResponse(jwt,
                userDetails.getId(),
                userDetails.getUsername(),
                userDetails.getRole());

        // The PHP frontend expects { success: true, data: { token: '...', user: {...} } }
        // Let's match exactly what the frontend ApiClient expects.
        Map<String, Object> responseData = new HashMap<>();
        responseData.put("token", jwt);
        
        Map<String, Object> userMap = new HashMap<>();
        userMap.put("id", userDetails.getId());
        userMap.put("username", userDetails.getUsername());
        userMap.put("role", userDetails.getRole());
        responseData.put("user", userMap);

        return ResponseEntity.ok(responseData);
    }
}
