const {commands} = require('@ayro/commons');
const fs = require('fs');
const path = require('path');
const Promise = require('bluebird');
const axios = require('axios');

const WORKING_DIR = path.resolve(__dirname, '../');
const LIB_FILE = path.join(WORKING_DIR, 'public/js/ayro-wordpress.min.js');

const writeFileAsync = Promise.promisify(fs.writeFile);

const githubContentClient = axios.create({
  baseURL: 'https://raw.githubusercontent.com',
});

function updateLib(version) {
  Promise.coroutine(function* () {
    try {
      commands.log(`Updating library ayro-wordpress to version ${version}`);
      const libUrl = `/ayrolabs/ayro-javascript/${version}/ayro-wordpress-${version}.min.js`;
      const response = yield githubContentClient.get(libUrl);
      yield writeFileAsync(LIB_FILE, response.data);
      commands.log(`Library ayro-wordpress updated with success: ${LIB_FILE}`);
    } catch (err) {
      commands.logError(`Error updating ayro-wordpress library: ${err.message}`);
    }
  })();
}

// Run this if call directly from command line
if (require.main === module) {
  if (!process.argv[2]) {
    commands.logError('Ayro library version must be defined.');
    process.exit(1);
  }
  updateLib(process.argv[2]);
}
