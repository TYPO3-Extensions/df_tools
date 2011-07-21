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
 * The shared extension object used for the concrete store implementations
 *
 * @namespace TYPO3.DfTools
 * @author Stefan Galinski <sgalinski@df.eu>
 */
TYPO3.DfTools.StoreExtension = {
	/**
	 * @cfg {Ext.data.Record}
	 */
	model: null,

	/**
	 * Called after a successful update event
	 *
	 * @param {Object} records
	 * @param {Object} result
	 * @param {Object} transaction
	 * @return {void}
	 */
	afterUpdate: Ext.emptyFn,

	/**
	 * Called after a successful create event
	 *
	 * @param {Object} records
	 * @param {Object} result
	 * @param {Object} transaction
	 * @return {void}
	 */
	afterCreate: Ext.emptyFn,

	/**
	 * Called after a successful destroy event
	 *
	 * @param {Object} records
	 * @param {Object} result
	 * @param {Object} transaction
	 * @return {void}
	 */
	afterDestroy: Ext.emptyFn,

	/**
	 * Called after a failed update event
	 *
	 * @param {Object} records
	 * @param {Object} response
	 * @param {Object} misc
	 * @return {void}
	 */
	afterUpdateException: Ext.emptyFn,

	/**
	 * Called after a failed create event
	 *
	 * @param {Object} records
	 * @param {Object} response
	 * @param {Object} misc
	 * @return {void}
	 */
	afterCreateException: Ext.emptyFn,

	/**
	 * Called after a failed destroy event
	 *
	 * @param {Object} records
	 * @param {Object} response
	 * @param {Object} misc
	 * @return {void}
	 */
	afterDestroyException: Ext.emptyFn,

	/**
	 * Initialization of the events
	 *
	 * @private
	 * @return {void}
	 */
	initEvents: function() {
		this.on('write', this.onWrite, this);
		this.on('exception', this.onException, this);
		this.on('beforewrite', this.addHmacToRecords, this);
	},

	/**
	 * Initializes the writer
	 *
	 * @private
	 * @return {Ext.data.JsonWriter}
	 */
	getWriter: function() {
		return new Ext.data.JsonWriter({
			encode: false,
			writeAllFields: true
		});
	},

	/**
	 * Initializes the reader
	 *
	 * @private
	 * @return {Ext.data.JsonReader}
	 */
	getReader: function() {
		return new Ext.data.JsonReader({
			idProperty: '__identity',
			totalProperty: 'total',
			root: 'records',
			fields: this.model
		});
	},

	/**
	 * Overridden ExtJS method to fix a nasty bug.
	 *
	 * The records object is always empty in the write event if there are multiple
	 * entries without the fix. Check the TYPO3 modification marks inside the code.
	 *
	 * @private
	 * @param {String} action
	 * @param {Object} rs
	 * @param {Object} batch
	 * @return {Function}
	 */
	createCallback : function(action, rs, batch) {
		return (action === 'read') ? this.loadRecords : function(data, response, success) {

			// TYPO3 MODIFICATION STARTS

				// copy the record store to be able to access the records later on
				// Note: The containing records are still the same with the applied changes!
			var copiedRecords = rs;
			if (success === true) {
				if (Ext.isArray(rs)) {
					copiedRecords = [];
					Ext.each(rs, function(record, index) {
						copiedRecords[index] = record;
					});
				}
			}
			// TYPO3 MODIFICATION ENDS

				// calls: onCreateRecords | onUpdateRecords | onDestroyRecords
			this['on' + Ext.util.Format.capitalize(action) + 'Records'](success, rs, [].concat(data));

				// If success === false here, exception will have been called in DataProxy
			if (success === true) {

				// TYPO3 MODIFICATION STARTS
				this.fireEvent('write', this, action, data, response, copiedRecords);
				// TYPO3 MODIFICATION ENDS

			}
			this.removeFromBatch(batch, action, data);
		};
	},

	/**
	 * Renders a flash message after a successful write event. You can overwrite the
	 * after(Update|Create|Destroy) methods to hook into this event.
	 *
	 * @private
	 * @param {Ext.data.Store} store
	 * @param {String} action
	 * @param {Object} result
	 * @param {Object} transaction
	 * @param {Object} records
	 * @return {void}
	 */
	onWrite: function(store, action, result, transaction, records) {
		var tempRecords = records;
		if (!Ext.isArray(records)) {
			tempRecords = [records];
		}

		var label = '';
		if (action === Ext.data.Api.actions.update) {
			label = TYPO3.lang['tx_dftools_common.updateSuccessful'];
			this.afterUpdate(tempRecords, result, transaction);

		} else if (action === Ext.data.Api.actions.create) {
			label = TYPO3.lang['tx_dftools_common.createSuccessful'];
			this.afterCreate(tempRecords, result, transaction);

		} else if (action === Ext.data.Api.actions.destroy) {
			label = TYPO3.lang['tx_dftools_common.deleteSuccessful'];
			this.afterDestroy(tempRecords, result, transaction);
		}

		if (label !== '') {
			TYPO3.Flashmessage.display(TYPO3.Severity.ok, TYPO3.lang['tx_dftools_common.info'], label);
		}
	},

	/**
	 * Renders a flash message if the given response contains a message. Override the
	 * after(Update|Create|Destroy)Exception methods to hook into this method.
	 *
	 * @private
	 * @param {Ext.data.DataProxy} proxy
	 * @param {String} mode
	 * @param {String} action
	 * @param {Object} misc
	 * @param {Object} response
	 * @param {Object} records
	 * @return {void}
	 */
	onException: function(proxy, mode, action, misc, response, records) {
		var header = TYPO3.lang['tx_dftools_common.exception'];
		TYPO3.Flashmessage.display(TYPO3.Severity.error, header, response.message || records);

		var tempRecords = records;
		if (!Ext.isArray(records)) {
			tempRecords = [records];
		}

		if (action === Ext.data.Api.actions.update) {
			this.afterUpdateException(tempRecords, response, misc);
		} else if (action === Ext.data.Api.actions.create) {
			this.afterCreateException(tempRecords, response, misc);
		} else if (action === Ext.data.Api.actions.destroy) {
			this.afterDestroyException(tempRecords, response, misc);
		}
	},

	/**
	 * Adds the __hmac to the records before sending them to the server side
	 *
	 * @private
	 * @param {Ext.data.Store} store
	 * @param {String} action
	 * @param {Object} records
	 * @return {Boolean}
	 */
	addHmacToRecords: function(store, action, records) {
		var hmac = store.reader.jsonData['__hmac'][action];
		if (!Ext.isEmpty(hmac)) {
			var tempRecords = records;
			if (!Ext.isArray(records)) {
				tempRecords = [records];
			}

			Ext.each(tempRecords, function(record) {
				record.data['__hmac'] = hmac;
			});
		}

		return true;
	}
};

