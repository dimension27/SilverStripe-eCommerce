<div id="Checkout">
   <h3 class="process"><span><% _t("Checkout.PROCESS","Process") %>:</span> &nbsp;<span class="current"><% _t("Checkout.CHECKOUT","Checkout") %></span> &nbsp;&gt;&nbsp;<% _t("Checkout.ORDERSTEP","Order Status") %></h3>

   <h1 class="pagetitle">$Title</h1>
   <div id="ContentHolder">
      <% if Content %>$Content<% end_if %>
   </div>

   <% if CanCheckout %>
      <% control Order %>
         <% include Order_Content_Editable %>
      <% end_control %>

      <% if ModifierForms %><% control ModifierForms %>
         $Me
      <% end_control %><% end_if %>

      <% if Order.Items %>$OrderForm<% end_if %>
   <% else %>
   <div id="CanNotCheckOut">
      <p><strong>$Message</strong></p>
      <% if UsefulLinks %>
      <ul id="UsefulLinks">
         <% control ActionLinks %><li><a href="$Link">$Title</a></li><% end_control %>
      </ul>
      <% end_if %>
   </div>
   <% end_if %>
</div>
