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

<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>
<?= @helper('behavior.mootools'); ?>

<script src="media://lib_koowa/js/koowa.js" />

<div class="com_routes">
    <form action="" class="form-horizontal -koowa-form" method="post">
        <div class="row-fluid">
            <div class="span12">
                <fieldset>
                    <legend><?= @text('DETAILS'); ?></legend>
                    <div class="control-group">
                        <label class="control-label"><?= @text('TITLE'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="title" value="<?= $pattern->title; ?>" placeholder="<?= @text('TITLE') ?>" />
                        </div>
                    </div>
					<div class="control-group">
						<label class="control-label"><?= @text('SLUG'); ?></label>
						<div class="controls">
							<input type="text" name="slug" value="<?= $pattern->slug; ?>" placeholder="<?= @text('SLUG') ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?= @text('PATH'); ?></label>
						<div class="controls">
							<input type="text" name="path" value="<?= $pattern->path; ?>" placeholder="<?= @text('PATH') ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?= @text('REQUIREMENTS'); ?></label>
						<div class="controls">
							<input type="text" name="requirements" value="<?= @escape($pattern->requirements); ?>" placeholder="<?= @text('REQUIREMENTS') ?>" />
						</div>
					</div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('PACKAGE'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="package" value="<?= $pattern->package; ?>" placeholder="<?= @text('PACKAGE') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('NAME'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="name" value="<?= $pattern->name; ?>" placeholder="<?= @text('NAME') ?>" />
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>