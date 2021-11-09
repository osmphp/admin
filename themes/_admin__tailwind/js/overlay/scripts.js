import Overlay from "./Overlay";

const overlay = new Overlay();

const show = overlay.show.bind(overlay);
const hide = overlay.hide.bind(overlay);

export { show, hide };
