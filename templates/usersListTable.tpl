{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<style>
    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }

    .table-responsive {
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    }

    .user-name {
        background: linear-gradient(to right, #07a93b 50%, #57b627 50%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-activity {
        background: linear-gradient(to right, #07a93b, #4eca10);
        color: white;
    }

    .data-name {
        color: #07a93b;
    }
    .my-icon {
      position: absolute;
      left: 5px; /* Ajusta la posición del icono */
      top: 50%;
      transform: translateY(-50%);
    }
.project-title{
    display: flex;
    justify-content: space-around;
    font-weight: bold;
    font-size: large;
    background-color: white;
}
</style>



<br><br><br>

<div class="panel panel-default" style="width: fit-content; margin-right: 100px !important;">
    <div class="panel-heading project-title">Módulo de gestión para la búsqueda de usuarios</div>
    <form method="POST">
    </form>
    <div class="panel-body">
        {if $usersTable}
            <div class="table-responsive">
                <table class="table">
                    <thead>

                        <th>

                        </th>
                        <form class="form-control" method="GET">
                            <th>
                                Nombre
                                <!--<input type="text" id="name" name="name" onchange="this.form.submit();"
                                    value="{$smarty.post.name}"> -->
                                <input type="text" id="name" name="name" onchange="sendInputValues('name');"
                                    value="{$smarty.get.name}">

                            </th>
                            <th>
                                Apellido
                                <input type="text" id="lastnm" name="lastnm" onchange="sendInputValues('lastnm');"
                                    value="{$smarty.get.lastnm}">
                            </th>
                            <th>
                                País
                                <select name="country" id='country' onchange="sendInputValues('country');"
                                    selected=value="{$smarty.get.country}">
                                    {foreach $optionsCountry as $value => $label}
                                        {if $value == $selectedCountryValue}
                                            <option value="{$value}" selected>{$label}</option>
                                        {else}
                                            <option value="{$value}">{$label}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </th>

                            <th>
                                Roles
                                <select name="roles" id='roles' onchange="sendInputValues('roles');"
                                    selected=value="{$smarty.get.roles}">
                                    {foreach $optionsRoles as $value => $label}
                                        {if $value == $selectedRolesValue}
                                            <option value="{$value}" selected>{$label}</option>
                                        {else}
                                            <option value="{$value}">{$label}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </th>

                        </form>
                        <th>
                            Mas detalles
                        </th>

                    </thead>
                    <tbody>
                        <form method="POST" id="exportForm">
                            <input type="hidden" name="selectedValues" value="">
                            <button class="btn btn-warning" type="submit" value="Exportar" id="export" disabled><span class="glyphicon glyphicon-export"></span> Exportar</button>
                            <button class="btn btn-danger" type=button value="Borrar Selección"
                                id="deleteSelection" style="padding: 5px; margin: 5px; width:140px;"
                                onclick="clearCheckboxValues();" disabled> <span class="glyphicon glyphicon-trash"></span> Borrar Selección</button>
                             

                        </form>
                        <form method="POST" id="exportForm">
                            <input type="hidden" name="exportAll" value="1">
                            <button class="btn btn-primary" type="submit" value="Exportar Todo" style="margin: 5px;"> <span class="glyphicon glyphicon-upload"></span> Exportar Todo</button>
                        </form>
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#chartModal" >
                        <span class="glyphicon glyphicon-eye-open"></span> </i>Visualizar
                        </button>&nbsp;
                        <div class="modal fade" id="chartModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
                            tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content ">
                                    <!-- Encabezado de la ventana modal -->
                                    <div class="modal-header">

                                        <div class="row">
                                            <div class="modal-title">
                                                <div class="h2" style="font-size: 30px; margin-left: 10px; ">Cantidad de
                                                    usuarios por pais</div>
                                            </div>

                                        </div>

                                    </div>
                                    <!-- Cuerpo de la ventana modal -->
                                    <div class="modal-body">

                                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                                        <canvas id="myChart"></canvas>
                                        <script>
                                            var canvas = document.getElementById('myChart');
                                            var ctx = canvas.getContext("2d");
                                            var data = {$data};
                                            var labels = {$labels};    
                                            // Datos del gráfico
                                            var data = {
                                                labels: labels,
                                                datasets: [{
                                                    label: "Usuarios por rol/país",
                                                    data: data,
                                                    backgroundColor: generarColoresUnicos(data.length),
                                                }, ],
                                            };

                                            // Crear el gráfico
                                            var myChart = new Chart(ctx, {
                                                type: "pie",
                                                data: data,
                                            });

                                            function generarColorAleatorio() {
                                                var letras = "0123456789ABCDEF";
                                                var color = "#";
                                                for (var i = 0; i < 6; i++) {
                                                    color += letras[Math.floor(Math.random() * 16)];
                                                }
                                                return color;
                                            }

                                            // Generar colores únicos para todas las secciones del diagrama pie
                                            function generarColoresUnicos(numSecciones) {
                                                var colores = [];
                                                for (var i = 0; i < numSecciones; i++) {
                                                    var color = generarColorAleatorio();
                                                    while (colores.includes(color)) {
                                                        // Si el color generado ya existe, generamos uno nuevo
                                                        color = generarColorAleatorio();
                                                    }
                                                    colores.push(color);
                                                }
                                                return colores;
                                            }
                                        </script>



                                    </div>
                                </div>
                            </div>
                        </div>
                        {$usersTable}


                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation">{$paginationControl}</nav>
            <div>Usuarios mostrados: {$totalUsers}</div>
        {else}
            <div class="alert alert-warning" role="alert">¡No se encontraron usuarios! <a
                    href="./userviewerpoliplugin-list">Click aquí para volver</a></div>

        {/if}
    </div>
</div>

{include file="frontend/components/footer.tpl"}