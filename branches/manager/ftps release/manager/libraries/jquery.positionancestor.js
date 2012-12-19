jQuery.fn.positionAncestor = function(selector) {
    var left = 0;
    var top = 0;
    this.each(function(index, element) {
        // check if current element has an ancestor matching a selector
        // and that ancestor is positioned
        var $ancestor = $(this).closest(selector);
        if ($ancestor.length && $ancestor.css("position") !== "static") {
            var $child = $(this);
            var childMarginEdgeLeft = $child.offset().left - parseInt($child.css("marginLeft"), 10);
            var childMarginEdgeTop = $child.offset().top - parseInt($child.css("marginTop"), 10);
            
            var borderWidth = parseInt($ancestor.css("borderLeftWidth"), 10);
            if (isNaN(borderWidth)) borderWidth = 0;
            var ancestorPaddingEdgeLeft = $ancestor.offset().left + borderWidth;

            var borderWidth = parseInt($ancestor.css("borderTopWidth"), 10);
            if (isNaN(borderWidth)) borderWidth = 0;
            var ancestorPaddingEdgeTop = $ancestor.offset().top + borderWidth;
            left = childMarginEdgeLeft - ancestorPaddingEdgeLeft;
            top = childMarginEdgeTop - ancestorPaddingEdgeTop;
            
            // we have found the ancestor and computed the position
            // stop iterating
            return false;
        }
    });
    return {
        left:    left,
        top:    top
    }
};