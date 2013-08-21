<!DOCTYPE html>
<html lang="es">
   
    <com:THead>
        <meta charset="utf-8">
        <title>Centric - Cuentas al Dia</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        
        <style type="text/css">
            body {
                background-color: #f9f9f9;
                padding-top: 30px;
                padding-bottom: 40px;
            }
        </style>
        
        <link href="themes/Default/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="themes/Default/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <script src="themes/Default/bootstrap/js/jquery-1.7.2.js"></script>
        <script src="themes/Default/bootstrap/js/bootstrap.js"></script>

    </com:THead>

    <body data-spy="scroll" data-target=".subnav" data-offset="50">
        <com:TForm>
        <div class="container">
            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#">Centric 1.0.1</a>
                    <div class="nav-collapse collapse">
                    <ul class="nav">
                        <li class="active"><a href="?page=Home">Inicio</a></li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display:<%=$this->OpcionMenu()%>">Maestros <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><com:THyperLink NavigateUrl="?page=maestros.terceros.Terceros" Text="Terceros" /></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.campanas.Campanas" Text="Campañas" /></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.usuarios.Usuarios" Text="Usuarios" /></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.permisos.AdministrarPermisos" Text="Administrar Permisos" Visible="false"/></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.departamentos.Departamentos" Text="Departamentos" /></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.ciudades.Ciudades" Text="Ciudades" /></li>
                                <li><com:THyperLink NavigateUrl="?page=maestros.eventos.Eventos" Text="Eventos" Visible="false" /></li>
                                
                            </ul>
                        </li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display:<%=$this->OpcionMenu()%>">Administrativas <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><com:THyperLink NavigateUrl="?page=administrativas.datos.CargarDatos" Text="Carga de Datos"  /></li>
                                <li><com:THyperLink NavigateUrl="?page=administrativas.tareas.AsignarTareas" Text="Asignar Tareas"  /></li>
                                <li><com:THyperLink NavigateUrl="?page=administrativas.gestion.Gestion" Text="Gestion" Visible="false" /></li>
                                <li><com:THyperLink NavigateUrl="?page=administrativas.gestion.Gestion" Text="Ver Obligaciones" Visible="false"/></li>
                                
                            </ul>
                        </li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Gestion <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><com:THyperLink NavigateUrl="?page=gestion.tareas.VerTareas" Text="Ver Tareas"/></li>
                                <li><com:THyperLink NavigateUrl="?page=gestion.gestion.ConsultarGestion" Text="Consultar Gestion"/></li>
                                <li><com:THyperLink NavigateUrl="?page=gestion.tareas.novedades" Text="Gestion" Visible="false"/></li>
                            </ul>
                        </li>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display:<%=$this->OpcionMenu()%>">Informes <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.EstadoCuenta" Text="Estados de Cuenta" /></li>
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.Ingreso" Text="Ingresos" Visible="false"/></li>
<!--                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.Cartera" Text="Cartera" /></li>-->
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.GestionTerceros" Text="Gestion por Terceros"/></li>
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.Gestion" Text="Gestion por Usuario"/></li>
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.Gestion" Text="Gestion Detalles"/></li>
                               <li><com:THyperLink NavigateUrl="?page=administrativas.informes.Gestion" Text="Pagos" Visible="false"/></li>
                               
                            </ul>
                        </li>
                        
                         <li ><com:THyperLink Text="Login" NavigateUrl="?page=auth.LoginUsuario" Visible="<%= $this->User->IsGuest %>"/><com:THyperLink NavigateUrl="?page=auth.Logout" Text="Salir" Visible="<%= !$this->User->IsGuest %>"/></li>
                    </ul>

                    </div><!--/.nav-collapse -->
                </div>
                </div>
            </div>

           <div class="container">
               
           <com:TActivePanel ID="APnError" CssClass="alert alert-error" Style="display:none; border-color: red; position:relative; margin-top: 20px;">
                <ul id="breadCrumbsError">                                       
                    <li><com:TImageButton ID="IMBVer" ImageUrl="img/Danger.png" Attributes.OnClick="return false;" Style="Width:1.5em"/> <com:THyperLink ID="LnkError" ForeColor="black"/></li>
                </ul>
            </com:TActivePanel>

            <com:TActivePanel ID="APnCompletado" CssClass="alert alert-success" Style="display:none; border-color: green; position:relative;margin-top: 20px">
                <ul id="breadCrumbsCompletado">
                    <li><com:THyperLink ID="LnkCompletado" ForeColor="black"/></li>
                </ul>
            </com:TActivePanel>

            <!-- Begin Main Column -->

            <com:TClientScript>
                function Error(Texto) {    
                    Effect.toggle('ctl0_APnError', 'appear');                
                    $('ctl0_LnkError').innerHTML = Texto;
                    setTimeout("OcultarError()",6000);
                }

                function Completado(Texto) {
                    Effect.toggle('ctl0_APnCompletado', 'appear');                
                    $('ctl0_LnkCompletado').innerHTML = Texto;
                    setTimeout("OcultarCompletado()",6000);
                }

                function OcultarError() {
                    Effect.toggle('ctl0_APnError', 'appear');            
                }
                
                function OcultarCompletado() {
                    Effect.toggle('ctl0_APnCompletado', 'appear');            
                }
            </com:TClientScript>
               
               
            <com:TContentPlaceHolder ID="Main"/>

            <!-- Begin Side Column -->

            <com:TContentPlaceHolder ID="Main2"/>
              </div> <!-- /container -->

            <hr>

        <!--
        <footer>
            <div class="navegator ">
                <div class="item-nav-1">
                    <div class="item-nav-1">   <h6>Conózcanos</h6></div>
                    <ul class="item-nav-lef">
                        <li><a href="conozca-quienes-somos.html">Quienes somos</a></li>
                        <li><a href="conozca-que-hacemos.html">Qué hacemos </a></li>
                        <li><a href="conozca-como-trabajamos.html">Cómo trabajamos</a></li>
                        <li><a href="conozca-como-asesoramos.html">Cómo Asesoramos</a></li>
                        <li><a href="conozca-nuestro-tecnologia.html">Tecnologia Aplicada</a></li>
                    </ul>
                </div>
        </footer>
        -->
        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
       
     </com:TForm>
    </body>
</html>

