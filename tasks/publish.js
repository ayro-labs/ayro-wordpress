const packageJson = require('../package');
const {publishTask, commands} = require('@ayro/commons');
const path = require('path');
const GitHubApi = require('@octokit/rest');
const Promise = require('bluebird');

const WORKING_DIR = path.resolve(__dirname, '../');
const GITHUB_REPOSITORY_NAME = 'ayro-wordpress';
const GITHUB_REPOSITORY_OWNER = 'ayrolabs';
const WP_REPOSITORY_USERNAME = 'ayrolabs';
const WP_REPOSITORY_URL = 'https://plugins.svn.wordpress.org/ayro';
const WP_TRUNK_REPOSITORY_NAME = 'ayro-wordpress-svn-trunk';
const WP_ASSETS_REPOSITORY_NAME = 'ayro-wordpress-svn-assets';
const TEMP_DIR = '/tmp';
const TEMP_GITHUB_REPOSITORY_DIR = path.join(TEMP_DIR, GITHUB_REPOSITORY_NAME);
const TEMP_WP_TRUNK_REPOSITORY_DIR = path.join(TEMP_DIR, WP_TRUNK_REPOSITORY_NAME);
const TEMP_WP_ASSETS_REPOSITORY_DIR = path.join(TEMP_DIR, WP_ASSETS_REPOSITORY_NAME);

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

function prepareGithubRepository() {
  return Promise.coroutine(function* () {
    commands.log('Preparing Github repository...');
    yield commands.exec(`rm -rf ${GITHUB_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`git clone git@github.com:${GITHUB_REPOSITORY_OWNER}/${GITHUB_REPOSITORY_NAME}.git ${GITHUB_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`rm -rf ${GITHUB_REPOSITORY_NAME}/*`, TEMP_DIR);
  })();
}

function copyGithubFiles() {
  return Promise.coroutine(function* () {
    commands.log('Copying files to Github repository...');
    yield commands.exec(`cp dist/ayro-wordpress.zip ${TEMP_GITHUB_REPOSITORY_DIR}`);
  })();
}

function pushGithubFiles() {
  return Promise.coroutine(function* () {
    commands.log('Committing, tagging and pushing files to Github repository...');
    yield commands.exec('git add .', TEMP_GITHUB_REPOSITORY_DIR);
    yield commands.exec(`git commit -am 'Release ${packageJson.version}'`, TEMP_GITHUB_REPOSITORY_DIR);
    yield commands.exec('git push origin master', TEMP_GITHUB_REPOSITORY_DIR);
    yield commands.exec(`git tag ${packageJson.version}`, TEMP_GITHUB_REPOSITORY_DIR);
    yield commands.exec('git push --tags', TEMP_GITHUB_REPOSITORY_DIR);
  })();
}

function createGithubRelease() {
  return Promise.coroutine(function* () {
    commands.log('Creating Github release...');
    const createRelease = Promise.promisify(gitHubApi.repos.createRelease);
    yield createRelease({
      owner: GITHUB_REPOSITORY_OWNER,
      repo: GITHUB_REPOSITORY_NAME,
      tag_name: packageJson.version,
      name: `Release ${packageJson.version}`,
    });
  })();
}

function beforePublish() {
  return Promise.coroutine(function* () {
    yield prepareGithubRepository();
    yield copyGithubFiles();
    yield pushGithubFiles();
    yield createGithubRelease();
  })();
}

function prepareSvnTrunkRepository() {
  return Promise.coroutine(function* () {
    commands.log('Preparing Subversion trunk repository...');
    yield commands.exec(`rm -rf ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`svn checkout ${WP_REPOSITORY_URL}/trunk ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`rm -rf ${WP_TRUNK_REPOSITORY_NAME}/*`, TEMP_DIR);
  })();
}

function copySvnTrunkFiles() {
  return Promise.coroutine(function* () {
    commands.log('Copying files to Subversion trunk repository...');
    yield commands.exec(`unzip dist/ayro-wordpress.zip -d ${TEMP_DIR}`);
    yield commands.exec(`mv ayro/* ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`rm -Rf ayro `, TEMP_DIR);
  })();
}

function pushSvnTrunkFiles() {
  return Promise.coroutine(function* () {
    commands.log('Committing and pushing files to Subversion trunk repository...');
    yield commands.exec('svn add *', TEMP_WP_TRUNK_REPOSITORY_DIR);
    yield commands.exec(`svn commit --force-interactive --username ${WP_REPOSITORY_USERNAME} -m 'Release ${packageJson.version}'`, TEMP_WP_TRUNK_REPOSITORY_DIR);
  })();
}

function prepareSvnAssetsRepository() {
  return Promise.coroutine(function* () {
    commands.log('Preparing Subversion assets repository...');
    yield commands.exec(`rm -rf ${WP_ASSETS_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`svn checkout ${WP_REPOSITORY_URL}/assets ${WP_ASSETS_REPOSITORY_NAME}`, TEMP_DIR);
    yield commands.exec(`rm -rf ${WP_ASSETS_REPOSITORY_NAME}/*`, TEMP_DIR);
  })();
}

function copySvnAssetsFiles() {
  return Promise.coroutine(function* () {
    commands.log('Copying files to Subversion assets repository...');
    yield commands.exec(`cp assets/* ${TEMP_WP_ASSETS_REPOSITORY_DIR}`);
  })();
}

function pushSvnAssetsFiles() {
  return Promise.coroutine(function* () {
    commands.log('Committing and pushing files to Subversion assets repository...');
    yield commands.exec('svn add *', TEMP_WP_ASSETS_REPOSITORY_DIR);
    yield commands.exec(`svn commit --force-interactive --username ${WP_REPOSITORY_USERNAME} -m 'Release ${packageJson.version}'`, TEMP_WP_ASSETS_REPOSITORY_DIR);
  })();
}

function publishToWordPressSvn() {
  return Promise.coroutine(function* () {
    yield prepareSvnTrunkRepository();
    yield copySvnTrunkFiles();
    yield pushSvnTrunkFiles();
    yield prepareSvnAssetsRepository();
    yield copySvnAssetsFiles();
    yield pushSvnAssetsFiles();
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
