<form action={concat( $module.functions.discountgroupedit.uri, '/', $discount_group.id )|ezurl} method="post" name="DiscountGroupEdit">
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Edit <%discount_group> [Discount group]'|i18n( 'design/standard/shop/editdiscountgroup',, hash( '%discount_group', $discount_group.name ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


<div class="context-attributes">

<div class="block">
    <label>{'Name'|i18n( 'design/admin/shop/editdiscountgroup' )}:</label>
    <input class="box" type="text" name="discount_group_name" value="{$discount_group.name}" />
</div>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="ApplyButton" value="{'OK'|i18n( 'design/admin/shop/editdiscountgroup' )}" />
<input class="button" type="submit" name="DiscardButton" value="{'Cancel'|i18n( 'design/admin/shop/editdiscountgroup' )}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>


</form>
