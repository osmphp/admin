@around({{ $footer ?? '' }})
    @proceed
    <div id="message-bar" class="fixed left-4 right-4 bottom-4 z-50
        pointer-events-none flex flex-col items-center">
    </div>

    <template id="message-template">
        <div id="@{{ id }}" class="p-4 mt-4 bg-gray-500 text-white rounded shadow">
            @{{ text }}
        </div>
    </template>

    <template id="error-template">
        <div id="@{{ id }}" class="p-4 mt-4 bg-red-800 text-white rounded shadow">
            @{{ text }}
        </div>
    </template>
@endaround