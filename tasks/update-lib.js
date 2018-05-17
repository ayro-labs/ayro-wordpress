'use strict';

const {commands} = require('@ayro/commons');
const fs = require('fs');
const path = require('path');
const Promise = require('bluebird');
const axios = require('axios');

const WORKING_DIR = path.resolve(__dirname, '../');
const LIB_FILE = path.join(WORKING_DIR, 'public/js/ayro.min.js');

const writeFileAsync = Promise.promisify(fs.writeFile);

const cdnClient = axios.create({
  baseURL: 'https://cdn.ayro.io',
});

async function updateLib(version) {
  try {
    commands.log(`Updating Ayro javascript library to version ${version}`);
    const libUrl = `/sdks/ayro-${version}.min.js`;
    const response = await cdnClient.get(libUrl);
    await writeFileAsync(LIB_FILE, response.data);
    commands.log('Ayro javascript library was updated with success');
  } catch (err) {
    commands.logError(`Error updating Ayro javascript library: ${err.message}`);
  }
}

// Run this if call directly from command line
if (require.main === module) {
  if (!process.argv[2]) {
    commands.logError('Library version must be defined');
    process.exit(1);
  }
  updateLib(process.argv[2]);
}
