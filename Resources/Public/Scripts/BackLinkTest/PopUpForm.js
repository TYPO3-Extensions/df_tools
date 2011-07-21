/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 domainfactory GmbH (Stefan Galinski <sgalinski@df.eu>)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

Ext.ns('TYPO3.DfTools.BackLinkTest');

/**
 * Provides an edit comment form that is opened inside a pop-up window
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @extends Ext.FormPanel
 * @class TYPO3.DfTools.BackLinkTest.PopUpForm
 * @namespace TYPO3.DfTools.BackLinkTest
 */
TYPO3.DfTools.BackLinkTest.PopUpForm = Ext.extend(Ext.FormPanel, {
	/**
	 * @private
	 * @type {Ext.Window}
	 */
	window: null,

	/**
	 * Internally mapped record
	 *
	 * @private
	 * @type {Ext.data.Record}
	 */
	record: null,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			defaultType: 'textarea',
			frame: true,
			labelAlign: 'right',
			title: TYPO3.lang['tx_dftools_domain_model_backlinktest.commentForm'],
			defaults: {
				anchor: '100%'
			},
			items: [{
				fieldLabel: TYPO3.lang['tx_dftools_domain_model_backlinktest.comment'],
				height: 100,
				name: 'comment',
				id: 'comment',
				allowBlank: true
			}],

			buttons: [{
				text: TYPO3.lang['tx_dftools_common.update'],
				scope: this,
				handler: this.updateAndClose
			}, {
				text: TYPO3.lang['tx_dftools_common.cancel'],
				scope: this,
				handler: this.close
			}]
		}, configuration);

		TYPO3.DfTools.BackLinkTest.PopUpForm.superclass.constructor.call(this, configuration);
	},

	/**
	 * Updates the record after an initial validation
	 *
	 * @return {Boolean} false if the form didn't passed the validation
	 */
	updateRecord: function() {
		if (this.record === null || !this.getForm().isValid()) {
			return false;
		}

		this.getForm().updateRecord(this.record);

		return true;
	},

	/**
	 * Loads a new record
	 *
	 * @param {Ext.data.Record} record
	 * @return {void}
	 */
	setRecord: function(record) {
		this.record = record;
		this.getForm().loadRecord(record);
	},

	/**
	 * Initialization of the instance
	 *
	 * @return {void}
	 */
	initComponent: function() {
		TYPO3.DfTools.BackLinkTest.PopUpForm.superclass.initComponent.apply(this, arguments);
	},

	/**
	 * Opens a new window with this form as child item
	 *
	 * @param {Ext.data.Store} store
	 * @param {Ext.data.Record} record
	 * @return {void}
	 */
	open: function(store, record) {
		this.window = TYPO3.Windows.getWindow({
			id: 'tx_dftools-groupEditWindow',
			items: this,

			listeners: {
				show: {
					scope: this,
					delay: 100, // needed to prevent a timing problem
					fn: this.selectCommentField
				}
			}
		});

		this.setRecord(record);
		this.window.show();
	},

	/**
	 * Selects the text inside the comment field and focus it
	 *
	 * @return {void}
	 */
	selectCommentField: function() {
		this.get('comment').focus().selectText();
	},

	/**
	 * Closes the opened form window
	 *
	 * @return {void}
	 */
	close: function() {
		if (this.window) {
			this.window.hide();
		}
	},

	/**
	 * Updates the record information's and closes the form window
	 *
	 * @return {void}
	 */
	updateAndClose: function() {
		this.updateRecord();
		this.close();
	}
});

Ext.reg('TYPO3.DfTools.BackLinkTest.PopUpForm', TYPO3.DfTools.BackLinkTest.PopUpForm);