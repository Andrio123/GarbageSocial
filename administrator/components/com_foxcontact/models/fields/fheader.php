<?php
/*
This file is part of "Fox Joomla Extensions".

You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

Author: Demis Palma
Documentation at http://www.fox.ra.it/forum/2-documentation.html
*/

defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldFHeader extends JFormField
	{
	protected $type = 'FHeader';

	protected function getInput()
		{
		return '';
		}

	protected function getLabel()
		{
/*
		(include_once JPATH_ROOT . "/components/com_foxcontact/helpers/flogger.php") or die(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", "flogger.php"));
		$log = new FLogger($this->type, "debug");
		$log->Write($this->element["name"] . " getLabel()");
*/
		$cn = basename(realpath(dirname(__FILE__) . DS . '..' . DS . '..'));
		$direction = intval(JFactory::getLanguage()->get('rtl', 0));
		$left  = $direction ? "right" : "left";
		$right = $direction ? "left" : "right";

		echo '<div class="clr"></div>';
		$image = '';
		$icon	= (string)$this->element['icon'];
		if (!empty($icon))
			{
			//$image 	= JHTML::_('image', 'media/com_foxcontact/images/'. $icon, '' );
			$image .= '<img style="margin:0; float:' . $left . ';" src="' . JURI::base() . '../media/' . $cn . '/images/' . $icon . '">';
			}

		$helpurl	= (string)$this->element['helpurl'];
		if (!empty($helpurl))
			{
			$image .= '<a href="' . $helpurl . '" target="_blank"><img style="margin:0; float:' . $right . ';" src="' . JURI::base() . '../media/' . $cn . '/images/question-button-16.png"></a>';
			}

		$style = 'background:#f4f4f4; color:#025a8d; border:1px solid silver; padding:5px; margin:5px 0;';
		if ($this->element['default'])
			{
			return '<div style="' . $style . '">' .
			$image .
			'<span style="padding-' . $left . ':5px; font-weight:bold; line-height:16px;">' .
			JText::_($this->element['default']) .
			'</span>' .
			'</div>';
			}
		else
			{
			return parent::getLabel();
			}

		echo '<div class="clr"></div>';
		}
	}
?>
