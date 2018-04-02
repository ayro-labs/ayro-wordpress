const {releaseTask, commands} = require('@ayro/commons');
const path = require('path');
const Promise = require('bluebird');

const WORKING_DIR = path.resolve(__dirname, '../');

function buildPlugin() {
  return Promise.coroutine(function* () {
    commands.log('Building plugin...');
    yield commands.exec('npm run build', WORKING_DIR);
  })();
}

// Run this if call directly from command line
if (require.main === module) {
  releaseTask.withWorkingDir(WORKING_DIR);
  releaseTask.withBuildTask(buildPlugin);
  releaseTask.run(process.argv[2], process.argv[3]);
}
