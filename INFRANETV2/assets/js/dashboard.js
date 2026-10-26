document.addEventListener("DOMContentLoaded", function () {

    /*==================================================
      MENU RESPONSIVE
    ==================================================*/

    const btnMenu = document.getElementById("btnMenu");
    const sidebar = document.querySelector(".sidebar");

    if (btnMenu && sidebar) {

        btnMenu.addEventListener("click", function () {

            sidebar.classList.toggle("show");

        });

    }


    /*==================================================
      SUBMENUS
    ==================================================*/

    const botonesSubmenu = document.querySelectorAll(".submenu-btn");

    botonesSubmenu.forEach(function (boton) {

        boton.addEventListener("click", function (e) {

            e.preventDefault();
            e.stopPropagation();

            const submenu = boton.parentElement;

            const lista = submenu.querySelector(":scope > .submenu-list");

            if (!lista) {
                return;
            }

            /*------------------------------------------
              ABRIR / CERRAR
            ------------------------------------------*/

            lista.classList.toggle("activo");


            /*------------------------------------------
              FLECHA
            ------------------------------------------*/

            const flecha = boton.querySelector(".flecha");

            if (flecha) {

                flecha.classList.toggle("rotada");

            }

        });

    });


    /*==================================================
      DETECTAR PAGINA ACTUAL
    ==================================================*/

    const parametros = new URLSearchParams(
        window.location.search
    );

    const paginaActual =
        parametros.get("page") || "inicio";


    /*==================================================
      ENCONTRAR ENLACE ACTIVO
    ==================================================*/

    const enlaces = document.querySelectorAll(
        '.menu a[href*="dashboard.php?page="]'
    );


    enlaces.forEach(function (enlace) {

        try {

            const url = new URL(
                enlace.href,
                window.location.href
            );

            const paginaEnlace =
                url.searchParams.get("page");


            /*------------------------------------------
              SI ES LA PAGINA ACTUAL
            ------------------------------------------*/

            if (paginaEnlace === paginaActual) {

                enlace.classList.add("activo");


                /*--------------------------------------
                  ABRIR SUBMENUS PADRE
                --------------------------------------*/

                let submenuActual =
                    enlace.closest(".submenu");


                while (submenuActual) {

                    const listaPadre =
                        submenuActual.querySelector(
                            ":scope > .submenu-list"
                        );


                    if (listaPadre) {

                        listaPadre.classList.add("activo");

                    }


                    const botonPadre =
                        submenuActual.querySelector(
                            ":scope > .submenu-btn"
                        );


                    if (botonPadre) {

                        const flecha =
                            botonPadre.querySelector(".flecha");


                        if (flecha) {

                            flecha.classList.add("rotada");

                        }

                    }


                    /*----------------------------------
                      BUSCAR SUBMENU PADRE
                    ----------------------------------*/

                    submenuActual =
                        submenuActual.parentElement
                            .closest(".submenu");

                }

            }

        } catch (error) {

            console.error(
                "Error en menú:",
                error
            );

        }

    });

});