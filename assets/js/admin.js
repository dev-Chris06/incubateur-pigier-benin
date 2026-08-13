/**
 * admin.js — Interactions du back-office d'administration
 */

document.addEventListener('DOMContentLoaded', function() {

    // ==============================================================
    // Aperçu d'image avant upload
    // ==============================================================
    document.querySelectorAll('.admin-form input[type="file"]').forEach(function(input) {
        input.addEventListener('change', function() {
            // Supprimer l'aperçu précédent
            var ancienApercu = input.parentElement.querySelector('.apercu-image');
            if (ancienApercu) ancienApercu.remove();

            if (this.files && this.files[0]) {
                var fichier = this.files[0];

                // Vérifier que c'est bien une image
                if (!fichier.type.startsWith('image/')) return;

                var lecteur = new FileReader();
                lecteur.onload = function(e) {
                    var conteneur = document.createElement('div');
                    conteneur.className = 'apercu-image';
                    conteneur.style.cssText = 'margin-top:0.75rem;';

                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Aperçu de l\'image';
                    img.style.cssText = 'max-width:200px;max-height:150px;border-radius:8px;' +
                        'border:2px solid #ddd;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.1);';

                    var info = document.createElement('p');
                    info.style.cssText = 'font-size:0.8rem;color:#666;margin-top:0.25rem;';
                    var taille = (fichier.size / 1024).toFixed(1);
                    info.textContent = fichier.name + ' (' + taille + ' Ko)';

                    conteneur.appendChild(img);
                    conteneur.appendChild(info);
                    input.parentElement.appendChild(conteneur);
                };
                lecteur.readAsDataURL(fichier);
            }
        });
    });

    // ==============================================================
    // Auto-masquage des alertes admin après 4 secondes
    // ==============================================================
    document.querySelectorAll('.alert').forEach(function(alerte) {
        setTimeout(function() {
            alerte.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alerte.style.opacity = '0';
            alerte.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                alerte.style.display = 'none';
            }, 400);
        }, 4000);
    });

    // ==============================================================
    // Compteur de caractères pour les textareas admin
    // ==============================================================
    document.querySelectorAll('.form-textarea').forEach(function(textarea) {
        var compteur = document.createElement('div');
        compteur.className = 'compteur-caracteres';
        compteur.style.cssText = 'font-size:0.75rem;color:#888;text-align:right;margin-top:0.25rem;';
        textarea.parentElement.appendChild(compteur);

        function mettreAJour() {
            compteur.textContent = textarea.value.length + ' caractères';
        }

        textarea.addEventListener('input', mettreAJour);
        mettreAJour();
    });

    // ==============================================================
    // Protection contre la navigation accidentelle avec formulaire modifié
    // ==============================================================
    var formulaireAdmin = document.querySelector('.admin-form');
    if (formulaireAdmin) {
        var formulaireModifie = false;

        formulaireAdmin.addEventListener('input', function() {
            formulaireModifie = true;
        });

        formulaireAdmin.addEventListener('change', function() {
            formulaireModifie = true;
        });

        formulaireAdmin.addEventListener('submit', function() {
            formulaireModifie = false;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formulaireModifie) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

});
