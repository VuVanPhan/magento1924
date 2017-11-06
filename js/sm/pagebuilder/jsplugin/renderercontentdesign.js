/**------------------------------------------------------------------------
 * SM Page Builder - Version 1.0.0
 * Copyright (c) 2015 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 -------------------------------------------------------------------------*/
(function($){
	/**
	 * Main pagedesignmanager function
	 * @method pagedesignmanager
	 * @returns pagedesignmanager
	 * @class pagedesignmanager
	 * @memberOf jQuery.fn
	 */
	$.pagedesignmanager = function(el, options){
		var pdm = this;
		pdm.$el = $(el);
		pdm.el = el;
		pdm.$el.data("pagedesignmanager", pdm);

		///**
		// * API
		// * @method appendHTMLSelectedCols
		// * @param {string} html - HTML to append to selected columns
		// * @returns null
		// */
		pdm.appendHTMLSelectedCols = function(html) {
			var canvas = pdm.$el.find("#" + pdm.options.canvasId);
			var cols = canvas.find(pdm.options.colSelector);
			$.each(cols, function(){
				if($(this).hasClass(pdm.options.pdmEditClassSelected)) {
					$('.'+pdm.options.pdmEditRegion, this).append(html);
				}
			});
		};

		/**
		 * INIT - Main initialising function to create the canvas, controls and initialise all click handlers
		 * @method init
		 * @returns null
		 */
		pdm.init = function(){
			pdm.options = $.extend({},$.pagedesignmanager.defaultOptions, options);
			pdm.log("INIT");
			//pdm.addCSS(pdm.options.cssInclude);
			pdm.rteControl("init");
			pdm.createCanvas();
			pdm.createControls();
			pdm.initControls();
			//pdm.initDefaultButtons();
			pdm.initCanvas();
			pdm.log("FINISHED");
		};

		/*------------------------------------------ Canvas & Controls ---------------------------------------*/


		/**
		 * Build and append the canvas, making sure existing HTML in the user's div is wrapped. Will also trigger Responsive classes to existing markup if specified
		 * @method createCanvas
		 * @returns null
		 */
		pdm.createCanvas = function(){
			pdm.log("+ Create Canvas");
			var html=pdm.$el.html();
			pdm.$el.html("");
			$('<div/>', {'id': pdm.options.canvasId, 'html':html }).appendTo(pdm.$el);
			// Add responsive classes after initial loading of HTML, otherwise we lose the rows
			if(pdm.options.addResponsiveClasses) {
				pdm.addResponsiveness(pdm.$el.find("#" + pdm.options.canvasId));
			}
			// Add default editable regions: we try and do this early on, as then we don't need to replicate logic to add regions
			if(pdm.options.autoEdit){
				pdm.initMarkup(
					pdm.$el.find("#" + pdm.options.canvasId)
						.find("."+pdm.options.colClass)
						.not("."+pdm.options.rowClass)
				);
			}

		};

		/**
		 * Add missing reponsive classes to existing HTML
		 * @method addResponsiveness
		 * @param {} html
		 * @returns CallExpression
		 */
		pdm.addResponsiveness = function(html) {
			if(html === '') { return; }
			var desktopRegex = pdm.options.colDesktopClass+'(\\d+)',
				laptopRegex = pdm.options.colLaptopClass+'(\\d+)',
				tabletRegex = pdm.options.colTabletClass+'(\\d+)',
				phoneRegex = pdm.options.colPhoneClass+'(\\d+)',
				desktopRegexObj = new RegExp(desktopRegex,'i'),
				laptopRegexObj = new RegExp(laptopRegex,'i'),
				tabletRegexObj = new RegExp(tabletRegex, 'i'),
				phoneRegexObj = new RegExp(phoneRegex, 'i');
			//new_html = '';
			return $(html).find(':regex(class,'+desktopRegex+'|'+laptopRegex+'|'+tabletRegex+'|'+phoneRegex+')').each(function(i, el) {
				var elClasses = $(this).attr('class'), colNum = 2;
				var hasDesktop = desktopRegexObj.test(elClasses),
					hasLaptop = laptopRegexObj.test(elClasses),
					hasPhone = phoneRegexObj.test(elClasses),
					hasTablet = tabletRegexObj.test(elClasses);

				colNum = (colNum = desktopRegexObj.exec(elClasses))? colNum[1] : ((colNum = laptopRegexObj.exec(elClasses))? colNum[1] : ((colNum = tabletRegexObj.exec(elClasses))? colNum[1] : phoneRegexObj.exec(elClasses)[1] ));

				if(!hasDesktop) {
					$(this).addClass(pdm.options.colDesktopClass+colNum);
				}

				if(!hasLaptop) {
					$(this).addClass(pdm.options.colLaptopClass+colNum);
				}
				if(!hasPhone) {
					$(this).addClass(pdm.options.colPhoneClass+colNum);
				}
				if(!hasTablet) {
					$(this).addClass(pdm.options.colTabletClass+colNum);
				}
				// Adds default column classes - probably shouldn't go here, but since we're doing an expensive search to add the responsive classes, it'll do for now.
				if(pdm.options.addDefaultColumnClass){
					if(!$(this).hasClass(pdm.options.colClass)){
						$(this).addClass(pdm.options.colClass);
					}
				}
			});
		};

		/**
		 * Looks for and wraps non pdm commented markup
		 * @method initMarkup
		 * @returns null
		 */
		pdm.initMarkup = function(cols){
			var cTagOpen = '<!--'+pdm.options.pdmEditRegion+'-->',
				cTagClose = '<!--\/'+pdm.options.pdmEditRegion+'-->';

			// Loop over each column
			$.each(cols, function(i, col){
				var hasGmComment = false,
					hasNested = $(col).children().hasClass(pdm.options.rowClass);

				// Search for comments within column contents
				// NB, at the moment this is just finding "any" comment for testing, but should search for <!--pdm-*
				$.each($(col).contents(), function(x, node){
					if($(node)[0].nodeType === 8){
						hasGmComment = true;
					}
				});

				// Apply default commenting markup
				if(!hasGmComment){
					if(hasNested){
						// Apply nested wrap
						$.each($(col).contents(), function(i, val){
							if($(val).hasClass(pdm.options.rowClass)){
								var prev=Array.prototype.reverse.call($(val).prevUntil("."+pdm.options.rowClass)),
									after=$(val).nextUntil("."+pdm.options.rowClass);

								if(!$(prev).hasClass(pdm.options.pdmEditRegion)){
									$(prev).first().before(cTagOpen).end()
										.last().after(cTagClose);
								}
								if(!$(after).hasClass(pdm.options.pdmEditRegion)){
									$(after).first().before(cTagOpen).end()
										.last().after(cTagClose);
								}
							}
						});

					}
					else {
						// Is there anything to wrap?
						if($(col).contents().length !== 0){
							// Apply default comment wrap
							$(col).html(cTagOpen+$(col).html()+cTagClose);
						}
					}
				}
			});
			pdm.log("initMarkup ran");
		};

		/**
		 * Build and prepend the control panel
		 * @method createControls
		 * @returns null
		 */
		pdm.createControls = function(){
			pdm.log("+ Create Controls");
			var buttons=[];
			//Dynamically generated row template buttons
			$.each(pdm.options.controlButtons, function(i, val){
				var _class = pdm.generateButtonClass(val);
				buttons.push("<a title='Add Row " + _class + "' class='" + pdm.options.controlButtonClass + " add" + _class + "'><span class='" + pdm.options.controlButtonSpanClass + "'></span> " + _class + "</a>");
				pdm.generateClickHandler(val);
			});

			/*
			 Generate the control bar markup
			 */
			pdm.$el.prepend(
				$('<div/>',
					{'id': pdm.options.controlId, 'class': pdm.options.pdmClearClass }
				).prepend(
					$('<div/>', {"class": pdm.options.rowClass}).html(
						//$('<div/>', {"class": pdm.options.colDesktopClass + pdm.options.colMax}).addClass(pdm.options.colAdditionalClass).html(
						//	$('<div/>', {'id': 'pdm-addnew'})
						//		.addClass(pdm.options.pdmBtnGroup)
						//		.addClass(pdm.options.pdmFloatLeft).html(
						//		buttons.join("")
						//	)
						//).append(pdm.options.controlAppend)
						$('<div/>', {"class": pdm.options.colDesktopClass + pdm.options.colMax}).append(pdm.options.controlAppend)
					)
				)
			);
		};

		/**
		 * Add click functionality to the buttons
		 * @method initControls
		 * @returns null
		 */
		pdm.initControls = function(){
			var canvas=pdm.$el.find("#" + pdm.options.canvasId);
			pdm.log("+ InitControls Running");

				// Switch editing mode
			pdm.$el.on("click", ".pdm-preview", function(){
				if(pdm.status){
					pdm.deinitCanvas();
					$(this).parent().find(".pdm-edit-mode").prop('disabled', true);
				} else {
					pdm.initCanvas();
					$(this).parent().find(".pdm-edit-mode").prop('disabled', false);
				}
				$(this).toggleClass(pdm.options.pdmDangerClass);

				// Switch Layout Mode
			}).on("click", ".pdm-layout-mode a", function() {
				pdm.switchLayoutMode($(this).data('width'));

				// Switch editing mode
			}).on("click", ".pdm-edit-mode", function(){
				if(pdm.mode === "visual"){
					pdm.deinitCanvas();
					canvas.html($('<textarea/>').attr("cols", 130).attr("rows", 25).val(canvas.html()));
					pdm.mode="html";
					$(this).parent().find(".pdm-preview, .pdm-layout-mode > button").prop('disabled', true);
				} else {
					var editedSource=canvas.find("textarea").val();
					canvas.html(editedSource);
					pdm.initCanvas();
					pdm.mode="visual";
					$(this).parent().find(".pdm-preview, .pdm-layout-mode > button").prop('disabled', false);
				}
				$(this).toggleClass(pdm.options.pdmDangerClass);

				// Turn editing grid on
			}).on("click", ".pdm-enable-grid", function(){
				var canvas=pdm.$el.find("#" + pdm.options.canvasId);
				canvas.addClass('grid-editor');

				// Turn editing grid on
			}).on("click", ".pdm-disable-grid", function(){
				var canvas=pdm.$el.find("#" + pdm.options.canvasId);
				canvas.removeClass('grid-editor');

				// Add new column to existing row
			}).on("click", "a.pdm-addColumn", function(){
				// $(this).parent().after(pdm.createCol(2));
				// pdm.switchLayoutMode(pdm.options.layoutDefaultMode);
				// pdm.reset();

				/**
				 * use js add column
				 * see pagebuilder.js
				 */
				var a = {};
				var parent = $(this).parent().parent()[0];
				var row = parent.getAttribute('row');
				PG.addColumn(row, a);

				// Add a nested row
			}).on("click", "a.pdm-addRow", function(){
				pdm.log("Adding nested row");
				// $(this).closest("." +pdm.options.pdmEditClass).append(
				// 	$("<div>").addClass(pdm.options.rowClass)
				// 		.html(pdm.createCol(6))
				// 		.append(pdm.createCol(6)));

				/**
				 * use js add layer
				 * see pagebuilder.js
				 */
				var b = {};
				PG.addLayer(b);

				// Edit Row Short Code
			}).on("click", "a.pdm-editRowShortcode", function(){
				pdm.log("Edit Row Short Code");
				alert('Edit Row Short Code');

				// Edit Row
			}).on("click", "a.pdm-rowSettings", function(){
				pdm.log("Edit Row");
				alert('Edit Row');

				// Duplicate Row
			}).on("click", "a.pdm-duplicate", function(){
				pdm.log("Duplicate Row");
				alert('Duplicate Row');

				// Adding Widget
			}).on("click", "a.pdm-addWidget", function(){
				pdm.log("Adding Widget");
				var url_video = pdm.options.addwidget;
				console.log(url_video);
				_PdmWidgetTools.openDialog(url_video);
				// _PdmMediabrowserUtility.openDialog(url_video, 'browserImagesWindow', null, null, 'SM Camera Slider Insert Files...', null);

				// Edit Column
			}).on("click", "a.pdm-colSettings", function(){
				pdm.log("Edit Column");
				alert('Edit Column');

				// Remove a col
			}).on("click", "a.pdm-removeCol", function(){
				$(this).closest("." +pdm.options.pdmEditClass).animate({opacity: 'hide', width: 'hide', height: 'hide'}, 400, function(){
					$(this).remove();
				});
				pdm.log("Column Removed");

				// Remove a row
			}).on("click", "a.pdm-removeRow", function(){
				pdm.log($(this).closest("." +pdm.options.colSelector));
				$(this).closest("." +pdm.options.pdmEditClass).animate({opacity: 'hide', height: 'hide'}, 400, function(){
					$(this).remove();
					// Check for multiple editable regions and merge?
				});
				pdm.log("Row Removed");

				// For all the above, prevent default.
			}).on("click", "a.pdm-removeRow, button.pdm-preview, a.pdm-addColumn", function(e){
				pdm.log("Clicked: "   + $.grep((this).className.split(" "), function(v){
					return v.indexOf('pdm-') === 0;
				}).join());
				e.preventDefault();
			});
		};

		/**
		 * Basically just turns [2,4,6] into 2-4-6
		 * @method generateButtonClass
		 * @param {array} arr - An array of widths
		 * @return string
		 */
		pdm.generateButtonClass=function(arr){
			var string="";
			$.each(arr, function( i , val ) {
				string=string + "-" + val;
			});
			return string;
		};

		/**
		 * click handlers for dynamic row template buttons
		 * @method generateClickHandler
		 * @param {array} colWidths - array of column widths, i.e [2,3,2]
		 * @returns null
		 */
		pdm.generateClickHandler= function(colWidths){
			var string="a.add" + pdm.generateButtonClass(colWidths);
			var canvas=pdm.$el.find("#" + pdm.options.canvasId);
			pdm.$el.on("click", string, function(e){
				pdm.log("Clicked " + string);
				canvas.prepend(pdm.createRow(colWidths));
				pdm.reset();
				e.preventDefault();

			});
		};

		/**
		 * Create a single row with appropriate editing tools & nested columns
		 * @method createRow
		 * @param {array} colWidths - array of css class integers, i.e [2,4,5]
		 * @returns row
		 */
		pdm.createRow = function(colWidths){
			var row= $("<div/>", {"class": pdm.options.rowClass + " " + pdm.options.pdmEditClass});
			//$.each(colWidths, function(i, val){
			//	row.append(pdm.createCol(val));
			//});
			pdm.log("++ Created Row");
			return row;
		};

		/**
		 * Turns canvas into pdm-editing mode - does most of the hard work here
		 * @method initCanvas
		 * @returns null
		 */
		pdm.initCanvas = function(){
			// cache canvas
			var canvas=pdm.$el.find("#" + pdm.options.canvasId);
			pdm.switchLayoutMode(pdm.options.layoutDefaultMode);
			var cols=canvas.find(pdm.options.colSelector);
			var rows=canvas.find(pdm.options.rowSelector);
			pdm.log("+ InitCanvas Running");
			// Show the template controls
			//pdm.$el.find("#pdm-addnew").show();
			// Sort Rows First
			pdm.activateRows(rows);
			// Now Columns
			pdm.activateCols(cols);
			// Run custom init callback filter
			pdm.runFilter(canvas, true);
			// Get cols & rows again after filter execution
			cols=canvas.find(pdm.options.colSelector);
			rows=canvas.find(pdm.options.rowSelector);
			// Make Rows sortable
			canvas.sortable({
				items: rows,
				axis: 'y',
				placeholder: pdm.options.rowSortingClass,
				handle: ".pdm-moveRow",
				forcePlaceholderSize: true,   opacity: 0.7,  revert: true,
				tolerance: "pointer",
				cursor: "move"
			});
			/*
			 Make columns sortable
			 This needs to be applied to each element, otherwise containment leaks
			 */
			$.each(rows, function(i, val){
				$(val).sortable({
					items: $(val).find(pdm.options.colSelector),
					axis: 'x',
					handle: ".pdm-moveCol",
					forcePlaceholderSize: true,   opacity: 0.7,  revert: true,
					tolerance: "pointer",
					containment: $(val),
					cursor: "move"
				});
			});
			/* Make rows sortable
			 cols.sortable({
			 items: pdm.options.rowSelector,
			 axis: 'y',
			 handle: ".pdm-moveRow",
			 forcePlaceholderSize: true,   opacity: 0.7,  revert: true,
			 tolerance: "pointer",
			 cursor: "move"
			 }); */
			pdm.status=true;
			pdm.mode="visual";
			pdm.initCustomControls();
			pdm.initGlobalCustomControls();
			pdm.initNewContentElem();
		};

		/**
		 * Switches the layout mode for Desktop, Tablets or Mobile Phones
		 * @method switchLayoutMode
		 * @param {} mode
		 * @returns null
		 */
		pdm.switchLayoutMode = function(mode) {
			var canvas=pdm.$el.find("#" + pdm.options.canvasId), temp_html = canvas.html(), regex1 = '', regex2 = '', uimode = '';
			var regex3 = '';
			// Reset previous changes
			temp_html = pdm.cleanSubstring(pdm.options.classRenameSuffix, temp_html, '');
			uimode = $('div.pdm-layout-mode > button > span');
			// Do replacements
			switch (mode) {
				case 992:
					regex1 = '(' + pdm.options.colDesktopClass  + '\\d+)';
					regex2 = '(' + pdm.options.colTabletClass + '\\d+)';
					regex3 = '(' + pdm.options.colPhoneClass + '\\d+)';
					pdm.options.currentClassMode = pdm.options.colLaptopClass;
					pdm.options.colSelector = pdm.options.colLaptopSelector;
					$(uimode).attr('class', 'fa fa-laptop').attr('title', 'Laptop');
					break;
				case 768:
					regex1 = '(' + pdm.options.colDesktopClass  + '\\d+)';
					regex2 = '(' + pdm.options.colLaptopClass + '\\d+)';
					regex3 = '(' + pdm.options.colPhoneClass + '\\d+)';
					pdm.options.currentClassMode = pdm.options.colTabletClass;
					pdm.options.colSelector = pdm.options.colTabletSelector;
					$(uimode).attr('class', 'fa fa-tablet').attr('title', 'Tablet');
					break;
				case 640:
					regex1 = '(' + pdm.options.colDesktopClass  + '\\d+)';
					regex2 = '(' + pdm.options.colLaptopClass + '\\d+)';
					regex3 = '(' + pdm.options.colTabletClass + '\\d+)';
					pdm.options.currentClassMode = pdm.options.colPhoneClass;
					pdm.options.colSelector = pdm.options.colPhoneSelector;
					$(uimode).attr('class', 'fa fa-mobile-phone').attr('title', 'Phone');
					break;
				default:
					regex1 = '(' + pdm.options.colPhoneClass  + '\\d+)';
					regex2 = '(' + pdm.options.colTabletClass + '\\d+)';
					regex3 = '(' + pdm.options.colLaptopClass + '\\d+)';
					pdm.options.currentClassMode = pdm.options.colDesktopClass;
					pdm.options.colSelector = pdm.options.colDesktopSelector;
					$(uimode).attr('class', 'fa fa-desktop').attr('title', 'Desktop');
			}
			pdm.options.layoutDefaultMode = mode;
			temp_html = temp_html.replace(new RegExp((regex1+'(?=[^"]*">)'), 'gm'), '$1'+pdm.options.classRenameSuffix);
			temp_html = temp_html.replace(new RegExp((regex2+'(?=[^"]*">)'), 'gm'), '$1'+pdm.options.classRenameSuffix);
			temp_html = temp_html.replace(new RegExp((regex3+'(?=[^"]*">)'), 'gm'), '$1'+pdm.options.classRenameSuffix);
			canvas.html(temp_html);
		};

		/**
		 * Clean all occurrences of a substring
		 * @method cleanSubstring
		 * @param {} regex
		 * @param {} source
		 * @param {} replacement
		 * @returns CallExpression
		 */
		pdm.cleanSubstring = function(regex, source, replacement) {
			return source.replace(new RegExp(regex, 'g'), replacement);
		};

		/*------------------------------------------ ROWS ---------------------------------------*/
		/**
		 * Look for pre-existing rows and add editing tools as appropriate
		 * @rows: elements to act on
		 * @method activateRows
		 * @param {object} rows - rows to act on
		 * @returns null
		 */
		pdm.activateRows = function(rows){
			pdm.log("++ Activate Rows");
			rows.addClass(pdm.options.pdmEditClass)
				.prepend(pdm.toolFactory(pdm.options.rowButtonsPrepend));
		};

		/*------------------------------------------ COLS ---------------------------------------*/
		/**
		 * Look for pre-existing columns and add editing tools as appropriate
		 * @method activateCols
		 * @param {object} cols - elements to act on
		 * @returns null
		 */
		pdm.activateCols = function(cols){
			cols.addClass(pdm.options.pdmEditClass);
			// For each column,
			$.each(cols, function(i, column){
				$(column).prepend(pdm.toolFactory(pdm.options.colButtonsPrepend));
			});
			pdm.log("++ Activate Cols Ran");
		};

		/*
		 Run (if set) any custom init/deinit filters on the gridmanager canvas
		 @canvasElem - canvas wrapper container with the entire layout html
		 @isInit - flag that indicates if the method is running during init or deinit.
		 true - if its running during the init process, or false - during the deinit (cleanup) process

		 returns void
		 */

		pdm.runFilter=function(canvasElem, isInit){
			if(typeof pdm.options.filterCallback === 'function') {
				pdm.options.filterCallback(canvasElem, isInit);
			}
			if(pdm.options.editableRegionEnabled) {
				pdm.editableAreaFilter(canvasElem, isInit);
			}
		};

		/*
		 Filter method to restore editable regions in edit mode.
		 */
		pdm.editableAreaFilter = function(canvasElem, isInit) {
			if(isInit) {
				var cTagOpen = '<!--'+pdm.options.pdmEditRegion+'-->',
					cTagClose = '<!--\/'+pdm.options.pdmEditRegion+'-->',
					regex = new RegExp('(?:'+cTagOpen+')\\s*([\\s\\S]+?)\\s*(?:'+cTagClose+')', 'g'),
					html = $(canvasElem).html(),
					rep = cTagOpen+'<div class="'+pdm.options.pdmEditRegion+' '+pdm.options.contentDraggableClass+'">'+pdm.options.controlContentElem +'<div class="'+pdm.options.pdmContentRegion+'">$1</div></div>'+cTagClose;

				html = html.replace(regex, rep);
				$(canvasElem).html(html);
				pdm.log("editableAreaFilter init ran");
			} else {
				$('.'+pdm.options.controlNestedEditable, canvasElem).remove();
				$('.'+pdm.options.pdmContentRegion).contents().unwrap();

				pdm.log("editableAreaFilter deinit ran");
			}
		};

		/*------------------------------------------ BTNs ---------------------------------------*/
		/**
		 * Returns an editing div with appropriate btns as passed in
		 * @method toolFactory
		 * @param {array} btns - Array of buttons (see options)
		 * @return MemberExpression
		 */
		pdm.toolFactory=function(btns){
			var tools=$("<div/>")
				.addClass(pdm.options.pdmToolClass)
				.addClass(pdm.options.pdmClearClass)
				.html(pdm.buttonFactory(btns));
			return tools[0].outerHTML;
		};

		/**
		 * Returns html string of buttons
		 * @method buttonFactory
		 * @param {array} btns - Array of button configurations (see options)
		 * @return CallExpression
		 */
		pdm.buttonFactory=function(btns){
			var buttons=[];
			$.each(btns, function(i, val){
				val.btnLabel = (typeof val.btnLabel === 'undefined')? '' : val.btnLabel;
				val.title = (typeof val.title === 'undefined')? '' : val.title;
				buttons.push("<" + val.element +" title='" + val.title + "' class='" + val.btnClass + "'><span class='"+val.iconClass+"'></span>&nbsp;" + val.btnLabel + "</" + val.element + "> ");
			});
			return buttons.join("");
		};

		/**
		 * Add any custom buttons configured on the data attributes
		 * returns void
		 * @method initCustomControls
		 * @returns null
		 */
		pdm.initCustomControls=function(){
			var canvas=pdm.$el.find("#" + pdm.options.canvasId),
				callbackParams = '',
				callbackScp = '',
				callbackFunc = '',
				btnLoc = '',
				btnObj = {},
				iconClass = '',
				btnLabel = '';

			$( ('.'+pdm.options.colClass+':data,'+' .'+pdm.options.rowClass+':data'), canvas).each(function(){
				for(prop in $(this).data()) {
					if(prop.indexOf('pdmButton') === 0) {
						callbackFunc = prop.replace('pdmButton','');
						callbackParams = $(this).data()[prop].split('|');
						// Cannot accept 0 params or empty callback function name
						if(callbackParams.length === 0 || callbackFunc === '') { break; }

						btnLoc =    (typeof callbackParams[3] !== 'undefined')? callbackParams[3] : 'top';
						iconClass = (typeof callbackParams[2] !== 'undefined')? callbackParams[2] : 'fa fa-file-code-o';
						btnLabel =  (typeof callbackParams[1] !== 'undefined')? callbackParams[1] : '';
						callbackScp = callbackParams[0];
						btnObj = {
							element: 'a',
							btnClass: ('pdm-'+callbackFunc),
							iconClass:  iconClass,
							btnLabel: btnLabel
						};
						pdm.setupCustomBtn(this, btnLoc, callbackScp, callbackFunc, btnObj);
						break;
					}
				}
			});
		};

		/**
		 * Configures custom button click callback function
		 * returns bool, true on success false on failure
		 * @container - container element that wraps the toolbar
		 * @btnLoc - button location: "top" for the upper toolbar and "bottom" for the lower one
		 * @callbackScp - function scope to use. "window" for global scope
		 * @callbackFunc - function name to call when the user clicks the custom button
		 * @btnObj - button object that contains properties for: tag name, title, icon class, button class and label
		 * @method setupCustomBtn
		 * @param {} container
		 * @param {} btnLoc
		 * @param {} callbackScp
		 * @param {} callbackFunc
		 * @param {} btnObj
		 * @returns Literal
		 */
		pdm.setupCustomBtn=function(container, btnLoc, callbackScp, callbackFunc, btnObj) {
			var callback = null;

			// Ensure we have a valid callback, if not skip
			if(typeof callbackFunc === 'string') {
				callback = pdm.isValidCallback(callbackScp, callbackFunc.toLowerCase());
			} else if(typeof callbackFunc === 'function') {
				callback = callbackFunc;
			} else {
				return false;
			}
			// Set default button location to the top toolbar
			btnLoc = (btnLoc === 'bottom')? ':last' : ':first';

			// Add the button to the selected toolbar
			$( ('.'+pdm.options.pdmToolClass+btnLoc), container).append(pdm.buttonFactory([btnObj])).find(':last').on('click', function(e) {
				callback(container, this);
				e.preventDefault();
			});
			return true;
		};

		/**
		 * Checks that a callback exists and returns it if available
		 * returns function
		 * @callbackScp - function scope to use. "window" for global scope
		 * @callbackFunc - function name to call when the user clicks the custom button
		 * @method isValidCallback
		 * @param {} callbackScp
		 * @param {} callbackFunc
		 * @returns callback
		 */
		pdm.isValidCallback=function(callbackScp, callbackFunc){
			var callback = null;

			if(callbackScp === 'window') {
				if(typeof window[callbackFunc] === 'function') {
					callback = window[callbackFunc];
				} else { // If the global function is not valid there is nothing to do
					return false;
				}
			} else if(typeof window[callbackScp][callbackFunc] === 'function') {
				callback = window[callbackScp][callbackFunc];
			} else { // If there is no valid callback there is nothing to do
				return false;
			}
			return callback;
		};

		/**
		 * Add any custom buttons globally on all rows / cols
		 * returns void
		 * @method initGlobalCustomControls
		 * @returns null
		 */
		pdm.initGlobalCustomControls=function(){
			var canvas=pdm.$el.find("#" + pdm.options.canvasId),
				elems=[],
				callback = null,
				btnClass = '';

			$.each(['row','col'], function(i, control_type) {
				if(typeof pdm.options.customControls['global_'+control_type] !== 'undefined') {
					elems=canvas.find(pdm.options[control_type+'Selector']);
					$.each(pdm.options.customControls['global_'+control_type], function(i, curr_control) {
						// controls with no valid callbacks set are skipped
						if(typeof curr_control.callback === 'undefined') { return; }

						if(typeof curr_control.loc === 'undefined') {
							curr_control.loc = 'top';
						}
						if(typeof curr_control.iconClass === 'undefined') {
							curr_control.iconClass = 'fa fa-file-code-o';
						}
						if(typeof curr_control.btnLabel === 'undefined') {
							curr_control.btnLabel = '';
						}
						if(typeof curr_control.title === 'undefined') {
							curr_control.title = '';
						}

						btnClass = (typeof curr_control.callback === 'function')? (i+'_btn') : (curr_control.callback);

						btnObj = {
							element: 'a',
							btnClass: 'pdm-'+btnClass,
							iconClass: curr_control.iconClass,
							btnLabel: curr_control.btnLabel,
							title: curr_control.title
						};

						$.each(elems, function(i, current_elem) {
							pdm.setupCustomBtn(current_elem, curr_control.loc, 'window', curr_control.callback, btnObj);
						});
					});
				}
			});
		};

		/*
		 Prepares any new content element inside columns so inner toolbars buttons work
		 and any drag & drop functionality.
		 @newElem  - Container of the new content element added into a col
		 returns void
		 */

		pdm.initNewContentElem = function(newElem) {
			var parentCols = null;

			if(typeof newElem === 'undefined') {
				parentCols = pdm.$el.find('.'+pdm.options.colClass);
			} else {
				parentCols = newElem.closest('.'+pdm.options.colClass);
			}

			$.each(parentCols, function(i, col) {
				$(col).on('click', '.pdm-delete', function(e) {
					$(this).closest('.'+pdm.options.pdmEditRegion).remove();
					pdm.resetCommentTags(col);
					e.preventDefault();
				});
				$(col).sortable({
					items: '.'+pdm.options.contentDraggableClass,
					axis: 'y',
					placeholder: pdm.options.rowSortingClass,
					handle: "."+pdm.options.controlMove,
					forcePlaceholderSize: true, opacity: 0.7, revert: true,
					tolerance: "pointer",
					cursor: "move",
					stop: function(e, ui) {
						pdm.resetCommentTags($(ui.item).parent());
					}
				});
			});
		};

		/*
		 Resets the comment tags for editable elements
		 @elem - Element to reset the editable comments on
		 returns void
		 */

		pdm.resetCommentTags = function(elem) {
			var cTagOpen = '<!--'+pdm.options.pdmEditRegion+'-->',
				cTagClose = '<!--\/'+pdm.options.pdmEditRegion+'-->';
			// First remove all existing comments
			pdm.clearComments(elem);
			// Now replace these comment tags
			$('.'+pdm.options.pdmEditRegion, elem).before(cTagOpen).after(cTagClose);
		};

		/*
		 Clears any comments inside a given element
		 @elem - element to clear html comments on
		 returns void
		 */

		pdm.clearComments = function(elem) {
			$(elem, '#'+pdm.options.canvasId).contents().filter(function() {
				return this.nodeType === 8;
			}).remove();
		};

		/*------------------------------------------ RTEs ---------------------------------------*/
		/**
		 * Starts, stops, looks for and  attaches RTEs
		 * @method rteControl
		 * @param {string} action  - options are init, attach, stop
		 * @param {object} element  - object to attach an RTE to
		 * @returns null
		 */
		pdm.rteControl=function(action, element){
			pdm.log("RTE " + pdm.options.rte + ' ' +action);

			switch (action) {
				case 'init':
					if(typeof window.CKEDITOR !== 'undefined'){
						pdm.options.rte='ckeditor';
						pdm.log("++ CKEDITOR Found");
						window.CKEDITOR.disableAutoInline = true;
					}
					if(typeof window.tinymce !== 'undefined'){
						pdm.options.rte='tinymce';
						pdm.log("++ TINYMCE Found");
					}
					break;
				case 'attach':
					switch (pdm.options.rte) {
						case 'tinymce':
							if(!(element).hasClass("mce-content-body")){
								element.tinymce(pdm.options.tinymce.config);
							}
							break;

						case 'ckeditor':
							$(element).ckeditor(pdm.options.ckeditor);

							break;
						default:
							pdm.log("No RTE specified for attach");
					}
					break; //end Attach
				case 'stop':
					switch (pdm.options.rte) {
						case 'tinymce':
							// destroy TinyMCE
							//window.tinymce.remove();
							pdm.log("-- TinyMCE destroyed");
							break;

						case 'ckeditor':
							// destroy ckeditor
							for(var name in window.CKEDITOR.instances)
							{
								window.CKEDITOR.instances[name].destroy();
							}
							pdm.log("-- CKEDITOR destroyed");
							break;

						default:
							pdm.log("No RTE specified for stop");
					}
					break; //end stop

				default:
					pdm.log("No RTE Action specified");
			}
		};

		/**
		 * Create a single column with appropriate editing tools
		 * @method createCol
		 * @param {integer} size - width of the column to create, i.e 6
		 * @returns null
		 */
		pdm.createCol =  function(size){
			var col= $("<div/>")
				.addClass(pdm.options.colClass)
				.addClass(pdm.options.colDesktopClass + size)
				.addClass(pdm.options.colLaptopClass + size)
				.addClass(pdm.options.colTabletClass + size)
				.addClass(pdm.options.colPhoneClass + size)
				.addClass(pdm.options.pdmEditClass)
				.addClass(pdm.options.colAdditionalClass)
				.html(pdm.toolFactory(pdm.options.colButtonsPrepend))
				.prepend(pdm.toolFactory(pdm.options.colButtonsPrepend));
			pdm.log("++ Created Column " + size);
			return col;
		};

		/*------------------------------------------ Useful functions ---------------------------------------*/

		/**
		 * Quick reset - deinit & init the canvas
		 * @method reset
		 * @returns null
		 */
		pdm.reset=function(){
			pdm.log("~~RESET~~");
			pdm.deinitCanvas();
			pdm.initCanvas();
		};

		/**
		 * Removes canvas editing mode
		 * @method deinitCanvas
		 * @returns null
		 */
		pdm.deinitCanvas = function(){
			// cache canvas
			var canvas=pdm.$el.find("#" + pdm.options.canvasId);
			var cols=canvas.find(pdm.options.colSelector);
			var rows=canvas.find(pdm.options.rowSelector);

			pdm.log("- deInitCanvas Running");
			// Hide template control
			//pdm.$el.find("#pdm-addnew").hide();
			// Sort Rows First
			pdm.deactivateRows(rows);
			// Now Columns
			pdm.deactivateCols(cols);
			// Clean markup
			pdm.cleanup();
			pdm.runFilter(canvas, false);
			pdm.status=false;
		};

		/**
		 * Look for pre-existing rows and remove editing classes as appropriate
		 * @rows: elements to act on
		 * @method deactivateRows
		 * @param {object} rows - rows to act on
		 * @returns null
		 */
		pdm.deactivateRows = function(rows){
			pdm.log("-- DeActivate Rows");
			rows.removeClass(pdm.options.pdmEditClass)
				.removeClass("ui-sortable")
				.removeAttr("style");
		};

		/**
		 * Look for pre-existing columns and removeediting tools as appropriate
		 * @method deactivateCols
		 * @param {object} cols - elements to act on
		 * @returns null
		 */
		pdm.deactivateCols = function(cols){
			cols.removeClass(pdm.options.pdmEditClass)
				.removeClass(pdm.options.pdmEditClassSelected)
				.removeClass("ui-sortable");
			$.each(cols.children(), function(i, val){
				// Grab contents of editable regions and unwrap
				if($(val).hasClass(pdm.options.pdmEditRegion)){
					if($(val).html() !== ''){
						$(val).contents().unwrap();
					} else {
						// Deals with empty editable regions
						$(val).remove();
					}
				}
			});
			pdm.log("-- deActivate Cols Ran");
		};

		/**
		 * Remove all extraneous markup
		 * @method cleanup
		 * @returns null
		 */

		pdm.cleanup =  function(){
			var canvas,
				content;

			// cache canvas
			canvas = pdm.$el.find("#" + pdm.options.canvasId);

			/**
			 * Determine the current edit mode and get the content based upon the resultant
			 * context to prevent content in source mode from being lost on save, as such:
			 *
			 * edit mode (source): canvas.find('textarea').val()
			 * edit mode (visual): canvas.html()
			 */
			content = pdm.mode !== "visual" ? canvas.find('textarea').val() : canvas.html();

			// Clean any temp class strings
			canvas.html(pdm.cleanSubstring(pdm.options.classRenameSuffix, content, ''));

			// Clean column markup
			canvas.find(pdm.options.colSelector)
				.removeAttr("style")
				.removeAttr("spellcheck")
				.removeClass("mce-content-body").end()
				// Clean img markup
				.find("img")
				.removeAttr("style")
				.addClass("img-responsive")
				.removeAttr("data-cke-saved-src")
				.removeAttr("data-mce-src").end()
				// Remove Tools
				.find("." + pdm.options.pdmToolClass).remove();
			// Destroy any RTEs
			pdm.rteControl("stop");
			pdm.log("~~Cleanup Ran~~");
		};

		/**
		 * Generic logging function
		 * @method log
		 * @param {object} logvar - The Object or string you want to pass to the console
		 * @returns null
		 * @property {boolean} pdm.options.debug
		 */
		pdm.log = function(logvar){
			if(pdm.options.debug){
				if ((window['console'] !== undefined)) {
					window.console.log(logvar);
				}
			}
		};
		// Run initializer
		pdm.init();
	};

	/**
	 Options which can be overridden by the .pagedesignmanager() call on the requesting page
	 */
	$.pagedesignmanager.defaultOptions = {
		/*
		 General Options---------------
		 */

		debug: 0,

		// Can add editable regions?
		editableRegionEnabled: true,

		// Should we try and automatically create editable regions?
		autoEdit: true,

		// Filter callback. Callback receives two params: the template grid element and whether is called from the init or deinit method
		filterCallback: null,

		/*
		 Canvas---------------
		 */
		// Canvas ID
		canvasId: "pdm-canvas",

		/*
		 Control Bar---------------
		 */
		// Top Control Row ID
		controlId:  "pdm-controls",

		// Move handle class
		controlMove: 'pdm-move',

		// Editable element toolbar class
		controlNestedEditable: 'pdm-controls-element',

		// Array of buttons for row templates
		controlButtons: [[12], [6,6], [4,4,4], [3,3,3,3], [2,2,2,2,2,2], [2,8,2], [4,8], [8,4]],

		// Custom Global Controls for rows & cols - available props: global_row, global_col
		customControls: { global_row: [], global_col: [] },

		// Default control button class
		controlButtonClass: "btn  btn-xs  btn-primary",

		// Default control button icon
		controlButtonSpanClass: "fa fa-plus-circle",

		// Control bar RH dropdown markup
		controlAppend: "<div class='btn-group pull-right'>" +
		"<button title='Edit Source Code' type='button' class='btn btn-xs btn-primary pdm-edit-mode'><span class='fa fa-code'></span></button>" +
		"<button title='Preview' type='button' class='btn btn-xs btn-primary pdm-preview'><span class='fa fa-eye'></span></button>" +
		"<div class='dropdown pull-left pdm-layout-mode'>" +
		"<button type='button' class='btn btn-xs btn-primary dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>" +
		"<ul class='dropdown-menu' role='menu'>" +
		"<li><a data-width='auto' title='Desktop'><span class='fa fa-desktop'></span> Desktop</a></li>" +
		"<li><a title='Laptop' data-width='992'><span class='fa fa-laptop'></span> Laptop</a></li>" +
		"<li><a title='Tablet' data-width='768'><span class='fa fa-tablet'></span> Tablet</a></li>" +
		"<li><a title='Phone' data-width='640'><span class='fa fa-mobile-phone'></span> Phone</a></li>" +
		"</ul>" +
		"</div>" +
		"<button type='button' class='btn  btn-xs  btn-primary dropdown-toggle' data-toggle='dropdown'><span class='caret'></span><span class='sr-only'>Toggle Dropdown</span></button>" +
		"<ul class='dropdown-menu' role='menu'><li><a title='Save'  href='#' class='pdm-save'><span class='fa fa-save'></span> Save</a></li>" +
		"<li><a title='Reset Grid' href='#' class='pdm-resetgrid'><span class='fa fa-trash-o'></span> Reset</a></li></ul></div>",

		// Controls for content elements
		controlContentElem: '<div class="pdm-controls-element"> <a class="pdm-move fa fa-arrows" href="#" title="Move"></a> <a class="pdm-delete fa fa-times" href="#" title="Delete"></a> </div>',

		/*
		 General editing classes---------------
		 */
		// Standard edit class, applied to active elements
		pdmEditClass: "pdm-editing",

		// Applied to the currently selected element
		pdmEditClassSelected: "pdm-editing-selected",

		// Editable region class
		pdmEditRegion: "pdm-editable-region",

		// Editable container class
		pdmContentRegion: "pdm-content",

		// Tool bar class which are inserted dynamically
		pdmToolClass: "pdm-tools",

		// Clearing class, used on most toolbars
		pdmClearClass: "clearfix",

		// generic float left and right
		pdmDangerClass: "btn-danger",

		/*
		 Rows---------------
		 */
		// Generic row class. change to row--fluid for fluid width in Bootstrap
		rowClass:    "row",

		// Used to find rows - change to div.row-fluid for fluid width
		rowSelector: "div.row",

		// class of background element when sorting rows
		rowSortingClass: "alert-warning",

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
				btnClass: "pdm-addRow add-12 pull-left",
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
		 Columns--------------
		 */
		// Column Class
		colClass: "column",

		// Class to allow content to be draggable
		contentDraggableClass: 'pdm-content-draggable',

		// Adds any missing classes in columns for muti-device support.
		addResponsiveClasses: true,

		// Adds "colClass" to columns if missing: addResponsiveClasses must be true for this to activate
		addDefaultColumnClass: true,

		// Generic desktop size layout class
		colDesktopClass: "col-lg-",

		// Generic desktop size layout class
		colLaptopClass: "col-md-",

		// Generic tablet size layout class
		colTabletClass: "col-sm-",

		// Generic phone size layout class
		colPhoneClass: "col-xs-",

		// Wild card column desktop selector
		colDesktopSelector: "div[class*=col-lg-]",

		// Wild card column laptop selector
		colLaptopSelector: "div[class*=col-md-]",

		// Wildcard column tablet selector
		colTabletSelector: "div[class*=col-sm-]",

		// Wildcard column phone selector
		colPhoneSelector: "div[class*=col-xs-]",

		// String used to temporarily rename column classes not in use
		classRenameSuffix: "-clsstmp",

		// Additional column class to add (foundation needs columns, bs3 doesn't)
		colAdditionalClass: "",

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

		// Maximum column span value: if you've got a 24 column grid via customised bootstrap, you could set this to 24.
		colMax: 12,

		addwidget: '',

		/*
		 Rich Text Editors---------------
		 */
		tinymce: {
			config: {
				inline: true,
				plugins: [
					"advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste"
				],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
			}
		},

		// Path to CK custom comfiguration
		ckeditor: {
			customConfig: ""
		}
	};

	/**
	 * Exposes pagedesignmanager as jquery function
	 * @method pagedesignmanager
	 * @param {object} options
	 * @returns CallExpression
	 */
	$.fn.pagedesignmanager = function(options){
		return this.each(function(){
			var element = $(this);
			var pagedesignmanager = new $.pagedesignmanager(this, options);
			element.data('pagedesignmanager', pagedesignmanager);
		});
	};

	/**
	 * General Utility Regex function used to get custom callback attributes
	 * @method regex
	 * @param {} elem
	 * @param {} index
	 * @param {} match
	 * @returns CallExpression
	 */
	$.expr[':'].regex = function(elem, index, match) {
		var matchParams = match[3].split(','),
			validLabels = /^(data|css):/,
			attr = {
				method: matchParams[0].match(validLabels) ?
					matchParams[0].split(':')[0] : 'attr',
				property: matchParams.shift().replace(validLabels,'')
			},
			regexFlags = 'ig',
			regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
		return regex.test(jQuery(elem)[attr.method](attr.property));
	};
})(jQuery);