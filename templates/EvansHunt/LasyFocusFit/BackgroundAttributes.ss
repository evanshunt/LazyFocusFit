class="$PictureClass lazyload"
style="background-image: url($TinySrc); background-position: $PercentageX% $PercentageY%;"
data-bgset="<% loop $PictureSources %><% loop $Sizes %>$URL $Descriptors<% if not $Last %>,<% end_if %> <% end_loop %>
    <% if $MinWidth %>
        [(min-width: $MinWidth)]
    <% end_if %>
    <% if not $Last %>|<% end_if %>
<% end_loop %>"
<% if $AutoSizes %>
    data-sizes="auto"
<% end_if %>
