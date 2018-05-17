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
  });
}());