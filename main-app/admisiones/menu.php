<?php
$valorInscripcion = !empty($config['cfgi_valor_inscripcion'])     ? $config['cfgi_valor_inscripcion']    : 0;
$fondoBarra       = !empty($config['cfgi_color_barra_superior'])  ? $config['cfgi_color_barra_superior'] : '#6017dc';
$colorTexto       = !empty($config['cfgi_color_texto'])           ? $config['cfgi_color_texto']          : '#FFF';
?>

<style>
.admision-navbar {
    background-color: <?= $fondoBarra; ?> !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    padding: 16px 24px;
    border-radius: 16px;
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
}

.admision-navbar .navbar-brand {
    color: <?= $colorTexto; ?> !important;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
}

.admision-navbar .navbar-brand:hover {
    transform: scale(1.05);
}

.admision-navbar .navbar-brand i {
    font-size: 24px;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.admision-navbar .navbar-nav {
    display: flex;
    gap: 8px;
    align-items: center;
}

.admision-navbar .nav-link {
    color: <?= $colorTexto; ?> !important;
    font-size: 15px;
    font-weight: 600;
    padding: 10px 20px !important;
    border-radius: 10px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0.9;
}

.admision-navbar .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    opacity: 1;
    transform: translateY(-2px);
}

.admision-navbar .nav-link.active {
    background: rgba(255, 255, 255, 0.25);
    opacity: 1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.admision-navbar .navbar-toggler {
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 8px 12px;
    border-radius: 8px;
}

.admision-navbar .navbar-toggler:focus {
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.2);
}

.admision-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='<?= urlencode($colorTexto); ?>' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Badge informativo */
.menu-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    margin-left: 8px;
}

@media (max-width: 992px) {
    .admision-navbar {
        border-radius: 12px;
        padding: 12px 16px;
    }
    
    .admision-navbar .navbar-brand {
        font-size: 18px;
    }
    
    .admision-navbar .navbar-collapse {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .admision-navbar .navbar-nav {
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }
    
    .admision-navbar .nav-link {
        width: 100%;
        justify-content: flex-start;
    }
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark admision-navbar">
    <a class="navbar-brand" href="#">
        <i class="fas fa-graduation-cap"></i>
        <span>ADMISIONES</span>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?php
    $idInst = '';
    if(!empty($_REQUEST['idInst'])){
        $idInst = $_REQUEST['idInst'];
    ?>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav ml-auto">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'admision.php') ? 'active' : ''; ?>" href="admision.php?idInst=<?= $idInst; ?>">
                <i class="fas fa-edit"></i>
                <span>Registro</span>
            </a>

            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'consultar-estado.php') ? 'active' : ''; ?>" href="consultar-estado.php?idInst=<?= $idInst; ?>">
                <i class="fas fa-search"></i>
                <span>Consultar Estado</span>
            </a>
        </div>
    </div>
    <?php
    }
    ?>
</nav>
