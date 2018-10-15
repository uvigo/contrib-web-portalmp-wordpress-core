
/* eslint-disable */

(function() {

  tinymce.PluginManager.add('content_block_button', function(editor) {

    var toolbar;

    function isPDPBlock( node ) {
        return editor.dom.getAttrib( node, 'data-mce-type' ) === 'custom-block';
    }

    function openWindowButton() {

        editor.windowManager.open(
            //  Properties of the window.
            {
                title: 'Bloque de estilo',  //    The title of the dialog window.
                width: 320,                 //    The width of the dialog
                height: 180,                //    The height of the dialog
                inline: 1,                  //    Whether to use modal dialog instead of separate browser window.
                body: [{
                    type: 'listbox',
                    name: 'styleBlock',
                    label: 'Estilo',
                    values: [
                        {
                            value: 'gray',
                            text: 'Gris oscuro'
                        },
                        {
                            value: 'graylight',
                            text: 'Gris claro'
                        },
                        {
                            value: 'primary',
                            text: 'Axul oscuro'
                        },
                        {
                            value: 'secondary',
                            text: 'Azul claro'
                        },
                        {
                            value: 'outlined',
                            text: 'Bordeado'
                        },
                        {
                            value: 'table-of-content',
                            text: 'Tabla de contenidos'
                        },
                    ],
                    onchange: function() {
                        data.title = this.value();
                    }

                }],
                onsubmit: function( e ) {

                    var selectionContent = editor.selection.getContent();
                    var styleBlock = e.data.styleBlock;
                    //  Construct the shortcode
                    var shortcode = '[uvigo_bloque estilo="' + styleBlock + '"]';

                    var content = '';
                    if ( selectionContent ) {
                        // si no tiene etiqueta le añado un párrafo
                        if( selectionContent.search(/^<[^>]*/) === -1 ) {
                            selectionContent = '<p>' + selectionContent + '</p>';
                        }
                        //If text is selected when button is clicked
                        //Wrap shortcode around it.
                        content = shortcode + selectionContent + '[/uvigo_bloque]';
                    } else {
                        content = shortcode + '<p>&nbsp;</p>' + '[/uvigo_bloque]';
                    }

                    editor.insertContent(content);
                }
            }
        );
    }

    function getShortcode( content ) {
        function getAttr( str, name ) {
            name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
            return name ? window.decodeURIComponent( name[1] ) : '';
        }

        //return content.replace( /\s*<div ([^>]+mcePDPBlock[^>]+)>\s*(?:<div [^>]+mceItem[^>]+>)?\s*([\s\S]*?)\s*<\/div>\s*(?:<\/div>)?\s*/gi, function( a, b, c ) {
        return content.replace( /\s*<div ([^>]+mceCustomBlock[^>]+)>\s*([\s\S]*?)\s*(?:<\/div>)\s*/gi, function( a, b, c ) {
            var data = getAttr( b, 'data-custom-block' );

            var toret = '<p>[uvigo_bloque';
            if ( data ) {
                toret += data;
            }
            toret += ']</p>';
            toret += c;
            toret += '<p>[/uvigo_bloque]</p>';

            return toret;
        });
    }

    // Convert shortcode to Html output
    function parseShortcode( content ) {
        return content.replace( /(?:<p>)?\[uvigo_bloque([^\]]*)\](?:<\/p>)?([\s\S]+?)(?:<p>)?\[\/uvigo_bloque\](?:<\/p>)?/g, function( a, b, c) {
        //return content.replace( /\[uvigo_bloque([^\]]*)\]/g, function( match ) {
            //return html( 'pdp-block', match );
            var cls, data, into;

            cls = 'custom-block';

            var re = /estilo="([^"]*)/;
            var m;
            if ((m = re.exec(b)) !== null) {
                if (m.index === re.lastIndex) {
                    re.lastIndex++;
                }
                cls += ' ' + m[1];
            }
            data = window.encodeURIComponent( b );
            /*
            //Clean content searching for tag with class="mceItem"
            into = c.replace( /(?:<[^>]+mceItem[^>]+>)?(.*)(?:<\/p>)/gi, function( all, match ) {
                return '<p>'> + match + '</p>';
            });
            */
            into = c;

            var toret = '<div class="mceCustomBlock ' + cls + '" data-custom-block="' + data + '" data-mce-type="custom-block">';
            //toret += '<div class="mceItem">';
            toret += into;
            //toret += '</div>';
            toret += '</div>';

            return toret;
        });
    }

    editor.addButton('content_block_button', {
        title: 'Insertar bloque de estilos',
        tooltip: 'Insertar bloque de estilos',
        text: '',
        icon: 'icon dashicons-layout',
        onclick: function() {
            openWindowButton( editor );
        }
    });

    editor.on( 'BeforeSetContent', function( event ) {
        if ( event.format !== 'raw' ) {
            event.content = parseShortcode( event.content );
        }
    });

    editor.on( 'PostProcess', function( event ) {
        if ( event.get ) {
            event.content = getShortcode( event.content );
        }
    });

  });

})();
