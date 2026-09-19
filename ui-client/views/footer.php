</main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 E-Commerce UI Client. Powered by Java API + PHP.</p>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
    
    <!-- Handle Force Refresh -->
    <script>
        // Check if any product on page has force_refresh flag
        if (typeof shouldForceRefresh !== 'undefined' && shouldForceRefresh) {
            // Auto refresh every 30 seconds
            setTimeout(() => {
                location.reload();
            }, 30000);
            
            // Show refresh countdown
            let countdown = 30;
            const countdownEl = document.createElement('div');
            countdownEl.className = 'refresh-countdown';
            countdownEl.innerHTML = `🔄 Halaman akan refresh dalam <span id="countdown">${countdown}</span> detik`;
            document.body.appendChild(countdownEl);
            
            setInterval(() => {
                countdown--;
                document.getElementById('countdown').textContent = countdown;
                if (countdown <= 0) countdown = 30;
            }, 1000);
        }
    </script>

    <!-- Load cart count -->
    <script>
        updateCartCount();
    </script>
</body>
</html>