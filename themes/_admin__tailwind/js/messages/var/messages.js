import Messages from '../Messages';

const messages = new Messages();

const notice = messages.notice.bind(messages);
const modal = messages.modal.bind(messages);
const fetch = messages.fetch.bind(messages);
const hide = messages.hide.bind(messages);

export { notice, modal, fetch, hide };