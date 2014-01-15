<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

defined('_JEXEC') or die;

if (!class_exists('Koowa'))
{
    if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_extman/extman.php')) {
        $error = JText::_('EXTMAN_ERROR');
    }
    elseif (!JPluginHelper::isEnabled('system', 'koowa')) {
        $error = sprintf(JText::_('EXTMAN_PLUGIN_ERROR'), JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system'));
    }

    return JFactory::getApplication()->redirect(JURI::base(), $error, 'error');
}

echo KService::get('com://admin/routes.dispatcher')->dispatch();