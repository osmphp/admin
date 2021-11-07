<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form\Create $form */
?>
<form method="POST" action="{{ "{$osm_app->area_url}{$form->save_url}" }}" data-js-form>
    @foreach ($form->chapters as $chapter)
        @include ($chapter->template, ['chapter' => $chapter])
    @endforeach
</form>
