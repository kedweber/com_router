<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */
 
defined('KOOWA') or die('Protected resource');

class ComRoutesModelMenus extends ComDefaultModelDefault
{
    /**
     * @param KConfig $config
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_state
            ->insert('menutype',    'string')
            ->insert('client_id',   'int', 0)
            ->insert('callback',    'cmd')
        ;
    }

    protected function _buildQueryJoins(KDatabaseQuery $query)
    {
        $state = $this->_state;

        $query->select('menutypes.title AS menutype_title');
        $query->join('LEFT', 'menu_types AS menutypes', 'tbl.menutype = menutypes.menutype');

        parent::_buildQueryJoins($query);
    }

    /**
     * @param KDatabaseQuery $query
     */
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
        $state = $this->_state;

        parent::_buildQueryWhere($query);

        if(is_numeric($state->client_id)) {
            $query->where('tbl.client_id', '=', $state->client_id);
        }

        if($state->menutype) {
            $query->where('tbl.menutype', '=', $state->menutype);
        }

        if($state->search) {
            $query->where('tbl.title', 'LIKE', '%'.$state->search.'%');
        }

        $query->where('tbl.parent_id', '>', 0);

        //TODO: Language select
        //$query->where('tbl.language', '=', JFactory::getLanguage()->getTag());
        //$query->where('tbl.language', '=', '*', 'OR');
    }
}