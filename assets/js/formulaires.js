/**
 * formulaires.js — Validation côté client des formulaires publics
 * Complète la validation serveur existante (ne la remplace pas)
 */

document.addEventListener('DOMContentLoaded', function() {

    // ==============================================================
    // Validation des formulaires
    // ==============================================================
    var formulaires = document.querySelectorAll('.formulaire');

    formulaires.forEach(function(formulaire) {
        formulaire.addEventListener('submit', function(e) {
            // Supprimer les erreurs précédentes
            formulaire.querySelectorAll('.champ-erreur').forEach(function(err) {
                err.remove();
            });
            formulaire.querySelectorAll('.formulaire-input.invalide, .formulaire-textarea.invalide').forEach(function(champ) {
                champ.classList.remove('invalide');
            });

            var erreurs = [];

            // Vérifier les champs requis
            formulaire.querySelectorAll('[required]').forEach(function(champ) {
                if (!champ.value.trim()) {
                    var label = formulaire.querySelector('label[for="' + champ.id + '"]');
                    var nomChamp = label ? label.textContent.replace('*', '').trim() : 'Ce champ';
                    ajouterErreur(champ, nomChamp + ' est obligatoire.');
                    erreurs.push(champ);
                }
            });

            // Valider les emails
            formulaire.querySelectorAll('input[type="email"]').forEach(function(champ) {
                if (champ.value.trim() && !validerEmail(champ.value.trim())) {
                    ajouterErreur(champ, 'Veuillez saisir une adresse email valide.');
                    erreurs.push(champ);
                }
            });

            // Valider les téléphones
            formulaire.querySelectorAll('input[type="tel"]').forEach(function(champ) {
                if (champ.value.trim() && !validerTelephone(champ.value.trim())) {
                    ajouterErreur(champ, 'Format de téléphone invalide (ex: +229 97 84 67 28).');
                    erreurs.push(champ);
                }
            });

            // Si des erreurs existent, empêcher la soumission
            if (erreurs.length > 0) {
                e.preventDefault();
                // Scroller vers la première erreur
                erreurs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                erreurs[0].focus();
            }
        });

        // Supprimer les erreurs au focus/saisie
        formulaire.addEventListener('input', function(e) {
            var champ = e.target;
            if (champ.classList.contains('invalide')) {
                champ.classList.remove('invalide');
                var erreur = champ.parentElement.querySelector('.champ-erreur');
                if (erreur) erreur.remove();
            }
        });
    });

    // ==============================================================
    // Compteur de caractères pour les textareas
    // ==============================================================
    var LIMITE_CARACTERES = 2000;

    document.querySelectorAll('.formulaire-textarea').forEach(function(textarea) {
        var compteur = document.createElement('div');
        compteur.className = 'compteur-caracteres';
        compteur.style.cssText = 'font-size:0.8rem;color:#666;text-align:right;margin-top:0.25rem;transition:color 0.2s ease;';
        textarea.parentElement.appendChild(compteur);

        function mettreAJour() {
            var nb = textarea.value.length;
            compteur.textContent = nb + ' / ' + LIMITE_CARACTERES + ' caractères';

            if (nb >= LIMITE_CARACTERES) {
                compteur.style.color = '#dc3545';
                compteur.style.fontWeight = '600';
            } else if (nb >= LIMITE_CARACTERES * 0.8) {
                compteur.style.color = '#f0ad4e';
                compteur.style.fontWeight = '500';
            } else {
                compteur.style.color = '#666';
                compteur.style.fontWeight = 'normal';
            }
        }

        textarea.addEventListener('input', mettreAJour);
        mettreAJour(); // Initialiser si du texte est pré-rempli
    });

    // ==============================================================
    // Fonctions utilitaires
    // ==============================================================

    function ajouterErreur(champ, message) {
        champ.classList.add('invalide');
        var span = document.createElement('span');
        span.className = 'champ-erreur';
        span.textContent = message;
        span.style.cssText = 'color:#dc3545;font-size:0.8rem;margin-top:0.25rem;display:block;';
        champ.parentElement.appendChild(span);
    }

    function validerEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validerTelephone(tel) {
        // Accepte les formats : +229 97 84 67 28, 97846728, +22997846728
        var telNettoye = tel.replace(/[\s\-\.\(\)]/g, '');
        return /^\+?[0-9]{8,15}$/.test(telNettoye);
    }

});
