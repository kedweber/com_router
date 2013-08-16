<?php
/**
 * ComRoute
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

defined('KOOWA') or die('Protected resource'); ?>

<?= @helper('behavior.bootstrap'); ?>

<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>
<?= @helper('behavior.mootools'); ?>

<script src="media://lib_koowa/js/koowa.js" />

<div class="com_routes">
    <form action="" class="form-horizontal -koowa-form" method="post">
        <div class="row-fluid">
            <div class="span12">
                <fieldset>
                    <legend><?= @text('Details'); ?></legend>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Pattern'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="pattern" value="<?= $pattern->pattern ?>" placeholder="<?= @text('Pattern') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Component'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="component" value="<?= $pattern->component ?>" placeholder="<?= @text('Component') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('View'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="view" value="<?= $pattern->view ?>" placeholder="<?= @text('View') ?>" />
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>