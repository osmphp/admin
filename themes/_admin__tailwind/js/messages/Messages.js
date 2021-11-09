import Mustache from 'mustache';
import {capture, release} from '../js/scripts';

export default class Messages {
    get message_bar_element() {
        return document.getElementById('message-bar');
    }

    notice(text) {
        return this.show('message', false, true,
            {text});
    }

    /**
     * @param {string }text
     * @returns {{modal: boolean, element: Element}}
     */
    modal(text) {
        return this.show('message', true, false,
            {text});
    }

    error(text) {
        return this.show('error', false, true,
            {text});
    }

    /**
     *
     * @param {string} template
     * @param {boolean} modal
     * @param {boolean} autohide
     * @param {Object} variables
     * @returns {{modal: boolean, element: Element}}
     */
    show(template, modal, autohide, variables) {
        if (modal) {
            capture(this.message_bar_element);
        }

        let html = document.getElementById(`${template}-template`)
            .innerHTML;

        html = Mustache.render(html, variables);

        let parser = new DOMParser();
        let element = parser.parseFromString(html, 'text/html')
            .body.children[0];

        this.message_bar_element.append(element);

        let message = {modal, element};

        if (autohide) {
            setTimeout(() => {
                this.hide(message);
            }, 5000);
        }

        return message;
    }

    /**
     * @param {{modal: boolean, element: Element}} message
     */
    hide(message) {
        this.message_bar_element.removeChild(message.element);

        if (message.modal) {
            release();
        }
    }

    fetch(resource, init) {
        init.redirect = 'error';

        let message;
        if (init.message) {
            message = this.modal(init.message);
            delete init.message;
        }

        return fetch(resource, init)
            .then(response => {
                if (message) {
                    this.hide(message);
                    message = null;
                }
                return this.handleResponse(response);
            })
            .catch(error => {
                if (message) {
                    this.hide(message);
                    message = null;
                }
                return this.handleError(error);
            });
    }

    handleResponse(response) {
        let contentType = response.headers.get('Content-Type')
            .split(';')[0].trim();

        if (response.ok) {
            if (contentType === 'text/html') {
                return response.text().then(text => {
                    if (!text.length) {
                        return Promise.reject('Unexpected empty response');
                    }

                    return response;
                });
            }

            return response;
        }

        switch (contentType) {
            case 'text/html':  return this.handleHtmlError(response);
            case 'text/plain': return this.handleTextError(response);
            case 'application/json': return Promise.reject(response);
        }

        console.log('Unhandled fetch error', response);
        return Promise.reject();
    }

    handleHtmlError(response) {
        return response.text().then(text => {
            if (!text.length) {
                return Promise.reject('Unexpected empty response');
            }

            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');

            if (!doc.title.length) {
                return Promise.reject('Unexpected empty response');
            }

            return Promise.reject(doc.title);
        });
    }

    handleTextError(response) {
        return response.text().then(text => {
            return Promise.reject(text.split("\n")[0].trim());
        });
    }

    handleError(error) {
        if (typeof error === 'string') {
            this.error(error);
            return Promise.reject();
        }

        if (error instanceof Error) {
            this.error(error.message);
            return Promise.reject();
        }

        if (error instanceof Response) {
            return Promise.reject(error);
        }

        console.log('Unhandled fetch error', error);
        return Promise.reject();
    }
};