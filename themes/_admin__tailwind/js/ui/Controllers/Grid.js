import Controller from "../../js/Controller";
import {register, controller} from '../../js/scripts';
import {notice, fetch} from '../../messages/var/messages';

/**
 * @property {boolean} options.s_selected Text says how many objects are
 *      currently selected.
 * @property {int} options.count Number of matching objects.
 * @property {string} options.edit_url Edit page URL, without filter parameters
 * @property {string} options.delete_url Delete route URL, without
 *      filter parameters
 * @property {string} options.s_deleting Message that shows up while selected
 *      objects are being deleted
 * @property {string} options.s_deleted Message informing that the selected
 *      objects have been successfully deleted
 * @property {string[]} options.url_parameters Currently applied filters, orders
 *      and other URL actions
 */
export default register('grid', class Grid extends Controller {
    _inverse_selection = false;

    get events() {
        return Object.assign({}, super.events, {
            // event selector
            'click .grid__row-handle': 'onRowHandleClick',
            'change .grid__row-handle input': 'onRowHandleChange',
            'click .grid__action.-delete': 'onDelete',
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
            .map(element => controller(element, 'grid-row'));
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

    get selected_count() {
        return this._inverse_selection
            ? this.options.count - this.deselected_row_count
            : this.selected_row_count;
    }

    updateSelected() {
        const selected = this.selected_count;

        this.selected_element.innerText = this.options.s_selected
            .replace(':selected', selected)
            .replace(':count', this.options.count);

        if (selected) {
            this.action_elements.forEach(element => {
                element.classList.remove('hidden');
            });

            // noinspection UnnecessaryLocalVariableJS
            let url = this.toUrl(this.options.edit_url, true,
                this.idFilter());

            this.edit_action_element.href = url;
        }
        else {
            this.action_elements.forEach(element => {
                element.classList.add('hidden');
            });
            this.edit_action_element.href = '#';
        }
    }

    idFilter() {
        const ids = this.ids(!this._inverse_selection).join(' ');

        let urlActions = ['-id', '-id-'];
        if (!ids.length) {
            return urlActions;
        }

        urlActions.push(this._inverse_selection ? `id-=${ids}` : `id=${ids}`);

        return urlActions;
    }

    toUrl(url, safe, actions) {
        let parameters = JSON.parse(
            JSON.stringify(this.options.url_parameters || {}));

        actions.forEach(action => {
            this.applyUrlAction(parameters, action);
        });

        if (safe && !this.urlHasFilters(parameters)) {
            parameters.all = true;
        }

        const urlParameters = this.renderUrlParameters(parameters);

        return urlParameters.length ? `${url}?${urlParameters}` : url;
    }

    renderUrlParameters(parameters) {
        let url = '';

        for (let param in parameters) {
            if (!parameters.hasOwnProperty(param)) {
                continue;
            }
            const values = parameters[param];

            if (url.length) {
                url += '&';
            }

            url += this.urlEncode(param);
            url += this.renderUrlValues(values);
        }

        return url;
    }

    renderUrlValues(values) {
        if (values === true) {
            return '';
        }

        let url = '';
        if (typeof values === 'string') {
            return url + '=' + this.urlEncode(values);
        }

        values.forEach(value => {
            if (url.length) {
                url += '+';
            }

            url += this.urlEncode(value);
        });

        return '=' + url;
    }

    urlHasFilters(parameters) {
        for (let param in parameters) {
            if (parameters.hasOwnProperty(param) && this.isFilter(param)) {
                return true;
            }
        }

        return false;
    }

    isFilter(param) {
        return this.non_filter_url_parameter_names.indexOf(param) === -1;
    }

    get non_filter_url_parameter_names() {
        return ['limit', 'offset', 'order', 'q', 'select'];
    }

    urlEncode(value) {
        return encodeURIComponent(value).replace('%20', '+');
    }

    applyUrlAction(parameters, action) {
        let parsed = action.match(/^([-+])?([^=]*)(?:=(.*))?$/);

        if (parsed === null) {
            throw `Invalid URL action syntax '${action}'`;
        }

        switch (parsed[1]) {
            case '-':
                if (parsed[2] === undefined) {
                    return this.removeUrlFilters(parameters);
                }
                if (parsed[3] === undefined) {
                    return this.removeUrlParameter(parameters, parsed[2]);
                }

                return this.removeUrlOption(parameters, parsed[2], parsed[3]);
            case '+':
                if (parsed[2] === undefined || parsed[3] === undefined) {
                    throw `Invalid URL action syntax '${action}'`;
                }

                this.addUrlOption(parameters, parsed[2], parsed[3]);
                break;
            case undefined:
                if (parsed[2] === undefined || parsed[3] === undefined) {
                    throw `Invalid URL action syntax '${action}'`;
                }

                this.setUrlParameter(parameters, parsed[2], parsed[3]);
                break;
            default:
                throw `Invalid URL action syntax '${action}'`;
        }
    }

    removeUrlFilters(parameters) {
        for (let param in parameters) {
            if (parameters.hasOwnProperty(param) && this.isFilter(param)) {
                delete parameters[param];
            }
        }
    }

    removeUrlParameter(parameters, param) {
        delete parameters[param];
    }

    removeUrlOption(parameters, param, value) {
        if (!Array.isArray(parameters[param])) {
            return;
        }

        const index = parameters[param].indexOf(value);
        if (index !== -1) {
            parameters[param].splice(index, 1);
        }
    }

    addUrlOption(parameters, param, value) {
        if (!Array.isArray(parameters[param])) {
            parameters[param] = [];
        }

        parameters[param].push(value);
    }

    setUrlParameter(parameters, param, value) {
        parameters[param] = value;
    }

    onDelete() {
        let url = this.toUrl(this.options.delete_url, true, [
            this.idFilter(),
        ]);

        fetch(url, {
            method: 'DELETE',
            message: this.options.s_deleting
                .replace(':selected', this.selected_count),
        })
        .then(response => {
            notice(this.options.s_deleted
                .replace(':selected', this.selected_count));

            return response.json();
        })
        .then(json => {
            location.href = json.url;
        })
        .catch(() => null);
    }
});