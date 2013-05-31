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
 * Back link test model
 *
 * @class TYPO3.DfTools.BackLinkTest.Model
 * @namespace TYPO3.DfTools.BackLinkTest
 * @extends Ext.data.Record
 */
TYPO3.DfTools.BackLinkTest.Model = Ext.data.Record.create([{
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
		name: 'testResult',
		type: 'int'
	}, {
		name: 'testMessage',
		type: 'string'
	}, {
		name: 'comment',
		type: 'string'
	}, {
		name: '__trustedProperties'
	}
]);

/**
 * Back Link Test Store
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @namespace TYPO3.DfTools.BackLinkTest
 * @class TYPO3.DfTools.BackLinkTest.Store
 * @extends TYPO3.DfTools.GroupingStore
 */
TYPO3.DfTools.BackLinkTest.Store = Ext.extend(TYPO3.DfTools.GroupingStore, {
	/**
	 * @cfg {Ext.data.Record}
	 */
	model: TYPO3.DfTools.BackLinkTest.Model,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 * @return {void}
	 */
	constructor: function(configuration) {
		configuration = Ext.apply({
			storeId: 'DfToolsBackLinkTestStore',
			autoLoad: false,
			groupField: 'testResult',
			groupDir: 'DESC',
			proxy: new Ext.data.DirectProxy({
				api: {
					read: TYPO3.DfTools.BackLinkTest.DataProvider.read,
					update: TYPO3.DfTools.BackLinkTest.DataProvider.update,
					create: TYPO3.DfTools.BackLinkTest.DataProvider.create,
					destroy: TYPO3.DfTools.BackLinkTest.DataProvider.destroy
				}
			})
		}, configuration);

		TYPO3.DfTools.BackLinkTest.Store.superclass.constructor.call(this, configuration);
	}
});