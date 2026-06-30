// ----------------------------------------------------------------------------
// Pagination Plugin - A jQuery Plugin to paginate content
// v 1.0 Beta
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2010 Rohit Singh Sengar
// http://rohitsengar.cueblocks.net/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------

//------------ initializing all the values needed in paginator. -----------------

	//--- Variables for internal use ----

	var pageElement = Array();

	var paginatorId = '';

	var currentPage = 1; // current page, default 1

	var allItems = 0; // no. of repeating items in the container where paginator is applied

	var lastPage = 1; // last page, default 1

	//--- Attributes that can be changed according to use ---

	var startPage = 1; // start page

	var itemsPerPage = 5; // no. of items you want to show on one page

	var firstPageSymbol = '<<'; // to indicate First Page

	var previousPageSymbol = '<'; // to indicate Previous Page

	var nextPageSymbol = '>'; // to indicate Next Page

	var lastPageSymbol = '>>'; // to indicate Last Page

	var separator = ' | '; // To separate paginator's items

	var paginatorPosition = 'bottom'; // where you want the paginator to be. Accepted values are 'top','bottom','both'

	var paginatorStyle = 1; // To define which style of paginator you need.
	// 1 - for << | < | 1 | 2 | 3 | > | >>
	// 2 - for << | < | 1/8 | > | >>
	// 3 - for < | 1 | 2 | 3 | >
	// 4 - for < | >
    
	var enablePageOfOption = false; // it shows on which are you currently, i.e. Page 3 of 6 Page(s), if turned true
    
	var enableGoToPage = false; // shows a drop down of all pages for go/jump to any page user want to go, if turned true. Useful incase there are large no. of pages
    
    var textGoToPage = 'Go to'; // text for above option. You can change it to 'Jump to Page' or anything you like. The above option needs to turned on for this.
    
	var enableSelectNoItems = false; // if you want to change items per page on the fly.
    
    var textSelectNoItems = 'Items Per Page'; // text for above option. You can change it to 'Change No. of tag/page' or anything you like. The above option needs to turned on for this.

	var paginatorValues = Array(5,10,15,20,25,30); // list of values for above option (enableSelectNoItems).

    var anchorLink = 'javascript:void(0)'; // if you want to change href of the paginator anchor text (links for page) to '#' or to something else. As # is append on the address bar upon clicking I used javascript:void(); which is clean.
    
    var showIfSinglePage = true; // set it tp false if you don't want to show paginator incase there is only one page, true if show paginator even if there is just one page.
	var colSpan = 6;


//-----------functions starts----------------------------------------------------
jQuery.fn.extend({
    pagination: function () {
        paginatorId = this;
        switch (paginatorPosition) {
        case 'top':
            {
                paginatorId.before('<div class="paginator"></div>');
                break
            }
        case 'bottom':
            {
                paginatorId.after('<tr><td colspan="'+colSpan+'" align="center"><div class="paginator"></div></td></tr>');
                break
            }
        case 'both':
            {
                paginatorId.before('<div class="paginator"></div>');
                paginatorId.after('<div class="paginator"></div>');
                break
            }
        default:
            {
                paginatorId.after('<div class="paginator"></div>')
            }
        }
        initPaginator()
    },
    depagination: function () {
        $('.paginator').remove();
        paginatorId.children().show()
    }
});

