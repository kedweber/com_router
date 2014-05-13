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
                    <?= @helper('grid.sort', array('column' => 'title')) ?>
                </th>
				<th>
                    <?= @helper('grid.sort', array('column' => 'path')) ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'package')) ?>
                </th>
                <th>
                    <?= @helper('grid.sort', array('column' => 'name')) ?>
                </th>
				<th>
					<?= @helper('grid.sort', array('column' => 'enabled')) ?>
				</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="20">
                    <?= @helper('paginator.pagination', array('total' => $total)) ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <? foreach($patterns as $pattern) : ?>
                <tr>
                    <td align="center">
                        <?= @helper('grid.checkbox' , array('row' => $pattern)) ?>
                    </td>
                    <td>
                        <a href="<?= @route('view=pattern&id='.$pattern->id) ?>">
                            <?= $pattern->title; ?>
                        </a>
                    </td>
					<td>
						<?= $pattern->path; ?>
					</td>
                    <td>
                        <?= $pattern->package; ?>
                    </td>
                    <td>
                        <?= $pattern->name; ?>
                    </td>
					<td>
						<?= @helper('grid.enable', array('row' => $pattern)); ?>
					</td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </form>
</div>