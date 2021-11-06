<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Group\Standard $group */
?>
<fieldset class="grid grid-cols-12">
    @foreach ($group->fields as $field)
        @include ($field->template, ['field' => $field, 'id' => ''])
    @endforeach
</fieldset>
