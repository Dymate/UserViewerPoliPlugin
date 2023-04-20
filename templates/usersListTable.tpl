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
                                País
                                <select name="country" onchange="this.form.submit()">
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
                                <select name="roles" onchange="this.form.submit()">
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
                        {$usersTable}
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation">{$paginationControl}</nav>
        {else}
            <div class="alert alert-warning" role="alert">¡No se encontraron usuarios! <a
                    href="./userviewerpoliplugin-list">Click aquí para volver</a></div>

        {/if}
    </div>
</div>

{include file="frontend/components/footer.tpl"}