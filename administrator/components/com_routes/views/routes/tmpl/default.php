<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */
 
defined('KOOWA') or die('Protected resource'); ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools'); ?>

<script src="media://lib_koowa/js/koowa.js" />
<style src="media://lib_koowa/css/koowa.css" />

<div class="com_routes">
    <form action="<?= @route(); ?>" class="-koowa-grid" method="get">
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="10"></th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'path')) ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'query')) ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'enabled')) ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="4">
                    <?= @helper('paginator.pagination', array('total' => $total)) ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <? foreach($routes as $route) : ?>
                <tr>
                    <td align="center">
                        <?= @helper('grid.checkbox' , array('row' => $route)) ?>
                    </td>
                    <td>
                        <a href="<?= @route('view=route&id='.$route->id) ?>">
                            <?= $route->path; ?>
                        </a>
                    </td>
                    <td>
                        <?= $route->query; ?>
                    </td>
                    <td>
                        <?= @helper('grid.enable' , array('row' => $route)) ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </form>
</div>