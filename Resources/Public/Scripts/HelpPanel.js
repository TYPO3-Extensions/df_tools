Ext.onReady(function() {
	(new Ext.Panel({
		title: TYPO3.lang['tx_dftools_common.help'],
		frame: true,
		width: 720,
		renderTo: 'tx_dftools-help',
		html: document.getElementById('tx_dftools-helpContent').innerHTML,
		collapsible: true,
		collapsed: true,
		titleCollapse: true
	}));
});