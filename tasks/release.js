const {releaseTask, commands} = require('@ayro/commons');
const fs = require('fs');
const path = require('path');
const util = require('util');
const Promise = require('bluebird');

const WORKING_DIR = path.resolve(__dirname, '../');
const MAIN_PHP_FILE = path.join(WORKING_DIR, 'ayro.php');
const PLUGIN_VERSION_REGEX = /Version:           (\d+\.\d+\.\d+)/;
const PLUGIN_VERSION_FORMAT = 'Version:           %s';
const PLUGIN_VERSION_DEFINE_REGEX = /define\('AYRO_PLUGIN_VERSION', '(\d+\.\d+\.\d+)'\);/;
const PLUGIN_VERSION_DEFINE_FORMAT = 'define(\'AYRO_PLUGIN_VERSION\', \'%s\');';

const readFileAsync = Promise.promisify(fs.readFile);
const writeFileAsync = Promise.promisify(fs.writeFile);

function buildPlugin() {
  return Promise.coroutine(function* () {
    commands.log('Building plugin...');
    yield commands.exec('npm run build', WORKING_DIR);
  })();
}

function getPluginVersion() {
  return Promise.coroutine(function* () {
    const mainFile = yield readFileAsync(MAIN_PHP_FILE, 'utf8');
    const match = PLUGIN_VERSION_REGEX.exec(mainFile);
    if (!match) {
      throw new Error('Could not find the project version name in main file');
    }
    return match[1];
  })();
};

function getPluginVersionInDefine() {
  return Promise.coroutine(function* () {
    const mainFile = yield readFileAsync(MAIN_PHP_FILE, 'utf8');
    const match = PLUGIN_VERSION_DEFINE_REGEX.exec(mainFile);
    if (!match) {
      throw new Error('Could not find the project version name in main file');
    }
    return match[1];
  })();
};

function updateProjectVersion(version) {
  return Promise.coroutine(function* () {
    const currentVersion = yield getPluginVersion();
    const currentVersionInDefine = yield getPluginVersionInDefine();
    const mainFile = yield readFileAsync(MAIN_PHP_FILE, 'utf8');
    const updatedMainFile = mainFile
      .replace(util.format(PLUGIN_VERSION_FORMAT, currentVersion), util.format(PLUGIN_VERSION_FORMAT, version))
      .replace(util.format(PLUGIN_VERSION_DEFINE_FORMAT, currentVersionInDefine), util.format(PLUGIN_VERSION_DEFINE_FORMAT, version))
    yield writeFileAsync(MAIN_PHP_FILE, updatedMainFile);
  })();
};

// Run this if call directly from command line
if (require.main === module) {
  releaseTask.withWorkingDir(WORKING_DIR);
  releaseTask.withAfterVersionUpdateTask(updateProjectVersion);
  releaseTask.withBuildTask(buildPlugin);
  releaseTask.run(process.argv[2], process.argv[3]);
}
