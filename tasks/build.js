const {commands} = require('@ayro/commons');
const path = require('path');
const Promise = require('bluebird');
const _ = require('lodash');

const WORKING_DIR = path.resolve(__dirname, '../');
const DIST_DIR = 'dist';
const ZIP_NAME = 'ayro.zip';
const ZIP_FILES = ['admin', 'includes', 'languages', 'public', 'ayro.php', 'index.php', 'uninstall.php'];

exports.run = () => {
  return Promise.coroutine(function* () {
    try {
      yield commands.exec(`mkdir -p ${DIST_DIR}`, WORKING_DIR);
      yield commands.exec(`rm -Rf ${DIST_DIR}/*`, WORKING_DIR);
      yield commands.exec(`zip -r ${DIST_DIR}/${ZIP_NAME} ${_.join(ZIP_FILES, ' ')}`, WORKING_DIR);
    } catch (err) {
      commands.logError(`Error building plugin: ${err.message}`);
    }
  })();
}

// Run this if call directly from command line
if (require.main === module) {
  this.run();
}
