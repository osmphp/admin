import Mustache from 'mustache';
import overlay from '../overlay/scripts';

export default class Messages {
    get message_bar_element() {
        return document.getElementById('message-bar');
    }

    get overlay() {
        return document.getElementById('overlay')
            .osm_controllers['overlay'];
    }

    message(text) {
        let message = this.show('message', false, {text});

        setTimeout(() => {
            this.hide(message);
        }, 5000);

        return message;
    }

    /**
     * @param {string }text
     * @returns {{modal: boolean, element: Element}}
     */
    modal(text) {
        return this.show('message', true, {text});
    }

    error(text) {
        return this.show('error', true, {text});
    }

    exception(text, stack_trace) {
        return this.show('exception', true,
            {text, stack_trace});
    }

    /**
     *
     * @param {string} template
     * @param {boolean} modal
     * @param {Object} variables
     * @returns {{modal: boolean, element: Element}}
     */
    show(template, modal, variables) {
        if (modal) {
            overlay.show();
        }

        let html = document.getElementById(`${template}-template`)
            .innerHTML;

        html = Mustache.render(html, variables);

        let parser = new DOMParser();
        let element = parser.parseFromString(html, 'text/html')
            .body.children[0];

        this.message_bar_element.append(element);

        return {modal, element};
    }

    /**
     * @param {{modal: boolean, element: Element}} message
     */
    hide(message) {
        this.message_bar_element.removeChild(message.element);

        if (message.modal) {
            overlay.hide();
        }
    }

    fetch(resource, init) {
        init.redirect = 'error';

        return fetch(resource, init)
            .then(response => this.handleResponse(response))
            .catch(error => this.handleError(error));
    }

    handleResponse(response) {
        let contentType = response.headers.get('Content-Type')
            .split(';')[0].trim();

        if (response.ok) {
            if (contentType === 'text/html') {
                return response.text().then(text => {
                    if (!text.length) {
                        throw 'Unexpected empty response';
                    }

                    return response;
                });
            }

            return response;
        }

        switch (contentType) {
            case 'text/html':  throw 'Not Implemented';
            case 'text/plain': throw 'Not Implemented';
            case 'application/json': throw 'Not Implemented';
            default: throw 'Not Implemented';
        }
    }

    handleError(error) {
        if (typeof error === 'string') {
            this.error(error);
            return;
        }

        console.log(error);
        throw 'not implemented';
    }
};