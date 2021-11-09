export default class Overlay {
    get overlay() {
        return document.getElementById('overlay')
            .osm_controllers['overlay'];
    }

    show() {
        this.overlay.show();
    }

    hide() {
        this.overlay.hide();
    }
}