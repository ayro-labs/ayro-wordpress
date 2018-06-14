/* eslint-disable no-undef */

'use strict';

Ayro.init({
  app_token: config.appToken,
  channel: 'wordpress',
  sounds: config.sounds === '1',
  chatbox: {
    title: config.chatboxHeaderTitle,
    input_placeholder: config.chatboxInputPlaceholder,
    connect_channels_message: {
      ask_for_email: config.askForEmail,
      email_provided: config.emailProvided,
      email_input_placeholder: config.emailInputPlaceholder,
      send_email_button: config.sendEmailButton,
      edit_email_button: config.editEmailButton,
    },
    errors: {
      file_size_limit_exceeded: config.chatboxErrorsFileSizeLimitExceeded,
    },
  },
});
