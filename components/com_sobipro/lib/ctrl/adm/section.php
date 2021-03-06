<?php
/**
 * @version: $Id: section.php 2317 2012-03-27 10:19:39Z Radek Suski $
 * @package: SobiPro Library
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2012 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/lgpl.html GNU/LGPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU Lesser General Public License version 3
 * ===================================================
 * $Date: 2012-03-27 12:19:39 +0200 (Tue, 27 Mar 2012) $
 * $Revision: 2317 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/lib/ctrl/adm/section.php $
 */

defined( 'SOBIPRO' ) || exit( 'Restricted access' );

SPLoader::loadController( 'controller' );
SPLoader::loadController( 'section' );

/**
 * @author Radek Suski
 * @version 1.0
 * @created 10-Jan-2009 4:39:25 PM
 */
class SPSectionAdmCtrl extends SPSectionCtrl
{
	/**
	 */
	public function execute()
	{
		switch ( $this->_task ) {
			case 'add':
				SPLoader::loadClass( 'html.input' );
				$this->setModel( SPLoader::loadModel( 'section' ) );
				$this->editForm();
				break;
			case 'edit':
				Sobi::Redirect( Sobi::Url( array( 'task' => 'config', 'sid' => SPRequest::sid() ) ), null, true );
				break;
			case 'view':
			case 'entries':
				SPLoader::loadClass( 'html.input' );
				Sobi::ReturnPoint();
				$this->view( $this->_task == 'entries', Sobi::GetUserState( 'entries_filter', 'sp_entries_filter', null ) );
				break;
			default:
				/* case plugin didn't registered this task, it was an error */
				if( !( parent::execute() ) ) {
					Sobi::Error( $this->name(), SPLang::e( 'SUCH_TASK_NOT_FOUND', SPRequest::task() ), SPC::NOTICE, 404, __LINE__, __FILE__ );
				}
				break;
		}
	}

