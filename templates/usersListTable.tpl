{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<style>
    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
</style>

<div class="panel panel-default" style="width: fit-content; margin-right: 100px !important;">
<div class="panel-heading" >Usuarios en la base de datos</div>
<div class="panel-body" >
    {if $usersTable}
        <div class="table-responsive" >
            <table class="table">
                <thead>
                    <th>
                         Seleccionar 
                         <br>
                         <br>
                         
                    </th>
                    <th >
                        Nombre
                        <br>
                        <input type="text" id="nombre" name="nombre" placeholder="nombre" class="form-control">
                    </th>
                    <th >
                        Apellido
                        <br>
                        <input type="text" id="apellido" name="apellido" placeholder="apellido" class="form-control">
                    </th>
                    <th >
                        Nombre Usuario
                        <br>
                        <input type="text" id="username" name="username" placeholder="username" class="form-control">
                    </th>
                    <th>
                        Email
                        <br>
                        <input type="text" id="email" name="email" placeholder="email" class="form-control">
                    </th>
                    <th>
                        País
                        <br>
                        <input type="text" id="country" name="country" placeholder="country" class="form-control">
                    </th>
                    <th>
                        Roles
                        <br>
                        <input type="text" id="role" name="role" placeholder="role" class="form-control">
                    </th>
                    <th>
                        Mas detalles
                    </th>
                </thead>
                <tbody>
                    {$usersTable}
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">{$paginationControl}</nav>
    {else}
        <div class="alert alert-warning" role="alert">¡No se encontraron usuarios.</div>
    {/if}
</div>
</div>
</div>

{include file="frontend/components/footer.tpl"}