/* eslint-disable import/no-extraneous-dependencies */

'use strict';

const {commands} = require('release-n-publish');
const path = require('path');
const _ = require('lodash');

const WORKING_DIR = path.resolve();
const DIST_DIR = 'dist';
const AYRO_DIR = 'ayro';
const DIST_AYRO_DIR = `${DIST_DIR}/${AYRO_DIR}`;
const ZIP_NAME = 'ayro-wordpress.zip';
const ZIP_FILES = [
  'src/admin',
  'src/includes',
  'src/languages',
  'src/public',
  'src/ayro.php',
  'src/index.php',
  'src/uninstall.php',
  'src/readme.md',
  'license.txt',
];

async function build() {
  try {
    await commands.exec(`mkdir -p ${DIST_DIR}`, WORKING_DIR);
    await commands.exec(`rm -Rf ${DIST_DIR}/*`, WORKING_DIR);
    await commands.exec(`mkdir -p ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`cp -Rf ${_.join(ZIP_FILES, ' ')} ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`cd ${DIST_AYRO_DIR}`, WORKING_DIR);
    await commands.exec(`zip -r ${ZIP_NAME} ${AYRO_DIR}`, path.resolve(DIST_DIR));
    await commands.exec(`rm -Rf ${DIST_AYRO_DIR}`, WORKING_DIR);
  } catch (err) {
    commands.logError(`Error building plugin: ${err.message}`);
  }
}

// Run this if call directly from command line
if (require.main === module) {
  build();
}
