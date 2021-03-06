<?php
/**
 * @version: $Id: edit.php 2155 2012-01-13 18:28:57Z Radek Suski $
 * @package: SobiPro Template
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2012 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 * ===================================================
 * $Date: 2012-01-13 19:28:57 +0100 (Fri, 13 Jan 2012) $
 * $Revision: 2155 $
 * $Author: Radek Suski $
 * File location: administrator/components/com_sobipro/field/edit.php $
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );
SPRequest::set( 'hidemainmenu', 1 );
SPFactory::header()->addCSSCode( 'body { min-width: 1200px; }' );
$row = 0;
?>
<script language="javascript" type="text/javascript">
	SPErrMsg = SobiPro.Txt( "Please fill in all required fields!" );
	function submitbutton( task )
	{
		var form = document.adminForm;
		if ( task == 'field.cancel' || SPValidateForm()  ) {
			if( SP_id( 'field.nid' ).value == '' ) {
				SP_id( 'field.nid' ).value = SP_id( 'field.name' ).value;
			}
			var nid = SP_id( 'field.nid' ).value;
			nid = nid.replace( /(\s+)/g, '_' );
			nid = nid.replace( /[^\w_]/g, '' );
			SP_id( 'field.nid' ).value = nid.toLowerCase();
			if( SP_id( 'field.nid' ).value.indexOf( 'field_' ) != 0 ) {
				SP_id( 'field.nid' ).value = 'field_' + SP_id( 'field.nid' ).value;
			}
			submitform( task );
		}
	}
	try { Joomla.submitbutton = function( task ) { submitbutton( task ); } } catch( e ) {}
</script>
<?php $this->trigger( 'OnStart' ); ?>
<div class="col width-30" style="float: right;">
	<fieldset class="adminform" style="border: 1px dashed silver;"><legend><?php $this->txt( 'FM.FIELD_PARAMETERS' ); ?></legend>
		<table class="admintable">
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'ENABLED' ); ?>
				</td>
				<td>
					<?php $this->field( 'states', 'field.enabled', 'value:field.enabled', 'enabled', 'enabled', 'class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<label for="show_in">
						<?php $this->txt( 'FM.AVAILABLE_IN' ); ?>
					</label>
				</td>
				<td>
					<?php $this->field( 'select', 'field.showIn', 'both=translate:[FM.BOTH_OPT], details=translate:[FM.DETAILS_VIEW_OPT], vcard=translate:[FM.V_CARD_OPT], hidden=translate:[FM.HIDDEN_OPT]', 'value:field.showIn', false, 'id=show_in, size=1, class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<label for="field_css">
						<?php $this->txt( 'FM.CSS_CLASS' ); ?>
					</label>
				</td>
				<td>
					<?php
						$params = array( 'id' => 'field_css', 'size' => 15, 'maxlength' => 50, 'class'=> 'inputbox', 'style' => 'text-align:center;' );
						if( $this->get( 'field.id' ) == SPFactory::config()->nameField()->get( 'id' ) ) {
							$params[ 'readonly' ] = 'readonly';
						}
						$this->field( 'text', 'field.cssClass', 'value:field.cssClass', $params );
					?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'FM.ADM_FIELD' ); ?>
				</td>
				<td>
					<?php $this->field( 'states', 'field.adminField', 'value:field.adminField', 'admin_field', 'yes_no', 'class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'FM.IS_REQUIRED' ); ?>
				</td>
				<td>
					<?php $this->field( 'states', 'field.required', 'value:field.required', 'required', 'yes_no', 'class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'FM.IS_EDITABLE' ); ?>
				</td>
				<td>
					<?php $this->field( 'states', 'field.editable', 'value:field.editable', 'editable', 'yes_no', 'class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'FM.SHOW_LABEL' ); ?>
				</td>
				<td>
					<?php $this->field( 'states', 'field.withLabel', 'value:field.withLabel', 'withLabel', 'yes_no', 'class=inputbox' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<label for="field_edit_limit">
						<?php $this->txt( 'FM.EDIT_LIMITS' ); ?>
					</label>
				</td>
				<td>
					<?php $this->field( 'text', 'field.editLimit', 'value:field.editLimit', 'id=field_edit_limit, size=5, maxlength=10, class=inputbox, style=text-align:center;' ); ?>
				</td>
			</tr>
			<tr class="row<?php echo ++$row%2; ?>">
				<td class="key">
					<?php $this->txt( 'VERSION' ); ?>
				</td>
				<td>
					&nbsp;<b><?php $this->show( 'field.version' ); ?></b>
				</td>
			</tr>
		</table>
		</fieldset>
		<?php $this->trigger( 'AfterFixedParams' ); ?>
		<fieldset class="adminform" style="border: 1px dashed silver;">
			<legend>
				<?php $this->txt( 'FM.FIELD_TYPE' ); ?>
			</legend>
			<table class="admintable">
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<label for="field_type">
							<?php $this->txt( 'FM.FIELD_TYPE' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'select', 'field.fieldType', 'value:types', 'value:field.fieldType', false, 'id=field_type, size=15, class=inputbox required, style=width:200px;', 'field_type' ); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform" style="border: 1px dashed silver;">
			<legend>
				<?php $this->txt( 'FM.PAYMENT' ); ?>
			</legend>
			<table class="admintable">
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<?php $this->txt( 'FM.FOR_FREE' ); ?>
					</td>
					<td>
						<?php $this->field( 'states', 'field.isFree', 'value:field.isFree', 'is_free', 'yes_no', 'class=inputbox' ); ?>
					</td>
				</tr>
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<label for="field_fee">
							<?php $this->txt( 'FM.FIELD_FEE' ); ?>
						</label>
					</td>
					<td>
						<?php
                            $this->field( 'text', 'field.fee', 'value:field.fee', 'id=field_fee, size=10, maxlength=10, class=inputbox, style=text-align:center;' );
                        ?>
						&nbsp; <?php echo Sobi::Cfg( 'payments.currency', 'EUR' ); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php $this->trigger( 'AfterParams' ); ?>
	</div>
	<?php $this->trigger( 'BeforeFixed' ); ?>
	<div class="col width-70" style="float: left;">
		<fieldset class="adminform">
			<table class="admintable">
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key">
						<label for="field.name">
							<?php $this->txt( 'FM.FIELD_LABEL' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'text', 'field.name', 'value:field.name', 'id=field.name, size=50, maxlength=255, class=inputbox required' ); ?>
					</td>
				</tr>
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key">
						<label for="field.nid">
							<?php $this->txt( 'FM.ALIAS' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'text', 'field.nid', 'value:field.nid', 'id=field.nid, size=50, maxlength=255, class=inputbox' ); ?>
					</td>
				</tr>
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<label for="field.notice">
							<?php $this->txt( 'FM.SUFFIX' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'text', 'field.suffix', 'value:field.suffix', 'id=field.suffix, size=50, maxlength=255, class=inputbox' ); ?>
					</td>
				</tr>
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<label for="field.notice">
							<?php $this->txt( 'FM.NOTICES' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'textarea', 'field.notice', 'value:field.notice', false, 550, 30, 'id=field.notice' ); ?>
					</td>
				</tr>
				<tr class="row<?php echo ++$row%2; ?>">
					<td class="key" valign="top">
						<label for="field.description">
							<?php $this->txt( 'FM.DESCRIPTION' ); ?>
						</label>
					</td>
					<td>
						<?php $this->field( 'textarea', 'field.description', 'value:field.description', false, 550, 150, 'id=field.description' ); ?>
					</td>
				</tr>
			</table>
	</fieldset>
	<?php $this->trigger( 'AfterFixed' ); ?>
</div>
<?php $this->trigger( 'OnEnd' ); ?>
