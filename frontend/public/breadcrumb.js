// Auto-insert breadcrumb similar to UMKM across pages (except specific exclusions)
(function() {
  try {
    // Exclusion rules: index, external links like survey/ppid/facebook/instagram are not pages here
    const path = window.location.pathname.toLowerCase();
    const file = path.split('/').pop();
    const decodedFile = decodeURIComponent(file);
    if (!file || file === 'index.html') return; // skip index

    // Determine page title text
    const h1 = document.querySelector('h1, .hero-title, .display-5, .display-4');
    const titleText = (h1 && h1.textContent.trim()) || document.title || 'Halaman';

    // Build breadcrumb element
    const breadcrumb = document.createElement('div');
    breadcrumb.className = 'breadcrumb-nav mb-4';
    breadcrumb.innerHTML = `
      <a href="index.html">
        <i class="bi bi-house-fill"></i>
        Beranda
      </a>
      <i class="bi bi-chevron-right"></i>
      <span>${titleText}</span>
    `;

    // Page-specific tweak: lower breadcrumb for RKP DU RKP page (responsive)
    if (decodedFile === 'rkp du rkp.html') {
      // Use class + CSS so media queries can take effect (avoid inline style override)
      breadcrumb.classList.add('breadcrumb--rkp');
      // Ensure it sits slightly lower to avoid overlapping navbar
      breadcrumb.style.marginTop = '80px'; // tweak as needed
      if (!document.querySelector('#breadcrumb-rkp-style')) {
        const rkpStyle = document.createElement('style');
        rkpStyle.id = 'breadcrumb-rkp-style';
        rkpStyle.textContent = `
          /* Extra offset for breadcrumb on RKP page (fallback if inline not applied) */
          .breadcrumb--rkp { margin-top: 80px; }
        `;
        document.head.appendChild(rkpStyle);
      }
    }

    // Page-specific tweak: lower breadcrumb slightly for APB Nagari page
    if (decodedFile === 'apb nagari.html') {
      breadcrumb.classList.add('breadcrumb--apb');
      breadcrumb.style.marginTop = '60px'; // small drop to clear navbar
      if (!document.querySelector('#breadcrumb-apb-style')) {
        const apbStyle = document.createElement('style');
        apbStyle.id = 'breadcrumb-apb-style';
        apbStyle.textContent = `
          .breadcrumb--apb { margin-top: 60px; }
        `;
        document.head.appendChild(apbStyle);
      }
    }

    // Page-specific tweak: lower breadcrumb slightly for LPPN page
    if (decodedFile === 'lppn.html') {
      breadcrumb.classList.add('breadcrumb--lppn');
      breadcrumb.style.marginTop = '60px'; // small drop to clear navbar
      if (!document.querySelector('#breadcrumb-lppn-style')) {
        const lppnStyle = document.createElement('style');
        lppnStyle.id = 'breadcrumb-lppn-style';
        lppnStyle.textContent = `
          .breadcrumb--lppn { margin-top: 60px; }
        `;
        document.head.appendChild(lppnStyle);
      }
    }

    // Page-specific tweak: lower breadcrumb slightly for LPJ page
    if (decodedFile === 'lpj.html') {
      breadcrumb.classList.add('breadcrumb--lpj');
      breadcrumb.style.marginTop = '60px'; // small drop to clear navbar
      if (!document.querySelector('#breadcrumb-lpj-style')) {
        const lpjStyle = document.createElement('style');
        lpjStyle.id = 'breadcrumb-lpj-style';
        lpjStyle.textContent = `
          .breadcrumb--lpj { margin-top: 60px; }
        `;
        document.head.appendChild(lpjStyle);
      }
    }

    // Page-specific tweak: lower breadcrumb slightly for LKPPN page
    if (decodedFile === 'lkppn.html') {
      breadcrumb.classList.add('breadcrumb--lkppn');
      breadcrumb.style.marginTop = '80px'; // drop a bit more to clear navbar
      if (!document.querySelector('#breadcrumb-lkppn-style')) {
        const lkppnStyle = document.createElement('style');
        lkppnStyle.id = 'breadcrumb-lkppn-style';
        lkppnStyle.textContent = `
          .breadcrumb--lkppn { margin-top: 80px; }
        `;
        document.head.appendChild(lkppnStyle);
      }
    }

    // Insert target: prefer inside hero section like UMKM
    let container = document.querySelector('.hero-content') || document.querySelector('.hero-section .container') || document.querySelector('.hero .container');
    if (!container) {
      // Fallback: insert at top of main content
      container = document.querySelector('main .container, section .container, .container');
    }

    if (container) {
      // Center align container for breadcrumb consistency
      try {
        const style = getComputedStyle(container);
        if (style.textAlign !== 'center') {
          container.style.textAlign = 'center';
        }
      } catch (_) {}

      // If there is already a breadcrumb, avoid duplicates
      if (!container.querySelector('.breadcrumb-nav')) {
        // Place before the first heading in container for better layout
        const firstHeading = container.querySelector('h1, .hero-title, h2');
        if (firstHeading && firstHeading.parentNode === container) {
          container.insertBefore(breadcrumb, firstHeading);
        } else {
          container.prepend(breadcrumb);
        }
      }
    }

    // Inject minimal styles if not present (so non-UMKM pages get the look)
    if (!document.querySelector('#breadcrumb-inline-style')) {
      const style = document.createElement('style');
      style.id = 'breadcrumb-inline-style';
      style.textContent = `
        .breadcrumb-nav { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 50px; padding: 12px 24px; display: inline-flex; align-items: center; gap: 10px; border: 1px solid rgba(255,255,255,0.2); margin-top: 14px; }
        .breadcrumb-nav a { color: inherit; text-decoration: none; font-weight: 500; }
        .breadcrumb-nav a:hover { color: #ffd700; }
      `;
      document.head.appendChild(style);
    }
  } catch (e) {
    console.error('breadcrumb.js error:', e);
  }
})();