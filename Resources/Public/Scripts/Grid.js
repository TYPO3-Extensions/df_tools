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

Ext.ns('TYPO3.DfTools');

/**
 * Custom grid component with the following additional feature set:
 *
 * - Auto-Fit the size (provided by an external plugin)
 * - Grouping (provided by a default view)
 * - Group Actions (provided by an external plugin)
 * - Row Editor (provided by an external plugin; optional)
 * - Basic API for creating and deleting records via the store
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @extends Ext.grid.GridPanel
 * @class TYPO3.DfTools.Grid
 * @namespace TYPO3.DfTools
 */
TYPO3.DfTools.Grid = Ext.extend(Ext.grid.GridPanel, {
	/**
	 * @type {Ext.ux.grid.RowEditor}
	 */
	rowEditorPlugin: null,

	/**
	 * @type {Ext.ux.grid.GroupActions}
	 */
	groupActionsPlugin: null,

	/**
	 * Group Actions
	 *
	 * @cfg {Object}
	 */
	groupActions: null,

	/**
	 * Indicator if the row editor should be enabled
	 *
	 * @cfg {Boolean}
	 */
	useRowEditor: true,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		this.groupActions = configuration.groupActions || {};
		this.groupActionsPlugin = this.getGroupActionsPlugin();

		var defaultPlugins = [
			new Ext.ux.plugins.FitToParent(),
			this.groupActionsPlugin
		];

		if (Ext.isArray(configuration.plugins)) {
			Ext.each(configuration.plugins, function(plugin) {
				defaultPlugins.push(plugin);
			});
			delete configuration.plugins;
		}

		configuration = Ext.apply({
			frame: true,
			border: false,
			view: (configuration.view || this.getViewInstance(configuration.viewConfiguration)),
			plugins: defaultPlugins,
			useRowEditor: true
		}, configuration);

		if (configuration.useRowEditor) {
			this.rowEditorPlugin = this.getRowEditorPlugin();
			configuration.plugins.push(this.rowEditorPlugin);
		}

		TYPO3.DfTools.Grid.superclass.constructor.call(this, configuration);
	},

	/**
	 * Initializes the component
	 */
	initComponent: function() {
		if (this.useRowEditor) {
			this.on('groupclick', function() {
				this.rowEditorPlugin.stopEditing();
			}, this);
		}

		if (this.store.baseParams.limit) {
			this.bbar = {
				xtype: 'paging',
				store: this.store,
				pageSize: this.store.baseParams.limit,
				displayInfo: true,
				afterPageText: TYPO3.lang['tx_dftools_common.pager.ofPages'],
				beforePageText: TYPO3.lang['tx_dftools_common.pager.page'],
				displayMsg: TYPO3.lang['tx_dftools_common.pager.displayAmountOfThis'],
				emptyMsg: TYPO3.lang['tx_dftools_common.pager.noData']
			};
		}

		TYPO3.DfTools.Grid.superclass.initComponent.apply(this, arguments);
	},

	/**
	 * Adds a record to the grid and the underlying store
	 *
	 * @param {Ext.data.Record} record
	 * @return {void}
	 */
	addRecord: function(record) {
		if (this.useRowEditor) {
			this.rowEditorPlugin.stopEditing();
		}

		this.getStore().insert(0, record);
		this.getView().refresh();
		this.getSelectionModel().selectRow(0);

		if (this.useRowEditor) {
			this.rowEditorPlugin.startEditing(0);
		}
	},

	/**
	 * Deletes a record from the grid and the underlying store
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	deleteRecord: function(grid, rowIndex) {
		if (this.useRowEditor) {
			this.rowEditorPlugin.stopEditing();
		}

		new TYPO3.Dialog.QuestionDialog({
			title: TYPO3.lang['tx_dftools_common.deleteQuestion.title'],
			msg: TYPO3.lang['tx_dftools_common.deleteQuestion.message'],
			buttons: Ext.Msg.YESNO,
			scope: grid,
			fn: function(answer) {
				return this.onDeleteRecordQuestion(answer, rowIndex);
			}
		});
	},

	/**
	 * Event callback for the question dialog of the delete record function
	 *
	 * @param {String} answer
	 * @param {int} rowIndex
	 * @return {Boolean}
	 */
	onDeleteRecordQuestion: function(answer, rowIndex) {
		if (answer !== 'yes') {
			return false;
		}

		var store = this.getStore();
		store.remove(store.getAt(rowIndex));

		return true;
	},

	/**
	 * Returns the default view of this grid
	 *
	 * @private
	 * @param {Object} viewConfiguration
	 * @return {Ext.grid.GroupingView}
	 */
	getViewInstance: function(viewConfiguration) {
		return new Ext.grid.GroupingView(Ext.apply({
			forceFit: true,
			showGroupName: false,

			getRowClass: function() {
				var scope = this.grid || this;
				if (Ext.isFunction(scope.fetchRowClass)) {
					return scope.fetchRowClass.apply(scope, arguments);
				}
			},

			emptyGroupText: TYPO3.lang['tx_dftools_common.emptyGroup'],
			groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "' +
				TYPO3.lang['tx_dftools_common.items'] + '" : "' +
				TYPO3.lang['tx_dftools_common.item'] + '"]})'
		}, (viewConfiguration || {})));
	},

	/**
	 * Returns a row editor instance
	 *
	 * @private
	 * @return {Ext.ux.grid.RowEditor}
	 */
	getRowEditorPlugin: function() {
		return new Ext.ux.grid.RowEditor({
			clicksToEdit: 2,
			frameWidth: 4,
			minButtonWidth: 100,
			saveText: TYPO3.lang['tx_dftools_common.update'],
			cancelText: TYPO3.lang['tx_dftools_common.cancel'],
			commitChangesText: TYPO3.lang['tx_dftools_common.editor_cancelOrCommit'],
			errorText: TYPO3.lang['tx_dftools_common.errors']
		});
	},

	/**
	 * Returns the group actions instance
	 *
	 * @private
	 * @return {Ext.ux.grid.GroupActions}
	 */
	getGroupActionsPlugin: function() {
		return new Ext.ux.grid.GroupActions({
			groupActions: this.groupActions
		});
	},

	/**
	 * Sets the current value as a tooltip for the cell
	 *
	 * @param {String} value
	 * @param {Object} metadata
	 * @return {String} current value
	 */
	setValueAsCellToolTipRenderer: function(value, metadata) {
		if (!Ext.isEmpty(value)) {
			metadata.attr = 'ext:qtip="' + value + '"';
		}

		return value;
	},

	/**
	 * Masks the grid
	 *
	 * @return {void}
	 */
	mask: function() {
		this.disable();
		this.getEl().mask('', 'x-mask-loading-message');
		this.addClass('t3-mask-loading');
	},

	/**
	 * Unmasks the grid
	 *
	 * @return {void}
	 */
	unmask: function() {
		this.enable();
		this.getEl().unmask();
		this.removeClass('t3-mask-loading');
	}
});

Ext.reg('TYPO3.DfTools.Grid', TYPO3.DfTools.Grid);