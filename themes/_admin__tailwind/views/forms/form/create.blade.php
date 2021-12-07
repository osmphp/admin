<?php
global $osm_app; /* @var \Osm\Core\App $osm_app */
/* @var \Osm\Admin\Forms\Form\Create $form */
?>
<form method="POST" action="{{ "{$osm_app->area_url}{$form->save_url}" }}" data-js-form>
    <h1 class="text-2xl sm:text-4xl pt-6 mb-6 border-t border-gray-300">
        {{ \Osm\__($form->title) }}
    </h1>
    <div>
        <button type="submit"
            class="text-white bg-blue-700
                hover:bg-blue-800 focus:ring-4 focus:ring-blue-300
                font-medium rounded-lg text-sm px-5 py-2.5 text-center
                mr-3 mb-3">{{ \Osm\__("Save")}}</button>
    </div>
    @foreach ($form->chapters as $chapter)
        @include ($chapter->template, ['chapter' => $chapter])
    @endforeach
</form>
