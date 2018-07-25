/* eslint-disable import/no-extraneous-dependencies */

'use strict';

const project = require('../package');
const {commands, publish} = require('release-n-publish');
const path = require('path');
const GitHubApi = require('@octokit/rest');
const Promise = require('bluebird');

const WORKING_DIR = path.resolve();
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

const createReleaseAsync = Promise.promisify(gitHubApi.repos.createRelease);

async function buildPlugin() {
  commands.log('Building plugin...');
  await commands.exec('npm run build', WORKING_DIR);
}

async function prepareGithubRepository() {
  commands.log('Preparing Github repository...');
  await commands.exec(`rm -rf ${GITHUB_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`git clone git@github.com:${GITHUB_REPOSITORY_OWNER}/${GITHUB_REPOSITORY_NAME}.git ${GITHUB_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`rm -rf ${GITHUB_REPOSITORY_NAME}/*`, TEMP_DIR);
}

async function copyGithubFiles() {
  commands.log('Copying files to Github repository...');
  await commands.exec(`cp dist/ayro-wordpress.zip ${TEMP_GITHUB_REPOSITORY_DIR}/ayro-wordpress-${project.version}.zip`);
}

async function pushGithubFiles() {
  commands.log('Committing, tagging and pushing files to Github repository...');
  await commands.exec('git add --all', TEMP_GITHUB_REPOSITORY_DIR);
  await commands.exec(`git commit -am 'Release ${project.version}'`, TEMP_GITHUB_REPOSITORY_DIR);
  await commands.exec('git push origin master', TEMP_GITHUB_REPOSITORY_DIR);
  await commands.exec(`git tag ${project.version}`, TEMP_GITHUB_REPOSITORY_DIR);
  await commands.exec('git push --tags', TEMP_GITHUB_REPOSITORY_DIR);
}

async function createGithubRelease() {
  commands.log('Creating Github release...');
  await createReleaseAsync({
    owner: GITHUB_REPOSITORY_OWNER,
    repo: GITHUB_REPOSITORY_NAME,
    tag_name: project.version,
    name: `Release ${project.version}`,
  });
}

async function beforePublish() {
  await prepareGithubRepository();
  await copyGithubFiles();
  await pushGithubFiles();
  await createGithubRelease();
}

async function prepareSvnTrunkRepository() {
  commands.log('Preparing Subversion trunk repository...');
  await commands.exec(`rm -rf ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`svn checkout ${WP_REPOSITORY_URL}/trunk ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`rm -rf ${WP_TRUNK_REPOSITORY_NAME}/*`, TEMP_DIR);
}

async function copyFilesToSvnTrunk() {
  commands.log('Copying files to Subversion trunk repository...');
  await commands.exec(`unzip dist/ayro-wordpress.zip -d ${TEMP_DIR}`);
  await commands.exec(`mv ayro/* ${WP_TRUNK_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec('rm -Rf ayro', TEMP_DIR);
}

async function pushFilesToSvnTrunk() {
  commands.log('Committing and pushing files to Subversion trunk repository...');
  await commands.exec('svn add * --force', TEMP_WP_TRUNK_REPOSITORY_DIR);
  await commands.exec(`svn commit --force-interactive --username ${WP_REPOSITORY_USERNAME} -m 'Release ${project.version}'`, TEMP_WP_TRUNK_REPOSITORY_DIR);
}

async function prepareSvnAssetsRepository() {
  commands.log('Preparing Subversion assets repository...');
  await commands.exec(`rm -rf ${WP_ASSETS_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`svn checkout ${WP_REPOSITORY_URL}/assets ${WP_ASSETS_REPOSITORY_NAME}`, TEMP_DIR);
  await commands.exec(`rm -rf ${WP_ASSETS_REPOSITORY_NAME}/*`, TEMP_DIR);
}

async function copyFilesToSvnAssets() {
  commands.log('Copying files to Subversion assets repository...');
  await commands.exec(`cp src/assets/* ${TEMP_WP_ASSETS_REPOSITORY_DIR}`);
}

async function pushFilesToSvnAssets() {
  commands.log('Committing and pushing files to Subversion assets repository...');
  await commands.exec('svn add * --force', TEMP_WP_ASSETS_REPOSITORY_DIR);
  await commands.exec(`svn commit --force-interactive --username ${WP_REPOSITORY_USERNAME} -m 'Release ${project.version}'`, TEMP_WP_ASSETS_REPOSITORY_DIR);
}

async function publishToWordPressSvn() {
  await prepareSvnTrunkRepository();
  await copyFilesToSvnTrunk();
  await pushFilesToSvnTrunk();
  await prepareSvnAssetsRepository();
  await copyFilesToSvnAssets();
  await pushFilesToSvnAssets();
}

// Run this if call directly from command line
if (require.main === module) {
  publish.setWorkingDir(WORKING_DIR);
  publish.setBuildTask(buildPlugin);
  publish.setBeforePublishTask(beforePublish);
  publish.setPublishTask(publishToWordPressSvn);
  publish.run();
}
