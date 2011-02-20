<% include ProductMenu %>
<div id="ProductGroup">
	<h1 class="pagetitle">$Title</h1>

	<% if Content %>
		<div id="ContentHolder">
			$Content
		</div>
	<% end_if %>

	<% if Products %>
		<div id="Products" class="category">
			there are products
			<div class="resultsBar">
				<% if SortLinks %><span class="sortOptions"><% _t('ProductGroup.SORTBY','Sort by') %> <% control SortLinks %><a href="$Link" class="sortlink $Current">$Name</a> <% end_control %></span><% end_if %>
			</div>
			<ul class="productList">
				<% control Products %>
					<% include ProductGroupItem %>
				<% end_control %>
			</ul>
			<div class="clear"><!-- --></div>
		</div>
	<% end_if %>

	<% if Products.MoreThanOnePage %>
	<div id="PageNumbers">
		<p>
			<% if Products.NotFirstPage %>
				<a class="prev" href="$Products.PrevLink" title="<% _t('ProductGroup.SHOWPREVIOUSPAGE','View the previous page') %>"><% _t('ProductGroup.PREVIOUS','previous') %></a>
			<% end_if %>

			<span>
		    		<% control Products.PaginationSummary(4) %>
					<% if CurrentBool %>
						$PageNum
					<% else %>
						<% if Link %>
							<a href="$Link" title="<% sprintf(_t("ProductGroup.GOTOPAGE","View page number %s"),$PageNum) %>">$PageNum</a>
						<% else %>
							&hellip;
						<% end_if %>
					<% end_if %>
				<% end_control %>
			</span>

			<% if Products.NotLastPage %>
				<a class="next" href="$Products.NextLink" title="<% _t('ProductGroup.SHOWNEXTPAGE','View the next page') %>"><% _t('ProductGroup.NEXT','next') %></a>
			<% end_if %>
		</p>
	</div>
	<% end_if %>

</div>
