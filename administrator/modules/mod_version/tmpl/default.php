<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_version
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php if (!empty($version)) : ?>
<div class="d-flex align-items-center">
	<p class="joomla-version"><?php echo $version; ?></p>
</div>
<?php endif; ?>
