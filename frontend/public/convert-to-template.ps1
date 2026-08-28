# Script untuk mengubah file HTML agar menggunakan template navbar dan footer

$files = @(
    "umkm.html",
    "Sejarah.html", 
    "Visi Dan Misi.html",
    "Struktur Nagari.html",
    "Perangkat Nagari.html",
    "Profil Walinagari.html"
)

foreach ($file in $files) {
    $filePath = "d:\MAGANG\Nagari Koto Malintang\frontend\public\$file"
    
    if (Test-Path $filePath) {
        Write-Host "Processing $file..."
        
        # Read file content
        $content = Get-Content $filePath -Raw
        
        # Add footer CSS link if not exists
        if ($content -notmatch 'footer-styles\.css') {
            $content = $content -replace '(<link href="https://unpkg\.com/aos@2\.3\.1/dist/aos\.css" rel="stylesheet">)', '$1`n    <link href="footer-styles.css" rel="stylesheet"/>'
        }
        
        # Replace navbar with template placeholder
        $content = $content -replace '(<body[^>]*>)\s*<!--.*?NAVBAR.*?-->\s*<nav.*?</nav>', '$1`n    <!-- Navbar placeholder -->`n    <div id="navbar"></div>'
        
        # Add footer placeholder and template loading script before closing body
        if ($content -notmatch 'id="footer"') {
            $templateScript = @"

      // Load navbar template
      fetch('navbar.html')
        .then(response => response.text()) 
        .then(data => {
          document.getElementById('navbar').innerHTML = data;
          
          // Initialize navbar scroll effect setelah navbar dimuat
          initNavbarScrollEffect();
        })
        .catch(error => {
          console.error('Error loading navbar:', error);
        });

      // Load footer template
      fetch('footer.html')
        .then(response => response.text()) 
        .then(data => {
          document.getElementById('footer').innerHTML = data;
        })
        .catch(error => {
          console.error('Error loading footer:', error);
        });

      // Function untuk inisialisasi navbar scroll effect
      function initNavbarScrollEffect() {
        // Set initial state
        const navbar = document.querySelector(".navbar");
        if (navbar) {
          // Set initial transparent state
          navbar.classList.add("transparent");
          
          // Add scroll event listener
          window.addEventListener("scroll", function () {
            if (window.scrollY > 100) {
              navbar.classList.remove("transparent");
              navbar.classList.add("scrolled");
            } else {
              navbar.classList.remove("scrolled");
              navbar.classList.add("transparent");
            }
          });
        }
      }
    </script>

    <!-- Footer placeholder -->
    <div id="footer"></div>
  </body>
</html>
"@
            
            $content = $content -replace '(\s*</script>\s*</body>\s*</html>)', $templateScript
        }
        
        # Write back to file
        Set-Content $filePath -Value $content -Encoding UTF8
        
        Write-Host "✓ $file updated successfully"
    } else {
        Write-Host "✗ $file not found"
    }
}

Write-Host "`nAll files processed!"