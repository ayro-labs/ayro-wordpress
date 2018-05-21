(function() {
  'use strict';

  Ayro.init({
    app_token: config.appToken,
    channel: 'wordpress',
    sounds: config.sounds === '1',
    chatbox: {
      title: config.chatboxHeaderTitle,
      input_placeholder: config.chatboxInputPlaceholder,
      connect_channels_message: config.chatboxConnectChannelsMessage,
    },
    connect_email: {
      description: config.connectEmailDescription,
      input_placeholder: config.connectEmailInputPlaceholder,
      send_button: config.connectEmailSendButton,
      success_message: config.connectEmailSuccessMessage,
    },
  });
}());