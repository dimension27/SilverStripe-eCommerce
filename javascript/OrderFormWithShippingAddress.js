;(function($) {
	$(document).ready(
		function() {
			OrderFormWithShippingAddress.init();
			OrderFormWithShippingAddress.removeEmailFromShippingCityHack();
		}
	);
	var OrderFormWithShippingAddress = {

		nameSelector: "#FirstName input, #Surname input",

		firstnameSelector: "#FirstName input",

		surnameSelector: "#Surname input",

		shippingNameSelector: "#ShippingName input",

		addressSelector: "#Address input",

		extraAddressSelector: "#AddressLine2 input",

		shippingAddressSelector: "#ShippingAddress input",

		shippingExtraAddressSelector: "#ShippingAddress2 input",

		citySelector: "#City input",

		shippingCitySelector: "#ShippingCity input",

		countrySelector: "#Country select",

		shippingCountrySelector: "#ShippingCountry select",

		postalCodeSelector: "#PostalCode input",

		shippingPostalCode: "#ShippingPostalCode input",

		shippingSectionSelector: "#ShippingFields",

		useShippingDetailsSelector: "input[name='UseShippingAddress']",

		init: function(){
			//hide shipping fields
			jQuery(OrderFormWithShippingAddress.shippingSectionSelector).hide();
			//turn-on shipping details toggle
			jQuery(OrderFormWithShippingAddress.useShippingDetailsSelector).change(
				function(){
					jQuery(OrderFormWithShippingAddress.shippingSectionSelector).slideToggle();
					jQuery(OrderFormWithShippingAddress.shippingNameSelector).focus();
					OrderFormWithShippingAddress.updateFields();
				}
			);
			//update on change
			var originatorFieldSelector =
					OrderFormWithShippingAddress.nameSelector+", "+
					OrderFormWithShippingAddress.addressSelector+" ,"+
					OrderFormWithShippingAddress.extraAddressSelector+", "+
					OrderFormWithShippingAddress.citySelector+", "+
					OrderFormWithShippingAddress.postalCodeSelector;
			jQuery(originatorFieldSelector).change(
				function() {
					OrderFormWithShippingAddress.updateFields();
				}
			);
			//update on focus
			jQuery(originatorFieldSelector).focus(
				function() {
					OrderFormWithShippingAddress.updateFields();
				}
			);
			if(jQuery(OrderFormWithShippingAddress.useShippingDetailsSelector).is(":checked")) {
				jQuery(OrderFormWithShippingAddress.shippingSectionSelector).slideToggle();
				jQuery(OrderFormWithShippingAddress.shippingNameSelector).focus();
				OrderFormWithShippingAddress.updateFields();
			}
		},

		updateFields: function() {
			//postal code
			var PostalCode =  jQuery(OrderFormWithShippingAddress.postalCodeSelector).val();
			var ShippingPostalCode =  jQuery(OrderFormWithShippingAddress.shippingPostalCode).val();
			if(!ShippingPostalCode && PostalCode) {
				jQuery(OrderFormWithShippingAddress.shippingPostalCode).val(PostalCode);
			}

			//country
			var Country = jQuery(OrderFormWithShippingAddress.countrySelector).val();
			var ShippingCountry =  jQuery(OrderFormWithShippingAddress.shippingCountrySelector).val();
			if((!ShippingCountry || ShippingCountry == "AF") && Country) {
				jQuery(OrderFormWithShippingAddress.shippingCountrySelector).val(Country);
			}

			//city
			var City =  jQuery(OrderFormWithShippingAddress.citySelector).val();
			var ShippingCity =  jQuery(OrderFormWithShippingAddress.shippingCitySelector).val();
			if(!ShippingCity && City) {
				jQuery(OrderFormWithShippingAddress.shippingCitySelector).val(City);
			}
			//address
			var Address =  jQuery(OrderFormWithShippingAddress.addressSelector).val();
			var ShippingAddress =  jQuery(OrderFormWithShippingAddress.shippingAddressSelector).val();
			if(!ShippingAddress && Address) {
				jQuery(OrderFormWithShippingAddress.shippingAddressSelector).val(Address);
			}
			//address 2
			var AddressLine2 =  jQuery(OrderFormWithShippingAddress.extraAddressSelector).val();
			var ShippingAddress2 =  jQuery(OrderFormWithShippingAddress.shippingExtraAddressSelector).val();
			if(!ShippingAddress2 && AddressLine2) {
				jQuery(OrderFormWithShippingAddress.shippingExtraAddressSelector).val(AddressLine2);
			}
			//name
			var FirstName =  jQuery(OrderFormWithShippingAddress.firstnameSelector).val();
			var Surname =  jQuery(OrderFormWithShippingAddress.surnameSelector).val();
			var ShippingName =  jQuery(OrderFormWithShippingAddress.shippingNameSelector).val();
			if(!ShippingName || (FirstName == ShippingName && Surname) || (Surname == ShippingName && FirstName)) {
				jQuery(OrderFormWithShippingAddress.shippingNameSelector).val(FirstName+" "+Surname);
			}
		},

		removeEmailFromShippingCityHack: function() {
			//this function exists, because FF was auto-completing Shipping City as the username part of a password / username combination (password being the next field)
			var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
			var shippingCitySelectorValue = jQuery(OrderFormWithShippingAddress.shippingCitySelector).val();
			if(pattern.test(shippingCitySelectorValue)){
				jQuery(OrderFormWithShippingAddress.shippingCitySelector).val(jQuery(OrderFormWithShippingAddress.citySelector).val());
			}
			else{
				//do nothing
			}

		}
	}
})(jQuery);


