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

Ext.ns('TYPO3.Backend.DfTools');

/**
 * Abstract Application Code
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @class TYPO3.DfTools.AbstractApp
 * @extends Ext.Component
 * @namespace TYPO3.DfTools
 */
TYPO3.DfTools.AbstractApp = Ext.extend(Ext.Component, {
	/**
	 * @type {TYPO3.DfTools.Grid}
	 */
	grid: null,

	/**
	 * @type {TYPO3.DfTools.GroupingStore}
	 */
	gridStore: null,

	/**
	 * Ext.Direct Data Provider
	 *
	 * @type {Object}
	 */
	dataProvider: null,

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
	 * @private
	 * @type {Object}
	 */
	severityMap: {
		UNTESTED: 9,
		EXCEPTION: 6,
		ERROR: 5,
		WARNING: 4,
		INFO: 3,
		OK: 2,
		IGNORE: 1
	},

	/**
	 * Runs the application
	 *
	 * Note: Override the function if you need a more fine-grained load logic
	 *
	 * @abstract
	 * @return {TYPO3.DfTools.AbstractApp}
	 */
	run: function() {
		this.grid.mask();
		this.grid.getView().on('refresh', this.grid.unmask, this.grid);
		this.gridStore.load();

		return this;
	},

	/**
	 * Runs the test for all entries in the link check test store.
	 *
	 * Note: The grid will be disabled until the tests are finished!
	 *
	 * @return {void}
	 */
	onRunTests: function() {
		this.grid.mask();

		var records = [];
		this.gridStore.each(function(record) {
			records.push(record);
		});

		this.runTest(records, null, 0);
	},

	/**
	 * Runs a single test
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {int} rowIndex
	 * @return {void}
	 */
	onRunSingleTest: function(grid, rowIndex) {
		var record = grid.getStore().getAt(rowIndex);
		this.runTest([record], null, rowIndex);
	},

	/**
	 * Runs tests for all entries inside a group
	 *
	 * Note: The grid will be disabled until the tests are finished!
	 *
	 * @param {TYPO3.DfTools.Grid} grid
	 * @param {String} groupId
	 * @param {String} groupField
	 * @param {String} groupValue
	 * @return {void}
	 */
	onRunTestsOfGroup: function(grid, groupId, groupField, groupValue) {
		var callback = this.getRecordsByGroupValue.createDelegate(this, [groupField, groupValue], true);
		var records = this.gridStore.queryBy(callback);

		var indices = [];
		records.each(function(record) {
			indices.push(this.gridStore.indexOf(record));
		}, this);

		if (records.getCount()) {
			grid.mask();
			grid.getView().toggleGroup(groupId, true);
			if (grid.rowEditorPlugin) {
				grid.rowEditorPlugin.stopEditing();
			}

			this.runTest(records.getRange(0, records.getCount()), indices);
		}
	},

	/**
	 * Runs one test after another until the identifiers array is empty
	 *
	 * @param {Array} records
	 * @param {Array} indices
	 * @param {int} index
	 * @return {void}
	 */
	runTest: function(records, indices, index) {
		if (!records.length) {
			this.grid.unmask();
			return;
		}

		this.closeExpandedRows();
		this.grid.getView().focusRow((Ext.isArray(indices) ? indices.shift() : index) || 0);

		var record = records.shift();
		record.isRunningTest = true;
		record.commit();

		this.dataProvider.runTest(record.get('__identity'), function(result) {
			this.evaluateTest(result, record, records, indices, index);
		}, this);
	},

	/**
	 * Evaluates an ext.direct call to runTest
	 *
	 * Note: This method redirects to runTest after the evaluation to start the next test.
	 *
	 * @private
	 * @param {Object} result
	 * @param {Ext.data.Record} record
	 * @param {Array} records
	 * @param {Array} indices
	 * @param {int} index
	 * @return {void}
	 */
	evaluateTest: function(result, record, records, indices, index) {
		record.data = Ext.apply(record.data, result.data || {});
		record.isRunningTest = false;
		record.commit();

		if (Ext.isFunction(this.gridStore.afterUpdate)) {
			this.gridStore.afterUpdate([record], {}, {});
		}

		this.runTest(records, indices, ++index);
	},

	/**
	 * Returns a row expander instance
	 *
	 * @private
	 * @param {Object} configuration
	 * @return {Ext.ux.grid.RowExpander}
	 */
	getRowExpander: function(configuration) {
		if (this.rowExpander === null) {
			this.rowExpander = new Ext.ux.grid.RowExpander(Ext.apply({
				expandOnDblClick: false
			}, configuration));

			this.rowExpander.on('expand', function(rowExpander, record, body, rowIndex) {
				this.expandedRows.push(rowIndex);
			}, this);
		}

		return this.rowExpander;
	},

	/**
	 * Closes all expanded rows
	 *
	 * @return {void}
	 */
	closeExpandedRows: function() {
		Ext.each(this.expandedRows, function(expandedRow) {
			this.rowExpander.collapseRow(expandedRow);
		}, this);
		this.expandedRows = [];
	},

	/**
	 * Returns true if the given record has the same value inside the group
	 * field as the provided reference value. If we searching for records
	 * inside the test result group, we use a dedicated logic.
	 *
	 * @param {Ext.data.Record} record
	 * @param {int} id
	 * @param {String} groupField
	 * @param {String} groupValue
	 * @return {boolean}
	 */
	getRecordsByGroupValue: function(record, id, groupField, groupValue) {
		var compareValue = record.get(groupField);

		var hasCompareValueErrorSeverity = (compareValue == this.severityMap.ERROR
			|| compareValue == this.severityMap.EXCEPTION);

		var hasGroupValueErrorSeverity = (groupValue == this.severityMap.ERROR
			|| groupValue == this.severityMap.EXCEPTION);

		return (groupField === 'testResult' && hasCompareValueErrorSeverity && hasGroupValueErrorSeverity)
			|| (String(compareValue) === String(groupValue));
	},

	/**
	 * Returns the current grid row class depending on the record status
	 *
	 * @param {Ext.data.Record} record
	 * @return {String}
	 */
	fetchRowClass: function(record) {
		var rowClass = '';
		var testResult = record.get('testResult');

		if (record.isRunningTest) {
			rowClass = 'tx_dftools-grid-row-test-running';
		} else if (testResult == this.severityMap.IGNORE) {
			rowClass = 'tx_dftools-grid-row-test-ignore';
		} else if (testResult == this.severityMap.OK) {
			rowClass = 'tx_dftools-grid-row-test-success';
		} else if (testResult == this.severityMap.INFO) {
			rowClass = 'tx_dftools-grid-row-test-info';
		} else if (testResult == this.severityMap.WARNING) {
			rowClass = 'tx_dftools-grid-row-test-warning';
		} else if (testResult == this.severityMap.EXCEPTION || testResult == this.severityMap.ERROR) {
			rowClass = 'tx_dftools-grid-row-test-error';
		} else {
			rowClass = 'tx_dftools-grid-row-untested';
		}

		return rowClass;
	},

	/**
	 * Renders the text of the test result
	 *
	 * @param {String} testResult
	 * @return {String}
	 */
	renderTestResult: function(testResult) {
		if (testResult == this.severityMap.IGNORE) {
			testResult = TYPO3.lang['tx_dftools_common.testResult.ignore'];
		} else if (testResult == this.severityMap.OK) {
			testResult = TYPO3.lang['tx_dftools_common.testResult.success'];
		} else if (testResult == this.severityMap.INFO) {
			testResult = TYPO3.lang['tx_dftools_common.testResult.information'];
		} else if (testResult == this.severityMap.WARNING) {
			testResult = TYPO3.lang['tx_dftools_common.testResult.warning'];
		} else if (testResult == this.severityMap.EXCEPTION || testResult == this.severityMap.ERROR) {
			testResult = TYPO3.lang['tx_dftools_common.testResult.error'];
		} else {
			testResult = TYPO3.lang['tx_dftools_common.testResult.noTestResult'];
		}

		return testResult;
	},

	/**
	 * Returns the icon class for the current test mode
	 *
	 * @private
	 * @param {String} value
	 * @param {Object} meta
	 * @param {Ext.data.Record} record
	 * @return {String}
	 */
	observeTestState: function(value, meta, record) {
		var iconClass = '';
		var testResult = record.get('testResult');

		if (record.isRunningTest) {
			iconClass = 'tx_dftools-loadingIndicator loading-indicator';
		} else if (testResult == this.app.severityMap.OK) {
			iconClass = 'tx_dftools-statusOk ' + TYPO3.settings.DfTools.Sprites.ok;
		} else if (testResult == this.app.severityMap.INFO) {
			iconClass = 'tx_dftools-statusInformation ' + TYPO3.settings.DfTools.Sprites.ok;
		} else if (testResult == this.app.severityMap.WARNING) {
			iconClass = 'tx_dftools-statusWarning ' + TYPO3.settings.DfTools.Sprites.warning;
		} else if (testResult == this.app.severityMap.EXCEPTION || testResult == this.app.severityMap.ERROR) {
			iconClass = 'tx_dftools-statusError ' + TYPO3.settings.DfTools.Sprites.error;
		}

		this.items[0].tooltip = record.get('testMessage');
		return iconClass;
	},

	/**
	 * Just redirects to the identically called grid method with
	 * the correct scope.
	 *
	 * @return {String}
	 */
	setValueAsCellToolTipRenderer: function() {
		return this.grid.setValueAsCellToolTipRenderer.apply(this.grid, arguments);
	}
});

Ext.onReady(function() {
	Ext.QuickTips.init();
	Ext.apply(Ext.QuickTips.getQuickTip(), {
		maxWidth: 500
	});
});