	/**
	 */
	protected function view( $allEntries, $term = null )
	{
		$config = SPFactory::config();
		/* @var SPdb $db */
		$db = SPFactory::db();
		$c = array();
		$e = array();
		if( !( Sobi::Section() ) ) {
			Sobi::Error( 'Section', SPLang::e( 'Missing section identifier' ), SPC::ERROR, 500, __LINE__, __FILE__ );
		}
		$this->_model->init( Sobi::Section() );

		/* get the lists ordering and limits */
		$eLimit 	= Sobi::GetUserState( 'adm.entries.limit', 'elimit', $config->key( 'adm_list.entries_limit', 25 ) );
		$cLimit 	= Sobi::GetUserState( 'adm.categories.limit', 'climit', $config->key( 'adm_list.cats_limit', 15 ) );
		$eLimStart 	= SPRequest::int( 'eLimStart', 0 );
		$cLimStart 	= SPRequest::int( 'cLimStart', 0 );

		/* get child categories and entries */
		/* @todo: need better method - the query can be very large with lot of entries  */
		if( !( $allEntries ) ) {
			$e = $this->_model->getChilds();
			$c = $this->_model->getChilds( 'category' );
		}
		elseif( !( $term && $allEntries ) ) {
			$c = $this->_model->getChilds( 'category', true );
			$c[] = Sobi::Section();
			if( count( $c ) ) {
				try {
					$db->select( 'id', 'spdb_object', array( 'parent' => $c, 'oType' => 'entry' ) );
					$e = $db->loadResultArray();
				}
				catch ( SPException $x ) {
					Sobi::Error( $this->name(), SPLang::e( 'DB_REPORTS_ERR', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
				}
			}
		}
		else {
			try {
				$db->select( 'sid', 'spdb_field_data', array( 'section' => Sobi::Section(), 'fid' => Sobi::Cfg( 'entry.name_field' ), 'baseData' => "%{$term}%" ) );
				$e = $db->loadResultArray();
			}
			catch ( SPException $x ) {
				Sobi::Error( $this->name(), SPLang::e( 'DB_REPORTS_ERR', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
			}
		}

		$cCount		= count( $c );
		$eCount		= count( $e );
		$entries 	= array();
		$categories = array();

		SPLoader::loadClass( 'models.dbobject' );
//		$cClass = SPLoader::loadModel( 'category' );
//		$eClass = SPLoader::loadModel( 'entry' );

		/* if there are entries in the root */
		if( count( $e ) ) {
			try {
				$Limit = $eLimit > 0 ? $eLimit : 0;
				$LimStart = $eLimStart ? ( ( $eLimStart - 1 ) * $eLimit ) : $eLimStart ;
				$eOrder = $this->parseOrdering( 'entries', 'eorder', 'position.asc', $Limit, $LimStart, $e );
				$db->select( 'id', 'spdb_object', array( 'id' => $e, 'oType' => 'entry' ), $eOrder, $Limit, $LimStart );
				$results = $db->loadResultArray();
			}
			catch ( SPException $x ) {
				Sobi::Error( $this->name(), SPLang::e( 'DB_REPORTS_ERR', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
			}
			foreach ( $results as $i => $entry ) {
				$entries[ $i ] = $entry;
			}
		}

		/* if there are categories in the root */
		if( count( $c ) ) {
			try {
				$LimStart = $cLimStart ? ( ( $cLimStart - 1 ) * $cLimit ) : $cLimStart ;
				$Limit = $cLimit > 0 ? $cLimit : 0;
				$cOrder = $this->parseOrdering( 'categories', 'corder', 'order.asc', $Limit, $LimStart, $c );
				$db->select( 'id', 'spdb_object', array( 'id' => $c, 'oType' => 'category' ), $cOrder, $Limit, $LimStart );
				$results = $db->loadResultArray();
			}
			catch ( SPException $x ) {
				Sobi::Error( $this->name(), SPLang::e( 'DB_REPORTS_ERR', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
			}
			foreach ( $results as $i => $category ) {
				$categories[ $i ] = SPFactory::Category( $category );
			}
		}

		/* create menu */
		$mClass = SPLoader::loadClass( 'helpers.adm.menu' );
		$menu = new $mClass( 'section.'.$this->_task, Sobi::Section() );
		/* load the menu definition */
		$cfg = SPLoader::loadIniFile( 'etc.adm.section_menu' );
		Sobi::Trigger( 'Create', 'AdmMenu', array( &$cfg ) );
		if( count( $cfg ) ) {
			foreach ( $cfg as $section => $keys ) {
				$menu->addSection( $section, $keys );
			}
		}
		Sobi::Trigger( 'AfterCreate', 'AdmMenu', array( &$menu ) );
		/* create new SigsiuTree */
		$tree = SPLoader::loadClass( 'mlo.tree' );
		$tree = new $tree( Sobi::GetUserState( 'categories.order', 'corder', 'order.asc' ) );
		/* set link */
		$tree->setHref( Sobi::Url( array( 'sid' => '{sid}' ) ) );
		$tree->setId( 'menuTree' );
		/* set the task to expand the tree */
		$tree->setTask( 'category.expand' );
		$tree->init( Sobi::Reg( 'current_section' ) );
		/* add the tree into the menu */
		$menu->addCustom( 'AMN.ENT_CAT', $tree->getTree() );


		/* get view class */
		$class 	= SPLoader::loadView( 'section', true );
		$entriesName = SPFactory::config()->nameField()->get( 'name' );
		$entriesField = SPFactory::config()->nameField()->get( 'nid' );
		$view 	= new $class();
		$view->assign( $entriesName, 'entries_name' );
		$view->assign( $entriesField, 'entries_field' );
		$view->assign( $eLimit, '$eLimit' );
		$view->assign( $cLimit, '$cLimit' );
		$view->assign( $eLimStart, '$eLimStart' );
		$view->assign( $cLimStart, '$cLimStart' );
		$view->assign( $cCount, '$cCount' );
		$view->assign( $eCount, '$eCount' );
		$view->assign( $this->_task, 'task' );
		$view->assign( $term, 'filter' );
		$view->assign( $this->customCols(), 'fields' );
		$view->assign( $this->_model, 'section' );
		if( $allEntries ) {
			$view->loadConfig( 'section.entries' );
			$view->setTemplate( 'section.entries' );
		}
		else {
			$view->loadConfig( 'section.list' );
			$view->setTemplate( 'section.list' );
		}
		$view->assign( $categories, 'categories' );
		$view->assign( $entries, 'entries' );
		$view->assign( SPFactory::config()->nameField()->get( 'name' ), 'entries_name' );
		$view->assign( $menu, 'menu' );
		$view->addHidden( Sobi::Section(), 'pid' );
		Sobi::Trigger( 'Section', 'View', array( &$view ) );
		$view->display();
	}

	// @todo duplicates the same method in category ctrl - need to merge it
	protected function customCols()
	{
		/* get fields for header */
		$fields = array();
		try {
			$fieldsData = SPFactory::db()
				->select( '*', 'spdb_field', array( '!admList' => 0, 'section' => Sobi::Reg( 'current_section' ) ), 'admList' )
				->loadObjectList();
		}
		catch ( SPException $x ) {
			Sobi::Error( $this->name(), SPLang::e( 'DB_REPORTS_ERR', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
		}
		if( count( $fieldsData ) ) {
			$fModel = SPLoader::loadModel( 'field', true );
			foreach ( $fieldsData as $field ) {
				$fit = new $fModel();
				/* @var SPField $fit */
				$fit->extend( $field );
				$fields[] = $fit;
			}
		}
		return $fields;
	}
	/**
	 * @param string $subject
	 * @param string $col
	 * @param string $def
	 * @param int $lim
	 * @param int $lStart
	 * @return string
	 */
	protected function parseOrdering( $subject, $col, $def, &$lim, &$lStart, &$sids )
	{
		$ord = Sobi::GetUserState( $subject.'.order', $col, $def );
		$ord = str_replace( array( 'e_s', 'c_s' ), null, $ord );
		if( strstr( $ord, '.' ) ) {
			$ord = explode( '.', $ord );
			$dir = $ord[ 1 ];
			$ord = $ord[ 0 ];
		}
		if( $ord == 'order' || $ord == 'position' ) {
			$subject = $subject == 'categories' ? 'category' : 'entry';
			/* @var SPdb $db */
			$db	=& SPFactory::db();
			$db->select( 'id', 'spdb_relations', array( 'oType' => $subject, 'pid' => $this->_model->get( 'id' ) ), 'position.'.$dir, $lim, $lStart );
			$fields = $db->loadResultArray();
			if( count( $fields ) ) {
				$sids = $fields;
				$fields = implode( ',', $fields );
				$ord = "field( id, {$fields} )";
				$lStart = 0;
				$lim = 0;
			}
			else {
				$ord = 'id.'.$dir;
			}
		}
		elseif( $ord == 'name' ) {
			$subject = $subject == 'categories' ? 'category' : 'entry';
			/* @var SPdb $db */
			$db	=& SPFactory::db();
			$db->select( 'id', 'spdb_language', array( 'oType' => $subject, 'sKey' => 'name', 'language' => Sobi::Lang() ), 'sValue.'.$dir );
			$fields = $db->loadResultArray();
			if( !count( $fields ) && Sobi::Lang() != Sobi::DefLang() ) {
				$db->select( 'id', 'spdb_language', array( 'oType' => $subject, 'sKey' => 'name', 'language' => Sobi::DefLang() ), 'sValue.'.$dir );
				$fields = $db->loadResultArray();
			}
			if( count( $fields ) ) {
				$fields = implode( ',', $fields );
				$ord = "field( id, {$fields} )";
			}
			else {
				$ord = 'id.'.$dir;
			}
		}
		elseif( strstr( $ord, 'field_' ) ) {
			$db	=& SPFactory::db();
			static $field = null;
			if( !$field ) {
				try {
					$db->select( 'fieldType', 'spdb_field', array( 'nid' => $ord, 'section' => Sobi::Section() ) );
					$fType = $db->loadResult();
				}
				catch ( SPException $x ) {
					Sobi::Error( $this->name(), SPLang::e( 'CANNOT_DETERMINE_FIELD_TYPE', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
				}
				if( $fType ) {
					$field = SPLoader::loadClass( 'opt.fields.'.$fType );
				}
			}
			/* *
			 * @TODO The whole sort by custom field method in admin panel has to be re-implemented -
			 * We could use the same field 'sortBy' method for backend and frontend.
			 * The current method could be very inefficient !!!
			 */
			if( $field && method_exists( $field, 'sortByAdm' ) ) {
				$fields =  call_user_func_array( array( $field, 'sortByAdm' ), array( &$ord, &$dir ) );
			}
			else {
				$join = array(
					array( 'table' => 'spdb_field', 'as' => 'def', 'key' => 'fid' ),
					array( 'table' => 'spdb_field_data', 'as' => 'fdata', 'key' => 'fid' )
				);
				$db->select( 'sid', $db->join( $join ), array( 'def.nid' => $ord, 'lang' => Sobi::Lang() ), 'baseData.'.$dir );
				$fields = $db->loadResultArray();
			}
			if( count( $fields ) ) {
				$fields = implode( ',', $fields );
				$ord = "field( id, {$fields} )";
			}
			else {
				$ord = 'id.'.$dir;
			}
		}
		elseif( $ord == 'state' ) {
			$ord = $ord.'.'.$dir.', validSince.'.$dir.', validUntil.'.$dir;
		}
		else {
			$ord = $ord.'.'.$dir;
		}
		return $ord;
	}

	/**
	 */
	private function editForm()
	{
		$class = SPLoader::loadView( 'section', true );
		$this->_model->formatDatesToEdit();
		$view = new $class();
		$view->assign( $this->_task, 'task' );
		$view->loadConfig( 'section.edit' );
		$view->setTemplate( 'section.edit' );
		$view->assign( $this->_model, 'section' );
		$view->display();
	}
}
