(function() {
  "use strict";

  Ayro.init({
    app_token: config.appToken,
    chatbox: {
      title: config.chatboxHeaderTitle,
      input_placeholder: config.chatboxInputPlaceholder,
    },
  });
}());