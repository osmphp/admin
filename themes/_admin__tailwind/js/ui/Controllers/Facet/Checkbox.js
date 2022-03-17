import Controller from "../../../js/Controller";
import {register} from '../../../js/scripts';

export default register('facet-checkbox', class Checkbox extends Controller {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click input': 'onCheckboxClick',
            'click a': 'onLinkClick',
        });
    }

    get link_element() {
        return this.element.querySelector('a');
    }

    get checkbox_element() {
        return this.element.querySelector('input');
    }

    onCheckboxClick(e) {
        location.href = this.link_element.href;
        e.stopPropagation();
    }

    onLinkClick() {
        this.checkbox_element.checked = !this.checkbox_element.checked;
    }
});