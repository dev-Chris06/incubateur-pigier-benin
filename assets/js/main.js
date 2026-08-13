/**
 * main.js — Interactions publiques du site START PROJECT PIGIER-BÉNIN
 */

// ============================================================
// Menu hamburger mobile (exécution immédiate)
// ============================================================
(function() {
    const hamburger = document.querySelector('.menu-hamburger');
    const navMobile = document.querySelector('.nav-mobile');

    if (hamburger && navMobile) {
        function fermerMenu() {
            hamburger.classList.remove('ouvert');
            navMobile.classList.remove('ouvert');
            hamburger.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        function ouvrirMenu() {
            hamburger.classList.add('ouvert');
            navMobile.classList.add('ouvert');
            hamburger.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        hamburger.addEventListener('click', function() {
            if (navMobile.classList.contains('ouvert')) {
                fermerMenu();
            } else {
                ouvrirMenu();
            }
        });

        // Fermer le menu mobile au clic sur un lien
        const liensMobile = navMobile.querySelectorAll('a');
        liensMobile.forEach(function(lien) {
            lien.addEventListener('click', fermerMenu);
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function(e) {
            if (navMobile.classList.contains('ouvert') &&
                !navMobile.contains(e.target) &&
                !hamburger.contains(e.target)) {
                fermerMenu();
            }
        });

        // Fermer le menu si on passe en mode desktop (resize)
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && navMobile.classList.contains('ouvert')) {
                fermerMenu();
            }
        });
    }
})();

// ============================================================
// Fonctions exécutées au chargement du DOM
// ============================================================
document.addEventListener('DOMContentLoaded', function() {

    // ----------------------------------------------------------
    // Smooth scroll pour les liens ancre (#)
    // ----------------------------------------------------------
    document.querySelectorAll('a[href^="#"]').forEach(function(lien) {
        lien.addEventListener('click', function(e) {
            var cible = document.querySelector(this.getAttribute('href'));
            if (cible) {
                e.preventDefault();
                cible.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ----------------------------------------------------------
    // Auto-masquage des alertes après 5 secondes
    // ----------------------------------------------------------
    document.querySelectorAll('.alert').forEach(function(alerte) {
        setTimeout(function() {
            alerte.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alerte.style.opacity = '0';
            alerte.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                alerte.style.display = 'none';
            }, 500);
        }, 5000);
    });

    // ----------------------------------------------------------
    // Bouton retour en haut de page
    // ----------------------------------------------------------
    var btnHaut = document.createElement('button');
    btnHaut.innerHTML = '↑';
    btnHaut.className = 'btn-retour-haut';
    btnHaut.setAttribute('aria-label', 'Retour en haut de page');
    btnHaut.style.cssText = 'position:fixed;bottom:2rem;right:2rem;width:48px;height:48px;' +
        'border-radius:50%;background:#05059f;color:#fff;border:none;font-size:1.4rem;' +
        'cursor:pointer;opacity:0;visibility:hidden;transition:all 0.3s ease;z-index:999;' +
        'box-shadow:0 4px 12px rgba(5,5,159,0.3);display:flex;align-items:center;justify-content:center;';
    document.body.appendChild(btnHaut);

    btnHaut.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            btnHaut.style.opacity = '1';
            btnHaut.style.visibility = 'visible';
        } else {
            btnHaut.style.opacity = '0';
            btnHaut.style.visibility = 'hidden';
        }
    });

});
