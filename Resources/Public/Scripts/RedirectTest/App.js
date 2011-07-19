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

Ext.ns('TYPO3.Backend.DfTools', 'TYPO3.DfTools.RedirectTest');

/**
 * Main Application Code For The Redirect Test App
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @class TYPO3.DfTools.RedirectTest.App
 * @extends TYPO3.DfTools.AbstractApp
 * @namespace TYPO3.DfTools.RedirectTest
 */
TYPO3.DfTools.RedirectTest.App = Ext.extend(TYPO3.DfTools.AbstractApp, {
	/**
	 * @type {TYPO3.DfTools.DirectStore}
	 */
	categoryStore: null,

	/**
	 * @private
	 * @type {TYPO3.DfTools.RedirectTestCategory.PopUpForm}
	 */
	popUpForm: null,

	/**
	 * Initializes the component
	 *
	 * @return {void}
	 */
	initComponent: function() {
		this.dataProvider = TYPO3.DfTools.RedirectTest.DataProvider;
		this.gridStore = new TYPO3.DfTools.RedirectTest.Store();
		this.categoryStore = new TYPO3.DfTools.RedirectTestCategory.Store();
		this.popUpForm = new TYPO3.DfTools.RedirectTestCategory.PopUpForm();

		this.grid = new TYPO3.DfTools.Grid({
			renderTo: 'tx_dftools',

			store: this.gridStore,
			cm: this.getColumnModel(),
			fetchRowClass: this.fetchRowClass.createDelegate(this),

			tbar: [{
					id: 'tx_dftools-button-runTest',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.run + '"></span>'
						+ '<span class="tx_dftools-button-text">'
						+ TYPO3.lang['tx_dftools_domain_model_redirecttest.runTests'] + '</span>',
					scope: this,
					handler: this.onRunTests
				}, {
					id: 'tx_dftools-button-createRecord',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.create + '"></span>'
						+ '<span class="tx_dftools-button-text">'
						+ TYPO3.lang['tx_dftools_domain_model_redirecttest.create'] + '</span>',
					scope: this,
					handler: this.onAddRecord
				}, {
					xtype: 'tbfill'
				}, {
					id: 'tx_dftools-button-deleteCategories',
					iconCls: '',
					text: '<span class="' + TYPO3.settings.DfTools.Sprites.destroy + '"></span>'
						+ '<span class="tx_dftools-button-text">'
						+ TYPO3.lang['tx_dftools_domain_model_redirecttestcategory.removeUnusedCategories'] + '</span>',
					scope: this,
					handler: this.onDeleteUnusedCategories
				}
			],

			groupActions: [{
					iconCls: TYPO3.settings.DfTools.Sprites.run,
					qtip: TYPO3.lang['tx_dftools_domain_model_redirecttest.runTests'],
					scope: this,
					callback: this.onRunTestsOfGroup
				}, {
					iconCls: TYPO3.settings.DfTools.Sprites.edit,
					qtip: TYPO3.lang['tx_dftools_domain_model_redirecttestcategory.edit'],
					visibleForGroups: ['categoryId'],
					scope: this,
					callback: this.onEditGroup
				}
			]
		});

		TYPO3.DfTools.RedirectTest.App.superclass.initComponent.apply(this, arguments);
	},

	/**
	 * Runs the application
	 *
	 * @return {TYPO3.DfTools.RedirectTest.App}
	 */
	run: function() {
		this.grid.mask();
		this.grid.getView().on('refresh', this.grid.unmask, this.grid);

		this.categoryStore.load({
			scope: this,
			callback: function() {
				this.gridStore.load();
			}
		});

		return this;
	},

	/**
	 * Ask the user if he really wants to delete all unused categories
	 *
	 * @return {void}
	 */
	onDeleteUnusedCategories: function() {
		new TYPO3.Dialog.QuestionDialog({
			title: TYPO3.lang['tx_dftools_domain_model_redirecttestcategory.massDeleteQuestion.title'],
			msg: TYPO3.lang['tx_dftools_domain_model_redirecttestcategory.massDeleteQuestion.message'],
			buttons: Ext.Msg.YESNO,
			scope: this,
			fn: function (answer) {
				if (answer === 'yes') {
					this.deleteUnusedCategories();
				}
			}
		});
	},

	/**
	 * Deletes all unused categories and reloads the category store
	 *
	 * @return {void}
	 */
	deleteUnusedCategories: function() {
		TYPO3.DfTools.RedirectTestCategory.DataProvider.deleteUnusedCategories(function(response) {
			if (response.success) {
				var label = TYPO3.lang['tx_dftools_domain_model_redirecttestcategory.unusedCategoriesDeleted'];
				TYPO3.Flashmessage.display(TYPO3.Severity.ok, TYPO3.lang['tx_dftools_common.info'], label);
			} else {
				var header = TYPO3.lang['tx_dftools_common.exception'];
				TYPO3.Flashmessage.display(TYPO3.Severity.error, header, response.message);
			}

			this.categoryStore.load();
		}, this);
	},

	/**
	 * Adds a new redirect test
	 *
	 * @return {void}
	 */
	onAddRecord: function() {
		var redirectTest = new TYPO3.DfTools.RedirectTest.Model({
			__identity: 0,
			categoryId: 0,
			testResult: 0,
			testMessage: '',
			testUrl: '/',
			expectedUrl: '/',
			httpStatusCode: 200
		});

		this.grid.addRecord(redirectTest);
	},

	/**
	 * Open the edit form pop-up window with the clicked group category
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {String} groupId
	 * @param {String} groupField
	 * @param {String} groupValue
	 * @return {void}
	 */
	onEditGroup: function(grid, groupId, groupField, groupValue) {
		if (groupField !== 'categoryId') {
			return;
		}

		var index = this.categoryStore.findExact('category', groupValue);
		var record = this.categoryStore.getAt(index);

		if (record) {
			grid.rowEditorPlugin.stopEditing();
			this.popUpForm.open(this.categoryStore, record);
		}
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
				sortable: true
			},

			columns: [
				new Ext.grid.RowNumberer({
					width: 30
				}), {
					id: 'testUrl',
					header: TYPO3.lang['tx_dftools_domain_model_redirecttest.test_url'],
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
					id: 'expectedUrl',
					header: TYPO3.lang['tx_dftools_domain_model_redirecttest.expected_url'],
					dataIndex: 'expectedUrl',
					groupable: false,
					width: 200,

					scope: this,
					renderer: this.setValueAsCellToolTipRenderer,

					editor: {
						xtype: 'textfield',
						allowBlank: false
					}
				}, {
					id: 'httpStatusCode',
					header: TYPO3.lang['tx_dftools_domain_model_redirecttest.http_status_code.grid'],
					dataIndex: 'httpStatusCode',
					align: 'center',
					width: 60,
					editor: {
						xtype: 'numberfield',
						allowBlank: false,
						allowNegative: false,
						maxValue: 1000
					}
				}, {
					id: 'category',
					header: TYPO3.lang['tx_dftools_domain_model_redirecttest.category'],
					dataIndex: 'categoryId',
					width: 100,

					renderer: function(value) {
						var record = this.editor.store.getById(value);
						return record ? record.get(this.editor.displayField) : value;
					},

					editor: {
						xtype: 'combo',
						allowBlank: false,
						valueField: '__identity',
						displayField: 'category',

						lazyRender: true,
						typeAhead: true,
						selectOnFocus: true,
						autoSelect: false,
						lastQuery: '',

						mode: 'local',
						minChars: 0,
						store: 'DfToolsRedirectTestCategoryStore',

						enableKeyEvents: true,
						listeners: {
							keydown: function(field, event) {
								if (event.getKey() === event.ENTER) {
									field.assertValue();
									field.blur();
								}
							},

							blur: function() {
								this.doQuery('', true);
							}
						}
					}
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
					width: 55,
					align: 'right',

					app: this,
					items: [{
							getClass: this.observeTestState
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.destroy,
							tooltip: TYPO3.lang['tx_dftools_common.delete'],
							scope: this,
							handler: function() {
								this.grid.deleteRecord.apply(this.grid, arguments);
							}
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.run,
							tooltip: TYPO3.lang['tx_dftools_domain_model_redirecttest.runTest'],
							scope: this,
							handler: this.onRunSingleTest
						}, {
							iconCls: TYPO3.settings.DfTools.Sprites.edit,
							tooltip: TYPO3.lang['tx_dftools_domain_model_redirecttest.edit'],
							scope: this,
							handler: this.onEditRecord
						}
					]
				}
			]
		});
	}
});

Ext.onReady(function() {
	TYPO3.Backend.DfTools.App = new TYPO3.DfTools.RedirectTest.App().run();
});