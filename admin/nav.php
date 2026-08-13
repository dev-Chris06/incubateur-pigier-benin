<!-- Navigation Admin -->
<nav class="admin-nav">
    <div class="admin-nav-container">
        <div class="admin-nav-logo">
            <a href="<?= SITE_URL ?>/admin/dashboard.php">📊 Administration</a>
        </div>
        <ul class="admin-nav-menu">
            <li><a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=personnes" class="<?= isset($_GET['table']) && $_GET['table'] === 'personnes' ? 'active' : '' ?>">Personnes</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=pech_editions" class="<?= isset($_GET['table']) && $_GET['table'] === 'pech_editions' ? 'active' : '' ?>">Éditions PECH</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=pech_realisations" class="<?= isset($_GET['table']) && $_GET['table'] === 'pech_realisations' ? 'active' : '' ?>">Réalisations PECH</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=evenements" class="<?= isset($_GET['table']) && $_GET['table'] === 'evenements' ? 'active' : '' ?>">Événements</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=contenu_statique" class="<?= isset($_GET['table']) && $_GET['table'] === 'contenu_statique' ? 'active' : '' ?>">Contenu du site</a></li>
            <li><a href="<?= SITE_URL ?>/admin/gerer.php?table=candidature_infos" class="<?= isset($_GET['table']) && $_GET['table'] === 'candidature_infos' ? 'active' : '' ?>">Ouverture candidatures</a></li>
            <li><a href="<?= SITE_URL ?>/admin/candidatures.php" class="<?= basename($_SERVER['PHP_SELF']) === 'candidatures.php' ? 'active' : '' ?>">Candidatures</a></li>
            <li><a href="<?= SITE_URL ?>/admin/messages.php" class="<?= basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : '' ?>">Messages</a></li>
            <li><a href="<?= SITE_URL ?>/admin/logout.php" class="logout">Déconnexion</a></li>
        </ul>
    </div>
</nav>
