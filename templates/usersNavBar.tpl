


{if $currentJournal && array_intersect(array(ROLE_ID_SITE_ADMIN, ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR), (array)$userRoles)}
    <script>
      $("#navigationPrimary").append(
        '<li class><a href="{url journal=$currentJournal->getPath()}/userviewerpoliplugin-list">{__('plugins.generic.userViewerPoliPlugin.managementMenuName')}</a></li>'
      );
    </script>
  {/if}