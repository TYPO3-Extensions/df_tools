/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <sgalinski@df.eu>, domainfactory GmbH
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

Ext.ns('TYPO3.Backend.DfTools', 'TYPO3.DfTools.ContentComparisonTest');

/**
 * Main Application Code For The Content Comparison Test App
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @class TYPO3.DfTools.ContentComparisonTest.App
 * @extends TYPO3.DfTools.AbstractApp
 * @namespace TYPO3.DfTools.ContentComparisonTest
 */
TYPO3.DfTools.ContentComparisonTest.App = Ext.extend(TYPO3.DfTools.AbstractApp, {
	/**
	 * @private
	 * @type {Ext.ux.grid.RowExpander}
	 */
	rowExpander: null,

	/**
	 * @private
	 * @type {Array}
	 */
	expandedRows: [],

	/**
	 * Initializes the component
	 *
	 * @return {void}
	 */
	initComponent: function() {
		this.dataProvider = TYPO3.DfTools.ContentComparisonTest.DataProvider;
		this.gridStore = new TYPO3.DfTools.ContentComparisonTest.Store();

		this.grid = new TYPO3.DfTools.Grid({
			renderTo: 'tx_dftools',

			store: this.gridStore,
			cm: this.getColumnModel(),
			fetchRowClass: this.fetchRowClass.createDelegate(this),

			plugins: [
				this.getRowExpander()
			],

			viewConfiguration: {
				hideGroupedColumn: true
			},

			tbar: [{
					id: 'tx_dftools-button-runTest',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.run + '"></span>'
							+ '<span class="tx_dftools-button-text">'
							+ TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.runTests'] + '</span>',
					scope: this,
					handler: this.onRunTests
				}, {
					id: 'tx_dftools-button-createRecord',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.create + '"></span>'
							+ '<span class="tx_dftools-button-text">'
							+ TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.create'] + '</span>',
					scope: this,
					handler: this.onAddRecord
				}
			],

			groupActions: [{
					iconCls: TYPO3.settings.DfTools.Sprites.run,
					qtip: TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.runTests'],
					scope: this,
					callback: this.onRunTestsOfGroup
				}
			]
		});

		TYPO3.DfTools.ContentComparisonTest.App.superclass.initComponent.apply(this, arguments);
	},

	/**
	 * Returns a row expander instance
	 *
	 * @private
	 * @return {Ext.ux.grid.RowExpander}
	 */
	getRowExpander: function() {
		return TYPO3.DfTools.ContentComparisonTest.App.superclass.getRowExpander.call(this, {
			enableCaching: false,
			tpl: new Ext.Template('<div id="tx_dftools-gridRow-{__identity}" >{difference}</div>')
		});
	},

	/**
	 * Adds a new content comparison test
	 *
	 * @return {void}
	 */
	onAddRecord: function() {
		var contentComparisonTest = new TYPO3.DfTools.ContentComparisonTest.Model({
			__identity: 0,
			testResult: 0,
			testMessage: '',
			testUrl: '/',
			compareUrl: '/',
			difference: ''
		});

		this.grid.addRecord(contentComparisonTest);
	},

	/**
	 * Returns the icon class for the test mode
	 *
	 * @private
	 * @param {String} value
	 * @param {Object} meta
	 * @param {Ext.data.Record} record
	 * @return {String}
	 */
	observeTestModeState: function(value, meta, record) {
		var tooltip = '', iconClass = '';
		if (record.get('testUrl') === record.get('compareUrl')) {
			tooltip = TYPO3.lang['tx_dftools_domain_model_contentcomparetest.updateTestContent'];
			iconClass = TYPO3.settings.DfTools.Sprites.refresh;
		}

		this.items[1].tooltip = tooltip;
		return iconClass;
	},

	/**
	 * Fires a server that updates the test content
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	onUpdateTestContent: function(grid, rowIndex) {
		this.closeExpandedRows();
		var record = grid.getStore().getAt(rowIndex);
		var id = record.get('__identity');

		TYPO3.DfTools.ContentComparisonTest.DataProvider.updateTestContent(id, function(response) {
			if (response.success) {
				record.data = Ext.apply(record.data, response.data || {});
				record.commit();

				var label = TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.testContentWasUpdated'];
				TYPO3.Flashmessage.display(TYPO3.Severity.ok, TYPO3.lang['tx_dftools_common.info'], label);
			}
		}, this);
	},

	/**
	 * Returns the column model for the content comparison test grid
	 *
	 * @private
	 * @return {Ext.grid.ColumnModel}
	 */
	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: {
				sortable: true
			},

			columns: [
				new Ext.grid.RowNumberer({
					width: 30
				}), this.getRowExpander(), {
					id: 'testUrl',
					header: TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.test_url'],
					dataIndex: 'testUrl',
					groupable: false,
					width: 200,

					scope: this,
					renderer: this.setValueAsCellToolTipRenderer,

					editor: {
						xtype: 'textfield',
						allowBlank: false
					}
				}, {
					id: 'compareUrl',
					header: TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.compare_url'],
					dataIndex: 'compareUrl',
					groupable: false,
					width: 200,

					scope: this,
					renderer: this.setValueAsCellToolTipRenderer,

					editor: {
						xtype: 'textfield',
						allowBlank: false
					}
				},{
					id: 'testResult',
					header: TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.test_result'],
					dataIndex: 'testResult',
					width: 50,
					editable: false,
					scope: this,
					renderer: this.renderTestResult
				}, {
					id: 'actions',
					xtype: 'actioncolumn',
					header: TYPO3.lang['tx_dftools_common.actions'],
					dataIndex: 'actions',
					sortable: false,
					groupable: false,
					hideable: false,
					menuDisabled: true,
					editable: false,
					width: 45,
					align: 'right',

					app: this,
					items: [{
							getClass: this.observeTestState
						}, {
							getClass: this.observeTestModeState,
							handler: this.onUpdateTestContent.createDelegate(this)
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.destroy,
							tooltip: TYPO3.lang['tx_dftools_common.delete'],
							scope: this,
							handler: function() {
								this.grid.deleteRecord.apply(this.grid, arguments);
							}
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.run,
							tooltip: TYPO3.lang['tx_dftools_domain_model_contentcomparisontest.runTest'],
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
	TYPO3.Backend.DfTools.App = new TYPO3.DfTools.ContentComparisonTest.App().run();
});