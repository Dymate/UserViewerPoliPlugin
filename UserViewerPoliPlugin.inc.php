<?php

/*
 *
 * Plugin Gestión de búsqueda de usuarios
 * Dylan Mateo Llano Jaramillo & Juan José Restrepo Correa
 * Politécnico Colombiano Jaime Isaza Cadavid
 * Medellín-Colombia Mayo de 2023
 *
 */
import('lib.pkp.classes.core.PKPRequest');
import('lib.pkp.classes.plugins.GenericPlugin');


class UserViewerPoliPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {   //Se obtiene el administrador de plantillas
		$templateMgr = TemplateManager::getManager();

        // Se registra el plugin incluso cuando no está activado
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) 
		{
            //si el plugin está activado se registran los DAO, y los hooks
            import('plugins.generic.userViewerPoliPlugin.classes.UsersListTableDAO');
            $usersListTableDAO = new UsersListTableDAO();
            DAORegistry::registerDAO('UsersListTableDAO', $usersListTableDAO);
            import('plugins.generic.userViewerPoliPlugin.classes.ReviewerAssignmentsDAO');
            $reviewerAssignmentsDAO= new ReviewerAssignmentsDAO();
            DAORegistry::registerDAO('ReviewerAssignmentsDAO',$reviewerAssignmentsDAO);
            import('plugins.generic.userViewerPoliPlugin.classes.AuthorActivityDAO');
            $authorActivityDAO= new AuthorActivityDAO();
			DAORegistry::registerDAO('AuthorActivityDAO',$authorActivityDAO);
            //Se registra la navbar con el botón de usuarios con el método callbackShowUsersMenu
			HookRegistry::register('Templates::Common::Footer::PageFooter', array($this, 'callbackShowUsersMenu'));
            //Se registra la PageHandler con el metodo setPageHandler
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

        // permite otros plugins interactuar con este hook
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
            $templateMgr->addJavaScript('chart.js', 'https://cdn.jsdelivr.net/npm/chart.js');
            $templateMgr->addStyleSheet('userViewerPoliPluginGeneralStyles', $this->getPluginBaseUrl() . '/css/userViewerPluginGeneralStyles.css');

            //Se mapean las url de cada una de las vistas del plugin
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