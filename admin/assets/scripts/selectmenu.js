/*eslint-disable */
/*eslint-enable */

export default {
  init() {
    $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
      _renderItem: function( ul, item ) {
        var li = $( "<li>" ),
          wrapper = $( "<div>", { text: item.label } );

        if ( item.disabled ) {
          li.addClass( "ui-state-disabled" );
        }

        $( "<span>", {
          style: item.element.attr( "data-style" ),
          "class": item.element.attr( "data-class" ),
        })
        .appendTo( wrapper );

        return li.append( wrapper ).appendTo( ul );
      },
    });

    // $( 'select.iconselectmenu' )
    //   .iconselectmenu()
    //   .iconselectmenu( 'menuWidget' )
    //   .addClass( 'iconselectmenu-widget' );
  },
};
