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

class ComRoutesViewFieldsetHtml extends ComDefaultViewHtml
{
    public function display()
    {
        $state = $this->getModel()->getState();

        $language= JFactory::getLanguage();
        $language->load($state->componentname);

        return parent::display();
    }
}