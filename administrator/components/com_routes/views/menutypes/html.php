<?php
/**
 * Com
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Socialhub
 * @subpackage  ...
 * @uses        Com_
 */
 
defined('KOOWA') or die('Protected resource');

class ComRoutesViewMenutypesHtml extends ComDefaultViewHtml
{
    public function display()
    {
        JLoader::import('joomla.application.component.model');
        JLoader::import('menutypes', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_menus' . DS . 'models');

        $items_model = JModel::getInstance( 'menutypes', 'MenusModel' );

        $this->assign('types', $items_model->getTypeOptions());

        return parent::display();
    }
}