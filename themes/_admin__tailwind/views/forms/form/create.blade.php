<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form\Create $form */
?>
@foreach ($form->chapters as $chapter)
    @include ($chapter->template, ['chapter' => $chapter])
@endforeach
