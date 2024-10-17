<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #41c4c4; margin-bottom:10px;">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="navbar-collapse" id="navbarSupportedContent">
  <a data-hint="Agrega una nueva publicación que tenga más contenido (Imagen, video, etc.)."   onclick="abrirModal('<?= $frases[212][$datosUsuarioActual['uss_idioma']]; ?>','../compartido/noticias-agregar-modal.php')" class="btn deepPink-bgcolor"><?=$frases[263][$datosUsuarioActual['uss_idioma']];?>  <i class="fa fa-plus"></i></a>

  <ul class="navbar-nav mr-auto">
    
    
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:<?=$Plataforma->colorUno;?>;">
            Más acciones
            <span class="fa fa-angle-down"></span>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" data-hint="Se mostrarán todas tus publicaciones que estén ocultas." href="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(1)?>"><?=$frases[135][$datosUsuarioActual['uss_idioma']];?></a>
          <a class="dropdown-item" data-hint="Se ocultarán todas tus publicaciones que estén siendo mostradas." href="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(0)?>"><?=$frases[136][$datosUsuarioActual['uss_idioma']];?></a>
          <a class="dropdown-item" data-hint="Se eliminarán todas tus publicaciones realizadas." href="#" name="../compartido/noticias-gestionar-noticia.php?e=<?=base64_encode(2)?>" onClick="deseaEliminar(this)"><?=$frases[137][$datosUsuarioActual['uss_idioma']];?></a>
        </div>
      </li>

  </ul> 

  <?php if(!empty($_GET["busqueda"]) || !empty($_GET["usuario"])){?>
      <a href="<?=$_SERVER['PHP_SELF'];?>" style="color:white; text-decoration:underline;">VER TODO</a>
      &nbsp;&nbsp;
    <?php }?>

    <form class="form-inline my-2 my-lg-0" action="noticias.php" method="get" data-hint="Aquí podrás buscar noticias específicas, dentro de todas las publicadas, usando palabras que claves que se encuentren en su titulo, descripción, etc.">
        <input class="form-control mr-sm-2" type="search" placeholder="<?=$frases[386][$datosUsuarioActual['uss_idioma']];?>..." aria-label="Search" name="busqueda" value="<?php if(isset($_GET['busqueda'])) echo $_GET["busqueda"];?>">
      <button class="btn deepPink-bgcolor my-2 my-sm-0" type="submit"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></button>
    </form>


  </div>
</nav>