function initPaginator() {
    if (itemsPerPage < 1) itemsPerPage = 5;
    allItems = paginatorId.children().length;
    if (allItems % itemsPerPage == 0) lastPage = parseInt(allItems / itemsPerPage);
    else lastPage = parseInt(allItems / itemsPerPage) + 1;
    if ((startPage < 1) || (startPage > lastPage)) startPage = 1;
    if (!showIfSinglePage) {
        if (lastPage > 1) appendContent(startPage, 1)
    } else appendContent(startPage, 1)
	//alert("LastPage=>"+lastPage+": StartPage=>"+startPage);
}
function appendContent(a, b) {
    if (a < 0) {
        if (a == -1) a = currentPage - 1;
        else a = currentPage + 1
    }
    currentPage = a;
    till = (currentPage - 1) * itemsPerPage;
    if (!b) {
        paginatorId.fadeOut("medium", function () {
            createPaginator();
            paginatorId.children().hide();
            paginatorId.children().slice(till, itemsPerPage + till).show();
            paginatorId.fadeIn("medium")
        })
    } else {
        createPaginator();
        paginatorId.children().hide();
        paginatorId.children().slice(till, itemsPerPage + till).show()
    }
}
function createPaginator() {
    $(".paginator").html("");
    var a = '';
    var b = '';
    var c = '';
    var d = '';
    var e = ' Page ' + currentPage + ' of ' + lastPage + ' Page(s) ';
    var f = ' ' + textGoToPage + ' <select onchange="appendContent(this.value);" >';
    var g = ' ' + textSelectNoItems + ' <select onchange="itemsPerPage=Number(this.value);initPaginator();" >';
    for (var i = 0; i < paginatorValues.length; i++) {
        if (itemsPerPage == paginatorValues[i]) g += '<option value="' + paginatorValues[i] + '" selected="selected">' + paginatorValues[i] + '</option>';
        else g += '<option value="' + paginatorValues[i] + '">' + paginatorValues[i] + '</option>'
    }
    g += '</select>';
    if (currentPage == 1) {
        style = '<a href="' + anchorLink + '" class="inactive" title="First Page">' + firstPageSymbol + '</a>' + separator;
        a = b = style;
        style = '<a href="' + anchorLink + '" class="inactive" title="Previous Page">' + previousPageSymbol + '</a>' + separator;
        a += style;
        b += style;
        c += style;
        d += style
    } else {
        style = '<a href="' + anchorLink + '" class="active" onclick="appendContent(1);" title="First Page">' + firstPageSymbol + '</a>' + separator;
        a = b = style;
        style = '<a href="' + anchorLink + '" class="active" onclick="appendContent(-1);" title="Previous Page">' + previousPageSymbol + '</a>' + separator;
        a += style;
        b += style;
        c += style;
        d += style
    }
    for (var i = 1; i <= lastPage; i++) {
        if (i == currentPage) {
            a += '<a href="' + anchorLink + '" class="inactive" title="Page ' + i + '">' + i + '</a>' + separator;
            b += '<a href="' + anchorLink + '" class="inactive" title="Page ' + i + '">' + i + '/' + lastPage + '</a>' + separator;
            c += '<a href="' + anchorLink + '" class="inactive" title="Page ' + i + '">' + i + '</a>' + separator;
            f += '<option value="' + i + '" selected="selected">' + i + '</option>'
        } else {
            style = '<a href="' + anchorLink + '" class="active" onclick="appendContent(' + i + ');" title="Page ' + i + '">' + i + '</a>' + separator;
            a += style;
            c += style;
            f += '<option value="' + i + '">' + i + '</option>'
        }
    }
    f += '</select>';
    if (currentPage == lastPage) {
        style = '<a href="' + anchorLink + '" class="inactive" title="Next Page">' + nextPageSymbol + '</a>';
        a += style;
        b += style;
        c += style;
        d += style;
        style = separator + '<a href="' + anchorLink + '" class="inactive" title="Last Page">' + lastPageSymbol + '</a>';
        a += style;
        b += style
    } else {
        style = '<a href="' + anchorLink + '" class="active" onclick="appendContent(-2);" title="Next Page">' + nextPageSymbol + '</a>';
        a += style;
        b += style;
        c += style;
        d += style;
        style = separator + '<a href="' + anchorLink + '" class="active" onclick="appendContent(' + lastPage + ');" title="Last Page">' + lastPageSymbol + '</a>';
        a += style;
        b += style
    }
    switch (paginatorStyle) {
    case 1:
        style = a;
        break;
    case 2:
        style = b;
        break;
    case 3:
        style = c;
        break;
    case 4:
        style = d;
        break;
    default:
        style = a
    }
    if (enablePageOfOption) style += '<span class="inactive" title="Page Information">' + e + '</span>';
    if (enableGoToPage) style += '<span class="inactive" title="Select Page">' + f + '</span>';
    if (enableSelectNoItems) style += '<span class="inactive" title="Select no. of items per page">' + g + '</span>';
    $(".paginator").html(style)
}