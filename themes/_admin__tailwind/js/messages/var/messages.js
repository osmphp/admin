import Messages from '../Messages';

const messages = new Messages();

const show = messages.show.bind(messages);
const modal = messages.modal.bind(messages);
const fetch = messages.fetch.bind(messages);

export { show, modal, fetch };