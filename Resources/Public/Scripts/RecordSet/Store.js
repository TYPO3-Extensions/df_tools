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

Ext.ns('TYPO3.DfTools.RecordSet');

/**
 * Link Check test model
 *
 * @class TYPO3.DfTools.RecordSet.Model
 * @namespace TYPO3.DfTools.RecordSet
 * @extends Ext.data.Record
 */
TYPO3.DfTools.RecordSet.Model = Ext.data.Record.create([{
		name: '__identity',
		type: 'int',
		allowBlank: false
	}, {
		name: 'tableName',
		type: 'string',
		allowBlank: false
	}, {
		name: 'humanReadableTableName',
		type: 'string',
		allowBlank: false
	}, {
		name: 'field',
		type: 'string',
		allowBlank: false
	}, {
		name: 'identifier',
		type: 'int'
	}, {
		name: '__trustedProperties'
	}
]);

/**
 * Record set store
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @namespace TYPO3.DfTools.RecordSet
 * @class TYPO3.DfTools.RecordSet.Store
 * @extends TYPO3.DfTools.DirectStore
 */
TYPO3.DfTools.RecordSet.Store = Ext.extend(TYPO3.DfTools.DirectStore, {
	/**
	 * @cfg {Ext.data.Record}
	 */
	model: TYPO3.DfTools.RecordSet.Model,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			storeId: 'DfToolsRecordSetStore',
			autoLoad: true,
			directFn: TYPO3.DfTools.RecordSet.DataProvider.read
		}, configuration);

		TYPO3.DfTools.RecordSet.Store.superclass.constructor.call(this, configuration);
	}
});