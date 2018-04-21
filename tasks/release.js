'use strict';

const {releaseTask, commands} = require('@ayro/commons');
const fs = require('fs');
const path = require('path');
const util = require('util');
const Promise = require('bluebird');

const WORKING_DIR = path.resolve(__dirname, '../');
const MAIN_PHP_FILE = path.join(WORKING_DIR, 'ayro.php');
const PLUGIN_VERSION_REGEX = /Version:\s{11}(\d+\.\d+\.\d+)/;
const PLUGIN_VERSION_FORMAT = 'Version:           %s';
const PLUGIN_VERSION_DEFINE_REGEX = /define\('AYRO_PLUGIN_VERSION', '(\d+\.\d+\.\d+)'\);/;
const PLUGIN_VERSION_DEFINE_FORMAT = 'define(\'AYRO_PLUGIN_VERSION\', \'%s\');';

const readFileAsync = Promise.promisify(fs.readFile);
const writeFileAsync = Promise.promisify(fs.writeFile);

async function buildPlugin() {
  commands.log('Building plugin...');
  await commands.exec('npm run build', WORKING_DIR);
}

async function getPluginVersion() {
  const mainFile = await readFileAsync(MAIN_PHP_FILE, 'utf8');
  const match = PLUGIN_VERSION_REGEX.exec(mainFile);
  if (!match) {
    throw new Error('Could not find the project version name in main file');
  }
  return match[1];
}

async function getPluginVersionInDefine() {
  const mainFile = await readFileAsync(MAIN_PHP_FILE, 'utf8');
  const match = PLUGIN_VERSION_DEFINE_REGEX.exec(mainFile);
  if (!match) {
    throw new Error('Could not find the project version name in main file');
  }
  return match[1];
}

async function updateProjectVersion(version) {
  const currentVersion = await getPluginVersion();
  const currentVersionInDefine = await getPluginVersionInDefine();
  const mainFile = await readFileAsync(MAIN_PHP_FILE, 'utf8');
  const updatedMainFile = mainFile
    .replace(util.format(PLUGIN_VERSION_FORMAT, currentVersion), util.format(PLUGIN_VERSION_FORMAT, version))
    .replace(util.format(PLUGIN_VERSION_DEFINE_FORMAT, currentVersionInDefine), util.format(PLUGIN_VERSION_DEFINE_FORMAT, version));
  await writeFileAsync(MAIN_PHP_FILE, updatedMainFile);
}

// Run this if call directly from command line
if (require.main === module) {
  releaseTask.withWorkingDir(WORKING_DIR);
  releaseTask.withAfterVersionUpdateTask(updateProjectVersion);
  releaseTask.withBuildTask(buildPlugin);
  releaseTask.run(process.argv[2], process.argv[3]);
}
