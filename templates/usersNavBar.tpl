
{if $currentJournal && array_intersect(array(ROLE_ID_SITE_ADMIN, ROLE_ID_MANAGER), (array)$userRoles)}
    <script>
      $("#navigationPrimary").append(
        '<li class><a href="{url journal=$currentJournal->getPath()}/userviewerpoliplugin-list">{__('plugins.generic.userViewerPoliPlugin.managementMenuName')}</a></li>'
      );
    </script>
  {/if}