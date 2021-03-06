<?php
	/*
	* GMapFP Component Google Map for Joomla! 1.6.x
	* Version 9.8
	* Creation date: D�cembre 2011
	* Author: Fabrice4821 - www.gmapfp.org
	* Author email: webmaster@gmapfp.org
	* License GNU/GPL
	*/

defined('_JEXEC') or die('Restricted access');

class gmapfpTableGMapFP extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db) {
		parent::__construct('#__gmapfp', 'id', $db);
	}

	function check()
	{
		//Remove all HTML tags from the title
		$filter = new JFilterInput(array(), array(), 0, 0);
		$this->nom = $filter->clean($this->nom);

		if(empty($this->alias)) {
			$this->alias = $this->nom;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}
}
?>
