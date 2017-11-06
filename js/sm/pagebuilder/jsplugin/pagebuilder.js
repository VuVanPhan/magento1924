/**
 * @package SM Page Builder
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright Copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.magentech.com
 */
"use strict";
// var $ = jQuery.noConflict();
var PageBuilder = PageBuilder || {};
PageBuilder = Class.create();
PageBuilder.prototype = {
	form: null,
	container: null,
	indexRow: 0,
	indexCol: 0,
	params: {},
	settings: {},
	settingsParams: "custom_css|custom_js|enable_wrapper|select_wrapper|wrapper_class|template_settings".split("|"),
	configParams: "custom_css|custom_js|enable_wrapper|select_wrapper|wrapper_class|template_settings".split("|"),
	/*
	 General editing classes---------------
	 */
	// Standard edit class, applied to active elements
	pdmEditClass: "pdm-editing",

	// Tool bar class which are inserted dynamically
	pdmToolClass: "pdm-tools",

	// Clearing class, used on most toolbars
	pdmClearClass: "clearfix",

	// Id row widget
	rowId: "pb-layer-",

	// Buttons at the top of each row
	rowButtonsPrepend: [
		{
			title:"Move",
			element: "a",
			btnClass: "pdm-moveRow pull-left",
			iconClass: "fa fa-arrows "
		},
		{
			title:"New Row",
			element: "a",
			btnClass: "pdm-addRow pull-left",
			iconClass: "fa fa-bars"
		},
		{
			title:"New Column",
			element: "a",
			btnClass: "pdm-addColumn pull-left",
			iconClass: "fa fa-plus"
		},
		{
			title:"Remove row",
			element: "a",
			btnClass: "pull-right pdm-removeRow",
			iconClass: "fa fa-trash-o"
		},
		{
			title:"Duplicate",
			element: "a",
			btnClass: "pull-right pdm-duplicate",
			iconClass: "fa fa-files-o"
		},
		{
			title:"Row Settings",
			element: "a",
			btnClass: "pull-right pdm-rowSettings",
			iconClass: "fa fa-cog"
		},
		{
			title:"Edit Row Short Code",
			element: "a",
			btnClass: "pull-right pdm-editRowShortcode",
			iconClass: "fa fa-pencil-square-o"
		}
	],
	/*
	 Rows---------------
	 */
	// Generic row class. change to row--fluid for fluid width in Bootstrap
	rowClass:    "row",

	/*
	 Columns--------------
	 */
	// Column Class
	colClass: "column",

	// Generic desktop size layout class
	colDesktopClass: "col-lg-",

	// Generic desktop size layout class
	colLaptopClass: "col-md-",

	// Generic tablet size layout class
	colTabletClass: "col-sm-",

	// Generic phone size layout class
	colPhoneClass: "col-xs-",

	// Additional column class to add (foundation needs columns, bs3 doesn't)
	colAdditionalClass: "",

	// Id cols widget
	colId : 'pb-col-',

	// Buttons to prepend to each column
	colButtonsPrepend: [
		{
			title:"Column Settings",
			element: "a",
			btnClass: "left pdm-colSettings",
			iconClass: "fa fa-pencil-square-o"
		},
		{
			title:"Add Nested Row",
			element: "a",
			btnClass: "left pdm-addRow",
			iconClass: "fa fa-plus-square"
		},
		{
			title:"Remove Column",
			element: "a",
			btnClass: "left pdm-removeCol",
			iconClass: "fa fa-trash-o"
		},
		{
			title:"Add Widget",
			element: "a",
			btnClass: "right pdm-addWidget",
			iconClass: "fa fa-cog"
		}
	],
	initialize: function(a){
		this.form = a;
		this.collectContainer();
	},
	collectContainer: function() {
		this.container = $("mypagebuilder");
	},
	save: function(a){
		if (this.form && this.form.validate()) {
			var b = this.form.validator.form;
			var c = b.action;
			var d = b.serialize(true);

			this.settings['custom_css'] = d.custom_css;
			this.settings['custom_js'] = d.custom_js;
			this.settings['wrapper_page'] = d.wrapper_page;
			this.settings['select_wrapper'] = d.select_wrapper;
			this.settings['wrapper_class'] = d.wrapper_class;
			if (a)
				d['back'] = 'edit';

			d.settings = JSON.stringify(this.settings);
			d.params = JSON.stringify(this.params);
			console.log(d);
			new Ajax.Request(c, {
				method: "post",
				parameters: d,
				onSuccess: function(b) {
					// if (a) window.location.href = b.responseText;
					// else if (0 === b.responseText.indexOf("http://")) window.location.href = b.responseText;
				}
			});
		}
	},
	addLayer: function (a) {
		var b = this.container.getDimensions();
		if (!b.width && !b.height) {
			setTimeout(function() {
				this.addLayer(a);
			}.bind(this), 500);
			return;
		}
		a.order = this.indexRow + 1;
		a.serial = this.indexRow + 1;
		this.params[a.serial] = a;
		var d = this.renderLayerHtml(a);
		$('pdm-canvas').insertBefore(d, $('add-row-first'));
		if (a.col)
			this.addColumn(a.serial, a.col);
		this.indexRow++;
	},
	addColumn: function (b, e) {
		e.parent = b;
		e.order = this.indexCol+1;
		e.serial = this.indexCol+1;
		e.size = 2;
		this.params[e.parent]['col'] = e;
		var f = this.renderColumnHtml(e),
			id = this.rowId.concat(b),
			pdmTools = jQuery('.pdm-tools', jQuery('#'+id+''));
		pdmTools.after(f);
		this.indexCol++;
	},
	renderLayerHtml: function (a) {
		var b = new Element("div", {
			id: this.rowId+a.serial,
			class: this.rowClass+ ' '+this.pdmEditClass+ ' ui-sortable',
			row: a.serial
		});

		b.insert(this.toolFactory(this.rowButtonsPrepend));
		return b;
	},
	renderColumnHtml: function (e) {
		console.log(e);
		var b = new Element("div", {
			id: this.colId+e.serial,
			class: this.colClass+ ' ' + this.colDesktopClass+e.size + ' ' + this.colLaptopClass+e.size + ' ' +
			this.colTabletClass+e.size + ' ' + this.colPhoneClass+e.size + ' ' + this.pdmEditClass + ' ' + this.colAdditionalClass
		});

		b.insert(this.toolFactory(this.colButtonsPrepend));
		return b;
	},
	/**
	 * Returns an editing div with appropriate btns as passed in
	 * @method toolFactory
	 * @param {array} btns - Array of buttons (see options)
	 * @return MemberExpression
	 */
	toolFactory: function(btns){
		var c = new Element("div", {
			class: this.pdmToolClass+ ' ' +this.pdmClearClass
		});
		var tools = c.insert(this.buttonFactory(btns));
		return tools;
	},
	/**
	 * Returns html string of buttons
	 * @method buttonFactory
	 * @param {array} btns - Array of button configurations (see options)
	 * @return CallExpression
	 */
	buttonFactory: function(btns){
		var buttons=[];
		jQuery.each(btns, function(i, val){
			val.btnLabel = (typeof val.btnLabel === 'undefined')? '' : val.btnLabel;
			val.title = (typeof val.title === 'undefined')? '' : val.title;
			buttons.push("<" + val.element +" title='" + val.title + "' class='" + val.btnClass + "'><span class='"+val.iconClass+"'></span>&nbsp;" + val.btnLabel + "</" + val.element + "> ");
		});
		return buttons.join("");
	}
};