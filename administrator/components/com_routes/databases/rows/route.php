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

class ComRoutesDatabaseRowRoute extends KDatabaseRowDefault
{
    public function save()
    {
        if(!$this->itemId) {
            $this->itemId = JApplication::getInstance('site')->getMenu()->getItems('link', 'index.php?'. $this->query, true)->id;
        }

        parent::save();
    }
}