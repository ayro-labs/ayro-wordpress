'use strict';

const {commands} = require('@ayro/commons');
const path = require('path');
const _ = require('lodash');

const WORKING_DIR = path.resolve(__dirname, '../');
const DIST_DIR = 'dist';
const AYRO_DIR = 'ayro';
const DIST_AYRO_DIR = `${DIST_DIR}/${AYRO_DIR}`;
const ZIP_NAME = 'ayro-wordpress.zip';
const ZIP_FILES = ['admin', 'includes', 'languages', 'public', 'ayro.php', 'index.php', 'uninstall.php', 'readme.md', 'license.txt'];

async function build() {
  try {
    await commands.exec(`mkdir -p ${DIST_DIR}`, WORKING_DIR);
    await commands.exec(`rm -Rf ${DIST_DIR}/*`, WORKING_DIR);
    await commands.exec(`mkdir -p ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`cp -Rf ${_.join(ZIP_FILES, ' ')} ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`cd ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`zip -r ${ZIP_NAME} ${AYRO_DIR}`, path.join(WORKING_DIR, DIST_DIR));
    await commands.exec(`rm -Rf ${DIST_AYRO_DIR}`, WORKING_DIR);
  } catch (err) {
    commands.logError(`Error building plugin: ${err.message}`);
  }
}

// Run this if call directly from command line
if (require.main === module) {
  build();
}