/**
 * Enhanced group store with some additional features and fixed bugs.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @class TYPO3.DfTools.GroupingStore
 * @namespace TYPO3.DfTools
 * @extends Ext.data.GroupingStore
 */
TYPO3.DfTools.GroupingStore = Ext.extend(Ext.data.GroupingStore, Ext.apply(TYPO3.DfTools.StoreExtension, {
	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			writer: this.getWriter(),
			reader: this.getReader()
		}, configuration);

		TYPO3.DfTools.GroupingStore.superclass.constructor.call(this, configuration);

		this.on('datachanged', this.onDataChanged, this);
		this.initEvents();
	},

	/**
	 * Regroup the group column based on the sorting information's. The sorting direction
	 * direction is used and will be used for the grouping direction.
	 *
	 * @private
	 * @return {void}
	 */
	onDataChanged: function() {
		var sortState = this.getSortState();
		if (!Ext.isDefined(sortState) || this.remoteSort) {
			return;
		}

		if (sortState.field === this.groupField && sortState.direction !== this.groupDir) {
			this.groupDir = sortState.direction;
			this.groupBy(this.groupField, true);
		}
	}
}));

/**
 * Enhanced direct store with some additional features and fixed bugs.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @class TYPO3.DfTools.DirectStore
 * @namespace TYPO3.DfTools
 * @extends Ext.data.DirectStore
 */
TYPO3.DfTools.DirectStore = Ext.extend(Ext.data.DirectStore, Ext.apply(TYPO3.DfTools.StoreExtension, {
	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			writer: this.getWriter(),
			reader: this.getReader()
		}, configuration);

		TYPO3.DfTools.DirectStore.superclass.constructor.call(this, configuration);
		this.initEvents();
	}
}));