<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
echo wordwrap(strip_tags($this->get('intro', '')));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'An account has been created for you.')));
?>


<?php 
echo strip_tags($this->translate('controller/jobs', 'Your account'));
?>

<?php 
echo $this->translate('controller/jobs', 'Account');
?>: <?php 
echo $this->get('account');
?>

<?php 
echo $this->translate('controller/jobs', 'Password');
?>: <?php 
echo $this->get('password') ?: $this->translate('controller/jobs', 'Like entered by you');
?>


<?php 
echo $this->translate('controller/jobs', 'Login');
?>: <?php 
echo $this->link('client/html/account/index/url', ['locale' => $this->address_item->get_language_id()], ['absoluteUri' => 1]);
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'If you have any questions, please reply to this e-mail')));