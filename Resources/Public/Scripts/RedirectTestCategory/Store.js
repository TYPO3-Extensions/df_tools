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

Ext.ns('TYPO3.DfTools.RedirectTestCategory');

/**
 * Category model
 *
 * @class TYPO3.DfTools.RedirectTestCategory.Model
 * @namespace TYPO3.DfTools.RedirectTestCategory
 * @extends Ext.data.Record
 */
TYPO3.DfTools.RedirectTestCategory.Model = Ext.data.Record.create([{
		name: '__identity',
		type: 'int'
	}, {
		name: 'category',
		allowBlank: false
	}, {
		name: '__hmac'
	}
]);

/**
 * Category Store that is coupled with the Redirect Test Store
 *
 * @requires TYPO3.DfTools.RedirectTest.Store
 * @author Stefan Galinski <sgalinski@df.eu>
 * @namespace TYPO3.DfTools.RedirectTestCategory
 * @class TYPO3.DfTools.RedirectTestCategory.Store
 * @extends TYPO3.DfTools.DirectStore
 */
TYPO3.DfTools.RedirectTestCategory.Store = Ext.extend(TYPO3.DfTools.DirectStore, {
	/**
	 * @cfg {Ext.data.Record}
	 */
	model: TYPO3.DfTools.RedirectTestCategory.Model,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			storeId: 'DfToolsRedirectTestCategoryStore',
			autoLoad: false,
			api: {
				read: TYPO3.DfTools.RedirectTestCategory.DataProvider.read,
				update: TYPO3.DfTools.RedirectTestCategory.DataProvider.update
			}
		}, configuration);

		TYPO3.DfTools.RedirectTestCategory.Store.superclass.constructor.call(this, configuration);
	},

	/**
	 * Called after a failed update event
	 *
	 * Resets the category records to the original values!
	 *
	 * @private
	 * @param {Object} records
	 * @return {void}
	 */
	afterUpdateException: function(records) {
		Ext.each(records, function(record) {
			record.data.category = record.json.category;
		});
	},

	/**
	 * Called after a successful update event
	 *
	 * Updates the grid and it's related store about the record category
	 * naming changes.
	 *
	 * @private
	 * @param {Object} records
	 * @return {void}
	 */
	afterUpdate: function(records) {
		var store = Ext.StoreMgr.lookup('DfToolsRedirectTestStore');
		Ext.each(records, function(record) {
			var oldCategory = record.json.category;
			var newCategory = record.data.category;
			record.json.category = newCategory;

			store.each(function(gridRecord) {
				if (gridRecord.get('categoryId') === oldCategory) {
					gridRecord.data.categoryId = newCategory;
				}
			});
		});

		store.groupBy(store.groupField, true);
	}
});