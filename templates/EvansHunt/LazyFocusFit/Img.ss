<img
    class="$PictureClass lazyload"
    $AttributeString
    data-original-width="$Width"
    data-original-height="$Height"
    src="$TinySrc"
    width="$Width"
    height="$CroppedHeight"
    alt="$Caption"
    data-srcset="$ImgSrcset"
    data-sizes="auto"
    <%if $IsObjectFit %>
        data-object-fit="$ObjectFitType"
        data-object-position="$PercentageX% $PercentageY%"
    <% end_if %>

    style="
        <%if $IsObjectFit %>
            object-position: $PercentageX% $PercentageY%;
        <% end_if %>
        <% if $IsNaturalWidth %>
            max-width: {$Width}px;
        <% end_if %>
    "
>
