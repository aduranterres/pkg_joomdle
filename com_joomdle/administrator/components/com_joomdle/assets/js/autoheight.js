function itspower( ifRef, setW, setH, fMargin, min_height ) {
    ifRef.height = 1;
    var ifDoc, margin = typeof fMargin === 'number' ? fMargin : 16, h, w, sTop, sLeft;
     
    try {
        ifDoc = ifRef.contentWindow.document.documentElement;
     }
 
     catch( e ){ ifDoc = null; }

     if( ifDoc )    {
          sLeft = document.body.scrollLeft + document.documentElement.scrollLeft;
          sTop = document.body.scrollTop + document.documentElement.scrollTop;

          if( setH )  {
               h = ifDoc.scrollHeight;

               // For PDF rendering for example
               if (min_height > 0)
               {
                   if (h < min_height)
                       h = min_height;
               }

               ifRef.height = 1;
               ifRef.height = h + margin;
          }
        
          if( setW ) {
               w = ifDoc.scrollWidth;
               ifRef.width = 1;
               ifRef.width = w + margin;
          }
     }

     window.scrollTo( sLeft, sTop );
}
