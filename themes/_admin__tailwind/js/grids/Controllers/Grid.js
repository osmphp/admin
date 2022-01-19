import Controller from "../../js/Controller";
import {register, controller} from '../../js/scripts';

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

    get rows() {
        return Array.from(this.element.querySelectorAll('.grid__row'))
            .map(element => controller(element, 'row'));
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

    ids(checked) {
        return this.rows
            .filter(row =>
                row.element.querySelector('.grid__row-handle input')
                    .checked === checked
            )
            .map(row => row.options.id);
    }

    get selected_element() {
        return this.element.querySelector('.grid__selected');
    }

    get action_elements() {
        return this.element.querySelectorAll('.grid__action');
    }

    get edit_action_element() {
        return this.element.querySelector('.grid__action.-edit');
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

        if (selected) {
            this.action_elements.forEach(element => {
                element.classList.remove('hidden');
            });
            this.updateEditLink();
        }
        else {
            this.action_elements.forEach(element => {
                element.classList.add('hidden');
            });
            this.edit_action_element.href = '#';
        }
    }

    updateEditLink() {
        if (this._inverse_selection) {
            const ids = this.ids(false);
            this.edit_action_element.href = this.options.edit_url +
                (ids.length
                    ? `?id-=${this.ids(false).join('+')}`
                    : '?all'
                );
        }
        else {
            this.edit_action_element.href = this.options.edit_url +
                `?id=${this.ids(true).join('+')}`;
        }
    }
});