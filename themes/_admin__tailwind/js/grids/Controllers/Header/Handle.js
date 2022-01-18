import Header from "../Header";
import {register, controller} from '../../../js/scripts';

export default register('grid-handle-header', class Handle extends Header {
    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click': 'onClick',
            'change input': 'onChange',
        });
    }

    get input_element() {
        return this.element.querySelector('input');
    }

    onClick(e) {
        if (e.target.tagName.toLowerCase() === 'input') {
            return;
        }

        const inputElement = e.currentTarget.querySelector('input');
        inputElement.checked = !inputElement.checked;
        this.grid.inverse_selection = inputElement.checked;
    }

    get grid_element() {
        return this.getFirstParentElement(this.element,
            element => element.classList.contains('grid_'));
    }

    get grid() {
        return controller(this.grid_element, 'grid');
    }

    onChange() {
        this.grid.inverse_selection = this.input_element.checked;
    }
});