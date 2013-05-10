<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');  

?>

<?php if (($this->map->params['service'] == 'staticmap') && ($this->lists['userAccess'])) { ?>	 
	<?php echo $this->mapview; ?>
<?php } 
	