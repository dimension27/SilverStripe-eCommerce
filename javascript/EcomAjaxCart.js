/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: adds ajax functionality to page
 *we have three options:
 * * addLinks (click to add to cart)
 * * delete links (remove from cart)
 * * and remove from cart (cart is expected to be as <li>......<a href="">click to remove</a>, with a tag being a direct child of li, and li holding item
 **/



;(function($) {
	$(document).ready(
		function() {
			//This needs testing...
			//EcomAjaxCart.init("body");
		}
	);
})(jQuery);

var EcomAjaxCart = {

	ConfirmDeleteText: 'Are you sure you would like to remove this item from your cart?',

	LoadingText: "updating cart ... ",

	LoadingClass: "loadingCartData",

	ajaxAddRemoveLinkSelector: ".ajaxAddToCartLink",

	showClass: "show",

	doNotShowClass: "doNotShow",

	addLinkSelector: ".ajaxAdd",

	removeLinkSelector: ".ajaxRemove",

	removeCartSelector: ".removeFromCart",

	InCartText: "In Cart",

	cartHolderSelector: "#CartHolder",

	UnconfirmedDelete: false,

	init: function(element) {
		jQuery(element).addAddLinks();
		jQuery(element).addRemoveLinks();
		jQuery(element).addCartRemove();
	},

	set_LoadingText: function(v) {
		this.LoadingText = v;
	},

	set_InCartText: function(v) {
		this.InCartText = v;
	},

	set_ConfirmDeleteText: function(v) {
		this.ConfirmDeleteText = v;
	},

	loadAjax: function( url, el ) {
		jQuery(EcomAjaxCart.cartHolderSelector).html('<span class="'+EcomAjaxCart.LoadingClass+'">'+EcomAjaxCart.LoadingText+'</span>');
		jQuery(el).addClass(EcomAjaxCart.LoadingClass);
		var clickedElement = el;
		jQuery.get(
			url,
			{},
			function(data) {
				jQuery(EcomAjaxCart.cartHolderSelector).html(data);
				jQuery(EcomAjaxCart.cartHolderSelector).addCartRemove();
				jQuery(clickedElement).removeClass(EcomAjaxCart.LoadingClass);
				jQuery(clickedElement).addClass(EcomAjaxCart.doNotShowClass).removeClass(EcomAjaxCart.showClass);
				jQuery(clickedElement).next("."+EcomAjaxCart.doNotShowClass).addClass(EcomAjaxCart.showClass).removeClass(EcomAjaxCart.doNotShowClass);
				jQuery(clickedElement).prev("."+EcomAjaxCart.doNotShowClass).addClass(EcomAjaxCart.showClass).removeClass(EcomAjaxCart.doNotShowClass);
			}
		);
		return true;
	}

}


jQuery.fn.extend(
	{
		addAddLinks: function() {
			jQuery(this).find(EcomAjaxCart.addLinkSelector).click(
				function(){
					var url = jQuery(this).attr("href");
					EcomAjaxCart.loadAjax(url, this);
					return false;
				}
			);
		},

		addCartRemove: function () {
			jQuery(this).find(EcomAjaxCart.removeCartSelector).click(
				function(){
					if(EcomAjaxCart.UnconfirmedDelete || confirm(EcomAjaxCart.ConfirmDeleteText)) {
						var url = jQuery(this).attr("href");
						var el = this;//we need this to retain link to element (this shifts focus)
						jQuery(el).parent("li").css("text-decoration", "line-through");
						jQuery.get(url, function(){ jQuery(el).parent("li").fadeOut();});
					}
					return false;
				}
			);
		},

		addRemoveLinks: function () {
			jQuery(this).find(EcomAjaxCart.removeLinkSelector).click(
				function(){
					if(EcomAjaxCart.UnconfirmedDelete || confirm(EcomAjaxCart.ConfirmDeleteText)) {
						var url = jQuery(this).attr("href");
						EcomAjaxCart.loadAjax(url, this);
					}
					return false;
				}
			);
		}

	}
);



