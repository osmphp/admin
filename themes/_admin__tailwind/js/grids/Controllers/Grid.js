import Controller from "../../js/Controller";
import {register} from '../../js/scripts';

export default register('grid', class Grid extends Controller {
    _inverse_selection = false;

    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click .grid__row-handle': 'onRowHandleClick',
            'change .grid__row-handle input': 'onRowHandleChange',
        });
    }

    onRowHandleClick(e) {
        if (e.target.tagName.toLowerCase() === 'input') {
            return;
        }

        const inputElement = e.currentTarget.querySelector('input');
        inputElement.checked = !inputElement.checked;
        this.updateSelected();
    }

    set inverse_selection(value) {
        this._inverse_selection = value;
        this.row_handle_elements.forEach(element => {
            element.querySelector('input').checked = value;
        });
        this.updateSelected();
    }

    get row_handle_elements() {
        return this.element.querySelectorAll('.grid__row-handle');
    }

    get selected_row_count() {
        return this.element
            .querySelectorAll('.grid__row-handle input:checked')
            .length;
    }

    get deselected_row_count() {
        return this.element
            .querySelectorAll('.grid__row-handle :not(input:checked)')
            .length;
    }

    get selected_element() {
        return this.element.querySelector('.grid__selected');
    }

    onRowHandleChange() {
        this.updateSelected();
    }

    updateSelected() {
        const selected = this._inverse_selection
            ? this.options.count - this.deselected_row_count
            : this.selected_row_count;

        this.selected_element.innerText = this.options.s_selected
            .replace(':selected', selected)
            .replace(':count', this.options.count);
    }
});