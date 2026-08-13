    <footer class="footer-principal">
        <div class="container">
            <div class="footer-grille">
                <!-- Colonne À propos -->
                <div class="footer-colonne">
                    <img src="<?= SITE_URL ?>/assets/images/logo_footer.png" alt="Logo <?= SITE_NOM ?>" class="footer-logo">
                    <p><strong><?= SITE_NOM ?></strong></p>
                    <p>Le premier incubateur étudiant du Bénin, dédié aux étudiants de PIGIER Bénin.</p>
                </div>

                <!-- Colonne Liens rapides -->
                <div class="footer-colonne">
                    <h3>Liens rapides</h3>
                    <ul class="footer-liste">
                        <li><a href="<?= SITE_URL ?>/index">Accueil</a></li>
                        <li><a href="<?= SITE_URL ?>/ecosysteme">Écosystème</a></li>
                        <li><a href="<?= SITE_URL ?>/pech">PECH</a></li>
                        <li><a href="<?= SITE_URL ?>/actualites">Actualités</a></li>
                        <li><a href="<?= SITE_URL ?>/contact">Contact</a></li>
                        <li><a href="<?= SITE_URL ?>/postuler">Postuler</a></li>
                    </ul>
                </div>

                <!-- Colonne Contact -->
                <div class="footer-colonne">
                    <h3>Contact</h3>
                    <ul class="footer-liste">
                        <li>
                            <strong>Téléphone fixe :</strong><br>
                            <?php
                            $tel_fixe = getContenu($pdo, 'global', 'telephone_fixe');
                            echo $tel_fixe ? echapper($tel_fixe) : 'Non renseigné';
                            ?>
                        </li>
                        <li>
                            <strong>Téléphone mobile :</strong><br>
                            <?php
                            $tel_mobile = getContenu($pdo, 'global', 'telephone_mobile');
                            echo $tel_mobile ? echapper($tel_mobile) : 'Non renseigné';
                            ?>
                        </li>
                        <li>
                            <strong>Email :</strong><br>
                            <?php
                            $email = getContenu($pdo, 'global', 'email_contact');
                            if ($email) {
                                echo '<a href="mailto:' . echapper($email) . '">' . echapper($email) . '</a>';
                            } else {
                                echo 'Non renseigné';
                            }
                            ?>
                        </li>
                    </ul>
                </div>

                <!-- Colonne Réseaux sociaux -->
                <div class="footer-colonne">
                    <h3>Suivez-nous</h3>
                    <div class="reseaux-sociaux">
                        <a href="https://www.facebook.com/pigierbeninofficiel" class="reseau-lien" aria-label="Facebook" title="Facebook">f</a>
                        <a href="https://twitter.com/PigierBeninOff" class="reseau-lien" aria-label="Twitter" title="Twitter">𝕏</a>
                        <a href="https://www.linkedin.com/company/pigier-benin" class="reseau-lien" aria-label="LinkedIn" title="LinkedIn">in</a>
                        <a href="https://www.instagram.com/pigierbeninofficiel/" class="reseau-lien" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:currentColor"><path d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2c1.65 0 3 1.35 3 3v10c0 1.65-1.35 3-3 3H7c-1.65 0-3-1.35-3-3V7c0-1.65 1.35-3 3-3h10zm1.25 1.5a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5zM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg></a>
                        <a href="https://www.youtube.com/@pigierbenin2280/" class="reseau-lien" aria-label="YouTube" title="YouTube"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg></a>
                        <a href="https://www.tiktok.com/@pigierbeninofficiel" class="reseau-lien" aria-label="TikTok" title="TikTok"><svg viewBox="0 0 24 24" aria-hidden="true" style="width:20px;height:20px;fill:currentColor"><path d="M19.6 6.2a5.6 5.6 0 0 1-3.5-1.2A5.6 5.6 0 0 1 14.2 1h-3.5v14.1a3 3 0 1 1-2.1-2.9V8.7a6.5 6.5 0 1 0 5.6 6.4V8.2a9 9 0 0 0 5.4 1.8V6.2z"/></svg></a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bas">
                <p>&copy; 2026 <?= SITE_NOM ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="<?= SITE_URL ?>/assets/js/formulaires.js"></script>
</body>
</html>
