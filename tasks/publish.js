const packageJson = require('../package');
const {publishTask, commands} = require('@ayro/commons');
const path = require('path');
const GitHubApi = require('@octokit/rest');
const Promise = require('bluebird');

const REPOSITORY_NAME = 'ayro-wordpress';
const REPOSITORY_OWNER = 'ayrolabs';
const WORKING_DIR = path.resolve(__dirname, '../');
const TEMP_DIR = '/tmp';
const TEMP_REPOSITORY_DIR = `${TEMP_DIR}/${REPOSITORY_NAME}`;

const gitHubApi = new GitHubApi();
gitHubApi.authenticate({
  type: 'token',
  token: process.env.GITHUB_ACCESS_TOKEN,
});

function buildPlugin() {
  return Promise.coroutine(function* () {
    commands.log('Building plugin...');
    yield commands.exec('npm run build', WORKING_DIR);
  })();
}

function prepareRepository() {
  return Promise.coroutine(function* () {
    commands.log('Preparing Github repository...');
    yield commands.exec(`rm -rf ${TEMP_REPOSITORY_DIR}`);
    yield commands.exec(`git clone git@github.com:${REPOSITORY_OWNER}/${REPOSITORY_NAME}.git ${REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec('rm -rf *', TEMP_REPOSITORY_DIR);
  })();
}

function copyFiles() {
  return Promise.coroutine(function* () {
    commands.log('Copying files...');
    yield commands.exec(`cp dist/${packageJson.name}.zip ${TEMP_REPOSITORY_DIR}/${packageJson.name}-${packageJson.version}.zip`);
  })();
}

function pushFiles() {
  return Promise.coroutine(function* () {
    commands.log('Committing, tagging and pushing files to Github repository...');
    yield commands.exec('git add .', TEMP_REPOSITORY_DIR);
    yield commands.exec(`git commit -am 'Release ${packageJson.version}'`, TEMP_REPOSITORY_DIR);
    yield commands.exec('git push origin master', TEMP_REPOSITORY_DIR);
    yield commands.exec(`git tag ${packageJson.version}`, TEMP_REPOSITORY_DIR);
    yield commands.exec('git push --tags', TEMP_REPOSITORY_DIR);
  })();
}

function createRelease() {
  return Promise.coroutine(function* () {
    commands.log('Creating Github release...');
    const createRelease = Promise.promisify(gitHubApi.repos.createRelease);
    yield createRelease({
      owner: REPOSITORY_OWNER,
      repo: REPOSITORY_NAME,
      tag_name: version,
      name: `Release ${packageJson.version}`,
    });
  })();
}

function beforePublish() {
  return Promise.coroutine(function* () {
    yield prepareRepository();
    yield copyFiles();
    yield pushFiles();
    yield createRelease();
  })();
}

function publishToWordPressSvn() {
  return Promise.coroutine(function* () {
    commands.log('Publishing to WordPress Subversion repository...');
  })();
}

// Run this if call directly from command line
if (require.main === module) {
  publishTask.withWorkingDir(WORKING_DIR);
  publishTask.withBuildTask(buildPlugin);
  publishTask.withBeforePublishTask(beforePublish);
  publishTask.withPublishTask(publishToWordPressSvn);
  publishTask.run();
}
