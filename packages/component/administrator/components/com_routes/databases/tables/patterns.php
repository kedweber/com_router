<?php

class ComRoutesDatabaseTablePatterns extends KDatabaseTableDefault
{
    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'sluggable',
            )
        ));

        parent::_initialize($config);
    }
}