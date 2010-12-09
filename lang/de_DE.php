<?php

/**
 * German (Germany) language pack
 * @package modules: ecommerce
 * @subpackage i18n
 */

i18n::include_locale_file('modules: ecommerce', 'en_US');

global $lang;

if(array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
	$lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
	$lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['General']['DATEFORMATNICE'] = '%e. %B %G';
$lang['de_DE']['General']['DATETIMEFORMATNICE'] = '%e. %B %G %H:%M';
$lang['de_DE']['General']['DATETIMEFORMATNICE24'] = 'd.m.Y H:i';
$lang['de_DE']['AccountPage']['Message'] = 'Sie müssen sich einloggen um auf Ihr Konto zugreifen zu können. Falls Sie nicht registriert sind, können Sie erst nach Ihrer ersten Bestellung auf Ihr Konto zugreifen. Fall Sie bereits registriert sind, geben Sie folgend Ihre Daten ein.';
$lang['de_DE']['AccountPage']['NOPAGE'] = 'Keine AccountPage auf dieser Website - erstellen Sie bitte eine!';
$lang['de_DE']['AccountPage.ss']['COMPLETED'] = 'Abgeschlossene Bestellungen';
$lang['de_DE']['AccountPage.ss']['HISTORY'] = 'Ihr Bestellhistorie';
$lang['de_DE']['AccountPage.ss']['INCOMPLETE'] = 'Offene Bestellungen';
$lang['de_DE']['AccountPage.ss']['Message'] = 'Bitte gebe Sie Ihre Daten ein, um sich zur Konto-Verwaltung einzuloggen.<br />Diese Seite ist nur nach der ersten Bestellung aufrufbar, wenn man ein Passwort vergibt.';
$lang['de_DE']['AccountPage.ss']['NOCOMPLETED'] = 'Es konnten keine abgeschlossenen Bestellungen gefunden werden.';
$lang['de_DE']['AccountPage.ss']['NOINCOMPLETE'] = 'Es konnten keine offenen Bestellungen gefunden werden.';
$lang['de_DE']['AccountPage.ss']['ORDER'] = 'Bestellung Nr.';
$lang['de_DE']['AccountPage.ss']['PRINTORDER'] = 'Bestellung drucken';
$lang['de_DE']['AccountPage.ss']['STATUS'] = 'Status';
$lang['de_DE']['AccountPage.ss']['READMORE'] = 'Zur Detail-Ansicht der Bestellung #%s';
$lang['de_DE']['AccountPage_order.ss']['ADDRESS'] = 'Adresse';
$lang['de_DE']['AccountPage_order.ss']['AMOUNT'] = 'Menge';
$lang['de_DE']['AccountPage_order.ss']['BACKTOCHECKOUT'] = 'Klicken Sie hier um zur Kasse zu gelangen';
$lang['de_DE']['AccountPage_order.ss']['BACKTOACCOUNT'] = 'Zurück zu Ihrer Kunden-Konto Übersicht';
$lang['de_DE']['AccountPage_order.ss']['BILLINGADDRESS'] = 'Rechnungsadresse';
$lang['de_DE']['AccountPage_order.ss']['SHIPPINGADDRESS'] = 'Lieferadresse';
$lang['de_DE']['AccountPage_order.ss']['CITY'] = 'Stadt';
$lang['de_DE']['AccountPage_order.ss']['COUNTRY'] = 'Land';
$lang['de_DE']['AccountPage_order.ss']['CONTENT'] = 'Inhalt';
$lang['de_DE']['AccountPage_order.ss']['DATE'] = 'Datum';
$lang['de_DE']['AccountPage_order.ss']['DETAILS'] = 'Details';
$lang['de_DE']['AccountPage_order.ss']['EMAILDETAILS'] = 'Zur Bestätigung Ihrer Bestellung wurde eine Kopie an Ihre E-Mail Adresse geschickt.';
$lang['de_DE']['AccountPage_order.ss']['NAME'] = 'Name';
$lang['de_DE']['AccountPage_order.ss']['PAYMENT'] = 'Zahlart';
$lang['de_DE']['AccountPage_order.ss']['PAYMENTMETHOD'] = 'Bezahlmethode';
$lang['de_DE']['AccountPage_order.ss']['PAYMENTSTATUS'] = 'Status der Zahlung';
$lang['de_DE']['Cart.ss']['ADDONE'] = 'Hinzufügen eine oder mehr von  &quot;%s&quot;  in den Warenkorb';
$lang['de_DE']['Cart.ss']['CheckoutClick'] = 'Hier klicken um auszuchecken';
$lang['de_DE']['Cart.ss']['CheckoutGoTo'] = 'Zur Kasse';
$lang['de_DE']['Cart.ss']['HEADLINE'] = 'Mein Warenkorb';
$lang['de_DE']['Cart.ss']['NOITEMS'] = 'In Ihrem Warenkorb befinden sich zur Zeit keine Artikel';
$lang['de_DE']['Cart.ss']['PRICE'] = 'Preis';
$lang['de_DE']['Cart.ss']['READMORE'] = 'Erfahren Sie hier mehr über &quot;%s&quot;';
$lang['de_DE']['Cart.ss']['Remove'] = '&quot;%s&quot; aus dem Warenkorb entfernen';
$lang['de_DE']['Cart.ss']['REMOVE'] = '&quot;%s&quot; aus Ihrem Warenkorb entfernen';
$lang['de_DE']['Cart.ss']['REMOVEALL'] = 'Entfernen alle von &quot;%s&quot; von dem Warenkorb';
$lang['de_DE']['Cart.ss']['RemoveAlt'] = 'entfernen';
$lang['de_DE']['Cart.ss']['REMOVEONE'] = 'Entfernen Sie eines von &quot;%s&quot; aus Ihrem Warenkorb';
$lang['de_DE']['Cart.ss']['SHIPPING'] = 'Versandkosten';
$lang['de_DE']['Cart.ss']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['Cart.ss']['TOTAL'] = 'Summe';
$lang['de_DE']['CheckoutPage']['NOPAGE'] = 'Auf dieser Site existiert keine Seite zum Ausschecken - bitte erstellen Sie eine neue Seite!';
$lang['de_DE']['CheckoutPage.ss']['CHECKOUT'] = 'Kasse';
$lang['de_DE']['CheckoutPage.ss']['ORDERSTATUS'] = 'Bestellstatus';
$lang['de_DE']['CheckoutPage.ss']['PROCESS'] = 'Prozess';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['BACKTOCHECKOUT'] = 'Klicken Sie hier um zur Kasse zurückzukehren';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['CHECKOUT'] = 'Kasse';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['CHEQUEINSTRUCTIONS'] = 'Falls Sie die Bezahlung per Scheck gewählt haben erhalten Sie eine E-Mail mit weiteren Details zur Abwicklung.';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['DETAILSSUBMITTED'] = 'Hier sind Ihre übermittelten Details';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['INCOMPLETE'] = 'Bestellung nicht vollständig';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['ORDERSTATUS'] = 'Bestellstatus';
$lang['de_DE']['CheckoutPage_OrderIncomplete.ss']['PROCESS'] = 'Prozess';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['BACKTOCHECKOUT'] = 'Klicken Sie hier um zur Kasse zurückzukehren';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['CHECKOUT'] = 'Auschecken';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['EMAILDETAILS'] = 'Zur Bestätigung wurde eine Kopie an Ihre E-Mail Adresse verschickt';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['ORDERSTATUS'] = 'Bestellstatus';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['PROCESS'] = 'Prozess';
$lang['de_DE']['CheckoutPage_OrderSuccessful.ss']['SUCCESSFULl'] = 'Bestellung erfolgreich durchgeführt';
$lang['de_DE']['ChequePayment']['MESSAGE'] = 'Bezahlung per Scheck (Vorkasse). Bitte beachten: Der Versand der Produkte erfolgt erst nach Zahlungseingang.';
$lang['de_DE']['DataReport']['EXPORTCSV'] = 'Export zu CSV';
$lang['de_DE']['EcommerceRole']['PERSONALINFORMATION'] = 'Ihre Daten';
$lang['de_DE']['EcommerceRole']['COUNTRY'] = 'Land';
$lang['de_DE']['EcommerceRole']['FIRSTNAME'] = 'Vorname';
$lang['de_DE']['EcommerceRole']['SURNAME'] = 'Nachname';
$lang['de_DE']['EcommerceRole']['HOMEPHONE'] = 'Tel.';
$lang['de_DE']['EcommerceRole']['MOBILEPHONE'] = 'Mobil';
$lang['de_DE']['EcommerceRole']['EMAIL'] = 'Email';
$lang['de_DE']['EcommerceRole']['ADDRESS'] = 'Adresse';
$lang['de_DE']['EcommerceRole']['ADDRESSLINE2'] = '&nbsp;';
$lang['de_DE']['EcommerceRole']['CITY'] = 'Stadt';
$lang['de_DE']['EcommerceRole']['POSTALCODE'] = 'PLZ';
$lang['de_DE']['EcomQuantityField.ss']['ADDONE'] = '1 &quot;%s&quot; zum Warenkorb hinzufügen';
$lang['de_DE']['EcomQuantityField.ss']['REMOVEONE'] = '1 &quot;%s&quot; aus dem Warenkorb entfernen';
$lang['de_DE']['FindOrderReport']['DATERANGE'] = 'Zeitraum';
$lang['de_DE']['MemberForm']['DETAILSSAVED'] = 'Ihre Daten wurden gespeichert';
$lang['de_DE']['MemberForm']['LOGGEDIN'] = 'Sie sind angemeldet als ';
$lang['de_DE']['MemberForm']['LOGOUT'] = 'Klicken Sie <a href="Security/logout" title="Klicken Sie hier um sich abzumelden">hier</a> um sich abzumelden.';
$lang['de_DE']['MemberForm']['LOGINDETAILS'] = 'Konto Details';
$lang['de_DE']['MemberForm']['PASSWORD'] = 'Passwort';
$lang['de_DE']['MemberForm']['SAVE'] = 'Speichern';
$lang['de_DE']['MemberForm']['SAVEANDPROCEED'] = 'Speichern und Bestellung abschließen';
$lang['de_DE']['Order']['INCOMPLETE'] = 'Bestellung unvollständig';
$lang['de_DE']['Order']['SUCCESSFULL'] = 'Bestellung Erfolgreich';
$lang['de_DE']['OrderInformation.ss']['ADDRESS'] = 'Adresse';
$lang['de_DE']['OrderInformation.ss']['AMOUNT'] = 'Betrag';
$lang['de_DE']['OrderInformation.ss']['BUYERSADDRESS'] = 'Käuferadresse';
$lang['de_DE']['OrderInformation.ss']['CITY'] = 'Stadt';
$lang['de_DE']['OrderInformation.ss']['COUNTRY'] = 'Land';
$lang['de_DE']['OrderInformation.ss']['CUSTOMERDETAILS'] = 'Kundendetails';
$lang['de_DE']['OrderInformation.ss']['DATE'] = 'Datum';
$lang['de_DE']['OrderInformation.ss']['DETAILS'] = 'Details';
$lang['de_DE']['OrderInformation.ss']['EMAIL'] = 'E-Mail';
$lang['de_DE']['OrderInformation.ss']['MOBILE'] = 'Handy';
$lang['de_DE']['OrderInformation.ss']['NAME'] = 'Name';
$lang['de_DE']['OrderInformation.ss']['ORDERSUMMARY'] = 'Bestellübersicht';
$lang['de_DE']['OrderInformation.ss']['PAYMENTID'] = 'Zahlungs ID';
$lang['de_DE']['OrderInformation.ss']['PAYMENTINFORMATION'] = 'Zahlungsinformationen';
$lang['de_DE']['OrderInformation.ss']['PAYMENTMETHOD'] = 'Methode';
$lang['de_DE']['OrderInformation.ss']['PAYMENTSTATUS'] = 'Bezahlstatus';
$lang['de_DE']['OrderInformation.ss']['PHONE'] = 'Telefon';
$lang['de_DE']['OrderInformation.ss']['PRICE'] = 'Preis';
$lang['de_DE']['OrderInformation.ss']['PRODUCT'] = 'Produkt';
$lang['de_DE']['OrderInformation.ss']['QUANTITY'] = 'Menge';
$lang['de_DE']['OrderInformation.ss']['READMORE'] = 'Klicken sie hier um mehr über &quot;%s&quot; zu lesen';
$lang['de_DE']['OrderInformation.ss']['SHIPPING'] = 'Versandkosten';
$lang['de_DE']['OrderInformation.ss']['SHIPPINGDETAILS'] = 'Lieferung Details';
$lang['de_DE']['OrderInformation.ss']['SHIPPINGTO'] = 'an';
$lang['de_DE']['OrderInformation.ss']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['OrderInformation.ss']['TABLESUMMARY'] = 'Hier werden alle Artikel im Warenkorb und eine Zusammenfassung aller für die Bestellung anfallender Gebühren angezeigt. Außerdem wird ein Überblick aller Zahlungsmöglichkeiten angezeigt.';
$lang['de_DE']['OrderInformation.ss']['TOTAL'] = 'Gesamt';
$lang['de_DE']['OrderInformation.ss']['TOTALl'] = 'Gesamt';
$lang['de_DE']['OrderInformation.ss']['TOTALOUTSTANDING'] = 'Gesamt ausstehend';
$lang['de_DE']['OrderInformation.ss']['TOTALPRICE'] = 'Gesamtpreis';
$lang['de_DE']['OrderInformation_NoPricing.ss']['ADDRESS'] = 'Adresse';
$lang['de_DE']['OrderInformation_NoPricing.ss']['BUYERSADDRESS'] = 'Käuferadresse';
$lang['de_DE']['OrderInformation_NoPricing.ss']['CITY'] = 'Stadt';
$lang['de_DE']['OrderInformation_NoPricing.ss']['COUNTRY'] = 'Land';
$lang['de_DE']['OrderInformation_NoPricing.ss']['CUSTOMERDETAILS'] = 'Kundendetails';
$lang['de_DE']['OrderInformation_NoPricing.ss']['EMAIL'] = 'E-Mail';
$lang['de_DE']['OrderInformation_NoPricing.ss']['MOBILE'] = 'Handy';
$lang['de_DE']['OrderInformation_NoPricing.ss']['NAME'] = 'Name';
$lang['de_DE']['OrderInformation_NoPricing.ss']['ORDERINFO'] = 'Informationen für Bestellung Nr.';
$lang['de_DE']['OrderInformation_NoPricing.ss']['PHONE'] = 'Telefon';
$lang['de_DE']['OrderInformation_NoPricing.ss']['TABLESUMMARY'] = 'Hier werden alle Artikel im Warenkorb und eine Zusammenfassung aller für die Bestellung anfallender Gebühren angezeigt. Außerdem wird ein Überblick aller Zahlungsmöglichkeiten angezeigt.';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['DESCRIPTION'] = 'Beschreibung';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['ITEM'] = 'Artikel';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['ORDERDATE'] = 'Bestelldatum';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['ORDERNUMBER'] = 'Bestellnummer';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['PAGETITLE'] = 'Shopbestellungen drucken';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['QUANTITY'] = 'Menge';
$lang['de_DE']['OrderInformation_PackingSlip.ss']['TABLESUMMARY'] = 'Hier werden alle Artikel im Warenkorb und eine Zusammenfassung aller für die Bestellung anfallender Gebühren angezeigt. Außerdem wird ein Überblick aller Zahlungsmöglichkeiten angezeigt.';
$lang['de_DE']['OrderInformation_Print.ss']['PAGETITLE'] = 'Bestellungen drucken';
$lang['de_DE']['OrderReport']['CHANGESTATUS'] = 'Lieferstatus ändern';
$lang['de_DE']['OrderReport']['NOTEEMAIL'] = 'Notiz/E-Mail';
$lang['de_DE']['OrderReport']['PRINTEACHORDER'] = 'Alle angezeigten Bestellungen drucken';
$lang['de_DE']['OrderReport']['SENDNOTETO'] = 'Diese Nachricht senden an %s (%s)';
$lang['de_DE']['Order_Content.ss']['NOITEMS'] = 'Ihre Bestellung weist <strong>keine</strong> Artikel auf';
$lang['de_DE']['Order_Content.ss']['PRICE'] = 'Preis';
$lang['de_DE']['Order_Content.ss']['PRODUCT'] = 'Produkt';
$lang['de_DE']['Order_Content.ss']['QUANTITY'] = 'Menge';
$lang['de_DE']['Order_Content.ss']['READMORE'] = 'Wenn zu mehr über &quot;%s&quot; erfahren willst, klick hier';
$lang['de_DE']['Order_Content.ss']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['Order_Content.ss']['TOTAL'] = 'Gesamt';
$lang['de_DE']['Order_Content.ss']['TOTALOUTSTANDING'] = 'Gesamt ausstehend';
$lang['de_DE']['Order_Content.ss']['TOTALPRICE'] = 'Gesamtpreis';
$lang['de_DE']['Order_Content_Editable.ss']['NOITEMS'] = 'Es sind <strong>keine</strong> Artikel in Ihrem Warenkorb';
$lang['de_DE']['Order_Content_Editable.ss']['ORDERINFORMATION'] = 'Bestellinformationen';
$lang['de_DE']['Order_Content_Editable.ss']['PRICE'] = 'Preis';
$lang['de_DE']['Order_Content_Editable.ss']['PRODUCT'] = 'Produkt';
$lang['de_DE']['Order_Content_Editable.ss']['QUANTITY'] = 'Menge';
$lang['de_DE']['Order_Content_Editable.ss']['READMORE'] = 'Erfahren Sie hier mehr über &quot;%s&quot;';
$lang['de_DE']['Order_Content_Editable.ss']['REMOVE'] = '&quot;%s&quot; aus Ihrem Warenkorb entfernen';
$lang['de_DE']['Order_Content_Editable.ss']['REMOVEALL'] = '&quot;%s&quot; komplett aus dem Warenkorb entfernen';
$lang['de_DE']['Order_Content_Editable.ss']['SHIPPING'] = 'Versandkosten';
$lang['de_DE']['Order_Content_Editable.ss']['SHIPPINGTO'] = 'an';
$lang['de_DE']['Order_Content_Editable.ss']['SUBTOTAL'] = 'Zwischensumme';
$lang['de_DE']['Order_Content_Editable.ss']['TABLESUMMARY'] = 'Hier werden alle Artikel im Warenkorb und eine Zusammenfassung aller für die Bestellung anfallender Gebühren angezeigt. Außerdem wird ein Überblick aller Zahlungsmöglichkeiten angezeigt.';
$lang['de_DE']['Order_Content_Editable.ss']['TOTAL'] = 'Gesamt';
$lang['de_DE']['Order_Content_Editable.ss']['TOTALPRICE'] = 'Gesamtpreis';
$lang['de_DE']['Order_Member.ss']['ADDRESS'] = 'Adresse';
$lang['de_DE']['Order_Member.ss']['CITY'] = 'Stadt';
$lang['de_DE']['Order_Member.ss']['COUNTRY'] = 'Land';
$lang['de_DE']['Order_Member.ss']['EMAIL'] = 'E-Mail';
$lang['de_DE']['Order_Member.ss']['MOBILE'] = 'Handy';
$lang['de_DE']['Order_Member.ss']['NAME'] = 'Name';
$lang['de_DE']['Order_Member.ss']['PHONE'] = 'Telefon';
$lang['de_DE']['Order_Payments.ss']['PAYMENTID'] = 'Zahlungs ID';
$lang['de_DE']['Order_Payments.ss']['PAYMENTINFORMATION'] = 'Zahlungsinformationen';
$lang['de_DE']['Order_Payments.ss']['PAYMENTMETHOD'] = 'Methode';
$lang['de_DE']['Order_Payments.ss']['PAYMENTSTATUS'] = 'Bezahlstatus';
$lang['de_DE']['Order_Payments.ss']['PAYMENTS'] = 'Zahlart';
$lang['de_DE']['Order_Payments.ss']['PAYMENTNOTE'] = 'Anmerkung';
$lang['de_DE']['Order_Payments.ss']['DATE'] = 'Datum';
$lang['de_DE']['Order_Payments.ss']['AMOUNT'] = 'Betrag';
$lang['de_DE']['Order_Shipping.ss']['TO'] = 'An';
$lang['de_DE']['Order_Shipping.ss']['SHIPTO'] = 'Lieferadresse (falls abweichend)';
$lang['de_DE']['OrderForm']['useDifferentShippingAddress'] = 'andere Lieferadresse wählen';
$lang['de_DE']['OrderForm']['processOrder'] = 'Bestellung ausführen';
$lang['de_DE']['OrderForm']['MembershipDetails'] = 'Kunden-Konto Details';
$lang['de_DE']['OrderForm']['Password'] = 'Passwort';
$lang['de_DE']['OrderForm']['AccountInfo'] = 'Bitte wählen Sie ein Passwort, damit Sie sich zukünftig einloggen können und Ihre Bestellhistorie anschauen können.';
$lang['de_DE']['OrderForm']['MemberInfo'] = 'Haben Sie bereits ein Kunden-Konto?';
$lang['de_DE']['OrderForm']['LogIn'] = 'Loggen Sie sich ein.';
$lang['de_DE']['OrderForm']['Country'] = 'Land';
$lang['de_DE']['OrderForm']['Name'] = 'Name';
$lang['de_DE']['OrderForm']['City'] = 'Stadt';
$lang['de_DE']['OrderForm']['Address'] = 'Adresse';
$lang['de_DE']['OrderForm']['Address2'] = '&nbsp;';
$lang['de_DE']['OrderForm']['SendGoodsToDifferentAddress'] = 'Abweichende Lieferadresse';
$lang['de_DE']['OrderForm']['ShippingNote'] = 'Die Bestellung wird an folgende Adresse versendet.';
$lang['de_DE']['OrderForm']['Help'] = 'Sie können dies für Geschenke benutzen. Es werden keine Rechnungsinformationen mit versandt.';
$lang['de_DE']['OrderForm']['UseBillingAddress'] = 'An Rechnungsadresse versenden.';
$lang['de_DE']['OrderForm']['NoItemsInCart'] = 'Sie haben keine Produkte ausgewählt. Bitte legen Sie Produkte in den Warenkorb.';
$lang['de_DE']['Order_receiptEmail.ss']['HEADLINE'] = 'Auftragsbearbeitung';
$lang['de_DE']['Order_ReceiptEmail.ss']['HEADLINE'] = 'Auftragsbestätigung';
$lang['de_DE']['Order_receiptEmail.ss']['TITLE'] = 'Shop Eingang';
$lang['de_DE']['Order_ReceiptEmail.ss']['TITLE'] = 'Shop Empfangsbestätigung';
$lang['de_DE']['Order_statusEmail.ss']['HEADLINE'] = 'Shop-Status ändern';
$lang['de_DE']['Order_StatusEmail.ss']['HEADLINE'] = 'Shop-Status Änderung';
$lang['de_DE']['Order_statusEmail.ss']['STATUSCHANGE'] = 'Status geändert auf  "%s" für die Bestellung Nr.';
$lang['de_DE']['Order_StatusEmail.ss']['STATUSCHANGE'] = 'Status geändert zu "%s" für Bestellung Nr.';
$lang['de_DE']['Order_statusEmail.ss']['TITLE'] = 'Shop-Status ändern';
$lang['de_DE']['Order_StatusEmail.ss']['TITLE'] = 'Shop-Status Änderung';
$lang['de_DE']['Payment']['Incomplete'] = 'Unvollständig';
$lang['de_DE']['Payment']['Success'] = 'Erfolg';
$lang['de_DE']['Payment']['Failure'] = 'Misserfolg';
$lang['de_DE']['Payment']['Pending'] = 'Warteschleife';
$lang['de_DE']['Payment']['Paid'] = 'Bezahlt';
$lang['de_DE']['Product.ss']['ADD'] = '&quot;%s&quot; zjm Warenkorb hinzufügen';
$lang['de_DE']['Product.ss']['ADDLINK'] = 'Diesen Artikel zum Warenkorb hinzufügen';
$lang['de_DE']['Product.ss']['ADDONE'] = '&quot;%s&quot; zum Warenkorb hinzufügen';
$lang['de_DE']['Product.ss']['AUTHOR'] = 'Autor';
$lang['de_DE']['Product.ss']['FEATURED'] = 'Wir empfehlen diesen Artikel.';
$lang['de_DE']['Product.ss']['GOTOCHECKOUT'] = 'Jetzt zur Kasse gehen';
$lang['de_DE']['Product.ss']['GOTOCHECKOUTLINK'] = '&raquo; Zur Kasse';
$lang['de_DE']['Product.ss']['IMAGE'] = '%s Bild';
$lang['de_DE']['Product.ss']['ItemID'] = 'Artikel Nr.';
$lang['de_DE']['Product.ss']['NOIMAGE'] = 'Keine Produktabbildung vorhanden für &quot;%s&quot;';
$lang['de_DE']['Product.ss']['QUANTITYCART'] = 'Menge im Warenkorb';
$lang['de_DE']['Product.ss']['REMOVE'] = '&quot;%s&quot; vom Warenkorb entfernen';
$lang['de_DE']['Product.ss']['REMOVEALL'] = '&quot;%s&quot; vom Warenkorb entfernen';
$lang['de_DE']['Product.ss']['REMOVELINK'] = '&raquo; Aus dem Warenkorb entfernen';
$lang['de_DE']['Product.ss']['SIZE'] = 'Größe';
$lang['de_DE']['ProductGroup.ss']['FEATURED'] = 'Empfohlene Artikel';
$lang['de_DE']['ProductGroup.ss']['OTHER'] = 'Andere Produkte';
$lang['de_DE']['ProductGroup.ss']['VIEWGROUP'] = 'Produktgruppe &quot;%s&quot; anzeigen';
$lang['de_DE']['ProductGroupItem.ss']['ADD'] = '&quot;%s&quot; zum Warenkorb hinzufügen';
$lang['de_DE']['ProductGroupItem.ss']['ADDLINK'] = 'Diesen Artikel zum Warenkorb hinzufügen';
$lang['de_DE']['ProductGroupItem.ss']['ADDONE'] = '&quot;%s&quot; zum Warenkorb hinzufügen';
$lang['de_DE']['ProductGroupItem.ss']['AUTHOR'] = 'Autor';
$lang['de_DE']['ProductGroupItem.ss']['GOTOCHECKOUT'] = 'Zur Kasse';
$lang['de_DE']['ProductGroupItem.ss']['GOTOCHECKOUTLINK'] = '&raquo; Zur Kasse';
$lang['de_DE']['ProductGroupItem.ss']['IMAGE'] = '%s Bild';
$lang['de_DE']['ProductGroupItem.ss']['NOIMAGE'] = 'Keine Produktabbildung vorhanden für &quot;%s&quot;';
$lang['de_DE']['ProductGroupItem.ss']['QUANTITY'] = 'Menge';
$lang['de_DE']['ProductGroupItem.ss']['QUANTITYCART'] = 'Menge im Warenkorb';
$lang['de_DE']['ProductGroupItem.ss']['READMORE'] = 'Erfahren Sie hier mehr über &quot;%s&quot;';
$lang['de_DE']['ProductGroupItem.ss']['READMORECONTENT'] = 'mehr &raquo;';
$lang['de_DE']['ProductGroupItem.ss']['REMOVE'] = '&quot;%s&quot; vom Warenkorb entfernen.';
$lang['de_DE']['ProductGroupItem.ss']['REMOVEALL'] = '1 Einheit von &quot;%s&quot; aus dem Warenkorb entferne';
$lang['de_DE']['ProductGroupItem.ss']['REMOVELINK'] = '&raquo; Aus dem Warenkorb entfernen';
$lang['de_DE']['ProductGroupItem.ss']['REMOVEONE'] = '&quot;%s&quot; vom Warenkorb entferrnen';
$lang['de_DE']['ProductMenu.ss']['GOTOPAGE'] = 'Zur %s Seite';
$lang['de_DE']['SSReport']['ALLCLICKHERE'] = 'Klicken Sie hier, um alle Produkte anzuzeigen';
$lang['de_DE']['SSReport']['INVOICE'] = 'Rechnung';
$lang['de_DE']['SSReport']['PRINT'] = 'drucken';
$lang['de_DE']['SSReport']['VIEW'] = 'anzeigen';
$lang['de_DE']['ViewAllProducts.ss']['AUTHOR'] = 'Autor';
$lang['de_DE']['ViewAllProducts.ss']['CATEGORIES'] = 'Kategorien';
$lang['de_DE']['ViewAllProducts.ss']['IMAGE'] = '%s Bild';
$lang['de_DE']['ViewAllProducts.ss']['LASTEDIT'] = 'Zuletzt bearbeitet';
$lang['de_DE']['ViewAllProducts.ss']['LINK'] = 'Link';
$lang['de_DE']['ViewAllProducts.ss']['NOCONTENT'] = 'Keine Inhalte vorhanden.';
$lang['de_DE']['ViewAllProducts.ss']['NOIMAGE'] = 'Kein Bild für &quot;%s&quot; vorhanden.';
$lang['de_DE']['ViewAllProducts.ss']['NOSUBJECTS'] = 'Keine Produkte vorhanden.';
$lang['de_DE']['ViewAllProducts.ss']['PRICE'] = 'Preis';
$lang['de_DE']['ViewAllProducts.ss']['PRODUCTID'] = 'Produkt ID';
$lang['de_DE']['ViewAllProducts.ss']['WEIGHT'] = 'Gewicht';