{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<style>
    input[type="search"]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
</style>

<div class="panel panel-default" style="width: fit-content; margin-right: 100px !important;">
    <div class="panel-heading">Usuarios en la base de datos</div>
    <div class="panel-body">
        {if $usersTable}
            <div class="table-responsive">
                <table class="table">
                    <thead>

                        <th>
                            Seleccionar
                        </th>
                        <form class="form-control" method="POST">
                            <th>
                                Nombre
                                <input type="text" id="name" name="name" onchange="this.form.submit();"
                                    value="{$smarty.post.name}">
                            </th>
                            <th>
                                Apellido
                                <input type="text" id="lastname" name="lastname" onchange="this.form.submit();"
                                    value="{$smarty.post.lastname}">
                            </th>
                            <th>
                                Nombre Usuario
                                <input type="text" id="username" name="username" onchange="this.form.submit();"
                                    value="{$smarty.post.username}">
                            </th>
                            <th>
                                Email
                                <input type="text" id="email" name="email" onchange="this.form.submit();"
                                    value="{$smarty.post.email}">
                            </th>
                            <th>
                                País
                                <input type="text" id="country" name="country" onchange="this.form.submit();"
                                    value="{$smarty.post.country}">
                            </th>
                            <th>
                                Roles
                                <input type="text" id="roles" name="roles" onchange="this.form.submit();"
                                    value="{$smarty.post.roles}">
                            </th>
                        </form>
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

{include file="frontend/components/footer.tpl"}