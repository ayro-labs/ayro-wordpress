(function() {
  'use strict';

  Ayro.init({
    app_token: config.appToken,
    sounds: config.sounds === '1',
    chatbox: {
      title: config.chatboxHeaderTitle,
      input_placeholder: config.chatboxInputPlaceholder,
    },
  });
}());