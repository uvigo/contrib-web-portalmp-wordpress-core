const path = require('path')
const fs = require('fs-extra')
const zip = require('bestzip');

// Base path
const basePath = path.resolve('./') + '/'

// Package read
const packageDev = require(basePath + 'package.json');

// Set package name
packageDev.name = 'wpcoreuvigo';

// Folder
const baseFolder = packageDev.name + '/'

// Array with files and folders
const sources = [
  'wpcoreuvigo.php',
  'index.php',
  'uninstall.php',
  'README.txt',
  'CHANGELOG.md',
  'LICENSE.md',
  'admin',
  'includes',
  'languages',
  'public',
  'updater',
];

const excludedFolders = ['admin/assets', 'public/assets'];
const excludedFiles = ['.DS_Store', 'rollup.config.js', 'config.json'];

// console.log(basePath + baseFolder);

const removeExcludedFolders = () => {
  excludedFolders.forEach(function(excPath) {
    console.log('Remove folder:', packageDev.name + '/' + excPath);
    fs.removeSync(basePath + packageDev.name + '/' + excPath);
  });
};

const removeFiles = (src) => {
  return ! excludedFiles.includes(path.basename(src));
}

const zipFile = () => {
  const filename = './' + packageDev.name + '-v' + packageDev.version + '.zip';
  zip(filename, [packageDev.name + '/'], function(err) {
    if  (err) {
      console.error(err.stack);
      process.exit(1);
    } else {
      console.log('Zip "' + filename + '" created.');
      fs.remove(basePath + baseFolder)
      .then(() => {
        console.log('All done!');
      })
      .catch(err => {
        console.error(err)
      })
    }
  });
};


// Remove folder to recreate
fs.remove(basePath + baseFolder)
.then(() => {
  console.log('Deleted old folder success!');

  fs.ensureDir(basePath + baseFolder)
  .then(() => {
    console.log('New folder created success!');

    let pending = sources.length;

    sources.forEach(function(item) {
      const itemSource = basePath + item;
      const itemDestination = basePath + baseFolder + item;
      fs.copy(itemSource, itemDestination, {
        preserveTimestamps: true,
        filter: removeFiles,
      })
      .then(() => {
        console.log(`Copied '${item}' success!`);
        if (!--pending) {
          removeExcludedFolders();
          zipFile();
        }
      })
      .catch(err => {
        console.error(err);
        process.exit(1);
      })
    });
  })
  .catch(err => {
    console.error(err);
    process.exit(1);
  })
})
.catch(err => {
  console.error(err);
  process.exit(1);
})
