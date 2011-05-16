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

/** @namespace TYPO3.DfTools.RedirectTestCategory.DataProvider */
/** @namespace TYPO3.DfTools.RedirectTestCategory.DataProvider */

Ext.ns('TYPO3.DfTools.RedirectTest');

/**
 * Redirect test model
 *
 * @class TYPO3.DfTools.RedirectTest.Model
 * @namespace TYPO3.DfTools.RedirectTest
 * @extends Ext.data.Record
 */
TYPO3.DfTools.RedirectTest.Model = Ext.data.Record.create([{
		name: '__identity',
		type: 'int',
		allowBlank: false
	}, {
		name: 'testUrl',
		type: 'string',
		allowBlank: false
	}, {
		name: 'expectedUrl',
		type: 'string',
		allowBlank: false
	}, {
		name: 'httpStatusCode',
		type: 'int'
	}, {
		name: 'testResult',
		type: 'int'
	}, {
		name: 'testMessage',
		type: 'string'
	}, {
		name: 'categoryId',
		allowBlank: false,
		type: 'string',
		sortType: 'asUCString',
		convert: function(categoryId) {
			var categoryStore = Ext.StoreMgr.lookup('DfToolsRedirectTestCategoryStore');

			var value = '';
			var category = categoryStore.getById(categoryId);
			if (category) {
				value = category.get('category');
			} else if (categoryStore.lastAddedCategory) {
				value = categoryStore.lastAddedCategory;
			} else {
				value = categoryId;
			}

			return value;
		}
	}, {
		name: '__hmac'
	}
]);

/**
 * Redirect Test Store that is coupled with the category store
 *
 * @requires TYPO3.DfTools.RedirectTestCategory.Store
 * @author Stefan Galinski <sgalinski@df.eu>
 * @namespace TYPO3.DfTools.RedirectTest
 * @class TYPO3.DfTools.RedirectTest.Store
 * @extends TYPO3.DfTools.GroupingStore
 */
TYPO3.DfTools.RedirectTest.Store = Ext.extend(TYPO3.DfTools.GroupingStore, {
	/**
	 * @cfg {Ext.data.Record}
	 */
	model: TYPO3.DfTools.RedirectTest.Model,

	/**
	 * The last added category name
	 *
	 * @type {String}
	 */
	lastAddedCategory: null,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			storeId: 'DfToolsRedirectTestStore',
			autoLoad: false,
			groupField: 'categoryId',
			multiSortInfo: {
				sorters: [{
					field: 'categoryId',
					direction: 'ASC'
				}, {
					field: 'testResult',
					direction: 'DESC'
				}]
			},
			proxy: new Ext.data.DirectProxy({
				api: {
					read: TYPO3.DfTools.RedirectTest.DataProvider.read,
					update: TYPO3.DfTools.RedirectTest.DataProvider.update,
					create: TYPO3.DfTools.RedirectTest.DataProvider.create,
					destroy: TYPO3.DfTools.RedirectTest.DataProvider.destroy
				}
			})
		}, configuration);

		TYPO3.DfTools.RedirectTest.Store.superclass.constructor.call(this, configuration);
		this.on('beforewrite', this.rememberLastAddedCategoryBeforeWrite, this);
	},

	/**
	 * Called after a successful update event
	 *
	 * Recalculate the category field and regroup after writing the data
	 * it's important that this is done after writing, because new categories
	 * are loaded from the server from this function
	 *
	 * @private
	 * @param {Object} records
	 * @return {void}
	 */
	afterUpdate: function(records) {
		var storeNeedsReload = false;
		var categoryStore = Ext.StoreMgr.lookup('DfToolsRedirectTestCategoryStore');
		Ext.each(records, function(record) {
			var category = record.get('categoryId');
			var storeRecord = categoryStore.getById(category);
			if (!storeRecord) {
				storeNeedsReload = true;
				record.data.categoryId = category;
			} else {
				record.data.categoryId = storeRecord.get('category');
			}
		});

		if (storeNeedsReload) {
			categoryStore.load({
				scope: this,
				callback: function() {
					this.groupBy(this.groupField, true);
				}
			});
			categoryStore.lastAddedCategory = null;
		}
	},

	/**
	 * Validates the category data to add the identifiers to the data before writing. If it's
	 * a new category it will added to an internal property "lastAddedCategory" to the category
	 * store for further use. It's unset in the write event!
	 *
	 * Note: Only the last unknown category in the records array is stored in the internal
	 * property. To prevent errors make sure that only single records are updated at once!
	 *
	 * @private
	 * @return {void}
	 */
	rememberLastAddedCategoryBeforeWrite: function(store, action, records) {
		var tempRecords = records;
		if (!Ext.isArray(records)) {
			tempRecords = [records];
		}

		var categoryStore = Ext.StoreMgr.lookup('DfToolsRedirectTestCategoryStore');
		Ext.each(tempRecords, function(record) {
			var category = record.get('categoryId');
			var index = categoryStore.findExact('category', category);
			if (index >= 0) {
				record.data.categoryId = categoryStore.getAt(index).get('__identity');
			} else {
				categoryStore.lastAddedCategory = record.get('categoryId');
			}

		});
	}
});