/* eslint-disable */

(function () {

	var defs = {}; // id -> {dependencies, definition, instance (possibly undefined)}

	// Used when there is no 'main' module.
	// The name is probably (hopefully) unique so minification removes for releases.
	var register_3795 = function (id) {
		var module = dem(id);
		var fragments = id.split('.');
		var target = Function('return this;')();
		for (var i = 0; i < fragments.length - 1; ++i) {
			if (target[fragments[i]] === undefined)
				target[fragments[i]] = {};
			target = target[fragments[i]];
		}
		target[fragments[fragments.length - 1]] = module;
	};

	var instantiate = function (id) {
		var actual = defs[id];
		var dependencies = actual.deps;
		var definition = actual.defn;
		var len = dependencies.length;
		var instances = new Array(len);
		for (var i = 0; i < len; ++i)
			instances[i] = dem(dependencies[i]);
		var defResult = definition.apply(null, instances);
		if (defResult === undefined)
			throw 'module [' + id + '] returned undefined';
		actual.instance = defResult;
	};

	var def = function (id, dependencies, definition) {
		if (typeof id !== 'string')
			throw 'module id must be a string';
		else if (dependencies === undefined)
			throw 'no dependencies for ' + id;
		else if (definition === undefined)
			throw 'no definition function for ' + id;
		defs[id] = {
			deps: dependencies,
			defn: definition,
			instance: undefined
		};
	};

	var dem = function (id) {
		var actual = defs[id];
		if (actual === undefined)
			throw 'module [' + id + '] was undefined';
		else if (actual.instance === undefined)
			instantiate(id);
		return actual.instance;
	};

	var req = function (ids, callback) {
		var len = ids.length;
		var instances = new Array(len);
		for (var i = 0; i < len; ++i)
			instances.push(dem(ids[i]));
		callback.apply(null, callback);
	};

	var ephox = {};

	ephox.bolt = {
		module: {
			api: {
				define: def,
				require: req,
				demand: dem
			}
		}
	};

	var define = def;
	var require = req;
	var demand = dem;
	// this helps with minificiation when using a lot of global references
	var defineGlobal = function (id, ref) {
		define(id, [], function () {
			return ref;
		});
	};

	/*jsc
	["tinymce.plugins.image.Plugin","tinymce.core.PluginManager","tinymce.core.util.Tools","tinymce.plugins.image.ui.Dialog","global!tinymce.util.Tools.resolve","global!document","global!Math","global!RegExp","tinymce.core.Env","tinymce.core.ui.Factory","tinymce.core.util.JSON","tinymce.core.util.XHR","tinymce.plugins.image.core.Uploader","tinymce.plugins.image.core.Utils","tinymce.core.util.Promise"]
	jsc*/
	defineGlobal("global!tinymce.util.Tools.resolve", tinymce.util.Tools.resolve);
	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.PluginManager', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.PluginManager');
		}
	);

	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.util.Tools', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.util.Tools');
		}
	);

	defineGlobal("global!document", document);
	defineGlobal("global!Math", Math);
	defineGlobal("global!RegExp", RegExp);
	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.Env', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.Env');
		}
	);

	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.ui.Factory', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.ui.Factory');
		}
	);

	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.util.JSON', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.util.JSON');
		}
	);

	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.util.XHR', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.util.XHR');
		}
	);

	/**
	 * ResolveGlobal.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	define(
		'tinymce.core.util.Promise', [
			'global!tinymce.util.Tools.resolve'
		],
		function (resolve) {
			return resolve('tinymce.util.Promise');
		}
	);

	/**
	 * Clase para trabar con los parámetros del shortcode
	 * - Recuperación e inserción de parámetros
	 * - Recuperación e inserción de elementos contenido.
	 */
	define(
		'tinymce.plugins.uvigo.floorimage.Data', [
			'tinymce.core.util.Tools'
		],
		function (Tools) {
			return function (editor) {

				function getAttributes(data_values) {
					var atributes = [];
					atributes.icon = '';
					atributes.image = '';
					atributes.image_id = '';
					atributes.classname = '';
					atributes.style = '';

					atributes.c_title = '';
					atributes.c_subtitle = '';
					atributes.c_linktitle = '';
					atributes.c_linkurl = '';

					atributes.data = data_values;

					var attrs = wp.shortcode.attrs(data_values).named;
					Tools.each(attrs, function (v, k) {
						atributes[k] = v;
					});

					return atributes;
				}

				function getAttributesNode(node) {
					var data_xwe_floor = editor.dom.getAttrib(node, 'data-uvigo-floor-image');
					var data_values = window.decodeURIComponent(data_xwe_floor);
					var atributes = getAttributes(data_values);
					return atributes;
				}

				function getBodyNode(node) {
					var query = tinymce.dom.DomQuery(node);
					return query.find('.uvigo-floor-image-body');
				}

				function getBodyContent(node) {
					var body_node = getBodyNode(node);
					floor_body = body_node.html();
					return floor_body;
				}

				function getBodySeralizedHtml(node) {
					var bodyNodeHTML = '';
					getBodyNode(node).each(function (index, item) {
						bodyNodeHTML += editor.serializer.serialize(item);
					});
					var e = jQuery(bodyNodeHTML);
					return e.html(); // bodyNodeHTML;
				}

				function getElementsOnNode(node) {
					var elements = [];
					elements.html = getBodySeralizedHtml(node);
					return elements;
				}

				/**
				 * Parse
				 */
				function html(cls, data, content) {
					var args_shortcode = getAttributes(data);
					var encodeData = window.encodeURIComponent(data);

					//Cuerpo shortcode
					var classnames = args_shortcode.style + ' ' + args_shortcode.classname;

					var outputShortcode = '';

					outputShortcode += '<div class="floor floor-header ' + classnames  + '"';
					outputShortcode += ' data-width="1200" data-height="400"';
					outputShortcode += '>';

					// Image
					outputShortcode += '<div class="floor__image"';
					if ( args_shortcode['image'] !== '') {
						outputShortcode += ' style="background-image: url(\'' + args_shortcode['image'] + '\');"';
					}
					outputShortcode += '>';

					outputShortcode += '<div class="floor__image__text">';
					outputShortcode += '<div class="container">';
					outputShortcode += '<div class="row">';
					outputShortcode += '<div class="col-12">';

					if ( args_shortcode.icon != '' ){
						outputShortcode += '<div class="floor__icon uvigo-iconfont ' + args_shortcode['icon'] + '"></div>';
					}

					outputShortcode += '<h1>' + args_shortcode['c_title']  + '</h1>';
					outputShortcode += '<h2>' + args_shortcode['c_subtitle']  + '</h2>';

					outputShortcode += '</div>';
					outputShortcode += '</div>';
					outputShortcode += '</div>';
					outputShortcode += '</div>';
					outputShortcode += '</div>';


					outputShortcode += '<div class="floor__text container">';
					outputShortcode += '<div class="row">';
					outputShortcode += '<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">';

					//Content
					outputShortcode += '<div class="uvigo-floor-image-body"><p>' + content  + '</p></div>';

					if ( args_shortcode.c_linkurl !== '' ) {
						outputShortcode += '<a class="btn floor_element_link" href="' + args_shortcode.c_linkurl + '">' + args_shortcode.c_linktitle + '</a>';
					}

					//End Content
					outputShortcode += '</div>';
					outputShortcode += '</div>';
					outputShortcode += '</div>';

					outputShortcode += '</div>'; // Div floor

					//Cuerpo Presentación : Vista
					var content_html = '<div class="mceItem ' + cls + '" ' + 'data-uvigo-floor-image="' + encodeData + '" >';
					content_html += '<div class="floor__banner">Piso: Fai click para opcións de edición.</div>';
					content_html += outputShortcode;
					content_html += '</div>';

					//Nodo
					var content_node = editor.dom.createFragment(content_html);
					var html = editor.dom.getOuterHTML(content_node);

					//Clean ps
					html = html.replace(/<p><\/p>/g, function (match) {
						return '';
					});

					return html;
				}

				function replaceFloorShortcodes(content) {
					return content.replace(/\[uvigo_floor_image([\s\S][^\]]*)\]((?:[\s\S](?!\/uvigo_floor_image))*)\[\/uvigo_floor_image\]/gmi, function (match, p1, p2, offset, string) {
						return html('uvigo-floor-image', p1, p2);
					});
				}

				/**
				 * Serialize
				 */
				function restoreFloorShortcodes(node) {

					var query = tinymce.dom.DomQuery(node);
					var result = query.find('div.uvigo-floor-image');
					result.each(function (index, item) {

						var attributes = getAttributesNode(item);
						var floor_parameters = attributes.data;
						var floor_body = getBodyContent(item);

						var text = '[uvigo_floor_image ' + floor_parameters + ']' + floor_body + '[/uvigo_floor_image]';
						tinymce.DOM.replace(editor.dom.createFragment(text), item, false);
					});
					return node;
				}

				/**
				 *
				 * Construye la cadena de shortcode [uvigo_floor_image] con los parámetros que se le pasan
				 * @param data : atributos shortcode
				 * @param contents : elementos del contenido
				 */
				function dataToShortcode(data, contents) {
					var parameters = '';
					var html = '';

					//Parseamos los atributos shortcode
					Tools.each(data, function (v, k) {
						parameters += ' ' + k + '="' + v + '" ';
					});
					//Parseamos los atributos contents
					Tools.each(contents, function (v, k) {
						if ( k  !== 'c_html') {
							parameters += ' ' + k + '="' + v + '" ';
						} else {
							html = v;
						}
					});

					//Construimos contenido html
					var shortcode = "[uvigo_floor_image " + parameters + ']' + html + '[/uvigo_floor_image]';

					return shortcode;
				}

				return {
					getAttributesNode: getAttributesNode,
					getAttributes: getAttributes,
					dataToShortcode: dataToShortcode,
					replaceFloorShortcodes: replaceFloorShortcodes,
					restoreFloorShortcodes: restoreFloorShortcodes,
					getElementsOnNode: getElementsOnNode
				};
			};
		}
	);

	/**
	 * @class tinymce.uvigo.floorimage.ui.ImageDialog
	 * @private
	 */
	define(
		'tinymce.plugins.uvigo.floorimage.ui.ImageDialog', [
			'tinymce.core.util.Tools'
		],
		function (Tools) {

			return function (fieldName, fieldValue, fieldValueID) {

				var frame_media;
				var addImgButton, delImgButton;

				var components;
				var image, imgContainer, imagePanel;

				function loadImage() {

					var query = tinymce.dom.DomQuery;

					query('#imgContainer-body img').remove();
					if ( image.value() !== '' ) {
						query('#imgContainer-body').append('<img src="' + image.value() + '" alt="image" style="width:auto; height:80px"/>');
					}
				}

				function updateData() {
					loadImage();
					if (image.value() === '') {
						addImgButton.show();
						delImgButton.hide();
					} else {
						addImgButton.hide();
						delImgButton.show();
					}
					//imagePanel.reflow();
				}

				function openMedia() {

					if ( ! frame_media ) {
						// Create a new media frame
						frame_media = wp.media({
							title: 'Fondo',
							button: {
								text: 'Establecer'
							},
							multiple: false // Set to true to allow multiple files to be selected
						});

						// When an image is selected in the media frame...
						frame_media.on('select', function () {

							// Get media attachment details from the frame state
							var attachment = frame_media.state().get('selection').first().toJSON();
							// Send the attachment URL to our custom image input field.
							image.value(attachment.url);
							imageAttachmentId.value(attachment.id);
							updateData();
						});
					}

					// Finally, open the modal on click
					frame_media.open();
				}

				function removeImage() {
					image.value('');
					imageAttachmentId.value('');
					updateData();
				}

				function setDisabled( value ) {
					addImgButton.disabled(value);
					delImgButton.disabled(value);
				}

				image = new tinymce.ui.TextBox({
					name: fieldName,
					value: fieldValue
				});
				image.hide();

				imageAttachmentId = new tinymce.ui.TextBox({
					name: fieldName + '_id',
					value: fieldValueID
				});
				imageAttachmentId.hide();

				addImgButton = new tinymce.ui.Button({
					size: 'small',
					text: 'Añadir'
				});
				addImgButton.on('click', openMedia);

				delImgButton = new tinymce.ui.Button({
					size: 'small',
					text: 'Eliminar'
				});
				delImgButton.on('click', removeImage);

				var buttons =  new tinymce.ui.Form({
					padding: 0,
					minWidth: '70',
					maxWidth: '70',
					items: [
						addImgButton,
						delImgButton
					]
				});

				imgContainer = new tinymce.ui.Panel({
					id: 'imgContainer',
					padding: 0,
					style: (new tinymce.html.Styles().parse('border: 1px solid #ddd')),
					minHeight: 80,
					minWidth: 250,
					items: [
						image,
						imageAttachmentId
					],
				});

				components = [
					imgContainer,
					buttons
				];

				imagePanel = new tinymce.ui.Form({
					padding: 0,
					label: 'Fondo',
					direction: 'row',
					items: components
				});

				return {
					panel: imagePanel,
					onPostRender: updateData,
					removeImage : removeImage,
					setDisabled : setDisabled,
				};
			};
		}
	);

	/**
	 * @class tinymce.plugins.uvigo.floorimage.ui.ContentDialog
	 * @private
	 */
	define(
		'tinymce.plugins.uvigo.floorimage.ui.ContentDialog', [
			'tinymce.core.util.Tools'
		],
		function (Tools) {

			return function (content) {

				var contentFormPanel;

				contentFormPanel = new tinymce.ui.Form({
					padding: 0,
					items: [{
							type: 'textbox',
							name: 'content_title',
							label: 'Título',
							value: content.title
						},
						{
							type: 'textbox',
							name: 'content_subtitle',
							label: 'Subtítulo',
							value: content.subtitle
						},
						{
							type: 'textbox',
							name: 'content_html',
							label: 'Contido',
							multiline: true,
							rows: 4,
							value: content.html
						},
						{
							type: 'textbox',
							name: 'content_linktitle',
							label: 'Título btn',
							value: content.linktitle
						},
						{
							type: 'textbox',
							name: 'content_linkurl',
							label: 'Url btn',
							value: content.linkurl
						},
					]
				});

				function getElementsOnSubmit( e ) {
					var new_data = e.data;
					var elements = {};
					Tools.each(new_data, function (v, k) {
						var key = '';
						if (k.startsWith("content")) {
							key = k.replace('content_', 'c_');
							elements[key] = v;
						}
					});
					return elements;
				}

				return {
					contentFormPanel: contentFormPanel,
					getElementsOnSubmit: getElementsOnSubmit
				};
			};
		}
	);

	/**
	 * @class tinymce.uvigo.floorimage.ui.Dialog
	 * @private
	 */
	define(
		'tinymce.plugins.uvigo.floorimage.ui.Dialog', [
			'global!document',
			'global!Math',
			'global!RegExp',
			'tinymce.core.Env',
			'tinymce.core.ui.Factory',
			'tinymce.core.util.JSON',
			'tinymce.core.util.Tools',
			'tinymce.core.util.XHR',
			'tinymce.plugins.uvigo.floorimage.ui.ImageDialog',
			'tinymce.plugins.uvigo.floorimage.ui.ContentDialog',
			'tinymce.plugins.uvigo.floorimage.Data'
		],
		function (document, Math, RegExp, Env, Factory, JSON, Tools, XHR, ImageDialog, ContentDialog, FloorData) {

			return function (editor) {

				var floorData = FloorData(editor);

				function open(node) {

					//Init values
					var data = {
						classname: '',
						image: '',
						image_id: '',
						icon: '',
						style: '',
						c_title: '',
						c_subtitle: '',
						c_linktitle: '',
						c_linkurl: ''
					};

					var content = {
						title: '',
						subtitle: '',
						linktitle: '',
						linkurl: '',
						html: ''
					};

					if (node) {
						data = floorData.getAttributesNode(node);

						console.log( data );
						content.title = data.c_title;
						content.subtitle = data.c_subtitle;
						content.linktitle = data.c_linktitle;
						content.linkurl = data.c_linkurl;

						elements = floorData.getElementsOnNode(node);
						content.html = elements.html;
					}

					var imageDialog, contentDialog;
					var generalFormItems = [];
					var iconListBox, classExtraField;

					var styleValue = data.style;
					var iconClassValue = data.icon;
					var classnameValue = data.classname;

					iconListBox = new tinymce.ui.ListBox({
						name: 'data_icon',
						padding: 0,
						label: 'Icona',
						minWidth: '150',
						maxWidth: '150',
						value: iconClassValue,
						values: [
							{
								value: '',
								text: 'Ninguno'
							},
							{
								value: 'uvigo-iconfont-halt',
								text: 'Alto',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-halt',
							},
							{
								value: 'uvigo-iconfont-heart',
								text: 'Corazón',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-heart',
							},
							{
								value: 'uvigo-iconfont-justice',
								text: 'Justicia',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-justice',
							},
							{
								value: 'uvigo-iconfont-map',
								text: 'Mapa',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-map',
							},
							{
								value: 'uvigo-iconfont-program',
								text: 'Programa',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-program',
							},
							{
								value: 'uvigo-iconfont-slideshow',
								text: 'Diapositivas',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-slideshow',
							},
							{
								value: 'uvigo-iconfont-speaker',
								text: 'Altavoz',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-speaker',
							},
							{
								value: 'uvigo-iconfont-transport',
								text: 'Transporte',
								icon: 'uvigo-icon uvigo-iconfont uvigo-iconfont-transport',
							}
						],
					});


					classExtraField = new tinymce.ui.TextBox({
						name: 'data_classname',
						label: 'CSS class',
						value: classnameValue
					});

					//Panel
					imageDialog = ImageDialog('data_image', data.image, data.image_id );

					generalFormItems.push(imageDialog.panel);

					//Panel de contenido
					contentDialog = ContentDialog(content);
					var contentFormPanel = contentDialog.contentFormPanel;

					generalFormItems.push(contentFormPanel);

					generalFormItems.push(iconListBox);

					generalFormItems.push(classExtraField);

					function _onPostRender() {
						imageDialog.onPostRender();
					}

					function onSubmitForm(e) {

						//volcamos contenido
						editor.undoManager.transact(function () {
							//Insertar contenido despues
							var new_data = e.data;
							var parameters = {};

							Tools.each(new_data, function (v, k) {
								var key = '';
								if (k.startsWith("data")) {
									key = k.replace('data_', '');
									parameters[key] = v;
								}
							});

							var contents = contentDialog.getElementsOnSubmit(e);

							var shortcode = FloorData(editor).dataToShortcode(parameters, contents);
							var html = FloorData(editor).replaceFloorShortcodes(shortcode);
							if (node) {
								editor.selection.select(node);
								editor.dom.replace(editor.dom.createFragment(html), node);
							} else {
								var nodeSelected = editor.selection.getNode();
								if (nodeSelected.tagName !== 'BODY') {
									var parentXWE = editor.dom.getParent(nodeSelected, 'div.uvigo-floor-image');
									if (parentXWE) {
										nodeSelected = parentXWE;
									}
									editor.selection.select(nodeSelected);
									var re = editor.dom.insertAfter(editor.dom.createFragment(html), nodeSelected);

								} else {
									editor.setContent( editor.getContent() + html );
								}
							}
							// Se hace así, para que el código que se inserta ( shortcodes de terceros )
							// se vuelva a actualizar en el dom.
							editor.save();
							editor.load();
						});
					}

					var body = [
						{
						  type: 'form',
						  padding: 0,
						  minWidth: 750,
						  items: generalFormItems
						}
					  ];

					// Open window
					var win = editor.windowManager.open({
						title: 'Parámetros de Piso con Imaxe',
						width: 800,
						height: 450,
						body: body,
						onsubmit: onSubmitForm,
						onPostRender: _onPostRender
					});
				}

				function newFloor() {
					open();
				}

				function updateFloor(node) {
					open(node);
				}

				return {
					newFloor: newFloor,
					updateFloor: updateFloor
				};
			};
		}
	);

	/**
	 * Plugin.js
	 *
	 * Released under LGPL License.
	 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
	 *
	 * License: http://www.tinymce.com/license
	 * Contributing: http://www.tinymce.com/contributing
	 */

	/**
	 * This class contains all core logic for the image plugin.
	 *
	 * @class tinymce.plugins.uvigo.floorimage.Plugin
	 * @private
	 */
	define(
		'tinymce.plugins.uvigo.floorimage.Plugin', [
			'tinymce.core.PluginManager',
			'tinymce.core.util.Tools',
			'tinymce.plugins.uvigo.floorimage.ui.Dialog',
			'tinymce.plugins.uvigo.floorimage.Data'
		],
		function (PluginManager, Tools, Dialog, FloorData) {

			PluginManager.add('content_floor_image_button', function (editor) {

				var floorData = FloorData(editor);

				function isFloorView( node ){
					var isfloor =  editor.dom.hasClass(node, 'uvigo-floor-image');
					if( !isfloor ){
						isfloor = editor.dom.hasClass(node, 'floor__banner');
					}
					return isfloor;
				}

				function getFloorNode( node ){
					var isfloor =  editor.dom.hasClass(node, 'uvigo-floor-image');
					if( !isfloor && editor.dom.hasClass(node, 'floor__banner') ){
						node = editor.dom.getParent(node, '.uvigo-floor-image', editor.getBody());
					}
					return node;
				}

				// Add a button that opens a window
				editor.addButton('content_floor_image_button', {
					title: 'Insertar bloque Piso con imaxe',
					tooltip: 'Insertar bloque Piso con imaxe',
					text: '',
					icon: 'icon dashicons-welcome-widgets-menus',
					onclick: Dialog(editor).newFloor
				});

				editor.on('BeforeSetContent', function (event) {
					event.content = floorData.replaceFloorShortcodes(event.content);
				});

				editor.on('PreProcess', function (event) {
					if (event.get) {
						event.node = floorData.restoreFloorShortcodes(event.node);
					}
				});

				editor.addButton( 'wp_floor_image_view_edit', {
					tooltip: 'Edit ', // trailing space is needed, used for context
					icon: 'dashicon dashicons-edit',
					onclick: function( ) {
						var node = editor.selection.getNode();
						if (  isFloorView( node ) ) {
							node = getFloorNode( node );
							editor.selection.select(node);

							Dialog(editor).updateFloor(node);
						}
					}
				});

				/** ACCIONES TOOLBAR */
				editor.addButton( 'wp_floor_image_view_remove', {
					tooltip: 'Remove',
					icon: 'dashicon dashicons-no',
					onclick: function( ) {
						var node = editor.selection.getNode();
						if (  isFloorView( node ) ) {
							node = getFloorNode( node );
							editor.selection.select(node);

							editor.dom.remove( node );
							editor.nodeChanged();
							editor.selection.collapse( true );
							editor.undoManager.add();
						}
					}
				} );

				editor.on('mouseup', function( event ){
					var node = event.target;
					if (  isFloorView( node ) ) {
						node = getFloorNode( node );
						editor.selection.select(node);
					}
				});

				editor.once( 'preinit', function() {
					var toolbar;
					if ( editor.wp && editor.wp._createToolbar ) {
						toolbar = editor.wp._createToolbar( [
							'wp_floor_image_view_edit',
							'wp_floor_image_view_remove'
						] );

						editor.on( 'wptoolbar', function( args ) {
							var node = args.element;
							if (  isFloorView( node ) ) {
								node = getFloorNode( node );
								editor.selection.select(node);

								args.toolbar = toolbar;
							}
						} );
					}
				} );
			});

			return function () {};
		}
	);
	dem('tinymce.plugins.uvigo.floorimage.Plugin')();
})();
