(()=>{"use strict";function t(t,e){for(var a=0;a<e.length;a++){var i=e[a];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,i.key,i)}}var e=function(){function e(){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,e)}var a,i;return a=e,(i=[{key:"init",value:function(){$('[data-slider="owl"] .owl-carousel').each((function(t,e){var a,i,n,o,s,l,d=$(e).parent();"true"===d.data("single-item")?(a=1,i=1,n=1,o=1,s=1,l=1):(a=d.data("items"),i=[1199,d.data("desktop-items")?d.data("desktop-items"):a],n=[979,d.data("desktop-small-items")?d.data("desktop-small-items"):3],o=[768,d.data("tablet-items")?d.data("tablet-items"):2],l=[479,d.data("mobile-items")?d.data("mobile-items"):1]),$(e).owlCarousel({items:a,itemsDesktop:i,itemsDesktopSmall:n,itemsTablet:o,itemsTabletSmall:s,itemsMobile:l,navigation:!!d.data("navigation"),navigationText:!1,slideSpeed:d.data("slide-speed"),paginationSpeed:d.data("pagination-speed"),singleItem:!!d.data("single-item"),autoPlay:d.data("auto-play")})}))}}])&&t(a.prototype,i),Object.defineProperty(a,"prototype",{writable:!1}),e}();$(document).ready((function(){(new e).init()}))})();
