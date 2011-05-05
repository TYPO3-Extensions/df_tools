Ext.ns('Ext.ux.grid');

/**
 * @class Ext.ux.grid.GroupActions
 * @extends Ext.util.Observable
 *
 * Adds the possibility to define group actions
 *
 * @author Ing. Jozef Sakáloš
 * @author Stefan Galinski <sgalinski@df.eu>
 *
 * @license Ext.ux.grid.GroupActions is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * <p>License details: <a href="http://www.gnu.org/licenses/lgpl.html"
 * target="_blank">http://www.gnu.org/licenses/lgpl.html</a></p>
 */
Ext.ux.grid.GroupActions = Ext.extend(Ext.util.Observable, {
	/**
	 * Template for group actions
	 *
	 * @private
	 * @cfg {String} tplGroup
	 */
	tplGroup: '<tpl for="actions">' +
		'<div class="ux-grow-action-item<tpl if="\'right\'===align"> ux-action-right</tpl> ' +
		'{cls}" style="{hide}{style}" qtip="{qtip}">{text}</div>' +
		'</tpl>',

	/**
	 * How to hide hidden icons. Valid values are: 'visibility' and 'display'
	 * (defaults to 'visibility'). If the mode is visibility the hidden icon is not visible but there
	 * is still blank space occupied by the icon. In display mode, the visible icons are shifted taking
	 * the space of the hidden icon.
	 *
	 * @cfg {String} hideMode
	 */
	hideMode:'visibility',

	/**
	 * Group Actions
	 *
	 * Properties:
	 * - iconCls
	 * - qtip
	 * - visibleForGroups (e.g. ['categoryId'])
	 * - callback
	 * - align (left|right)
	 * - style
	 * - hide
	 * - cls
	 * - scope
	 *
	 * @cfg {Object}
	 */
	groupActions: null,

	/**
	 * Constructor
	 *
	 * @param {Object} configuration
	 */
	constructor: function(configuration) {
		Ext.apply(this, configuration);
		Ext.ux.grid.GroupActions.superclass.constructor.call(this, configuration);
	},

	/**
	 * Initializes the plugin
	 *
	 * @param {Ext.grid.GridPanel} grid
	 */
	init: function(grid) {
		this.grid = grid;
		var view = grid.getView();

		var originalGroupTemplate = view.groupTextTpl;
		var defineGroupTextTemplate = function() {
			view.groupTextTpl = '<div class="ux-grow-action-text">' + originalGroupTemplate + '</div>'
				+ this.processActions(this.groupActions, this.tplGroup).apply();
		};

		this.grid.on('groupmousedown', function(grid, groupField, groupId, event) {
			if (event.getTarget('.ux-grow-action-item')) {
				event.button = 'STOP!';
				this.onClick(event, event.target);
			}
		}, this);

		this.grid.on('groupchange', function(grid, groupField) {
			var refreshGroupTemplate = false;
			Ext.each(this.groupActions, function(action) {
				if (!Ext.isArray(action.visibleForGroups)) {
					return;
				}

				var inIndex = (action.visibleForGroups.indexOf(groupField) >= 0);
				if (inIndex && action.hide) {
					action.hide = false;
					refreshGroupTemplate = true;
				} else if (!inIndex && !action.hide) {
					action.hide = true;
					refreshGroupTemplate = true;
				}
			});

			if (refreshGroupTemplate) {
				defineGroupTextTemplate.call(this);
				view.startGroup = null;
				view.initTemplates();
			}
		}, this);

		defineGroupTextTemplate.call(this);
	},

	/**
	 * Processes actions configs and returns template.
	 *
	 * @private
	 * @param {Array} actions
	 * @param {String} template
	 * @return {String}
	 */
	processActions:function(actions, template) {
		var definedActions = [];
		Ext.each(actions, function(action) {
			if (action.iconCls && Ext.isFunction(action.callback)) {
				this.callbacks = this.callbacks || {};
				this.scopes = this.scopes || {};

				this.callbacks[action.iconCls] = action.callback;
				this.scopes[action.iconCls] = action.scope || this;
			}

			var output = {
				align: action.align || 'right',
				cls: (action.iconCls ? action.iconCls : ''),
				qtip: (action.tooltip || action.qtip ? action.tooltip || action.qtip : ''),
				text: (action.text ? action.text : ''),
				hide: (action.hide ? ('display' === this.hideMode ? 'display: none' : 'visibility: hidden;') : ''),
				style: (action.style ? action.style : '')
			};
			definedActions.push(output);

		}, this);

		var xt = new Ext.XTemplate(template);
		return new Ext.XTemplate(xt.apply({
			actions: definedActions
		}));
	},

	/**
	 * Grid body actionEvent event handler
	 *
	 * @private
	 * @param {Ext.EventObject} e
	 * @param {String} target
	 * @return {void}
	 */
	onClick:function(e, target) {
		var eventTarget = e.getTarget('.ux-grow-action-item');
		var groupId = this.grid.getView().findGroup(target);

		var groupField = '';
		var groupValue = '';
		if (groupId) {
			var strippedGroup = groupId ? groupId.id.replace(/ext-gen[0-9]+-gp-/, '') : null;
			var groupParts = strippedGroup.split('-');
			groupField = groupParts.shift();
			groupValue = groupParts.join('-');
		}

		var action = eventTarget.className.replace(/ux-grow-action-item (ux-action-right )*/, '');
		if (Ext.isFunction(this.callbacks[action])) {
			this.callbacks[action].call(this.scopes[action], this.grid, groupId, groupField, groupValue);
		}
	}
});

Ext.reg('groupactions', Ext.ux.grid.GroupActions);