/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 domainfactory GmbH (Stefan Galinski <stefan@sgalinski.de>)
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

Ext.ns('TYPO3.Backend.DfTools', 'TYPO3.DfTools.LinkCheck');

/**
 * Main Application Code For The Link Check Test App
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @class TYPO3.DfTools.LinkCheck.App
 * @extends TYPO3.DfTools.AbstractApp
 * @namespace TYPO3.DfTools.LinkCheck
 */
TYPO3.DfTools.LinkCheck.App = Ext.extend(TYPO3.DfTools.AbstractApp, {
	/**
	 * Initializes the component
	 *
	 * @return {void}
	 */
	initComponent: function() {
		this.dataProvider = TYPO3.DfTools.LinkCheck.DataProvider;
		this.gridStore = new TYPO3.DfTools.LinkCheck.Store({
			remoteSort: true,
			baseParams: {
				start: 0,
				limit: 200,
				sort: 'testResult',
				dir: 'DESC'
			}
		});

		this.grid = new TYPO3.DfTools.Grid({
			renderTo: 'tx_dftools',

			store: this.gridStore,
			cm: this.getColumnModel(),
			fetchRowClass: this.fetchRowClass.createDelegate(this),
			useRowEditor: false,

			plugins: [
				this.getRowExpander()
			],

			viewConfiguration: {
				hideGroupedColumn: true
			},

			listeners: {
				celldblclick: {
					scope: this,
					fn: this.onCellDoubleClick
				}
			},

			tbar: [{
					id: 'tx_dftools-button-runTest',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.run + '"></span>'
							+ '<span class="tx_dftools-button-text">'
							+ TYPO3.lang['tx_dftools_domain_model_linkcheck.runTests'] + '</span>',
					scope: this,
					handler: this.onRunTests
				}, {
					xtype: 'tbfill'
				}, {
					id: 'tx_dftools-button-synchronize',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.refresh + '"></span>'
							+ '<span class="tx_dftools-button-text">'
							+ TYPO3.lang['tx_dftools_domain_model_linkcheck.synchronize'] + '</span>',
					scope: this,
					handler: this.onSynchronize
				}
			],

			groupActions: [{
					iconCls: TYPO3.settings.DfTools.Sprites.run,
					qtip: TYPO3.lang['tx_dftools_domain_model_linkcheck.runTests'],
					scope: this,
					callback: this.onRunTestsOfGroup
				}
			]
		});

		this.grid.getBottomToolbar().on('beforechange', this.closeExpandedRows, this);
		TYPO3.DfTools.LinkCheck.App.superclass.initComponent.apply(this, arguments);
	},

	/**
	 * Returns a row expander instance
	 *
	 * @private
	 * @return {Ext.ux.grid.RowExpander}
	 */
	getRowExpander: function() {
		return TYPO3.DfTools.LinkCheck.App.superclass.getRowExpander.call(this, {
			tpl: new Ext.Template('<div id="tx_dftools-gridRow-{__identity}" ></div>'),
			listeners: {
				expand: {
					scope: this,
					fn: this.createRecordSetGrid
				}
			}
		});
	},

	/**
	 * Creates a very simple grid for the record set visualization
	 *
	 * @private
	 * @param {Ext.ux.grid.RowExpander} rowExpander
	 * @param {Ext.data.Record} record
	 * @param {void}
			*/
	createRecordSetGrid: function(rowExpander, record) {
		var identity = record.get('__identity');

		var recordSetGrid = new Ext.grid.GridPanel({
			renderTo: 'tx_dftools-gridRow-' + identity,
			store: new TYPO3.DfTools.RecordSet.Store({
				baseParams: {
					identity: identity
				}
			}),

			height: 150,
			width: 700,
			viewConfig: {
				autoFill: true
			},

			stripeRows: true,
			frame: true,
			border: false,

			columns: [{
					id: 'humanReadableTableName',
					header: TYPO3.lang['tx_dftools_domain_model_recordset.table_name'],
					dataIndex: 'humanReadableTableName',
					sortable: true,
					width: 170
				}, {
					id: 'field',
					header: TYPO3.lang['tx_dftools_domain_model_recordset.field'],
					dataIndex: 'field',
					sortable: true,
					width: 130
				}, {
					id: 'identifier',
					header: TYPO3.lang['tx_dftools_domain_model_recordset.identifier'],
					dataIndex: 'identifier',
					sortable: true,
					width: 80,
					align: 'center'
				}, {
					id: 'actions',
					xtype: 'actioncolumn',
					header: TYPO3.lang['tx_dftools_common.actions'],
					hideable: false,
					menuDisabled: true,
					width: 50,
					align: 'center',

					items: [{
							iconCls: TYPO3.settings.DfTools.Sprites.edit,
							tooltip: TYPO3.lang['tx_dftools_common.editRecord'],
							urlIdentity: identity,
							scope: this,
							handler: this.onOpenRecordSet
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.showPage,
							tooltip: TYPO3.lang['tx_dftools_common.showPage'],
							scope: this,
							handler: this.onShowPage
						}
					]
				}
			]
		});

		// stop some events from bubbling up
		recordSetGrid.getEl().swallowEvent(['mouseover', 'mousedown', 'click', 'dblclick']);
	},

	/**
	 * Opens the edit dialog for this record set inside a new window
	 *
	 * @param {Ext.grid.GridPanel} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	onOpenRecordSet: function(grid, rowIndex) {
		var dataSet = grid.getStore().getAt(rowIndex);
		var url = 'alt_doc.php?edit[' + dataSet.get('tableName') +
				'][' + dataSet.get('identifier') + ']=edit&returnUrl=' +
				TYPO3.settings.DfTools.Settings.destroyWindowFile;
		window.open(url, 'newTYPO3frontendWindow').focus();
	},

	/**
	* Opens a new window with the related page of the data set
	*
	* @param {Ext.grid.GridPanel} grid
	* @param {int} rowIndex
	* @return {void}
	*/
	onShowPage: function(grid, rowIndex) {
		var dataSet = grid.getStore().getAt(rowIndex);
		var frontendWindow = window.open('', 'newTYPO3frontendWindow');
		TYPO3.DfTools.RecordSet.DataProvider.getViewLink(
			dataSet.get('tableName'),
			dataSet.get('identifier'),
			function(result) {
				frontendWindow.location = result;
				frontendWindow.focus();
			}
		);
	},

	/**
	 * Triggers the synchronize Ext.Direct call and reloads the data
	 *
	 * @return {void}
	 */
	onSynchronize: function() {
		this.grid.mask();
		TYPO3.DfTools.LinkCheck.DataProvider.synchronize(function(response) {
			if (response.success) {
				var message = TYPO3.lang['tx_dftools_domain_model_linkcheck.synchronizationSuccessful'];
				TYPO3.Flashmessage.display(TYPO3.Severity.ok, TYPO3.lang['tx_dftools_common.info'], message);
			} else {
				var label = TYPO3.lang['tx_dftools_common.exception'];
				TYPO3.Flashmessage.display(TYPO3.Severity.error, label, response.message);
			}

			this.gridStore.load();
			this.grid.unmask();
		}, this);
	},

	/**
	 * Checks if the value of a cell looks like a valid url and opens this one in a
	 * dedicated window
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @param {int} columnIndex
	 * @return {void}
	 */
	onCellDoubleClick: function(grid, rowIndex, columnIndex) {
		var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
		var value = grid.getSelectionModel().getSelected().get(fieldName);
		if (!Ext.isString(value)) {
			return;
		}

		if (value.indexOf('http') === 0 || value.indexOf('ftp') === 0) {
			window.open(value.replace(/&amp;/gi, '&'), 'newTYPO3frontendWindow').focus();
		}
	},

	/**
	 * Toggles the ignore test result of a record
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	onToggleIgnoreRecord: function(grid, rowIndex) {
		var record = grid.getStore().getAt(rowIndex);
		var callback = null;
		if (record.get('testResult') == this.severityMap.IGNORE) {
			callback = TYPO3.DfTools.LinkCheck.DataProvider.observeRecord;
		} else {
			callback = TYPO3.DfTools.LinkCheck.DataProvider.ignoreRecord;
		}

		callback.call(this, record.get('__identity'), function(response) {
			if (response.success) {
				record.data = Ext.apply(record.data, response.data || {});
				record.commit();
			}
		}, this);
	},

	/**
	 * Returns the css classes for an ignore or observe behaviour depending
	 * on the record state
	 *
	 * @private
	 * @param {String} value
	 * @param {Object} meta
	 * @param {Ext.data.Record} record
	 * @return {String}
	 */
	toggleIgnoreOberserve: function(value, meta, record) {
		var tooltip = '', iconClass = '';
		if (record.get('testResult') == this.app.severityMap.IGNORE) {
			tooltip = TYPO3.lang['tx_dftools_domain_model_linkcheck.observeTestRecord'];
			iconClass = TYPO3.settings.DfTools.Sprites.unhide + ' tx_dftools-statusIgnore';
		} else {
			tooltip = TYPO3.lang['tx_dftools_domain_model_linkcheck.ignoreTestRecord'];
			iconClass = TYPO3.settings.DfTools.Sprites.hide + ' tx_dftools-statusObserve';
		}

		this.items[1].tooltip = tooltip;
		return iconClass;
	},

	/**
	 * Forces the test result to be handled as an false positive severity
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	onToggleFalsePositiveAction: function(grid, rowIndex) {
		var record = grid.getStore().getAt(rowIndex);
		var callback = null;
		if (record.get('testResult') == this.severityMap.INFO) {
			callback = TYPO3.DfTools.LinkCheck.DataProvider.resetAsFalsePositive;
		} else {
			callback = TYPO3.DfTools.LinkCheck.DataProvider.setAsFalsePositive;
		}

		callback.call(this, record.get('__identity'), function(response) {
			if (response.success) {
				record.data = Ext.apply(record.data, response.data || {});
				record.commit();
			}
		}, this);
	},

	/**
	 * Returns the css classes for the false positive record action
	 *
	 * @private
	 * @param {String} value
	 * @param {Object} meta
	 * @param {Ext.data.Record} record
	 * @return {String}
	 */
	toggleFalsePositiveClass: function(value, meta, record) {
		var tooltip = '', iconClass = '';
		if (record.get('testResult') ==  this.app.severityMap.INFO) {
			tooltip = TYPO3.lang['tx_dftools_domain_model_linkcheck.resetFalsePositiveState'];
			iconClass = TYPO3.settings.DfTools.Sprites.information;
		} else {
			tooltip = TYPO3.lang['tx_dftools_domain_model_linkcheck.forceAsFalsePositiveState'];
			iconClass = TYPO3.settings.DfTools.Sprites.unknown;
		}

		this.items[2].tooltip = tooltip;
		return iconClass;
	},

	/**
	 * Returns the column model for the redirect test grid
	 *
	 * @private
	 * @return {Ext.grid.ColumnModel}
	 */
	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: {
				sortable: true,
				groupable: false
			},

			columns: [
				new Ext.grid.RowNumberer({
					width: 30
				}), this.getRowExpander() ,{
					id: 'testUrl',
					header: TYPO3.lang['tx_dftools_domain_model_linkcheck.test_url'],
					dataIndex: 'testUrl',
					width: 200,
					sortable: false,
					scope: this,
					renderer: this.setValueAsCellToolTipRenderer
				}, {
					id: 'resultUrl',
					header: TYPO3.lang['tx_dftools_domain_model_linkcheck.result_url'],
					dataIndex: 'resultUrl',
					width: 200,
					sortable: false,
					scope: this,
					renderer: this.setValueAsCellToolTipRenderer
				}, {
					id: 'httpStatusCode',
					header: TYPO3.lang['tx_dftools_domain_model_linkcheck.http_status_code.grid'],
					dataIndex: 'httpStatusCode',
					sortable: true,
					align: 'center',
					width: 50
				}, {
					id: 'testResult',
					header: TYPO3.lang['tx_dftools_domain_model_linkcheck.test_result'],
					dataIndex: 'testResult',
					width: 50,
					scope: this,
					renderer: this.renderTestResult
				}, {
					id: 'actions',
					xtype: 'actioncolumn',
					header: TYPO3.lang['tx_dftools_common.actions'],
					hideable: false,
					menuDisabled: true,
					sortable: false,
					width: 48,
					align: 'right',

					app: this,
					items: [{
							getClass: this.observeTestState
						}, {
							getClass: this.toggleIgnoreOberserve,
							handler: this.onToggleIgnoreRecord.createDelegate(this)
						}, {
							getClass: this.toggleFalsePositiveClass,
							handler: this.onToggleFalsePositiveAction.createDelegate(this)
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.run,
							tooltip: TYPO3.lang['tx_dftools_domain_model_linkcheck.runTest'],
							scope: this,
							handler: this.onRunSingleTest
						}
					]
				}
			]
		});
	}
});

Ext.onReady(function() {
	TYPO3.Backend.DfTools.App = new TYPO3.DfTools.LinkCheck.App().run();
});