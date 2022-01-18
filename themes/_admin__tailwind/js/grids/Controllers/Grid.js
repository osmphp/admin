import Controller from "../../js/Controller";
import {register} from '../../js/scripts';

export default register('grid', class Grid extends Controller {
    _inverse_selection = false;

    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click .grid__row-handle': 'onRowHandleClick',
        });
    }

    onRowHandleClick(e) {
        if (e.target.tagName.toLowerCase() === 'input') {
            return;
        }

        const inputElement = e.currentTarget.querySelector('input');
        inputElement.checked = !inputElement.checked;
    }

    set inverse_selection(value) {
        this._inverse_selection = value;
        this.row_handle_elements.forEach(element => {
            element.querySelector('input').checked = value;
        });
    }

    get row_handle_elements() {
        return this.element.querySelectorAll('.grid__row-handle');
    }
});