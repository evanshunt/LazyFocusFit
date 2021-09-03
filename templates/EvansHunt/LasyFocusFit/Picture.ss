<picture
    class="$PictureClass"
    $AttributeString
    data-original-width="$Width"
    data-original-height="$Height"
    <% if $IsNaturalWidth %>
        style="max-width: {$Width}px;"
    <% end_if %>
>
    <% loop $PictureSources %>
        <source
            data-srcset="<% loop $Sizes %>$URL $Descriptors<% if not $Last %>,<% end_if %><% end_loop %>"
            <% if $MinWidth %>
                media="(min-width: $MinWidth)"
            <% end_if %>
        />
    <% end_loop %>
    <img
        class="lazyload"
        src="$TinySrc"
        width="$CroppedWidth"
        height="$CroppedHeight"
        alt="$Caption"
        $ImgAttributeString
        <% if $AutoSizes %>
            data-sizes="auto"
        <% end_if %>
        <%if $IsObjectFit %>
            style="object-position: $PercentageX% $PercentageY%;"
            data-object-fit="$ObjectFitType"
            data-object-position="$PercentageX% $PercentageY%"
        <% end_if %>
    >
</picture>
