<div id="{$id}_group" class="form-group-editor admidio-form-group mb-4{if $property eq 1} admidio-form-group-required{/if}">
    <label for="{$id}" class="form-label">
        {include file="sys-template-parts/parts/form.part.icon.tpl"}
        {$label}
    </label>
    <div class="editor {$class}"
        {foreach $data.attributes as $itemvar}
            {$itemvar@key}="{$itemvar}"
        {/foreach}
    >
        <textarea id="{$id}" name="{$id}" style="width: 100%">{$value}</textarea>
    </div>

    {include file="sys-template-parts/parts/form.part.helptext.tpl"}
    {include file="sys-template-parts/parts/form.part.warning.tpl"}
</div>
