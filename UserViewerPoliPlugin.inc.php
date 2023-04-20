<?php


import('lib.pkp.classes.core.PKPRequest');
import('lib.pkp.classes.plugins.GenericPlugin');


class UserViewerPoliPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
		$templateMgr = TemplateManager::getManager();

        // Register the plugin even when it is not enabled
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) 
		{

            import('plugins.generic.userViewerPoliPlugin.classes.UsersListTableDAO');
            $usersListTableDAO = new UsersListTableDAO();
            DAORegistry::registerDAO('UsersListTableDAO', $usersListTableDAO);
			
			HookRegistry::register('Templates::Common::Footer::PageFooter', array($this, 'callbackShowUsersMenu'));

            HookRegistry::register('LoadHandler', array($this, 'setPageHandler'));
		}
		
		return $success;
	}
    
	public function callbackShowUsersMenu($hookName, $args)
    {
        $output = &$args[2];
        $request = &Registry::get('request');
        $dispatcher = $request->getDispatcher();
        $templateMgr = TemplateManager::getManager($request);
        $output .= $templateMgr->fetch($this->getTemplateResource('usersNavBar.tpl'));

        // Permit other plugins to continue interacting with this hook
        return false;
    }

	public function setPageHandler($hookName, $params)
    {
        $page = $params[0];

        if (str_contains($page, 'userviewerpoliplugin')) {

            //Se registran los estilos CSS y scripts que se usarán en todo el plugin
            $templateMgr = TemplateManager::getManager();
            $templateMgr->addStyleSheet('bootstrapStyle', '//cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css');
            $templateMgr->addJavaScript('bootstrapScript', '//cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js');
            $templateMgr->addJavaScript('JqueryScript','//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
            $templateMgr->addJavaScript('PopperScript','//cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js');
            $templateMgr->addStyleSheet('sweetalert2Style', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
            $templateMgr->addJavaScript('sweetalert2Script', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js');
            $templateMgr->addStyleSheet('select2Style', '//select2.github.io/select2/select2-3.4.1/select2.css');
            $templateMgr->addStyleSheet('select2BootstrapStyle', '//fk.github.io/select2-bootstrap-css/css/select2-bootstrap.css');
            $templateMgr->addJavaScript('select2Script', '//select2.github.io/select2/select2-3.4.1/select2.js');

            $templateMgr->addStyleSheet('userViewerPoliPluginGeneralStyles', $this->getPluginBaseUrl() . '/css/userViewerPluginGeneralStyles.css');

            //Se mapean las url cada una de las páginas del plugin

            // http://localhost/index.php/pol/summariespoliplugin-history
            // http://{site_url}/index.php/pol/summariespoliplugin-history
            if ($page === 'userviewerpoliplugin-list') {
                $this->import('controllers/UsersListTableHandler');
                define('HANDLER_CLASS', 'UsersListTableHandler');
                return true;
            }

        }

        return false;
    }
	public function getPluginBaseUrl()
    {
        //3.3.0.14 Using $this when not in object context 
				// $baseUrl = PKPRequest::getBaseUrl();
        $baseUrl = new PKPRequest;
				return $baseUrl->getBaseUrl() . '/plugins/generic/userViewerPoliPlugin';
    }
	
	/**
	 * Get the display name for this plugin.
	 * @return string
	 */
	public function getDisplayName() {
        return __('plugins.generic.userViewerPoliPlugin.displayName');
	}
	
	/**
	 * Get a description of this plugin.
	 * @return string
	 */
	public function getDescription() {
        return __('plugins.generic.userViewerPoliPlugin.description');
	}
}